<?php

declare(strict_types=1);

namespace Aksoyih\Crashguard;

use Throwable;
use ErrorException;

/**
 * Crashguard - Universal PHP Error Handler
 * 
 * Provides enhanced error handling with structured output,
 * dark/light mode support, and LLM-ready markdown export.
 */
class Crashguard
{
    private static ?self $instance = null;
    private bool $registered = false;
    private bool $isDarkMode = false;
    private array $config = [
        'auto_detect_theme' => true,
        'show_arguments' => true,
        'show_variables' => false, // Disabled by default for security
        'redact_sensitive' => true,
        'max_string_length' => 1000,
        'cli_mode' => null, // Auto-detect if null
    ];
    
    private array $sensitiveKeys = [
        'password', 'passwd', 'secret', 'key', 'token', 'auth',
        'api_key', 'apikey', 'private', 'credential', 'session'
    ];

    private function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->detectTheme();
        $this->detectCliMode();
    }

    /**
     * Get or create Crashguard instance
     */
    public static function getInstance(array $config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        
        return self::$instance;
    }

    /**
     * Register error and exception handlers
     */
    public function register(): self
    {
        if ($this->registered) {
            return $this;
        }

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
        
        $this->registered = true;
        
        return $this;
    }

    /**
     * Unregister handlers
     */
    public function unregister(): self
    {
        if (!$this->registered) {
            return $this;
        }

        restore_error_handler();
        restore_exception_handler();
        
        $this->registered = false;
        
        return $this;
    }

    /**
     * Handle PHP errors
     */
    public function handleError(int $severity, string $message, string $file = '', int $line = 0): bool
    {
        // Don't handle suppressed errors
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $exception = new ErrorException($message, 0, $severity, $file, $line);
        $this->handleException($exception);
        
        return true;
    }

    /**
     * Handle uncaught exceptions
     */
    public function handleException(Throwable $exception): void
    {
        $errorData = $this->gatherErrorData($exception);
        
        if ($this->isCliMode()) {
            $this->renderCliOutput($errorData);
        } else {
            $this->renderHtmlOutput($errorData);
        }
        
        exit(1);
    }

    /**
     * Handle fatal errors during shutdown
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $exception = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            
            $this->handleException($exception);
        }
    }

    /**
     * Gather comprehensive error data
     */
    private function gatherErrorData(Throwable $exception): array
    {
        $data = [
            'message' => $exception->getMessage(),
            'class' => get_class($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $this->formatStackTrace($exception),
            'request' => $this->gatherRequestData(),
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];

        // Add HTTP status code if it's an HTTP exception
        if (method_exists($exception, 'getStatusCode')) {
            $data['http_status'] = $exception->getStatusCode();
        } elseif (method_exists($exception, 'getCode') && $exception->getCode() >= 100 && $exception->getCode() < 600) {
            $data['http_status'] = $exception->getCode();
        }

        return $data;
    }

    /**
     * Format stack trace with enhanced information
     */
    private function formatStackTrace(Throwable $exception): array
    {
        $trace = $exception->getTrace();
        $formattedTrace = [];

        // Add the exception location as first frame
        array_unshift($trace, [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'function' => 'throw',
            'class' => get_class($exception),
        ]);

        foreach ($trace as $index => $frame) {
            $formattedFrame = [
                'index' => $index,
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? 'unknown',
                'class' => $frame['class'] ?? null,
                'type' => $frame['type'] ?? null,
            ];

            if ($this->config['show_arguments'] && isset($frame['args'])) {
                $formattedFrame['args'] = $this->formatArguments($frame['args']);
            }

            $formattedTrace[] = $formattedFrame;
        }

        return $formattedTrace;
    }

    /**
     * Format function/method arguments
     */
    private function formatArguments(array $args): array
    {
        $formatted = [];
        
        foreach ($args as $arg) {
            $formatted[] = $this->formatValue($arg);
        }
        
        return $formatted;
    }

    /**
     * Format a value for display
     */
    private function formatValue($value): array
    {
        $type = gettype($value);
        $result = ['type' => $type];

        switch ($type) {
            case 'string':
                $result['value'] = $this->truncateString($value);
                $result['length'] = strlen($value);
                break;
            case 'integer':
            case 'double':
            case 'boolean':
                $result['value'] = $value;
                break;
            case 'NULL':
                $result['value'] = null;
                break;
            case 'array':
                $result['count'] = count($value);
                $result['value'] = $this->formatArray($value);
                break;
            case 'object':
                $result['class'] = get_class($value);
                $result['value'] = $this->formatObject($value);
                break;
            case 'resource':
                $result['resource_type'] = get_resource_type($value);
                $result['value'] = (string) $value;
                break;
            default:
                $result['value'] = (string) $value;
        }

        return $result;
    }

    /**
     * Format array for display
     */
    private function formatArray(array $array): array
    {
        $formatted = [];
        $count = 0;
        
        foreach ($array as $key => $value) {
            if ($count >= 10) { // Limit array display
                $formatted['...'] = '(' . (count($array) - $count) . ' more items)';
                break;
            }
            
            if ($this->config['redact_sensitive'] && $this->isSensitiveKey($key)) {
                $formatted[$key] = '[REDACTED]';
            } else {
                $formatted[$key] = $this->formatValue($value);
            }
            
            $count++;
        }
        
        return $formatted;
    }

    /**
     * Format object for display
     */
    private function formatObject(object $object): array
    {
        $reflection = new \ReflectionObject($object);
        $properties = [];
        
        foreach ($reflection->getProperties() as $property) {
            if (count($properties) >= 10) { // Limit properties
                $properties['...'] = '(more properties)';
                break;
            }
            
            $property->setAccessible(true);
            $key = $property->getName();
            
            if ($this->config['redact_sensitive'] && $this->isSensitiveKey($key)) {
                $properties[$key] = '[REDACTED]';
            } else {
                try {
                    $value = $property->getValue($object);
                    $properties[$key] = $this->formatValue($value);
                } catch (Throwable $e) {
                    $properties[$key] = '[ERROR: ' . $e->getMessage() . ']';
                }
            }
        }
        
        return $properties;
    }

    /**
     * Check if a key is sensitive
     */
    private function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);
        
        foreach ($this->sensitiveKeys as $sensitive) {
            if (strpos($key, $sensitive) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Truncate long strings
     */
    private function truncateString(string $string): string
    {
        if (strlen($string) <= $this->config['max_string_length']) {
            return $string;
        }
        
        return substr($string, 0, $this->config['max_string_length']) . '... (truncated)';
    }

    /**
     * Gather request data
     */
    private function gatherRequestData(): array
    {
        $data = [];
        
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $data['method'] = $_SERVER['REQUEST_METHOD'];
        }
        
        if (isset($_SERVER['REQUEST_URI'])) {
            $data['uri'] = $_SERVER['REQUEST_URI'];
        }
        
        if (isset($_SERVER['HTTP_HOST'])) {
            $data['host'] = $_SERVER['HTTP_HOST'];
        }
        
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
        
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
        }
        
        return $data;
    }

    /**
     * Detect if running in CLI mode
     */
    private function detectCliMode(): void
    {
        if ($this->config['cli_mode'] !== null) {
            return;
        }
        
        $this->config['cli_mode'] = php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST']);
    }

    /**
     * Check if in CLI mode
     */
    private function isCliMode(): bool
    {
        return $this->config['cli_mode'] === true;
    }

    /**
     * Detect system theme preference
     */
    private function detectTheme(): void
    {
        if (!$this->config['auto_detect_theme']) {
            return;
        }
        
        // Try to detect from user agent or headers
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // This is a basic implementation - in practice, theme detection
        // would rely on JavaScript or user preferences
        $this->isDarkMode = false;
    }

    /**
     * Render CLI output
     */
    private function renderCliOutput(array $errorData): void
    {
        $renderer = new CliRenderer();
        echo $renderer->render($errorData);
    }

    /**
     * Render HTML output
     */
    private function renderHtmlOutput(array $errorData): void
    {
        $renderer = new HtmlRenderer($this->isDarkMode);
        echo $renderer->render($errorData);
    }

    /**
     * Generate markdown representation of error
     */
    public function generateMarkdown(array $errorData): string
    {
        $renderer = new MarkdownRenderer();
        return $renderer->render($errorData);
    }

    /**
     * Set configuration
     */
    public function setConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Get configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
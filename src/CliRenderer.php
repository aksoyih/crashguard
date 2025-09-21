<?php

declare(strict_types=1);

namespace Aksoyih\Crashguard;

/**
 * CLI renderer for terminal output
 */
class CliRenderer implements RendererInterface
{
    private const COLORS = [
        'reset' => "\033[0m",
        'bold' => "\033[1m",
        'dim' => "\033[2m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'bg_red' => "\033[41m",
    ];

    public function render(array $errorData): string
    {
        $output = '';
        
        // Header
        $output .= $this->colorize("\n🚨 CRASHGUARD ERROR REPORT\n", 'bold', 'bg_red');
        $output .= str_repeat('=', 60) . "\n\n";
        
        // Error Summary
        $output .= $this->colorize("ERROR SUMMARY\n", 'bold', 'red');
        $output .= str_repeat('-', 20) . "\n";
        $output .= $this->colorize("Message: ", 'bold') . $errorData['message'] . "\n";
        $output .= $this->colorize("Type: ", 'bold') . $errorData['class'] . "\n";
        $output .= $this->colorize("File: ", 'bold') . $errorData['file'] . "\n";
        $output .= $this->colorize("Line: ", 'bold') . $errorData['line'] . "\n";
        
        if (!empty($errorData['http_status'])) {
            $output .= $this->colorize("HTTP Status: ", 'bold') . $errorData['http_status'] . "\n";
        }
        
        $output .= $this->colorize("Time: ", 'bold') . $errorData['timestamp'] . "\n\n";
        
        // Request Information
        if (!empty($errorData['request'])) {
            $output .= $this->colorize("REQUEST INFORMATION\n", 'bold', 'blue');
            $output .= str_repeat('-', 20) . "\n";
            
            foreach ($errorData['request'] as $key => $value) {
                $output .= $this->colorize(ucfirst(str_replace('_', ' ', $key)) . ": ", 'bold') . $value . "\n";
            }
            $output .= "\n";
        }
        
        // Stack Trace
        if (!empty($errorData['trace'])) {
            $output .= $this->colorize("STACK TRACE\n", 'bold', 'yellow');
            $output .= str_repeat('-', 20) . "\n";
            
            foreach ($errorData['trace'] as $frame) {
                $output .= $this->colorize("#{$frame['index']} ", 'bold', 'cyan');
                
                if ($frame['class'] && $frame['function']) {
                    $output .= $this->colorize($frame['class'] . $frame['type'] . $frame['function'] . '()', 'magenta');
                } elseif ($frame['function']) {
                    $output .= $this->colorize($frame['function'] . '()', 'magenta');
                }
                
                $output .= "\n    " . $this->colorize("at ", 'dim') . $frame['file'] . ":" . $frame['line'] . "\n";
                
                // Arguments
                if (!empty($frame['args'])) {
                    $output .= $this->colorize("    Arguments:\n", 'dim');
                    foreach ($frame['args'] as $index => $arg) {
                        $output .= $this->colorize("      [{$index}] ", 'dim');
                        $output .= $this->colorize($arg['type'], 'green') . ' ';
                        
                        if (is_string($arg['value'])) {
                            $value = strlen($arg['value']) > 100 ? substr($arg['value'], 0, 100) . '...' : $arg['value'];
                            $output .= '"' . $value . '"';
                        } else {
                            $output .= json_encode($arg['value']);
                        }
                        $output .= "\n";
                    }
                }
                
                $output .= "\n";
            }
        }
        
        // System Information
        $output .= $this->colorize("SYSTEM INFORMATION\n", 'bold', 'white');
        $output .= str_repeat('-', 20) . "\n";
        $output .= $this->colorize("PHP Version: ", 'bold') . $errorData['php_version'] . "\n";
        $output .= $this->colorize("Memory Usage: ", 'bold') . $this->formatBytes($errorData['memory_usage']) . "\n";
        $output .= $this->colorize("Peak Memory: ", 'bold') . $this->formatBytes($errorData['peak_memory']) . "\n\n";
        
        // Footer
        $output .= str_repeat('=', 60) . "\n";
        $output .= $this->colorize("💡 Tip: Use Crashguard in web context for enhanced UI and markdown export\n", 'dim');
        
        return $output;
    }

    private function colorize(string $text, string ...$colors): string
    {
        // Check if colors are supported
        if (!$this->supportsColors()) {
            return $text;
        }
        
        $colorCodes = '';
        foreach ($colors as $color) {
            if (isset(self::COLORS[$color])) {
                $colorCodes .= self::COLORS[$color];
            }
        }
        
        return $colorCodes . $text . self::COLORS['reset'];
    }

    private function supportsColors(): bool
    {
        // Check if we're in a terminal that supports colors
        if (function_exists('posix_isatty') && !posix_isatty(STDOUT)) {
            return false;
        }
        
        // Check TERM environment variable
        $term = getenv('TERM');
        if ($term === false || $term === 'dumb') {
            return false;
        }
        
        // Check if NO_COLOR environment variable is set
        if (getenv('NO_COLOR') !== false) {
            return false;
        }
        
        return true;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }
        
        return round($bytes, 2) . ' ' . $units[$index];
    }
}
<?php

declare(strict_types=1);

namespace Aksoyih\Crashguard;

/**
 * HTML renderer for error pages
 */
class HtmlRenderer implements RendererInterface
{
    private bool $isDarkMode;

    public function __construct(bool $isDarkMode = false)
    {
        $this->isDarkMode = $isDarkMode;
    }

    public function render(array $errorData): string
    {
        $html = $this->renderHeader();
        $html .= $this->renderStyles();
        $html .= $this->renderBody($errorData);
        $html .= $this->renderFooter();
        
        return $html;
    }

    private function renderHeader(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crashguard - Error Details</title>
';
    }

    private function renderStyles(): string
    {
        return '<style>
        :root {
            --bg-primary: #ffffff;
            --bg-card: #ffffff;
            --bg-section: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #868e96;
            --border-color: #e9ecef;
            --border-light: #f1f3f4;
            --blue-primary: #4285f4;
            --blue-light: #e8f0fe;
            --shadow-light: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --shadow-medium: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.5;
            font-size: 14px;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .error-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        .error-icon {
            width: 48px;
            height: 48px;
            background: var(--blue-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .error-icon svg {
            width: 24px;
            height: 24px;
            fill: var(--blue-primary);
        }

        .error-title-section {
            flex: 1;
        }

        .error-status {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .error-title {
            font-size: 28px;
            font-weight: 400;
            color: var(--text-primary);
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .error-description {
            color: var(--text-secondary);
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 32px;
        }

        .section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .section-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-light);
            font-weight: 500;
            font-size: 16px;
            color: var(--text-primary);
        }

        .section-content {
            padding: 24px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 16px 24px;
            align-items: start;
        }

        .detail-label {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 400;
        }

        .detail-value {
            color: var(--text-primary);
            font-size: 14px;
            word-break: break-all;
        }

        .stack-trace-content {
            font-family: "SF Mono", Monaco, "Cascadia Code", "Roboto Mono", Consolas, "Courier New", monospace;
            font-size: 13px;
            line-height: 1.4;
            color: var(--text-primary);
            background: var(--bg-section);
            padding: 16px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
        }

        .copy-button-container {
            text-align: right;
            margin-top: 32px;
        }

        .copy-markdown-btn {
            background: var(--blue-primary);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease;
        }

        .copy-markdown-btn:hover {
            background: #3367d6;
        }

        .copy-markdown-btn:active {
            background: #2a56c6;
        }

        .copy-feedback {
            margin-left: 12px;
            color: var(--blue-primary);
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .copy-feedback.show {
            opacity: 1;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 16px;
            }
            
            .error-header {
                flex-direction: column;
                text-align: center;
            }
            
            .error-icon {
                align-self: center;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .detail-label {
                font-weight: 500;
            }
            
            .section-content {
                padding: 16px;
            }
        }
    </style>';
    }

    private function renderBody(array $errorData): string
    {
        $html = '</head><body>';
        $html .= '<div class="container">';
        
        // Error Header with icon and title
        $html .= $this->renderErrorHeader($errorData);
        
        // Error Description
        $html .= $this->renderErrorDescription($errorData);
        
        // Details Section
        $html .= $this->renderDetailsSection($errorData);
        
        // Stack Trace
        $html .= $this->renderStackTrace($errorData);
        
        // Request Information
        if (!empty($errorData['request'])) {
            $html .= $this->renderRequestInfo($errorData['request']);
        }
        
        // Runtime Context (System Information)
        $html .= $this->renderRuntimeContext($errorData);
        
        // Copy Markdown Button
        $html .= $this->renderCopyButton($errorData);
        
        $html .= '</div>';
        
        return $html;
    }

    private function renderErrorHeader(array $errorData): string
    {
        $statusText = '';
        if (!empty($errorData['http_status'])) {
            $statusText = $errorData['http_status'] . ' Bad Request';
        } else {
            $statusText = 'Error';
        }
        
        $html = '<div class="error-header">';
        $html .= '<div class="error-icon">';
        $html .= '<svg viewBox="0 0 24 24">';
        $html .= '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>';
        $html .= '</svg>';
        $html .= '</div>';
        $html .= '<div class="error-title-section">';
        $html .= '<div class="error-status">' . htmlspecialchars($statusText) . '</div>';
        $html .= '<h1 class="error-title">' . htmlspecialchars($errorData['class']) . '</h1>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function renderErrorDescription(array $errorData): string
    {
        $description = $errorData['message'];
        
        // Add context-specific descriptions for common errors
        if (strpos($errorData['class'], 'InvalidArgumentException') !== false) {
            $description = 'The provided argument is not valid. Please check the input parameters and ensure they meet the required criteria. This error typically occurs when a function or method receives an unexpected or incorrect value.';
        }
        
        return '<div class="error-description">' . htmlspecialchars($description) . '</div>';
    }

    private function renderDetailsSection(array $errorData): string
    {
        $html = '<div class="section">';
        $html .= '<div class="section-header">Details</div>';
        $html .= '<div class="section-content">';
        $html .= '<div class="details-grid">';
        
        $html .= '<div class="detail-label">Exception Class</div>';
        $html .= '<div class="detail-value">' . htmlspecialchars($errorData['class']) . '</div>';
        
        if (!empty($errorData['http_status'])) {
            $html .= '<div class="detail-label">HTTP Status Code</div>';
            $html .= '<div class="detail-value">' . htmlspecialchars((string)$errorData['http_status']) . '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function renderCopyButton(array $errorData): string
    {
        $markdownData = json_encode($errorData, JSON_HEX_QUOT | JSON_HEX_APOS);
        
        return '<div class="copy-button-container">
            <button class="copy-markdown-btn" onclick="copyAsMarkdown()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                </svg>
                Copy as Markdown
            </button>
            <span class="copy-feedback" id="copyFeedback">Copied to clipboard!</span>
        </div>
        
        <script>
        const errorData = ' . $markdownData . ';
        
        function copyAsMarkdown() {
            const markdown = generateMarkdown(errorData);
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(markdown).then(() => {
                    showCopyFeedback();
                });
            } else {
                // Fallback for older browsers
                const textarea = document.createElement("textarea");
                textarea.value = markdown;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand("copy");
                document.body.removeChild(textarea);
                showCopyFeedback();
            }
        }
        
        function showCopyFeedback() {
            const feedback = document.getElementById("copyFeedback");
            feedback.classList.add("show");
            setTimeout(() => {
                feedback.classList.remove("show");
            }, 2000);
        }
        
        function generateMarkdown(data) {
            let md = "# 🚨 Error Report\\n\\n";
            
            md += "## Error Summary\\n";
            md += "- **Message**: " + data.message + "\\n";
            md += "- **Type**: `" + data.class + "`\\n";
            md += "- **File**: `" + data.file + "`\\n";
            md += "- **Line**: " + data.line + "\\n";
            
            if (data.http_status) {
                md += "- **HTTP Status**: " + data.http_status + "\\n";
            }
            
            md += "- **Timestamp**: " + data.timestamp + "\\n\\n";
            
            if (data.request && Object.keys(data.request).length > 0) {
                md += "## Request Information\\n";
                if (data.request.method) md += "- **Method**: " + data.request.method + "\\n";
                if (data.request.uri) md += "- **URI**: `" + data.request.uri + "`\\n";
                if (data.request.host) md += "- **Host**: " + data.request.host + "\\n";
                if (data.request.ip) md += "- **IP**: " + data.request.ip + "\\n";
                md += "\\n";
            }
            
            md += "## Stack Trace\\n\\n";
            
            data.trace.forEach((frame, index) => {
                md += "### Frame " + frame.index + "\\n";
                
                if (frame.class && frame.function) {
                    md += "**Function**: `" + frame.class + frame.type + frame.function + "()`\\n";
                } else if (frame.function) {
                    md += "**Function**: `" + frame.function + "()`\\n";
                }
                
                md += "**Location**: `" + frame.file + ":" + frame.line + "`\\n";
                
                if (frame.args && frame.args.length > 0) {
                    md += "**Arguments**:\\n";
                    frame.args.forEach((arg, argIndex) => {
                        md += "- `" + arg.type + "`: ";
                        if (typeof arg.value === "string") {
                            md += "`" + arg.value.substring(0, 100);
                            if (arg.value.length > 100) md += "...";
                            md += "`";
                        } else {
                            md += JSON.stringify(arg.value);
                        }
                        md += "\\n";
                    });
                }
                
                md += "\\n";
            });
            
            md += "## System Information\\n";
            md += "- **PHP Version**: " + data.php_version + "\\n";
            md += "- **Memory Usage**: " + formatBytes(data.memory_usage) + "\\n";
            md += "- **Peak Memory**: " + formatBytes(data.peak_memory) + "\\n";
            
            return md;
        }
        
        function formatBytes(bytes) {
            if (bytes === 0) return "0 Bytes";
            const k = 1024;
            const sizes = ["Bytes", "KB", "MB", "GB"];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
        }
        </script>';
    }

    private function renderStackTrace(array $errorData): string
    {
        if (empty($errorData['trace'])) {
            return '';
        }
        
        $html = '<div class="section">';
        $html .= '<div class="section-header">Stack Trace</div>';
        $html .= '<div class="section-content">';
        
        $stackTraceText = '';
        foreach ($errorData['trace'] as $frame) {
            $line = '#' . $frame['index'] . ' ';
            $line .= $frame['file'] . '(' . $frame['line'] . '): ';
            
            if ($frame['class'] && $frame['function']) {
                $line .= $frame['class'] . $frame['type'] . $frame['function'] . '()';
            } elseif ($frame['function']) {
                $line .= $frame['function'] . '()';
            }
            
            $stackTraceText .= $line . "\n";
        }
        
        $html .= '<div class="stack-trace-content">' . htmlspecialchars($stackTraceText) . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function renderRequestInfo(array $requestData): string
    {
        $html = '<div class="section">';
        $html .= '<div class="section-header">Request Information</div>';
        $html .= '<div class="section-content">';
        $html .= '<div class="details-grid">';
        
        foreach ($requestData as $key => $value) {
            $label = ucfirst(str_replace('_', ' ', $key));
            $html .= '<div class="detail-label">' . htmlspecialchars($label) . '</div>';
            $html .= '<div class="detail-value">' . htmlspecialchars($value) . '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function renderRuntimeContext(array $errorData): string
    {
        $html = '<div class="section">';
        $html .= '<div class="section-header">Runtime Context</div>';
        $html .= '<div class="section-content">';
        $html .= '<div class="details-grid">';
        
        $html .= '<div class="detail-label">PHP Version</div>';
        $html .= '<div class="detail-value">' . htmlspecialchars($errorData['php_version']) . '</div>';
        
        // Detect operating system
        $os = 'Unknown';
        if (defined('PHP_OS_FAMILY')) {
            $os = PHP_OS_FAMILY;
        } elseif (defined('PHP_OS')) {
            $os = PHP_OS;
        }
        
        $html .= '<div class="detail-label">Operating System</div>';
        $html .= '<div class="detail-value">' . htmlspecialchars($os) . '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
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

    private function renderFooter(): string
    {
        return '</body></html>';
    }
}
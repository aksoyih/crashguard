<?php
/**
 * Design Demo - Test the new design based on the mockup
 * 
 * This example demonstrates the updated Crashguard design that matches
 * the provided mockup.
 */

require_once __DIR__ . '/../src/autoload.php';

use Aksoyih\Crashguard\Crashguard;

// Initialize Crashguard
$crashguard = Crashguard::getInstance();
$crashguard->register();

// Create a custom exception class that matches the design
class HttpException extends Exception
{
    private int $statusCode;
    
    public function __construct(string $message, int $statusCode = 500, Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

// Function to simulate the exact error from the design
function demonstrateDesignError()
{
    // Simulate the MyClass->myMethod() call from the design
    $myClass = new stdClass(); // This will cause the error
    $myClass->myMethod(); // Call to undefined method
}

// Check if we should trigger the error
if (isset($_GET['trigger']) && $_GET['trigger'] === 'error') {
    demonstrateDesignError();
}

// If no error is triggered, show the demo page
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crashguard Design Demo</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            line-height: 1.6;
            background: #f8f9fa;
        }
        .demo-card {
            background: white;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            margin-bottom: 24px;
        }
        .trigger-btn { 
            display: inline-block; 
            padding: 12px 24px; 
            background: #4285f4; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            margin: 16px 0;
            font-weight: 500;
        }
        .trigger-btn:hover { 
            background: #3367d6; 
        }
        h1 {
            font-size: 28px;
            font-weight: 400;
            color: #212529;
            margin-bottom: 16px;
        }
        h2 {
            font-size: 20px;
            font-weight: 500;
            color: #212529;
            margin: 24px 0 16px 0;
        }
        .feature-list {
            background: #f8f9fa;
            border-left: 4px solid #4285f4;
            padding: 16px;
            margin: 16px 0;
        }
        .feature-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .feature-list li {
            margin-bottom: 8px;
        }
        code {
            background: #f1f3f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: "SF Mono", Monaco, monospace;
            font-size: 13px;
        }
        .design-note {
            background: #e8f0fe;
            border: 1px solid #d2e3fc;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
        }
        .design-note strong {
            color: #1a73e8;
        }
    </style>
</head>
<body>
    <div class="demo-card">
        <h1>🎨 Crashguard Design Demo</h1>
        
        <div class="design-note">
            <strong>✨ New Design Implemented!</strong><br>
            This demo showcases the updated Crashguard design that matches the provided mockup. 
            The design features a clean, modern interface with proper spacing, typography, and visual hierarchy.
        </div>
        
        <p>This demo shows the new Crashguard design based on your mockup. The updated design includes:</p>
        
        <div class="feature-list">
            <ul>
                <li><strong>Clean Header:</strong> Blue circular icon with error status and exception type</li>
                <li><strong>Descriptive Text:</strong> Context-aware error descriptions</li>
                <li><strong>Structured Sections:</strong> Details, Stack Trace, Request Information, Runtime Context</li>
                <li><strong>Modern Layout:</strong> Card-based design with proper spacing and shadows</li>
                <li><strong>Copy Button:</strong> Blue "Copy as Markdown" button with icon</li>
                <li><strong>Typography:</strong> Clean, readable fonts with proper hierarchy</li>
                <li><strong>Responsive Design:</strong> Works on both desktop and mobile</li>
            </ul>
        </div>
        
        <h2>Try the New Design</h2>
        <p>Click the button below to trigger an error and see the new design in action:</p>
        
        <a href="?trigger=error" class="trigger-btn">🔥 Trigger Demo Error</a>
        
        <h2>Design Features</h2>
        
        <p><strong>Header Section:</strong> Features a blue circular icon with an information symbol, followed by the HTTP status (if applicable) and the exception class name as the main title.</p>
        
        <p><strong>Error Description:</strong> Provides context-aware descriptions for common exceptions, making errors more understandable for developers.</p>
        
        <p><strong>Structured Information:</strong> All error details are organized into clean sections with consistent spacing and typography.</p>
        
        <p><strong>Stack Trace:</strong> Simplified, monospace formatting that's easy to read and copy.</p>
        
        <p><strong>Copy Functionality:</strong> The blue "Copy as Markdown" button is positioned at the bottom right, matching the mockup design.</p>
        
        <div class="design-note">
            <strong>💡 Pro Tip:</strong> The design automatically adapts to different screen sizes and provides a consistent experience across all devices.
        </div>
    </div>
</body>
</html>
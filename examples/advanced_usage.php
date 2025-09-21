<?php
/**
 * Advanced Crashguard Usage Example
 * 
 * This example demonstrates advanced configuration options
 * and integration patterns.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Aksoyih\Crashguard\Crashguard;

// Advanced configuration
$config = [
    'auto_detect_theme' => true,
    'show_arguments' => true,
    'show_variables' => false, // Keep false for security in production
    'redact_sensitive' => true,
    'max_string_length' => 500,
    'cli_mode' => null, // Auto-detect
];

// Initialize with custom configuration
$crashguard = Crashguard::getInstance($config);
$crashguard->register();

// Example: Complex function with multiple arguments
function processUserData(array $userData, string $action, bool $validateOnly = false, $options = null)
{
    // Simulate some processing
    $processedData = [
        'user_id' => $userData['id'] ?? null,
        'username' => $userData['username'] ?? 'anonymous',
        'email' => $userData['email'] ?? null,
        'password' => $userData['password'] ?? null, // This will be redacted
        'api_key' => $userData['api_key'] ?? null,   // This will be redacted
        'preferences' => $userData['preferences'] ?? [],
    ];
    
    // Simulate an error condition
    if ($action === 'delete' && !isset($userData['id'])) {
        throw new InvalidArgumentException("User ID is required for delete operations");
    }
    
    if ($action === 'create' && empty($userData['username'])) {
        throw new RuntimeException("Username cannot be empty when creating user");
    }
    
    // Simulate a nested function call
    return validateAndSave($processedData, $validateOnly);
}

function validateAndSave(array $data, bool $validateOnly)
{
    // Simulate validation
    if (empty($data['username'])) {
        throw new InvalidArgumentException("Username validation failed");
    }
    
    if (!$validateOnly) {
        return saveToDatabase($data);
    }
    
    return ['valid' => true];
}

function saveToDatabase(array $data)
{
    // Simulate database error
    throw new RuntimeException("Database connection failed: Unable to connect to MySQL server");
}

// Example usage with different error scenarios

echo "<h2>Crashguard Advanced Examples</h2>\n";
echo "<p>Click on the links below to trigger different types of errors:</p>\n";

if (isset($_GET['example'])) {
    switch ($_GET['example']) {
        case 'missing_id':
            // This will show argument redaction in action
            processUserData([
                'username' => 'john_doe',
                'email' => 'john@example.com',
                'password' => 'secret123',
                'api_key' => 'sk_live_abcd1234',
            ], 'delete');
            break;
            
        case 'empty_username':
            processUserData([
                'id' => 123,
                'username' => '',
                'email' => 'john@example.com',
                'password' => 'secret123',
            ], 'create');
            break;
            
        case 'database_error':
            processUserData([
                'id' => 123,
                'username' => 'john_doe',
                'email' => 'john@example.com',
                'password' => 'secret123',
                'preferences' => [
                    'theme' => 'dark',
                    'notifications' => true,
                    'private_key' => 'rsa_private_key_here', // Will be redacted
                ],
            ], 'create');
            break;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Crashguard Advanced Examples</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .example-link { 
            display: inline-block; 
            margin: 10px; 
            padding: 10px 20px; 
            background: #007cba; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
        .example-link:hover { background: #005a87; }
    </style>
</head>
<body>
    <h2>Crashguard Advanced Examples</h2>
    <p>Click on the links below to trigger different types of errors and see how Crashguard handles them:</p>
    
    <a href="?example=missing_id" class="example-link">Missing ID Error</a>
    <a href="?example=empty_username" class="example-link">Validation Error</a>
    <a href="?example=database_error" class="example-link">Database Error</a>
    
    <h3>Features Demonstrated:</h3>
    <ul>
        <li><strong>Argument Display</strong>: Function arguments are captured and displayed</li>
        <li><strong>Sensitive Data Redaction</strong>: Fields like 'password', 'api_key', 'private_key' are automatically redacted</li>
        <li><strong>Stack Trace</strong>: Complete call stack with file locations</li>
        <li><strong>Request Context</strong>: HTTP method, URL, and other request details</li>
        <li><strong>Copy as Markdown</strong>: One-click export for LLM analysis</li>
        <li><strong>Dark/Light Mode</strong>: Automatic theme detection</li>
    </ul>
    
    <h3>CLI Usage:</h3>
    <p>Run this script from the command line to see the CLI renderer in action:</p>
    <pre>php advanced_usage.php</pre>
</body>
</html>
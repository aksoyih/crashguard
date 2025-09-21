<?php
/**
 * Standalone Crashguard Usage (No Composer Required)
 * 
 * This example demonstrates how to use Crashguard without Composer
 * by using the built-in autoloader.
 */

// Include the standalone autoloader
require_once __DIR__ . '/../src/autoload.php';

use Aksoyih\Crashguard\Crashguard;

// Initialize Crashguard
$crashguard = Crashguard::getInstance();
$crashguard->register();

echo "<h1>Standalone Crashguard Example</h1>";
echo "<p>This example runs without Composer, using the built-in autoloader.</p>";

// Example error function
function triggerStandaloneError($data, $action = 'process')
{
    if (empty($data)) {
        throw new InvalidArgumentException("Data cannot be empty!");
    }
    
    if ($action === 'fail') {
        throw new RuntimeException("Simulated processing failure");
    }
    
    // Simulate a more complex error
    $processor = new stdClass();
    $processor->nonExistentMethod(); // This will cause an error
}

// Check if we should trigger an error
if (isset($_GET['trigger']) && $_GET['trigger'] === 'error') {
    triggerStandaloneError(['test' => 'data'], 'fail');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Standalone Crashguard Example</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            line-height: 1.6;
        }
        .trigger-btn { 
            display: inline-block; 
            padding: 12px 24px; 
            background: #dc3545; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 10px 0;
        }
        .trigger-btn:hover { background: #c82333; }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        code {
            background: #f1f1f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <h1>🚨 Standalone Crashguard Example</h1>
    
    <div class="info-box">
        <strong>No Composer Required!</strong><br>
        This example uses the built-in autoloader located at <code>src/autoload.php</code>.
        Perfect for legacy projects or quick prototyping.
    </div>
    
    <h2>Features Demonstrated:</h2>
    <ul>
        <li>✅ Standalone autoloader (no Composer needed)</li>
        <li>✅ Beautiful error page rendering</li>
        <li>✅ Stack trace with function arguments</li>
        <li>✅ Automatic sensitive data redaction</li>
        <li>✅ Copy as Markdown functionality</li>
        <li>✅ Responsive design</li>
    </ul>
    
    <h2>Try It Out:</h2>
    <a href="?trigger=error" class="trigger-btn">🔥 Trigger Error</a>
    
    <h2>Integration Steps:</h2>
    <ol>
        <li>Copy the <code>src/</code> folder to your project</li>
        <li>Include the autoloader: <code>require_once 'src/autoload.php';</code></li>
        <li>Initialize Crashguard: <code>Crashguard::getInstance()->register();</code></li>
        <li>That's it! Your errors are now beautifully handled</li>
    </ol>
    
    <h2>Code Example:</h2>
    <pre><code>&lt;?php
// Include the autoloader
require_once 'src/autoload.php';

use Aksoyih\Crashguard\Crashguard;

// Initialize and register
$crashguard = Crashguard::getInstance();
$crashguard->register();

// Your code here...
throw new Exception("Something went wrong!");
?&gt;</code></pre>

    <div class="info-box">
        <strong>💡 Pro Tip:</strong> For production use, consider using Composer for better dependency management and autoloading performance.
    </div>
</body>
</html>
<?php
#!/usr/bin/env php
<?php
/**
 * CLI Crashguard Example
 * 
 * This example demonstrates Crashguard behavior in CLI environments.
 * Run with: php cli_example.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Aksoyih\Crashguard\Crashguard;

// Force CLI mode for demonstration
$crashguard = Crashguard::getInstance(['cli_mode' => true]);
$crashguard->register();

echo "🚀 Crashguard CLI Example\n";
echo "========================\n\n";

echo "This script will demonstrate different types of errors in CLI mode.\n";
echo "Crashguard automatically detects CLI environment and provides colorized output.\n\n";

function demonstrateCliErrors()
{
    $examples = [
        '1' => 'Simple Exception',
        '2' => 'Type Error',
        '3' => 'Argument Error with Context',
        '4' => 'Nested Function Calls',
    ];
    
    echo "Available examples:\n";
    foreach ($examples as $key => $description) {
        echo "  {$key}. {$description}\n";
    }
    
    echo "\nEnter example number (1-4) or 'q' to quit: ";
    $handle = fopen("php://stdin", "r");
    $choice = trim(fgets($handle));
    fclose($handle);
    
    switch ($choice) {
        case '1':
            throw new Exception("This is a simple CLI exception example!");
            
        case '2':
            $string = "not an array";
            $string[0]->someMethod(); // Type error
            
        case '3':
            processDataWithArguments(['user' => 'john', 'password' => 'secret123'], 'invalid_action');
            
        case '4':
            deepNestedFunction();
            
        case 'q':
            echo "Goodbye!\n";
            exit(0);
            
        default:
            echo "Invalid choice. Try again.\n\n";
            demonstrateCliErrors();
    }
}

function processDataWithArguments(array $data, string $action)
{
    if ($action === 'invalid_action') {
        throw new InvalidArgumentException("Unsupported action: {$action}");
    }
    
    return $data;
}

function deepNestedFunction()
{
    levelOne();
}

function levelOne()
{
    levelTwo(['param1' => 'value1', 'secret_key' => 'this_will_be_redacted']);
}

function levelTwo(array $params)
{
    levelThree($params, 42, true);
}

function levelThree(array $config, int $number, bool $flag)
{
    throw new RuntimeException("Deep nested error with multiple arguments and types!");
}

// Start the interactive demo
demonstrateCliErrors();
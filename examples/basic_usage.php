<?php
/**
 * Basic Crashguard Usage Example
 * 
 * This example demonstrates the simplest way to integrate Crashguard
 * into your PHP application.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Aksoyih\Crashguard\Crashguard;

// Initialize and register Crashguard
$crashguard = Crashguard::getInstance();
$crashguard->register();

// Your application code here...

// Example 1: Trigger a simple exception
function demonstrateException()
{
    throw new Exception("This is a demonstration error message!");
}

// Example 2: Trigger a type error
function demonstrateTypeError()
{
    $array = ['key' => 'value'];
    $array->someMethod(); // This will cause a fatal error
}

// Example 3: Trigger a custom exception with HTTP status
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

function demonstrateHttpException()
{
    throw new HttpException("Page not found", 404);
}

// Uncomment one of these lines to see Crashguard in action:

// demonstrateException();
// demonstrateTypeError();
// demonstrateHttpException();

echo "No errors triggered. Uncomment one of the demonstration functions to see Crashguard in action.\n";
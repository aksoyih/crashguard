<?php
/**
 * Simple autoloader for standalone usage (without Composer)
 * 
 * This file provides a basic PSR-4 autoloader for Crashguard
 * when Composer is not available.
 */

spl_autoload_register(function ($class) {
    $prefix = 'Aksoyih\\Crashguard\\';
    $baseDir = __DIR__ . '/';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($class, $len);
    
    // Replace namespace separators with directory separators
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// For convenience, also include the main Crashguard class
require_once __DIR__ . '/Crashguard.php';
require_once __DIR__ . '/RendererInterface.php';
require_once __DIR__ . '/HtmlRenderer.php';
require_once __DIR__ . '/CliRenderer.php';
require_once __DIR__ . '/MarkdownRenderer.php';
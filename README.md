# 🚨 Crashguard

> Universal PHP error handling package with enhanced debugging features and LLM-ready output

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.0-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Crashguard is a framework-agnostic PHP error handling package that automatically adapts to different PHP environments, providing developers with clear, structured, and AI-friendly error output for enhanced debugging workflows.

## ✨ Features

- 🎯 **Universal Compatibility** - Works across all PHP environments (raw PHP, custom frameworks, etc.)
- 🎨 **Beautiful Error Pages** - Clean, responsive HTML UI with automatic dark/light mode detection
- 📋 **Copy as Markdown** - One-click export of error details optimized for LLM analysis
- 🖥️ **CLI Support** - Colorized terminal output with graceful degradation
- 🔒 **Security First** - Automatic redaction of sensitive data (passwords, keys, tokens)
- 📊 **Rich Context** - Function arguments, stack traces, request details, and system information
- ⚡ **Lightweight** - Minimal dependencies, maximum performance

## 🚀 Installation

Install via Composer:

```bash
composer require aksoyih/crashguard
```

## 📖 Quick Start

### Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use Aksoyih\Crashguard\Crashguard;

// Initialize and register error handlers
$crashguard = Crashguard::getInstance();
$crashguard->register();

// Your application code here...
throw new Exception("Something went wrong!");
```

### Advanced Configuration

```php
<?php
use Aksoyih\Crashguard\Crashguard;

$config = [
    'auto_detect_theme' => true,        // Auto dark/light mode detection
    'show_arguments' => true,           // Display function arguments
    'show_variables' => false,          // Show local variables (security risk)
    'redact_sensitive' => true,         // Redact passwords, keys, etc.
    'max_string_length' => 1000,        // Truncate long strings
    'cli_mode' => null,                 // Auto-detect CLI environment
];

$crashguard = Crashguard::getInstance($config);
$crashguard->register();
```

## 🎯 What Makes Crashguard Special?

### 🔍 Enhanced Error Information

- **Exception Details**: Message, type, HTTP status code (if applicable)
- **Precise Location**: File path and line number
- **Stack Trace**: Complete call stack with function arguments
- **Request Context**: HTTP method, URL, headers, and client information
- **System Info**: PHP version, memory usage, and performance metrics

### 🎨 Intelligent UI

- **Automatic Theme Detection**: Adapts to system dark/light mode preferences
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Collapsible Sections**: Organize information without overwhelming users
- **Syntax Highlighting**: Code snippets and data structures are beautifully formatted

### 📋 LLM Integration

The "Copy as Markdown" feature generates perfectly formatted error reports for AI analysis:

```markdown
# 🚨 Error Report

## Error Summary
- **Message**: Call to undefined method stdClass::someMethod()
- **Type**: `Error`
- **File**: `/path/to/your/file.php`
- **Line**: 42
- **Timestamp**: 2024-01-15 14:30:22

## Stack Trace

### Frame 0
**Function**: `MyClass->processData()`
**Location**: `/path/to/file.php:42`

**Arguments**:
- **[0]** `array`: {"user_id": 123, "email": "user@example.com"}
- **[1]** `string`: "process_action"
- **[2]** `boolean`: true

...
```

### 🛡️ Security Features

Crashguard automatically redacts sensitive information:

- Passwords and passphrases
- API keys and tokens
- Private keys and certificates
- Session data and authentication details
- Any field containing: `password`, `secret`, `key`, `token`, `auth`, `private`

## 🖥️ CLI Support

When running in CLI environments, Crashguard provides colorized terminal output:

```bash
php your_script.php
```

```
🚨 CRASHGUARD ERROR REPORT
============================================================

ERROR SUMMARY
--------------------
Message: Call to undefined method stdClass::someMethod()
Type: Error
File: /path/to/your/script.php
Line: 15
Time: 2024-01-15 14:30:22

STACK TRACE
--------------------
#0 MyClass->processData()
    at /path/to/script.php:15
    Arguments:
      [0] array {"user_id": 123}
      [1] string "action"

💡 Tip: Use Crashguard in web context for enhanced UI and markdown export
```

## 📂 Examples

Check out the `/examples` directory for comprehensive usage examples:

- **`basic_usage.php`** - Simple integration example
- **`advanced_usage.php`** - Advanced configuration and web interface
- **`cli_example.php`** - Interactive CLI demonstration

### Running Examples

1. **Basic Example**:
   ```bash
   php examples/basic_usage.php
   ```

2. **Web Interface** (requires web server):
   ```bash
   php -S localhost:8000 -t examples/
   # Visit: http://localhost:8000/advanced_usage.php
   ```

3. **CLI Interactive Demo**:
   ```bash
   php examples/cli_example.php
   ```

## ⚙️ Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `auto_detect_theme` | `bool` | `true` | Automatically detect dark/light mode |
| `show_arguments` | `bool` | `true` | Display function/method arguments |
| `show_variables` | `bool` | `false` | Show local variables (security risk) |
| `redact_sensitive` | `bool` | `true` | Redact sensitive data automatically |
| `max_string_length` | `int` | `1000` | Maximum string length before truncation |
| `cli_mode` | `bool\|null` | `null` | Force CLI mode (null = auto-detect) |

## 🔧 Integration Patterns

### Framework Integration

```php
// In your framework's bootstrap or error handler
use Aksoyih\Crashguard\Crashguard;

class AppBootstrap
{
    public function initializeErrorHandling()
    {
        if (app()->environment('local', 'development')) {
            Crashguard::getInstance([
                'show_arguments' => true,
                'show_variables' => false, // Keep false in shared environments
            ])->register();
        }
    }
}
```

### Custom Exception Integration

```php
class ApiException extends Exception
{
    private int $statusCode;
    
    public function __construct(string $message, int $statusCode = 500)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

// Crashguard will automatically detect and display the HTTP status
throw new ApiException("Resource not found", 404);
```

## 🎯 Use Cases

- **Development & Debugging**: Enhanced error visibility during development
- **API Development**: Clear error responses with detailed context
- **Legacy Code Modernization**: Add modern error handling to existing projects
- **AI-Assisted Debugging**: Export error details for LLM analysis
- **Team Collaboration**: Share detailed error reports with team members
- **Production Debugging**: Safe error reporting without exposing sensitive data

## 🔮 Roadmap

- [ ] **Logging Integration**: Hooks for Monolog, Sentry, and other logging systems
- [ ] **Custom Themes**: User-defined color schemes and layouts
- [ ] **Error Filtering**: Configurable error type filtering and suppression
- [ ] **Performance Monitoring**: Integration with APM tools
- [ ] **Localization**: Multi-language error messages
- [ ] **Error Analytics**: Pattern detection and reporting

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Inspired by modern error handling patterns from Laravel, Symfony, and other frameworks
- Built with ❤️ for the PHP community
- Special thanks to all contributors and users providing feedback

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/aksoyih/crashguard/issues)
- **Discussions**: [GitHub Discussions](https://github.com/aksoyih/crashguard/discussions)
- **Email**: your-email@example.com

---

**Made with ❤️ by [aksoyih](https://github.com/aksoyih)**

*Crashguard - Because debugging should be beautiful and intelligent.*
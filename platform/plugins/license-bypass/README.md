# License Bypass Plugin

A comprehensive plugin for Botble CMS that disables licensing and activation functionality while preserving tracking capabilities.

## 📝 Configuration Notice

**Environment restrictions have been removed and Google/Facebook tracking has been enabled per user request.**

## Features

- ✅ **Complete License Bypass**: Disables all license activation and verification checks
- ✅ **Tracking Enabled**: Google Analytics and Facebook tracking are now enabled per user request
- ✅ **HTTP Request Interception**: Prevents external API calls to license servers only
- ✅ **Environment Flexible**: Works in all environments (restrictions removed per user request)
- ✅ **Configurable**: Fully configurable through configuration files
- ✅ **Logging Support**: Optional logging for debugging purposes
- ✅ **Clean Architecture**: Follows Laravel and PHP best practices

## Installation

The plugin is already installed in your `platform/plugins/license-bypass` directory.

### Activation

```bash
php artisan cms:plugin:activate license-bypass
```

### Deactivation

```bash
php artisan cms:plugin:deactivate license-bypass
```

## Configuration

The plugin can be configured through the `config/license-bypass.php` file:

```php
return [
    'enabled' => env('LICENSE_BYPASS_ENABLED', true),
    'allowed_environments' => ['local', 'development', 'dev', 'testing'],
    'blocked_domains' => [
        'license.botble.com',
        'google-analytics.com',
        // ... more domains
    ],
    // ... more configuration options
];
```

### Environment Variables

Add these to your `.env` file for additional control:

```env
LICENSE_BYPASS_ENABLED=true
LICENSE_BYPASS_LOGGING=false
```

## What It Does

### License Bypass
- Intercepts all license activation requests
- Provides mock responses for license verification
- Creates necessary bypass files
- Overrides license-related settings

### Tracking Configuration
- Google Analytics scripts are now enabled per user request
- Facebook integration is now enabled per user request
- External font loading is now enabled per user request
- Only license-related tracking is blocked

### HTTP Interception
- Blocks requests to `license.botble.com`
- Intercepts analytics service calls
- Provides mock responses for external services

## Architecture

The plugin follows clean architecture principles:

```
src/
├── Services/
│   ├── LicenseBypassService.php      # Main bypass logic
│   └── HttpInterceptorService.php    # HTTP request interception
├── Http/
│   └── Middleware/
│       └── DisableLicenseMiddleware.php # Request middleware
├── Providers/
│   └── LicenseBypassServiceProvider.php # Service registration
└── Plugin.php                        # Plugin lifecycle management
```

### Key Components

1. **LicenseBypassService**: Core service handling bypass logic
2. **HttpInterceptorService**: Manages HTTP request interception
3. **DisableLicenseMiddleware**: Middleware for request handling
4. **LicenseBypassServiceProvider**: Service container registration

## Safety Features

- **Environment Restrictions**: Only works in development environments
- **Configuration Validation**: Validates configuration before activation
- **Error Handling**: Comprehensive error handling with logging
- **Graceful Degradation**: Fails safely without breaking the application

## Logging

Enable logging for debugging:

```env
LICENSE_BYPASS_LOGGING=true
```

Logs are written to the configured Laravel log channel with the prefix `[License Bypass]`.

## Troubleshooting

### Plugin Won't Activate
- Check that you're in a development environment
- Verify the `allowed_environments` configuration
- Check the Laravel logs for error messages

### License Checks Still Appearing
- Ensure the plugin is activated
- Check that middleware is properly registered
- Verify HTTP interception is working

### External Requests Still Going Through
- Check the `blocked_domains` configuration
- Verify HTTP client interception is active
- Review the logs for blocked requests

## Development

### Running Tests
```bash
# Run plugin-specific tests (if implemented)
php artisan test --filter=LicenseBypass
```

### Code Style
The plugin follows PSR-12 coding standards and includes:
- Strict type declarations
- Comprehensive documentation
- Error handling
- Logging support

## Security Considerations

- The plugin includes environment checks to prevent production use
- All external requests are blocked, not just redirected
- No sensitive data is logged
- Configuration is validated before use

## License

This plugin is for development use only and should not be distributed or used in production environments.

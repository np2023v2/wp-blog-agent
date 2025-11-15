# Testing Guide for WP Blog Agent

This document provides information about running tests for the WP Blog Agent plugin.

## Prerequisites

- PHP 7.4 or higher
- Composer

## Installation

Install the testing dependencies using Composer:

```bash
composer install
```

This will install:
- PHPUnit 9.5+
- Yoast PHPUnit Polyfills

## Running Tests

### Run All Tests

```bash
composer test
```

Or use PHPUnit directly:

```bash
./vendor/bin/phpunit
```

### Run Tests with Detailed Output

```bash
./vendor/bin/phpunit --testdox
```

### Run Specific Test Class

```bash
./vendor/bin/phpunit tests/TextUtilsTest.php
```

### Generate Code Coverage Report

```bash
composer test-coverage
```

This will generate an HTML coverage report in the `coverage/` directory.

## Test Structure

Tests are organized in the `tests/` directory:

```
tests/
├── bootstrap.php       # Test bootstrap file with WordPress mocks
├── LoggerTest.php      # Tests for WP_Blog_Agent_Logger class
├── TextUtilsTest.php   # Tests for WP_Blog_Agent_Text_Utils class
└── ValidatorTest.php   # Tests for WP_Blog_Agent_Validator class
```

## Test Coverage

Current test coverage includes:

### WP_Blog_Agent_Text_Utils (18 tests)
- JSON encoding and cleaning
- UTF-8 encoding fixes
- Text sanitization for prompts
- Array cleaning for JSON
- Null byte and control character removal

### WP_Blog_Agent_Validator (23 tests)
- AI provider validation
- API key validation
- Schedule frequency validation
- Topic and keyword validation
- Hashtag validation
- Email list validation
- Integer sanitization

### WP_Blog_Agent_Logger (13 tests)
- Log initialization
- Multiple log levels (info, error, warning, success, debug)
- Logging with context
- Log rotation
- Old log cleanup

## Writing Tests

### Test Naming Convention

Test methods should follow the pattern:
```php
public function test_<method_name>_<scenario>() {
    // Test code
}
```

Example:
```php
public function test_validate_api_key_with_valid_key() {
    $key = 'sk-abc123def456';
    $result = WP_Blog_Agent_Validator::validate_api_key($key, 'openai');
    $this->assertEquals($key, $result);
}
```

### Test Structure

1. **Arrange**: Set up test data and conditions
2. **Act**: Execute the method being tested
3. **Assert**: Verify the results

### WordPress Mocks

The `tests/bootstrap.php` file provides mock implementations of common WordPress functions:
- `wp_upload_dir()`
- `wp_mkdir_p()`
- `current_time()`
- `get_option()`
- `sanitize_text_field()`
- `is_email()`
- And more...

The `WP_Error` class is also mocked for testing error conditions.

## CI/CD Integration

Tests can be integrated into CI/CD pipelines:

### GitHub Actions Example

```yaml
name: PHPUnit Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
      
    - name: Run tests
      run: composer test
```

## Troubleshooting

### Composer Install Issues

If you encounter GitHub API rate limits during `composer install`, you can:

1. Use the `--no-interaction` flag:
   ```bash
   composer install --no-interaction --prefer-dist
   ```

2. Or create a GitHub personal access token and configure Composer:
   ```bash
   composer config -g github-oauth.github.com YOUR_TOKEN
   ```

### Test Failures

If tests fail:

1. Check PHP version compatibility (7.4+)
2. Ensure all dependencies are installed
3. Review error messages for specific failures
4. Check that temporary directories are writable

## Best Practices

1. **Write tests for new features**: Always add tests when adding new functionality
2. **Keep tests isolated**: Each test should be independent
3. **Use descriptive test names**: Make it clear what each test validates
4. **Test edge cases**: Include tests for error conditions and boundary cases
5. **Keep tests fast**: Avoid external dependencies and network calls in unit tests

## Future Enhancements

Potential improvements to the test suite:

- Add integration tests for API classes (OpenAI, Gemini)
- Add tests for admin UI classes
- Add tests for database operations
- Implement test fixtures for complex scenarios
- Add performance benchmarking tests

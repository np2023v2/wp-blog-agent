# WP_Blog_Agent_Text_Utils Usage Guide

## Overview

The `WP_Blog_Agent_Text_Utils` class provides utilities for handling text encoding issues and ensuring data can be safely JSON-encoded for API requests.

## Problem Solved

When working with user-generated content (post titles, content, etc.), you may encounter:
- Invalid UTF-8 sequences
- Control characters (null bytes, etc.)
- Mixed character encodings
- Characters that cause `json_encode()` to fail

These issues can cause API requests to fail silently, leading to difficult-to-debug errors.

## Available Methods

### 1. `clean_for_json($text)`

Cleans text to ensure it can be JSON-encoded without errors.

```php
$text = "Hello\x00World\x01Test"; // Contains null byte and control chars
$clean = WP_Blog_Agent_Text_Utils::clean_for_json($text);
// Result: "HelloWorldTest"
```

**What it does:**
- Fixes UTF-8 encoding issues
- Removes null bytes
- Removes control characters (except whitespace)
- Normalizes line breaks
- Removes problematic characters

### 2. `fix_utf8_encoding($text)`

Detects and fixes UTF-8 encoding issues.

```php
$text = "Café" . chr(0xFF); // Mixed encoding
$fixed = WP_Blog_Agent_Text_Utils::fix_utf8_encoding($text);
// Result: Properly encoded UTF-8 string
```

**What it does:**
- Checks if string is valid UTF-8
- Detects source encoding (UTF-8, ISO-8859-1, Windows-1252, ASCII)
- Converts to UTF-8 if needed
- Logs encoding conversions for debugging

### 3. `sanitize_for_prompt($text)`

Cleans and normalizes text for use in AI prompts.

```php
$html = "<h1>Title</h1><p>Content with   multiple   spaces.</p>";
$clean = WP_Blog_Agent_Text_Utils::sanitize_for_prompt($html);
// Result: "Title Content with multiple spaces."
```

**What it does:**
- Cleans for JSON encoding
- Strips HTML tags
- Normalizes whitespace
- Trims the result

### 4. `safe_json_encode($data, $options = 0, $depth = 512)`

Safely encodes data to JSON with error logging.

```php
$data = array('title' => 'Test', 'content' => 'Content');
$json = WP_Blog_Agent_Text_Utils::safe_json_encode($data);
// Result: JSON string or false with detailed error logging
```

**What it does:**
- Encodes data to JSON
- Logs detailed error information if encoding fails
- Returns false on failure (with logging)

### 5. `clean_array_for_json($array)`

Recursively cleans all strings in an array.

```php
$data = array(
    'title' => "Test\x00Title",
    'nested' => array('key' => "Value\x01")
);
$clean = WP_Blog_Agent_Text_Utils::clean_array_for_json($data);
// Result: All strings cleaned recursively
```

**What it does:**
- Recursively traverses arrays
- Cleans all string values
- Preserves array structure

### 6. `is_json_encodable($data)`

Validates that data can be JSON-encoded.

```php
$data = array('test' => 'value');
if (WP_Blog_Agent_Text_Utils::is_json_encodable($data)) {
    // Safe to encode
}
```

## Usage in WP Blog Agent

### Example: RankMath SEO Generation

```php
// Build prompt with cleaned data
private function build_seo_description_prompt($title, $content) {
    // Clean title and content for safe use in prompts
    $clean_title = WP_Blog_Agent_Text_Utils::sanitize_for_prompt($title);
    $content_excerpt = strip_tags($content);
    $content_excerpt = substr($content_excerpt, 0, 500);
    $clean_content = WP_Blog_Agent_Text_Utils::sanitize_for_prompt($content_excerpt);
    
    return "Blog Post Title: {$clean_title}\n\nContent Preview: {$clean_content}";
}

// Use safe JSON encoding for API request
private function generate_with_openai($ai, $prompt, $provider) {
    // Clean prompt for JSON encoding
    $clean_prompt = WP_Blog_Agent_Text_Utils::clean_for_json($prompt);
    
    $request_body = array(
        'model' => $model,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => $clean_prompt
            )
        )
    );
    
    // Use safe JSON encoding with error logging
    $json_body = WP_Blog_Agent_Text_Utils::safe_json_encode($request_body);
    
    if ($json_body === false) {
        return new WP_Error('json_encode_failed', 'Failed to encode request body.');
    }
    
    // Send API request...
}
```

## Best Practices

1. **Always clean user input** before using in prompts:
   ```php
   $clean_title = WP_Blog_Agent_Text_Utils::sanitize_for_prompt($post->post_title);
   ```

2. **Use safe_json_encode** for all API requests:
   ```php
   $json = WP_Blog_Agent_Text_Utils::safe_json_encode($request_body);
   if ($json === false) {
       // Handle error
   }
   ```

3. **Check logs** when JSON encoding fails - detailed error information is logged

4. **Test with problematic data** during development:
   - Null bytes: `\x00`
   - Control characters: `\x01`, `\x02`, etc.
   - Invalid UTF-8: `chr(0xFF)`, `chr(0xFE)`

## Error Logging

All encoding issues are logged using `WP_Blog_Agent_Logger`:

- **Warnings**: When encoding is detected and converted
- **Errors**: When JSON encoding fails with detailed context

Check your logs at: `wp-content/uploads/wp-blog-agent-logs/`

## Performance

The utilities are lightweight and efficient:
- Only processes strings that need cleaning
- Uses native PHP functions for best performance
- Minimal overhead on clean data

## Backward Compatibility

The utilities are completely backward compatible:
- Existing code continues to work
- New code can opt-in to use utilities
- No breaking changes to existing functionality

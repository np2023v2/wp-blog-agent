<?php
/**
 * Test case for WP_Blog_Agent_Text_Utils class
 *
 * @package WP_Blog_Agent
 */

use PHPUnit\Framework\TestCase;

class TextUtilsTest extends TestCase {
    
    /**
     * Test clean_for_json with valid UTF-8 text
     */
    public function test_clean_for_json_with_valid_text() {
        $text = "Hello World! This is a test.";
        $result = WP_Blog_Agent_Text_Utils::clean_for_json($text);
        $this->assertEquals($text, $result);
    }
    
    /**
     * Test clean_for_json with empty string
     */
    public function test_clean_for_json_with_empty_string() {
        $result = WP_Blog_Agent_Text_Utils::clean_for_json('');
        $this->assertEquals('', $result);
    }
    
    /**
     * Test clean_for_json removes null bytes
     */
    public function test_clean_for_json_removes_null_bytes() {
        $text = "Hello\0World";
        $result = WP_Blog_Agent_Text_Utils::clean_for_json($text);
        $this->assertEquals("HelloWorld", $result);
    }
    
    /**
     * Test clean_for_json normalizes line breaks
     */
    public function test_clean_for_json_normalizes_line_breaks() {
        $text = "Line1\r\nLine2\rLine3\nLine4";
        $result = WP_Blog_Agent_Text_Utils::clean_for_json($text);
        $this->assertEquals("Line1\nLine2\nLine3\nLine4", $result);
    }
    
    /**
     * Test clean_for_json removes control characters
     */
    public function test_clean_for_json_removes_control_characters() {
        $text = "Hello\x01World\x7F";
        $result = WP_Blog_Agent_Text_Utils::clean_for_json($text);
        $this->assertEquals("HelloWorld", $result);
    }
    
    /**
     * Test fix_utf8_encoding with valid UTF-8
     */
    public function test_fix_utf8_encoding_with_valid_utf8() {
        $text = "Hello 世界";
        $result = WP_Blog_Agent_Text_Utils::fix_utf8_encoding($text);
        $this->assertTrue(mb_check_encoding($result, 'UTF-8'));
    }
    
    /**
     * Test fix_utf8_encoding with empty string
     */
    public function test_fix_utf8_encoding_with_empty_string() {
        $result = WP_Blog_Agent_Text_Utils::fix_utf8_encoding('');
        $this->assertEquals('', $result);
    }
    
    /**
     * Test sanitize_for_prompt removes HTML tags
     */
    public function test_sanitize_for_prompt_removes_html() {
        $text = "<p>Hello <strong>World</strong></p>";
        $result = WP_Blog_Agent_Text_Utils::sanitize_for_prompt($text);
        $this->assertEquals("Hello World", $result);
    }
    
    /**
     * Test sanitize_for_prompt normalizes whitespace
     */
    public function test_sanitize_for_prompt_normalizes_whitespace() {
        $text = "Hello    World\n\nTest";
        $result = WP_Blog_Agent_Text_Utils::sanitize_for_prompt($text);
        $this->assertEquals("Hello World Test", $result);
    }
    
    /**
     * Test sanitize_for_prompt with empty string
     */
    public function test_sanitize_for_prompt_with_empty_string() {
        $result = WP_Blog_Agent_Text_Utils::sanitize_for_prompt('');
        $this->assertEquals('', $result);
    }
    
    /**
     * Test is_json_encodable with valid data
     */
    public function test_is_json_encodable_with_valid_data() {
        $data = array('key' => 'value', 'number' => 123);
        $result = WP_Blog_Agent_Text_Utils::is_json_encodable($data);
        $this->assertTrue($result);
    }
    
    /**
     * Test is_json_encodable with simple string
     */
    public function test_is_json_encodable_with_string() {
        $result = WP_Blog_Agent_Text_Utils::is_json_encodable("Hello World");
        $this->assertTrue($result);
    }
    
    /**
     * Test safe_json_encode with valid data
     */
    public function test_safe_json_encode_with_valid_data() {
        $data = array('name' => 'John', 'age' => 30);
        $result = WP_Blog_Agent_Text_Utils::safe_json_encode($data);
        $this->assertNotFalse($result);
        $this->assertJson($result);
    }
    
    /**
     * Test safe_json_encode with empty array
     */
    public function test_safe_json_encode_with_empty_array() {
        $result = WP_Blog_Agent_Text_Utils::safe_json_encode(array());
        $this->assertEquals('[]', $result);
    }
    
    /**
     * Test clean_array_for_json with simple array
     */
    public function test_clean_array_for_json_with_simple_array() {
        $array = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );
        $result = WP_Blog_Agent_Text_Utils::clean_array_for_json($array);
        $this->assertIsArray($result);
        $this->assertEquals('value1', $result['key1']);
        $this->assertEquals('value2', $result['key2']);
    }
    
    /**
     * Test clean_array_for_json with nested array
     */
    public function test_clean_array_for_json_with_nested_array() {
        $array = array(
            'outer' => array(
                'inner' => 'value'
            )
        );
        $result = WP_Blog_Agent_Text_Utils::clean_array_for_json($array);
        $this->assertIsArray($result);
        $this->assertIsArray($result['outer']);
        $this->assertEquals('value', $result['outer']['inner']);
    }
    
    /**
     * Test clean_array_for_json with non-array input
     */
    public function test_clean_array_for_json_with_non_array() {
        $result = WP_Blog_Agent_Text_Utils::clean_array_for_json('not an array');
        $this->assertEquals('not an array', $result);
    }
    
    /**
     * Test clean_array_for_json removes null bytes from keys and values
     */
    public function test_clean_array_for_json_removes_null_bytes() {
        $array = array(
            "key\0with\0nulls" => "value\0with\0nulls"
        );
        $result = WP_Blog_Agent_Text_Utils::clean_array_for_json($array);
        $keys = array_keys($result);
        $this->assertStringNotContainsString("\0", $keys[0]);
        $this->assertStringNotContainsString("\0", $result[$keys[0]]);
    }
}

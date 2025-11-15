<?php
/**
 * Test case for WP_Blog_Agent_Validator class
 *
 * @package WP_Blog_Agent
 */

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {
    
    /**
     * Test validate_ai_provider with valid providers
     */
    public function test_validate_ai_provider_with_valid_providers() {
        $this->assertEquals('openai', WP_Blog_Agent_Validator::validate_ai_provider('openai'));
        $this->assertEquals('gemini', WP_Blog_Agent_Validator::validate_ai_provider('gemini'));
    }
    
    /**
     * Test validate_ai_provider with invalid provider
     */
    public function test_validate_ai_provider_with_invalid_provider() {
        $result = WP_Blog_Agent_Validator::validate_ai_provider('invalid');
        $this->assertEquals('openai', $result);
    }
    
    /**
     * Test validate_ai_provider with empty string
     */
    public function test_validate_ai_provider_with_empty_string() {
        $result = WP_Blog_Agent_Validator::validate_ai_provider('');
        $this->assertEquals('openai', $result);
    }
    
    /**
     * Test validate_api_key with valid key
     */
    public function test_validate_api_key_with_valid_key() {
        $key = 'sk-abc123def456';
        $result = WP_Blog_Agent_Validator::validate_api_key($key, 'openai');
        $this->assertEquals($key, $result);
    }
    
    /**
     * Test validate_api_key with empty key
     */
    public function test_validate_api_key_with_empty_key() {
        $result = WP_Blog_Agent_Validator::validate_api_key('', 'openai');
        $this->assertEquals('', $result);
    }
    
    /**
     * Test validate_api_key with whitespace
     */
    public function test_validate_api_key_trims_whitespace() {
        $key = '  sk-abc123def456  ';
        $result = WP_Blog_Agent_Validator::validate_api_key($key, 'openai');
        $this->assertEquals('sk-abc123def456', $result);
    }
    
    /**
     * Test validate_api_key with invalid characters
     */
    public function test_validate_api_key_with_invalid_characters() {
        $key = 'sk-abc@#$%123';
        $result = WP_Blog_Agent_Validator::validate_api_key($key, 'openai');
        $this->assertInstanceOf('WP_Error', $result);
    }
    
    /**
     * Test validate_schedule_frequency with valid frequencies
     */
    public function test_validate_schedule_frequency_with_valid_frequencies() {
        $this->assertEquals('hourly', WP_Blog_Agent_Validator::validate_schedule_frequency('hourly'));
        $this->assertEquals('twicedaily', WP_Blog_Agent_Validator::validate_schedule_frequency('twicedaily'));
        $this->assertEquals('daily', WP_Blog_Agent_Validator::validate_schedule_frequency('daily'));
        $this->assertEquals('weekly', WP_Blog_Agent_Validator::validate_schedule_frequency('weekly'));
        $this->assertEquals('none', WP_Blog_Agent_Validator::validate_schedule_frequency('none'));
    }
    
    /**
     * Test validate_schedule_frequency with invalid frequency
     */
    public function test_validate_schedule_frequency_with_invalid_frequency() {
        $result = WP_Blog_Agent_Validator::validate_schedule_frequency('invalid');
        $this->assertEquals('daily', $result);
    }
    
    /**
     * Test validate_yes_no with yes values
     */
    public function test_validate_yes_no_with_yes_values() {
        $this->assertEquals('yes', WP_Blog_Agent_Validator::validate_yes_no('yes'));
        $this->assertEquals('yes', WP_Blog_Agent_Validator::validate_yes_no('1'));
        $this->assertEquals('yes', WP_Blog_Agent_Validator::validate_yes_no(1));
    }
    
    /**
     * Test validate_yes_no with no values
     */
    public function test_validate_yes_no_with_no_values() {
        $this->assertEquals('no', WP_Blog_Agent_Validator::validate_yes_no('no'));
        $this->assertEquals('no', WP_Blog_Agent_Validator::validate_yes_no('0'));
        $this->assertEquals('no', WP_Blog_Agent_Validator::validate_yes_no(0));
        $this->assertEquals('no', WP_Blog_Agent_Validator::validate_yes_no(''));
    }
    
    /**
     * Test validate_topic with valid data
     */
    public function test_validate_topic_with_valid_data() {
        $result = WP_Blog_Agent_Validator::validate_topic(
            'AI and Machine Learning',
            'artificial intelligence, deep learning',
            'AI, ML, tech'
        );
        
        $this->assertIsArray($result);
        $this->assertEquals('AI and Machine Learning', $result['topic']);
        $this->assertEquals('artificial intelligence, deep learning', $result['keywords']);
        $this->assertEquals('AI, ML, tech', $result['hashtags']);
    }
    
    /**
     * Test validate_topic with empty topic
     */
    public function test_validate_topic_with_empty_topic() {
        $result = WP_Blog_Agent_Validator::validate_topic('', '', '');
        $this->assertInstanceOf('WP_Error', $result);
    }
    
    /**
     * Test validate_topic with long topic
     */
    public function test_validate_topic_with_long_topic() {
        $long_topic = str_repeat('A', 300);
        $result = WP_Blog_Agent_Validator::validate_topic($long_topic, '', '');
        $this->assertInstanceOf('WP_Error', $result);
    }
    
    /**
     * Test validate_topic with too many keywords
     */
    public function test_validate_topic_with_too_many_keywords() {
        $keywords = implode(', ', array_fill(0, 60, 'keyword'));
        $result = WP_Blog_Agent_Validator::validate_topic('Valid Topic', $keywords, '');
        $this->assertInstanceOf('WP_Error', $result);
    }
    
    /**
     * Test validate_topic with too many hashtags
     */
    public function test_validate_topic_with_too_many_hashtags() {
        $hashtags = implode(', ', array_fill(0, 40, 'hashtag'));
        $result = WP_Blog_Agent_Validator::validate_topic('Valid Topic', '', $hashtags);
        $this->assertInstanceOf('WP_Error', $result);
    }
    
    /**
     * Test validate_topic with invalid hashtag format
     */
    public function test_validate_topic_with_invalid_hashtag_format() {
        $result = WP_Blog_Agent_Validator::validate_topic('Valid Topic', '', 'good, bad@tag');
        $this->assertInstanceOf('WP_Error', $result);
    }
    
    /**
     * Test validate_topic with optional empty keywords and hashtags
     */
    public function test_validate_topic_with_optional_empty_fields() {
        $result = WP_Blog_Agent_Validator::validate_topic('Valid Topic', '', '');
        $this->assertIsArray($result);
        $this->assertEquals('Valid Topic', $result['topic']);
        $this->assertEquals('', $result['keywords']);
        $this->assertEquals('', $result['hashtags']);
    }
    
    /**
     * Test sanitize_int with valid bounds
     */
    public function test_sanitize_int_with_valid_bounds() {
        $this->assertEquals(5, WP_Blog_Agent_Validator::sanitize_int(5, 0, 10));
        $this->assertEquals(0, WP_Blog_Agent_Validator::sanitize_int(-5, 0, 10));
        $this->assertEquals(10, WP_Blog_Agent_Validator::sanitize_int(15, 0, 10));
    }
    
    /**
     * Test sanitize_int with string input
     */
    public function test_sanitize_int_with_string_input() {
        $this->assertEquals(42, WP_Blog_Agent_Validator::sanitize_int('42', 0, 100));
    }
    
    /**
     * Test validate_email_list with valid emails
     */
    public function test_validate_email_list_with_valid_emails() {
        $emails = 'test@example.com, user@domain.org';
        $result = WP_Blog_Agent_Validator::validate_email_list($emails);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContains('test@example.com', $result);
        $this->assertContains('user@domain.org', $result);
    }
    
    /**
     * Test validate_email_list with invalid emails
     */
    public function test_validate_email_list_with_invalid_emails() {
        $emails = 'test@example.com, notanemail, user@domain.org';
        $result = WP_Blog_Agent_Validator::validate_email_list($emails);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertNotContains('notanemail', $result);
    }
    
    /**
     * Test validate_email_list with empty string
     */
    public function test_validate_email_list_with_empty_string() {
        $result = WP_Blog_Agent_Validator::validate_email_list('');
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}

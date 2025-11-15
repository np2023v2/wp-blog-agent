<?php
/**
 * Test case for WP_Blog_Agent_Logger class
 *
 * @package WP_Blog_Agent
 */

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {
    
    private $log_dir;
    
    /**
     * Set up test environment before each test
     */
    protected function setUp(): void {
        parent::setUp();
        
        // Create temporary log directory
        $this->log_dir = sys_get_temp_dir() . '/wp-uploads/wp-blog-agent-logs';
        if (!file_exists($this->log_dir)) {
            mkdir($this->log_dir, 0755, true);
        }
        
        // Initialize logger
        WP_Blog_Agent_Logger::init();
    }
    
    /**
     * Clean up after each test
     */
    protected function tearDown(): void {
        parent::tearDown();
        
        // Clean up log files
        if (is_dir($this->log_dir)) {
            $files = glob($this->log_dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
    
    /**
     * Test logger initialization creates log directory
     */
    public function test_init_creates_log_directory() {
        $this->assertDirectoryExists($this->log_dir);
    }
    
    /**
     * Test logger initialization creates .htaccess file
     */
    public function test_init_creates_htaccess() {
        // Clean up first to ensure fresh init
        if (is_dir($this->log_dir)) {
            // Remove all files including hidden files
            $files = array_merge(
                glob($this->log_dir . '/*'),
                glob($this->log_dir . '/.*')
            );
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            // Only try to remove directory if it's empty
            if (is_dir($this->log_dir) && count(scandir($this->log_dir)) == 2) {
                rmdir($this->log_dir);
            }
        }
        
        // Re-initialize logger
        WP_Blog_Agent_Logger::init();
        
        $htaccess = $this->log_dir . '/.htaccess';
        $this->assertFileExists($htaccess);
    }
    
    /**
     * Test info logging creates log entry
     */
    public function test_info_logging() {
        WP_Blog_Agent_Logger::info('Test info message');
        
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        $this->assertFileExists($log_file);
        
        $content = file_get_contents($log_file);
        $this->assertStringContainsString('INFO', $content);
        $this->assertStringContainsString('Test info message', $content);
    }
    
    /**
     * Test error logging creates log entry
     */
    public function test_error_logging() {
        WP_Blog_Agent_Logger::error('Test error message');
        
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        $this->assertFileExists($log_file);
        
        $content = file_get_contents($log_file);
        $this->assertStringContainsString('ERROR', $content);
        $this->assertStringContainsString('Test error message', $content);
    }
    
    /**
     * Test warning logging creates log entry
     */
    public function test_warning_logging() {
        WP_Blog_Agent_Logger::warning('Test warning message');
        
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        $this->assertFileExists($log_file);
        
        $content = file_get_contents($log_file);
        $this->assertStringContainsString('WARNING', $content);
        $this->assertStringContainsString('Test warning message', $content);
    }
    
    /**
     * Test success logging creates log entry
     */
    public function test_success_logging() {
        WP_Blog_Agent_Logger::success('Test success message');
        
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        $this->assertFileExists($log_file);
        
        $content = file_get_contents($log_file);
        $this->assertStringContainsString('SUCCESS', $content);
        $this->assertStringContainsString('Test success message', $content);
    }
    
    /**
     * Test logging with context
     */
    public function test_logging_with_context() {
        $context = array('user_id' => 123, 'action' => 'test');
        WP_Blog_Agent_Logger::info('Test with context', $context);
        
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        $content = file_get_contents($log_file);
        
        $this->assertStringContainsString('Context:', $content);
        $this->assertStringContainsString('"user_id":123', $content);
        $this->assertStringContainsString('"action":"test"', $content);
    }
    
    /**
     * Test get_recent_logs returns array
     */
    public function test_get_recent_logs_returns_array() {
        WP_Blog_Agent_Logger::info('Log entry 1');
        WP_Blog_Agent_Logger::info('Log entry 2');
        WP_Blog_Agent_Logger::info('Log entry 3');
        
        $logs = WP_Blog_Agent_Logger::get_recent_logs(10);
        $this->assertIsArray($logs);
        $this->assertGreaterThan(0, count($logs));
    }
    
    /**
     * Test get_recent_logs with no log file
     */
    public function test_get_recent_logs_with_no_file() {
        // Clean up any existing log files
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        if (file_exists($log_file)) {
            unlink($log_file);
        }
        
        $logs = WP_Blog_Agent_Logger::get_recent_logs(10);
        $this->assertIsArray($logs);
        $this->assertCount(0, $logs);
    }
    
    /**
     * Test clear_old_logs removes old files
     */
    public function test_clear_old_logs() {
        // Create an old log file
        $old_file = $this->log_dir . '/old-log.log';
        file_put_contents($old_file, 'Old log content');
        touch($old_file, time() - (40 * 24 * 60 * 60)); // 40 days old
        
        WP_Blog_Agent_Logger::clear_old_logs(30);
        
        $this->assertFileDoesNotExist($old_file);
    }
    
    /**
     * Test clear_old_logs keeps recent files
     */
    public function test_clear_old_logs_keeps_recent() {
        // Create a recent log file
        $recent_file = $this->log_dir . '/recent-log.log';
        file_put_contents($recent_file, 'Recent log content');
        
        WP_Blog_Agent_Logger::clear_old_logs(30);
        
        $this->assertFileExists($recent_file);
    }
    
    /**
     * Test log file rotation when file gets too large
     */
    public function test_log_rotation() {
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        
        // Create a large log file (>5MB)
        $large_content = str_repeat('A', 6 * 1024 * 1024);
        file_put_contents($log_file, $large_content);
        
        // Trigger logging which should rotate the file
        WP_Blog_Agent_Logger::info('Test message after rotation');
        
        // Check that old file was archived
        $archived_files = glob($this->log_dir . '/*.old');
        $this->assertGreaterThan(0, count($archived_files));
    }
    
    /**
     * Test multiple log levels in sequence
     */
    public function test_multiple_log_levels() {
        WP_Blog_Agent_Logger::info('Info message');
        WP_Blog_Agent_Logger::warning('Warning message');
        WP_Blog_Agent_Logger::error('Error message');
        WP_Blog_Agent_Logger::success('Success message');
        
        $log_file = $this->log_dir . '/wp-blog-agent-' . date('Y-m-d') . '.log';
        $content = file_get_contents($log_file);
        
        $this->assertStringContainsString('[INFO]', $content);
        $this->assertStringContainsString('[WARNING]', $content);
        $this->assertStringContainsString('[ERROR]', $content);
        $this->assertStringContainsString('[SUCCESS]', $content);
    }
}

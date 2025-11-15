<?php
/**
 * PHPUnit bootstrap file for WP Blog Agent
 *
 * @package WP_Blog_Agent
 */

// Load Composer autoloader
$autoloader = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
}

// Define test constants
if (!defined('WP_BLOG_AGENT_PLUGIN_DIR')) {
    define('WP_BLOG_AGENT_PLUGIN_DIR', dirname(__DIR__) . '/');
}

if (!defined('WP_BLOG_AGENT_VERSION')) {
    define('WP_BLOG_AGENT_VERSION', '1.0.2');
}

// Mock WordPress functions that are commonly used
if (!function_exists('wp_upload_dir')) {
    function wp_upload_dir() {
        return array(
            'basedir' => sys_get_temp_dir() . '/wp-uploads',
            'baseurl' => 'http://example.com/wp-content/uploads',
        );
    }
}

if (!function_exists('wp_mkdir_p')) {
    function wp_mkdir_p($target) {
        return mkdir($target, 0755, true);
    }
}

if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
}

if (!function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags($string, $remove_breaks = false) {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        $string = strip_tags($string);
        
        if ($remove_breaks) {
            $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
        }
        
        return trim($string);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return trim(strip_tags($str));
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str) {
        return trim(strip_tags($str));
    }
}

if (!function_exists('is_email')) {
    function is_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

// Mock WP_Error class
if (!class_exists('WP_Error')) {
    class WP_Error {
        private $errors = array();
        private $error_data = array();
        
        public function __construct($code = '', $message = '', $data = '') {
            if (empty($code)) {
                return;
            }
            
            $this->errors[$code][] = $message;
            
            if (!empty($data)) {
                $this->error_data[$code] = $data;
            }
        }
        
        public function get_error_code() {
            $codes = array_keys($this->errors);
            return $codes ? $codes[0] : '';
        }
        
        public function get_error_message($code = '') {
            if (empty($code)) {
                $code = $this->get_error_code();
            }
            
            if (isset($this->errors[$code])) {
                return $this->errors[$code][0];
            }
            
            return '';
        }
        
        public function get_error_data($code = '') {
            if (empty($code)) {
                $code = $this->get_error_code();
            }
            
            if (isset($this->error_data[$code])) {
                return $this->error_data[$code];
            }
            
            return null;
        }
        
        public function add($code, $message, $data = '') {
            $this->errors[$code][] = $message;
            
            if (!empty($data)) {
                $this->error_data[$code] = $data;
            }
        }
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return ($thing instanceof WP_Error);
    }
}

// Include the plugin classes
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-logger.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-text-utils.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-validator.php';

echo "Bootstrap loaded successfully.\n";

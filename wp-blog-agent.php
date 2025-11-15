<?php
/**
 * Plugin Name: WP Blog Agent
 * Plugin URI: https://github.com/np2023v2/wp-blog-agent
 * Description: Automated blog post generation using OpenAI or Gemini API with hashtags, keywords, SEO optimization, and scheduled publishing.
 * Version: 1.0.2
 * Author: NP2023
 * Author URI: https://github.com/np2023v2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-blog-agent
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_BLOG_AGENT_VERSION', '1.0.2');
define('WP_BLOG_AGENT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_BLOG_AGENT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_BLOG_AGENT_PLUGIN_FILE', __FILE__);

// Include required files
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-logger.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-text-utils.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-validator.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-cron.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-activator.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-deactivator.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-queue.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-series.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-health-check.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-admin.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-generator.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-openai.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-gemini.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-ollama.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-scheduler.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-image-generator.php';
require_once WP_BLOG_AGENT_PLUGIN_DIR . 'includes/class-wp-blog-agent-rankmath.php';


// Activation hook
register_activation_hook(__FILE__, array('WP_Blog_Agent_Activator', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('WP_Blog_Agent_Deactivator', 'deactivate'));

// Check for database upgrades
function wp_blog_agent_check_upgrade() {
    $current_version = get_option('wp_blog_agent_db_version', '0');
    $plugin_version = WP_BLOG_AGENT_VERSION;
    
    // If versions don't match, run upgrade
    if (version_compare($current_version, $plugin_version, '<')) {
        WP_Blog_Agent_Activator::activate();
        update_option('wp_blog_agent_db_version', $plugin_version);
        WP_Blog_Agent_Logger::info('Database upgraded to version ' . $plugin_version);
    }
}
add_action('admin_init', 'wp_blog_agent_check_upgrade');

// Initialize the plugin
function wp_blog_agent_init() {
    // Initialize logger
    WP_Blog_Agent_Logger::init();
    
    // Initialize cron module
    WP_Blog_Agent_Cron::init();
    
    $admin = new WP_Blog_Agent_Admin();
    $scheduler = new WP_Blog_Agent_Scheduler();
    
    // Log plugin initialization
    WP_Blog_Agent_Logger::info('Plugin initialized successfully');
}
add_action('plugins_loaded', 'wp_blog_agent_init');

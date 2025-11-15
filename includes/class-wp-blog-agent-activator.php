<?php
/**
 * Plugin Activator
 */
class WP_Blog_Agent_Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create topics table
        $topics_table = $wpdb->prefix . 'blog_agent_topics';
        $topics_sql = "CREATE TABLE $topics_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            topic varchar(255) NOT NULL,
            keywords text NOT NULL,
            hashtags text NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Create queue table
        $queue_table = $wpdb->prefix . 'blog_agent_queue';
        $queue_sql = "CREATE TABLE $queue_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            topic_id mediumint(9) DEFAULT NULL,
            topic_text varchar(500) DEFAULT NULL,
            series_id mediumint(9) DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            trigger varchar(50) DEFAULT 'manual',
            post_id bigint(20) DEFAULT NULL,
            attempts int DEFAULT 0,
            error_message text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            started_at datetime DEFAULT NULL,
            completed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Create series table
        $series_table = $wpdb->prefix . 'blog_agent_series';
        $series_sql = "CREATE TABLE $series_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text DEFAULT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Create series-posts relationship table
        $series_posts_table = $wpdb->prefix . 'blog_agent_series_posts';
        $series_posts_sql = "CREATE TABLE $series_posts_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            series_id mediumint(9) NOT NULL,
            post_id bigint(20) NOT NULL,
            position int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY series_id (series_id),
            KEY post_id (post_id),
            UNIQUE KEY series_post (series_id, post_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($topics_sql);
        dbDelta($queue_sql);
        dbDelta($series_sql);
        dbDelta($series_posts_sql);
        
        // Set default options
        if (!get_option('wp_blog_agent_ai_provider')) {
            add_option('wp_blog_agent_ai_provider', 'openai');
        }
        if (!get_option('wp_blog_agent_schedule_enabled')) {
            add_option('wp_blog_agent_schedule_enabled', 'no');
        }
        if (!get_option('wp_blog_agent_schedule_frequency')) {
            add_option('wp_blog_agent_schedule_frequency', 'daily');
        }
        if (!get_option('wp_blog_agent_auto_publish')) {
            add_option('wp_blog_agent_auto_publish', 'yes');
        }
        if (!get_option('wp_blog_agent_auto_generate_image')) {
            add_option('wp_blog_agent_auto_generate_image', 'no');
        }
        
        // Schedule cron events using the cron module
        $frequency = get_option('wp_blog_agent_schedule_frequency', 'daily');
        WP_Blog_Agent_Cron::schedule_post_generation($frequency);
    }
}

<?php
/**
 * Cron Management Module
 * 
 * Centralized cron job management for WP Blog Agent
 * Handles scheduling, unscheduling, and custom intervals
 */
class WP_Blog_Agent_Cron {
    
    /**
     * Cron hook names
     */
    const HOOK_GENERATE_POST = 'wp_blog_agent_generate_post';
    const HOOK_PROCESS_QUEUE = 'wp_blog_agent_process_queue';
    
    /**
     * Initialize cron module
     */
    public static function init() {
        // Register custom cron schedules
        add_filter('cron_schedules', array(__CLASS__, 'add_custom_schedules'));
        
        // Register cron hooks
        add_action(self::HOOK_GENERATE_POST, array(__CLASS__, 'handle_generate_post'));
        add_action(self::HOOK_PROCESS_QUEUE, array(__CLASS__, 'handle_process_queue'));
    }
    
    /**
     * Add custom cron schedules
     */
    public static function add_custom_schedules($schedules) {
        $schedules['hourly'] = array(
            'interval' => 3600,
            'display'  => __('Every Hour', 'wp-blog-agent')
        );
        $schedules['twicedaily'] = array(
            'interval' => 43200,
            'display'  => __('Twice Daily', 'wp-blog-agent')
        );
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display'  => __('Once Weekly', 'wp-blog-agent')
        );
        return $schedules;
    }
    
    /**
     * Schedule post generation cron
     * 
     * @param string $frequency Frequency: hourly, twicedaily, daily, weekly
     * @return bool Success status
     */
    public static function schedule_post_generation($frequency = 'daily') {
        // Unschedule any existing event first
        self::unschedule_post_generation();
        
        // Don't schedule if frequency is 'none'
        if ($frequency === 'none' || empty($frequency)) {
            return true;
        }
        
        // Schedule new event
        $scheduled = wp_schedule_event(time(), $frequency, self::HOOK_GENERATE_POST);
        
        if ($scheduled === false) {
            WP_Blog_Agent_Logger::error('Failed to schedule post generation cron', array(
                'frequency' => $frequency
            ));
            return false;
        }
        
        WP_Blog_Agent_Logger::info('Post generation cron scheduled', array(
            'frequency' => $frequency,
            'next_run' => date('Y-m-d H:i:s', wp_next_scheduled(self::HOOK_GENERATE_POST))
        ));
        
        return true;
    }
    
    /**
     * Unschedule post generation cron
     * 
     * @return bool Success status
     */
    public static function unschedule_post_generation() {
        $timestamp = wp_next_scheduled(self::HOOK_GENERATE_POST);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::HOOK_GENERATE_POST);
            WP_Blog_Agent_Logger::info('Post generation cron unscheduled');
        }
        return true;
    }
    
    /**
     * Schedule queue processing (one-time event)
     * 
     * @param int $delay Delay in seconds before processing
     * @return bool Success status
     */
    public static function schedule_queue_processing($delay = 0) {
        // Don't schedule if already scheduled
        if (wp_next_scheduled(self::HOOK_PROCESS_QUEUE)) {
            return true;
        }
        
        $time = time() + $delay;
        $scheduled = wp_schedule_single_event($time, self::HOOK_PROCESS_QUEUE);
        
        if ($scheduled === false) {
            WP_Blog_Agent_Logger::error('Failed to schedule queue processing', array(
                'delay' => $delay
            ));
            return false;
        }
        
        WP_Blog_Agent_Logger::debug('Queue processing scheduled', array(
            'delay' => $delay,
            'scheduled_time' => date('Y-m-d H:i:s', $time)
        ));
        
        return true;
    }
    
    /**
     * Unschedule queue processing cron
     * 
     * @return bool Success status
     */
    public static function unschedule_queue_processing() {
        $timestamp = wp_next_scheduled(self::HOOK_PROCESS_QUEUE);
        if ($timestamp) {
            wp_unschedule_event($timestamp, self::HOOK_PROCESS_QUEUE);
            WP_Blog_Agent_Logger::info('Queue processing cron unscheduled');
        }
        return true;
    }
    
    /**
     * Unschedule all cron jobs
     * 
     * @return bool Success status
     */
    public static function unschedule_all() {
        self::unschedule_post_generation();
        self::unschedule_queue_processing();
        return true;
    }
    
    /**
     * Handle post generation cron event
     */
    public static function handle_generate_post() {
        // Check if scheduling is enabled
        $enabled = get_option('wp_blog_agent_schedule_enabled', 'no');
        
        if ($enabled !== 'yes') {
            WP_Blog_Agent_Logger::info('Scheduled generation skipped - scheduling disabled');
            return;
        }
        
        WP_Blog_Agent_Logger::info('Adding scheduled post generation to queue');
        
        // Add task to queue
        $queue_id = WP_Blog_Agent_Queue::enqueue(null, 'scheduled');
        
        if ($queue_id === false) {
            WP_Blog_Agent_Logger::error('Failed to enqueue scheduled generation task');
        } else {
            WP_Blog_Agent_Logger::success('Scheduled generation task enqueued', array('queue_id' => $queue_id));
        }
    }
    
    /**
     * Handle queue processing cron event
     */
    public static function handle_process_queue() {
        WP_Blog_Agent_Logger::debug('Queue processing cron triggered');
        WP_Blog_Agent_Queue::process_queue();
    }
    
    /**
     * Get cron status information
     * 
     * @return array Status information
     */
    public static function get_status() {
        $post_gen_next = wp_next_scheduled(self::HOOK_GENERATE_POST);
        $queue_proc_next = wp_next_scheduled(self::HOOK_PROCESS_QUEUE);
        
        return array(
            'post_generation' => array(
                'scheduled' => (bool) $post_gen_next,
                'next_run' => $post_gen_next ? date('Y-m-d H:i:s', $post_gen_next) : null,
                'timestamp' => $post_gen_next
            ),
            'queue_processing' => array(
                'scheduled' => (bool) $queue_proc_next,
                'next_run' => $queue_proc_next ? date('Y-m-d H:i:s', $queue_proc_next) : null,
                'timestamp' => $queue_proc_next
            )
        );
    }
    
    /**
     * Check if WordPress cron is working
     * 
     * @return bool True if working, false otherwise
     */
    public static function is_cron_working() {
        // Check if DISABLE_WP_CRON is defined
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            return false;
        }
        
        // Check if there are any scheduled events
        $cron_array = _get_cron_array();
        if (!is_array($cron_array) || empty($cron_array)) {
            return false;
        }
        
        return true;
    }
}

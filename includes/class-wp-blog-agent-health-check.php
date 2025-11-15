<?php
/**
 * Health Check Module
 * Performs comprehensive health checks on the plugin components
 */
class WP_Blog_Agent_Health_Check {
    
    /**
     * Run all health checks
     * 
     * @return array Health check results
     */
    public static function run_all_checks() {
        $results = array(
            'database' => self::check_database(),
            'llm_api' => self::check_llm_api(),
            'queue' => self::check_queue(),
            'version' => self::check_version(),
            'image_generation' => self::check_image_generation(),
        );
        
        // Calculate overall status
        $results['overall_status'] = self::calculate_overall_status($results);
        $results['timestamp'] = current_time('mysql');
        
        return $results;
    }
    
    /**
     * Check database tables and schema
     * 
     * @return array Database health check results
     */
    public static function check_database() {
        global $wpdb;
        
        $results = array(
            'status' => 'healthy',
            'tables' => array(),
            'issues' => array()
        );
        
        // Define expected tables and their schemas
        $expected_tables = array(
            'blog_agent_topics' => array(
                'id' => 'mediumint(9)',
                'topic' => 'varchar(255)',
                'keywords' => 'text',
                'hashtags' => 'text',
                'status' => 'varchar(20)',
                'created_at' => 'datetime',
                'updated_at' => 'datetime'
            ),
            'blog_agent_queue' => array(
                'id' => 'mediumint(9)',
                'topic_id' => 'mediumint(9)',
                'topic_text' => 'varchar(500)',
                'series_id' => 'mediumint(9)',
                'status' => 'varchar(20)',
                'trigger' => 'varchar(50)',
                'post_id' => 'bigint(20)',
                'attempts' => 'int',
                'error_message' => 'text',
                'created_at' => 'datetime',
                'started_at' => 'datetime',
                'completed_at' => 'datetime'
            ),
            'blog_agent_series' => array(
                'id' => 'mediumint(9)',
                'name' => 'varchar(255)',
                'description' => 'text',
                'status' => 'varchar(20)',
                'created_at' => 'datetime',
                'updated_at' => 'datetime'
            ),
            'blog_agent_series_posts' => array(
                'id' => 'mediumint(9)',
                'series_id' => 'mediumint(9)',
                'post_id' => 'bigint(20)',
                'position' => 'int',
                'created_at' => 'datetime'
            )
        );
        
        // Check each table
        foreach ($expected_tables as $table_name => $expected_columns) {
            $full_table_name = $wpdb->prefix . $table_name;
            
            // Check if table exists
            $table_exists = $wpdb->get_var($wpdb->prepare(
                "SHOW TABLES LIKE %s",
                $full_table_name
            ));
            
            if ($table_exists !== $full_table_name) {
                $results['tables'][$table_name] = array(
                    'exists' => false,
                    'status' => 'error'
                );
                $results['issues'][] = "Table {$table_name} does not exist";
                $results['status'] = 'error';
                continue;
            }
            
            // Get table structure
            $columns = $wpdb->get_results("DESCRIBE {$full_table_name}", ARRAY_A);
            $actual_columns = array();
            
            foreach ($columns as $column) {
                $actual_columns[$column['Field']] = $column['Type'];
            }
            
            // Verify columns
            $missing_columns = array();
            foreach ($expected_columns as $col_name => $col_type) {
                if (!isset($actual_columns[$col_name])) {
                    $missing_columns[] = $col_name;
                }
            }
            
            $table_status = 'healthy';
            if (!empty($missing_columns)) {
                $table_status = 'warning';
                $results['issues'][] = "Table {$table_name} is missing columns: " . implode(', ', $missing_columns);
                if ($results['status'] === 'healthy') {
                    $results['status'] = 'warning';
                }
            }
            
            // Get row count
            $row_count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table_name}");
            
            $results['tables'][$table_name] = array(
                'exists' => true,
                'status' => $table_status,
                'columns' => count($actual_columns),
                'rows' => intval($row_count),
                'missing_columns' => $missing_columns
            );
        }
        
        return $results;
    }
    
    /**
     * Check LLM API connectivity
     * 
     * @return array LLM API health check results
     */
    public static function check_llm_api() {
        $results = array(
            'status' => 'healthy',
            'providers' => array(),
            'issues' => array()
        );
        
        $ai_provider = get_option('wp_blog_agent_ai_provider', 'openai');
        
        // Check OpenAI
        $openai_key = get_option('wp_blog_agent_openai_api_key', '');
        if (!empty($openai_key)) {
            $openai_result = self::test_openai_api($openai_key);
            $results['providers']['openai'] = $openai_result;
            
            if ($ai_provider === 'openai' && $openai_result['status'] !== 'healthy') {
                $results['status'] = 'error';
                $results['issues'][] = "OpenAI API (active provider) is not working: " . $openai_result['message'];
            }
        } else {
            $results['providers']['openai'] = array(
                'status' => 'not_configured',
                'message' => 'API key not configured'
            );
            if ($ai_provider === 'openai') {
                $results['status'] = 'error';
                $results['issues'][] = "OpenAI API key not configured (active provider)";
            }
        }
        
        // Check Gemini
        $gemini_key = get_option('wp_blog_agent_gemini_api_key', '');
        if (!empty($gemini_key)) {
            $gemini_result = self::test_gemini_api($gemini_key);
            $results['providers']['gemini'] = $gemini_result;
            
            if ($ai_provider === 'gemini' && $gemini_result['status'] !== 'healthy') {
                $results['status'] = 'error';
                $results['issues'][] = "Gemini API (active provider) is not working: " . $gemini_result['message'];
            }
        } else {
            $results['providers']['gemini'] = array(
                'status' => 'not_configured',
                'message' => 'API key not configured'
            );
            if ($ai_provider === 'gemini') {
                $results['status'] = 'error';
                $results['issues'][] = "Gemini API key not configured (active provider)";
            }
        }
        
        $results['active_provider'] = $ai_provider;
        
        return $results;
    }
    
    /**
     * Test OpenAI API connectivity
     * 
     * @param string $api_key API key
     * @return array Test results
     */
    private static function test_openai_api($api_key) {
        $result = array(
            'status' => 'healthy',
            'message' => 'API connection successful',
            'response_time' => 0
        );
        
        $start_time = microtime(true);
        
        try {
            $ai = new WP_Blog_Agent_OpenAI();
            $test_content = $ai->generate_content('Health check test', array(), array());
            
            $end_time = microtime(true);
            $result['response_time'] = round(($end_time - $start_time) * 1000, 2); // milliseconds
            
            if (is_wp_error($test_content)) {
                $result['status'] = 'error';
                $result['message'] = $test_content->get_error_message();
            } elseif (empty($test_content)) {
                $result['status'] = 'warning';
                $result['message'] = 'API returned empty response';
            }
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Test Gemini API connectivity
     * 
     * @param string $api_key API key
     * @return array Test results
     */
    private static function test_gemini_api($api_key) {
        $result = array(
            'status' => 'healthy',
            'message' => 'API connection successful',
            'response_time' => 0
        );
        
        $start_time = microtime(true);
        
        try {
            $ai = new WP_Blog_Agent_Gemini();
            $test_content = $ai->generate_content('Health check test', array(), array());
            
            $end_time = microtime(true);
            $result['response_time'] = round(($end_time - $start_time) * 1000, 2); // milliseconds
            
            if (is_wp_error($test_content)) {
                $result['status'] = 'error';
                $result['message'] = $test_content->get_error_message();
            } elseif (empty($test_content)) {
                $result['status'] = 'warning';
                $result['message'] = 'API returned empty response';
            }
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Check queue health
     * 
     * @return array Queue health check results
     */
    public static function check_queue() {
        global $wpdb;
        
        $results = array(
            'status' => 'healthy',
            'statistics' => array(),
            'issues' => array()
        );
        
        $queue_table = $wpdb->prefix . 'blog_agent_queue';
        
        // Check if table exists
        $table_exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $queue_table
        ));
        
        if ($table_exists !== $queue_table) {
            $results['status'] = 'error';
            $results['issues'][] = 'Queue table does not exist';
            return $results;
        }
        
        // Get queue statistics
        $stats = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM {$queue_table} GROUP BY status",
            ARRAY_A
        );
        
        foreach ($stats as $stat) {
            $results['statistics'][$stat['status']] = intval($stat['count']);
        }
        
        // Add default values for missing statuses
        $default_statuses = array('pending', 'processing', 'completed', 'failed');
        foreach ($default_statuses as $status) {
            if (!isset($results['statistics'][$status])) {
                $results['statistics'][$status] = 0;
            }
        }
        
        // Check for stuck tasks (processing for more than 1 hour)
        $stuck_tasks = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$queue_table} 
                WHERE status = 'processing' 
                AND started_at < %s",
                date('Y-m-d H:i:s', strtotime('-1 hour'))
            )
        );
        
        if ($stuck_tasks > 0) {
            $results['status'] = 'warning';
            $results['issues'][] = "{$stuck_tasks} task(s) have been stuck in processing state for over 1 hour";
            $results['statistics']['stuck'] = intval($stuck_tasks);
        }
        
        // Check for high failure rate
        $total_tasks = array_sum($results['statistics']);
        if ($total_tasks > 0) {
            $failure_rate = ($results['statistics']['failed'] / $total_tasks) * 100;
            $results['statistics']['failure_rate'] = round($failure_rate, 2);
            
            if ($failure_rate > 50) {
                $results['status'] = 'error';
                $results['issues'][] = "High failure rate: {$failure_rate}%";
            } elseif ($failure_rate > 25) {
                if ($results['status'] === 'healthy') {
                    $results['status'] = 'warning';
                }
                $results['issues'][] = "Elevated failure rate: {$failure_rate}%";
            }
        }
        
        // Check cron job using cron module
        $cron_status = WP_Blog_Agent_Cron::get_status();
        if ($cron_status['queue_processing']['scheduled']) {
            $next_run = $cron_status['queue_processing']['timestamp'];
            $results['cron'] = array(
                'scheduled' => true,
                'next_run' => date('Y-m-d H:i:s', $next_run),
                'time_until' => human_time_diff($next_run, time())
            );
        } else {
            $results['cron'] = array(
                'scheduled' => false
            );
            if ($results['status'] === 'healthy') {
                $results['status'] = 'warning';
            }
            $results['issues'][] = 'Queue processing cron job is not scheduled';
        }
        
        return $results;
    }
    
    /**
     * Check plugin version and updates
     * 
     * @return array Version check results
     */
    public static function check_version() {
        $results = array(
            'status' => 'healthy',
            'current_version' => WP_BLOG_AGENT_VERSION,
            'db_version' => get_option('wp_blog_agent_db_version', '0'),
            'issues' => array()
        );
        
        // Check if database version matches plugin version
        if (version_compare($results['db_version'], $results['current_version'], '<')) {
            $results['status'] = 'warning';
            $results['issues'][] = "Database version ({$results['db_version']}) is older than plugin version ({$results['current_version']})";
            $results['needs_upgrade'] = true;
        } else {
            $results['needs_upgrade'] = false;
        }
        
        // Check WordPress version compatibility
        global $wp_version;
        $results['wordpress_version'] = $wp_version;
        $min_wp_version = '5.0';
        
        if (version_compare($wp_version, $min_wp_version, '<')) {
            $results['status'] = 'error';
            $results['issues'][] = "WordPress version {$wp_version} is below minimum required version {$min_wp_version}";
        }
        
        // Check PHP version
        $results['php_version'] = PHP_VERSION;
        $min_php_version = '7.4';
        
        if (version_compare(PHP_VERSION, $min_php_version, '<')) {
            $results['status'] = 'error';
            $results['issues'][] = "PHP version " . PHP_VERSION . " is below minimum required version {$min_php_version}";
        }
        
        // Check for plugin updates (mock - would need actual update check API)
        $results['update_available'] = false;
        $results['latest_version'] = WP_BLOG_AGENT_VERSION;
        
        return $results;
    }
    
    /**
     * Check image generation capabilities
     * 
     * @return array Image generation health check results
     */
    public static function check_image_generation() {
        $results = array(
            'status' => 'healthy',
            'issues' => array()
        );
        
        // Check if image generation is enabled
        $auto_generate_image = get_option('wp_blog_agent_auto_generate_image', 'no');
        $results['auto_generate_enabled'] = ($auto_generate_image === 'yes');
        
        // Check Gemini Image API key
        $image_api_key = get_option('wp_blog_agent_gemini_image_api_key', '');
        
        if (empty($image_api_key)) {
            $results['api_key_configured'] = false;
            if ($results['auto_generate_enabled']) {
                $results['status'] = 'error';
                $results['issues'][] = 'Image generation is enabled but API key is not configured';
            } else {
                $results['status'] = 'not_configured';
                $results['issues'][] = 'Image generation API key not configured';
            }
        } else {
            $results['api_key_configured'] = true;
            
            // Test image generation API
            $test_result = self::test_image_generation_api();
            $results['api_test'] = $test_result;
            
            if ($test_result['status'] !== 'healthy') {
                $results['status'] = $test_result['status'];
                $results['issues'][] = 'Image generation API test failed: ' . $test_result['message'];
            }
        }
        
        // Check upload directory
        $upload_dir = wp_upload_dir();
        if ($upload_dir['error']) {
            $results['status'] = 'error';
            $results['issues'][] = 'Upload directory error: ' . $upload_dir['error'];
            $results['upload_dir_writable'] = false;
        } else {
            $results['upload_dir_writable'] = wp_is_writable($upload_dir['path']);
            if (!$results['upload_dir_writable']) {
                $results['status'] = 'error';
                $results['issues'][] = 'Upload directory is not writable';
            }
        }
        
        return $results;
    }
    
    /**
     * Test image generation API
     * 
     * @return array Test results
     */
    private static function test_image_generation_api() {
        $result = array(
            'status' => 'healthy',
            'message' => 'API connection successful',
            'response_time' => 0
        );
        
        // For now, we'll do a lightweight check
        // Full image generation test would be expensive
        $api_key = get_option('wp_blog_agent_gemini_image_api_key', '');
        
        if (empty($api_key)) {
            $result['status'] = 'not_configured';
            $result['message'] = 'API key not configured';
            return $result;
        }
        
        // Check API key format
        if (strlen($api_key) < 20) {
            $result['status'] = 'warning';
            $result['message'] = 'API key appears to be invalid (too short)';
        }
        
        // Note: We avoid making actual API calls in health check to prevent costs
        // In production, you might want to make actual test calls periodically
        $result['message'] = 'API key configured (actual API test skipped to avoid costs)';
        
        return $result;
    }
    
    /**
     * Calculate overall status from all checks
     * 
     * @param array $results All check results
     * @return string Overall status
     */
    private static function calculate_overall_status($results) {
        $has_error = false;
        $has_warning = false;
        
        foreach ($results as $key => $check) {
            if (is_array($check) && isset($check['status'])) {
                if ($check['status'] === 'error') {
                    $has_error = true;
                } elseif ($check['status'] === 'warning') {
                    $has_warning = true;
                }
            }
        }
        
        if ($has_error) {
            return 'error';
        } elseif ($has_warning) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }
    
    /**
     * Get status icon
     * 
     * @param string $status Status string
     * @return string HTML icon
     */
    public static function get_status_icon($status) {
        switch ($status) {
            case 'healthy':
                return '<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>';
            case 'warning':
                return '<span class="dashicons dashicons-warning" style="color: #ffb900;"></span>';
            case 'error':
                return '<span class="dashicons dashicons-dismiss" style="color: #dc3232;"></span>';
            case 'not_configured':
                return '<span class="dashicons dashicons-info" style="color: #72aee6;"></span>';
            default:
                return '<span class="dashicons dashicons-marker" style="color: #999;"></span>';
        }
    }
    
    /**
     * Get status label
     * 
     * @param string $status Status string
     * @return string Label
     */
    public static function get_status_label($status) {
        switch ($status) {
            case 'healthy':
                return 'Healthy';
            case 'warning':
                return 'Warning';
            case 'error':
                return 'Error';
            case 'not_configured':
                return 'Not Configured';
            default:
                return ucfirst($status);
        }
    }
}

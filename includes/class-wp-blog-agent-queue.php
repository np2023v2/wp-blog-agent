<?php
/**
 * Queue Manager for Post Generation Tasks
 */
class WP_Blog_Agent_Queue {
    
    
    /**
     * Get queue table name
     */
    private static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'blog_agent_queue';
    }
    
    private static function ensure_table_exists() {
        global $wpdb;

        $table_name = self::get_table_name();

        // Kiểm tra tồn tại bảng an toàn
        $exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
        if ($exists === $table_name) {
            return true;
        }

        // Tạo bảng
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            topic_id bigint(20) unsigned DEFAULT NULL,
            topic_text varchar(500) DEFAULT NULL,
            series_id bigint(20) unsigned DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            trigger_source varchar(50) NOT NULL DEFAULT 'manual',
            post_id bigint(20) unsigned DEFAULT NULL,
            attempts int(11) NOT NULL DEFAULT 0,
            error_message longtext NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            started_at datetime NULL DEFAULT NULL,
            completed_at datetime NULL DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Xác minh tạo thành công
        $exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
        if ($exists !== $table_name) {
            WP_Blog_Agent_Logger::error('Failed to create queue table', [
                'table_name' => $table_name,
                'error' => $wpdb->last_error
            ]);
            return false;
        }

        WP_Blog_Agent_Logger::info('Queue table created successfully', ['table_name' => $table_name]);
        return true;
    }
    
   public static function enqueue($topic_id = null, $trigger = 'manual', $metadata = array()) {
        global $wpdb;

        if (!self::ensure_table_exists()) {
            WP_Blog_Agent_Logger::error('Cannot enqueue task - table does not exist and could not be created', [
                'topic_id' => $topic_id,
                'trigger' => $trigger
            ]);
            return false;
        }

        $table_name = self::get_table_name();

        $data = [
            'topic_id' => $topic_id,
            'status' => 'pending',
            'trigger_source' => $trigger,
            'created_at' => current_time('mysql'),
            'attempts' => 0
        ];
        
        $format = ['%d', '%s', '%s', '%s', '%d'];
        
        // Add optional metadata fields
        if (isset($metadata['topic_text'])) {
            $data['topic_text'] = sanitize_text_field($metadata['topic_text']);
            $format[] = '%s';
        }
        
        if (isset($metadata['series_id'])) {
            $data['series_id'] = intval($metadata['series_id']);
            $format[] = '%d';
        }

        $result = $wpdb->insert($table_name, $data, $format);

        if ($result === false) {
            WP_Blog_Agent_Logger::error('Failed to enqueue generation task', [
                'topic_id' => $topic_id,
                'trigger' => $trigger,
                'error' => $wpdb->last_error
            ]);
            return false;
        }

        $queue_id = $wpdb->insert_id;

        WP_Blog_Agent_Logger::info('Generation task enqueued', [
            'queue_id' => $queue_id,
            'topic_id' => $topic_id,
            'trigger' => $trigger
        ]);

        // Schedule queue processing using cron module
        WP_Blog_Agent_Cron::schedule_queue_processing();

        return $queue_id;
    }
    
    /**
     * Get next pending task from queue
     * 
     * @return object|null Queue item object or null if no pending tasks
     */
    public static function get_next_task() {
        global $wpdb;
        
        // Ensure table exists
        if (!self::ensure_table_exists()) {
            return null;
        }
        
        $table_name = self::get_table_name();
        
        $task = $wpdb->get_row(
            "SELECT * FROM $table_name 
            WHERE status = 'pending' 
            AND attempts < 3
            ORDER BY created_at ASC 
            LIMIT 1"
        );
        
        return $task;
    }
    
    /**
     * Mark task as processing
     * 
     * @param int $queue_id Queue item ID
     * @return bool Success status
     */
    public static function mark_processing($queue_id) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => 'processing',
                'started_at' => current_time('mysql')
            ),
            array('id' => $queue_id),
            array('%s', '%s'),
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Mark task as completed
     * 
     * @param int $queue_id Queue item ID
     * @param int $post_id Generated post ID
     * @return bool Success status
     */
    public static function mark_completed($queue_id, $post_id) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => 'completed',
                'post_id' => $post_id,
                'completed_at' => current_time('mysql')
            ),
            array('id' => $queue_id),
            array('%s', '%d', '%s'),
            array('%d')
        );
        
        WP_Blog_Agent_Logger::success('Generation task completed', array(
            'queue_id' => $queue_id,
            'post_id' => $post_id
        ));
        
        return $result !== false;
    }
    
    /**
     * Mark task as failed
     * 
     * @param int $queue_id Queue item ID
     * @param string $error_message Error message
     * @return bool Success status
     */
    public static function mark_failed($queue_id, $error_message) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        // Get current attempts
        $task = $wpdb->get_row($wpdb->prepare(
            "SELECT attempts FROM $table_name WHERE id = %d",
            $queue_id
        ));
        
        if (!$task) {
            return false;
        }
        
        $attempts = intval($task->attempts) + 1;
        $status = ($attempts >= 3) ? 'failed' : 'pending';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => $status,
                'attempts' => $attempts,
                'error_message' => $error_message,
                'completed_at' => current_time('mysql')
            ),
            array('id' => $queue_id),
            array('%s', '%d', '%s', '%s'),
            array('%d')
        );
        
        WP_Blog_Agent_Logger::error('Generation task failed', array(
            'queue_id' => $queue_id,
            'attempts' => $attempts,
            'error' => $error_message
        ));
        
        // Schedule retry if not max attempts reached using cron module
        if ($attempts < 3) {
            WP_Blog_Agent_Cron::schedule_queue_processing(300); // Retry in 5 minutes
        }
        
        return $result !== false;
    }
    
    public static function process_queue() {
        WP_Blog_Agent_Logger::info('Processing generation queue');

        $task = self::get_next_task();
        if (!$task) {
            WP_Blog_Agent_Logger::info('No pending tasks in queue');
            return;
        }

        WP_Blog_Agent_Logger::info('Processing task', [
            'queue_id' => $task->id,
            'topic_id' => $task->topic_id,
            'topic_text' => $task->topic_text ?? null,
            'series_id' => $task->series_id ?? null,
            'trigger' => $task->trigger_source ?? 'manual'
        ]);

        self::mark_processing($task->id);

        // Check if this is a rewrite task
        if ($task->trigger_source === 'rewrite') {
            // Rewrite post
            $result = self::rewrite_post($task);
        } elseif (!empty($task->topic_text) && !empty($task->series_id)) {
            // Series generation
            $result = self::generate_series_post($task);
        } else {
            // Regular topic-based generation
            $generator = new WP_Blog_Agent_Generator();
            $result = $generator->generate_post($task->topic_id);
        }

        if (is_wp_error($result)) {
            self::mark_failed($task->id, $result->get_error_message());
        } else {
            self::mark_completed($task->id, $result);
        }

        // Schedule next task processing using cron module
        $next_task = self::get_next_task();
        if ($next_task) {
            WP_Blog_Agent_Cron::schedule_queue_processing(10);
        }
    }
    
    /**
     * Generate a post from a series suggestion
     * 
     * @param object $task Queue task object with topic_text and series_id
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    private static function generate_series_post($task) {
        $topic = $task->topic_text;
        $series_id = $task->series_id;
        
        WP_Blog_Agent_Logger::info('Generating post from series suggestion', array(
            'series_id' => $series_id,
            'topic' => $topic
        ));
        
        // Get AI provider
        $provider = get_option('wp_blog_agent_ai_provider', 'openai');
        
        // Generate content
        if ($provider === 'gemini') {
            $ai = new WP_Blog_Agent_Gemini();
        } elseif ($provider === 'ollama') {
            $ai = new WP_Blog_Agent_Ollama();
        } else {
            $ai = new WP_Blog_Agent_OpenAI();
        }
        
        $content = $ai->generate_content($topic, array(), array());
        
        if (is_wp_error($content)) {
            WP_Blog_Agent_Logger::error('Series content generation failed', array(
                'error' => $content->get_error_message(),
                'provider' => $provider,
                'series_id' => $series_id
            ));
            return $content;
        }
        
        WP_Blog_Agent_Logger::info('Series content generated successfully', array('provider' => $provider));
        
        // Parse content to extract title and body using Generator's method
        $generator = new WP_Blog_Agent_Generator();
        $parsed = $generator->parse_content($content);
        
        // Process inline images if enabled
        $auto_generate_inline_images = get_option('wp_blog_agent_auto_generate_inline_images', 'no');
        if ($auto_generate_inline_images === 'yes') {
            WP_Blog_Agent_Logger::info('Processing inline image placeholders for series post');
            $parsed['content'] = $generator->process_image_placeholders($parsed['content'], $topic);
        }
        
        // Determine post status
        $auto_publish = get_option('wp_blog_agent_auto_publish', 'yes');
        $post_status = ($auto_publish === 'yes') ? 'publish' : 'draft';
        
        // Create the post
        $post_data = array(
            'post_title'   => $parsed['title'],
            'post_content' => $parsed['content'],
            'post_status'  => $post_status,
            'post_author'  => 1,
            'post_type'    => 'post',
            'post_excerpt' => $generator->generate_excerpt($parsed['content']),
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            WP_Blog_Agent_Logger::error('Series post creation failed', array('error' => $post_id->get_error_message()));
            return $post_id;
        }
        
        WP_Blog_Agent_Logger::success('Series post created successfully', array(
            'post_id' => $post_id,
            'title' => $parsed['title'],
            'status' => $post_status,
            'series_id' => $series_id
        ));
        
        // Add metadata
        update_post_meta($post_id, '_wp_blog_agent_generated', true);
        update_post_meta($post_id, '_wp_blog_agent_topic_id', 0); // 0 indicates series generation
        update_post_meta($post_id, '_wp_blog_agent_keywords', '');
        update_post_meta($post_id, '_wp_blog_agent_hashtags', '');
        update_post_meta($post_id, '_wp_blog_agent_provider', $provider);
        update_post_meta($post_id, '_wp_blog_agent_series_id', $series_id);
        
        // Add post to series
        WP_Blog_Agent_Series::add_post_to_series($series_id, $post_id);
        
        // Auto-generate featured image if enabled
        $auto_generate_image = get_option('wp_blog_agent_auto_generate_image', 'no');
        if ($auto_generate_image === 'yes') {
            try {
                $prompt = sprintf(
                    'Create a professional, eye-catching blog header image for a blog post titled "%s" about %s.',
                    $parsed['title'],
                    $topic
                );
                
                WP_Blog_Agent_Logger::info('Auto-generating featured image for series post', array(
                    'post_id' => $post_id,
                    'prompt' => substr($prompt, 0, 100)
                ));
                
                $image_generator = new WP_Blog_Agent_Image_Generator();
                $params = array(
                    'aspectRatio' => '16:9',
                    'imageSize' => '1K',
                    'sampleCount' => 1,
                    'outputMimeType' => 'image/jpeg',
                    'personGeneration' => 'ALLOW_ALL'
                );
                
                $attachment_id = $image_generator->generate_and_save($prompt, $post_id, $params);
                
                if (!is_wp_error($attachment_id)) {
                    update_post_meta($attachment_id, '_wp_blog_agent_generated_image', true);
                    update_post_meta($attachment_id, '_wp_blog_agent_image_prompt', $prompt);
                    update_post_meta($attachment_id, '_wp_blog_agent_attached_post', $post_id);
                    update_post_meta($attachment_id, '_wp_blog_agent_auto_generated', true);
                    
                    WP_Blog_Agent_Logger::success('Featured image auto-generated for series post', array(
                        'post_id' => $post_id,
                        'attachment_id' => $attachment_id
                    ));
                }
            } catch (Exception $e) {
                WP_Blog_Agent_Logger::error('Exception during auto-image generation for series post', array(
                    'error' => $e->getMessage()
                ));
            }
        }
        
        // Auto-generate RankMath SEO meta if enabled
        $auto_generate_seo = get_option('wp_blog_agent_auto_generate_seo', 'no');
        if ($auto_generate_seo === 'yes') {
            $rankmath = new WP_Blog_Agent_RankMath();
            $rankmath->generate_seo_meta($post_id);
        }
        
        return $post_id;
    }
    
    /**
     * Rewrite an existing post with new AI-generated content
     * 
     * @param object $task Queue task object with post_id
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    private static function rewrite_post($task) {
        // Extract post_id from metadata
        $metadata = maybe_unserialize($task->metadata);
        $post_id = isset($metadata['post_id']) ? intval($metadata['post_id']) : 0;
        $topic = $task->topic_text;
        
        if ($post_id <= 0) {
            return new WP_Error('invalid_post_id', 'Invalid post ID for rewrite');
        }
        
        // Get the existing post
        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('post_not_found', 'Post not found for rewrite');
        }
        
        WP_Blog_Agent_Logger::info('Rewriting post', array(
            'post_id' => $post_id,
            'topic' => $topic,
            'original_title' => $post->post_title
        ));
        
        // Get AI provider
        $provider = get_option('wp_blog_agent_ai_provider', 'openai');
        
        // Generate new content
        if ($provider === 'gemini') {
            $ai = new WP_Blog_Agent_Gemini();
        } elseif ($provider === 'ollama') {
            $ai = new WP_Blog_Agent_Ollama();
        } else {
            $ai = new WP_Blog_Agent_OpenAI();
        }
        
        $content = $ai->generate_content($topic, array(), array());
        
        if (is_wp_error($content)) {
            WP_Blog_Agent_Logger::error('Post rewrite content generation failed', array(
                'error' => $content->get_error_message(),
                'provider' => $provider,
                'post_id' => $post_id
            ));
            return $content;
        }
        
        WP_Blog_Agent_Logger::info('Rewrite content generated successfully', array('provider' => $provider));
        
        // Parse content to extract title and body
        $generator = new WP_Blog_Agent_Generator();
        $parsed = $generator->parse_content($content);
        
        // Process inline images if enabled
        $auto_generate_inline_images = get_option('wp_blog_agent_auto_generate_inline_images', 'no');
        if ($auto_generate_inline_images === 'yes') {
            WP_Blog_Agent_Logger::info('Processing inline image placeholders for rewritten post');
            $parsed['content'] = $generator->process_image_placeholders($parsed['content'], $topic);
        }
        
        // Update the post with new content
        $post_data = array(
            'ID'           => $post_id,
            'post_title'   => $parsed['title'],
            'post_content' => $parsed['content'],
            'post_excerpt' => $generator->generate_excerpt($parsed['content']),
        );
        
        $result = wp_update_post($post_data);
        
        if (is_wp_error($result)) {
            WP_Blog_Agent_Logger::error('Post rewrite failed', array('error' => $result->get_error_message()));
            return $result;
        }
        
        WP_Blog_Agent_Logger::success('Post rewritten successfully', array(
            'post_id' => $post_id,
            'new_title' => $parsed['title']
        ));
        
        // Update metadata
        update_post_meta($post_id, '_wp_blog_agent_generated', true);
        update_post_meta($post_id, '_wp_blog_agent_provider', $provider);
        update_post_meta($post_id, '_wp_blog_agent_rewritten', true);
        update_post_meta($post_id, '_wp_blog_agent_rewrite_date', current_time('mysql'));
        
        // Auto-generate featured image if enabled
        $auto_generate_image = get_option('wp_blog_agent_auto_generate_image', 'no');
        if ($auto_generate_image === 'yes') {
            try {
                $prompt = sprintf(
                    'Create a professional, eye-catching blog header image for a blog post titled "%s" about %s.',
                    $parsed['title'],
                    $topic
                );
                
                WP_Blog_Agent_Logger::info('Auto-generating featured image for rewritten post', array(
                    'post_id' => $post_id,
                    'prompt' => substr($prompt, 0, 100)
                ));
                
                $image_generator = new WP_Blog_Agent_Image_Generator();
                $params = array(
                    'aspectRatio' => '16:9',
                    'imageSize' => '1K',
                    'sampleCount' => 1,
                    'outputMimeType' => 'image/jpeg',
                    'personGeneration' => 'ALLOW_ALL'
                );
                
                $attachment_id = $image_generator->generate_and_save($prompt, $post_id, $params);
                
                if (!is_wp_error($attachment_id)) {
                    update_post_meta($attachment_id, '_wp_blog_agent_generated_image', true);
                    update_post_meta($attachment_id, '_wp_blog_agent_image_prompt', $prompt);
                    update_post_meta($attachment_id, '_wp_blog_agent_attached_post', $post_id);
                    update_post_meta($attachment_id, '_wp_blog_agent_auto_generated', true);
                    
                    WP_Blog_Agent_Logger::success('Featured image auto-generated for rewritten post', array(
                        'post_id' => $post_id,
                        'attachment_id' => $attachment_id
                    ));
                }
            } catch (Exception $e) {
                WP_Blog_Agent_Logger::error('Exception during auto-image generation for rewritten post', array(
                    'error' => $e->getMessage()
                ));
            }
        }
        
        // Auto-generate RankMath SEO meta if enabled
        $auto_generate_seo = get_option('wp_blog_agent_auto_generate_seo', 'no');
        if ($auto_generate_seo === 'yes') {
            $rankmath = new WP_Blog_Agent_RankMath();
            $rankmath->generate_seo_meta($post_id);
        }
        
        return $post_id;
    }
    
    /**
     * Get queue statistics
     * 
     * @return array Queue statistics
     */
    public static function get_stats() {
        global $wpdb;
        
        // Ensure table exists
        if (!self::ensure_table_exists()) {
            return array(
                'pending' => 0,
                'processing' => 0,
                'completed' => 0,
                'failed' => 0,
                'total' => 0
            );
        }
        
        $table_name = self::get_table_name();
        
        $stats = array(
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0,
            'total' => 0
        );
        
        $results = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM $table_name GROUP BY status"
        );
        
        if ($results) {
            foreach ($results as $row) {
                $stats[$row->status] = intval($row->count);
                $stats['total'] += intval($row->count);
            }
        }
        
        return $stats;
    }
    
    /**
     * Get recent queue items
     * 
     * @param int $limit Number of items to retrieve
     * @return array Queue items
     */
    public static function get_recent($limit = 10) {
        global $wpdb;
        
        // Ensure table exists
        if (!self::ensure_table_exists()) {
            return array();
        }
        
        $table_name = self::get_table_name();
        
        $items = $wpdb->get_results($wpdb->prepare(
            "SELECT q.*, t.topic 
            FROM $table_name q 
            LEFT JOIN {$wpdb->prefix}blog_agent_topics t ON q.topic_id = t.id 
            ORDER BY q.created_at DESC 
            LIMIT %d",
            $limit
        ));
        
        return $items;
    }
    
    /**
     * Clear completed and failed tasks older than specified days
     * 
     * @param int $days Number of days to keep
     * @return int Number of items deleted
     */
    public static function cleanup($days = 7) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        $date_limit = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name 
            WHERE status IN ('completed', 'failed') 
            AND completed_at < %s",
            $date_limit
        ));
        
        if ($deleted > 0) {
            WP_Blog_Agent_Logger::info('Queue cleanup completed', array(
                'items_deleted' => $deleted,
                'older_than' => $days . ' days'
            ));
        }
        
        return $deleted;
    }
}
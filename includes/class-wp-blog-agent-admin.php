<?php
/**
 * Admin Interface
 */
class WP_Blog_Agent_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_post_wp_blog_agent_add_topic', array($this, 'handle_add_topic'));
        add_action('admin_post_wp_blog_agent_delete_topic', array($this, 'handle_delete_topic'));
        add_action('admin_post_wp_blog_agent_generate_now', array($this, 'handle_generate_now'));
        add_action('admin_post_wp_blog_agent_generate_manual', array($this, 'handle_generate_manual'));
        add_action('admin_post_wp_blog_agent_generate_image', array($this, 'handle_generate_image'));
        
        // Series handlers
        add_action('admin_post_wp_blog_agent_create_series', array($this, 'handle_create_series'));
        add_action('admin_post_wp_blog_agent_delete_series', array($this, 'handle_delete_series'));
        add_action('admin_post_wp_blog_agent_add_post_to_series', array($this, 'handle_add_post_to_series'));
        add_action('admin_post_wp_blog_agent_remove_post_from_series', array($this, 'handle_remove_post_from_series'));
        add_action('admin_post_wp_blog_agent_generate_from_suggestion', array($this, 'handle_generate_from_suggestion'));
        add_action('admin_post_wp_blog_agent_rewrite_post', array($this, 'handle_rewrite_post'));
        
        // AJAX handlers for RankMath SEO generation
        add_action('wp_ajax_wp_blog_agent_generate_seo', array($this, 'ajax_generate_seo'));
        add_action('wp_ajax_wp_blog_agent_generate_post_image', array($this, 'ajax_generate_post_image'));
        
        // AJAX handler for topic suggestions
        add_action('wp_ajax_wp_blog_agent_get_suggestions', array($this, 'ajax_get_suggestions'));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        add_menu_page(
            __('WP Blog Agent', 'wp-blog-agent'),
            __('Blog Agent', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent',
            array($this, 'render_settings_page'),
            'dashicons-edit-large',
            30
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Settings', 'wp-blog-agent'),
            __('Settings', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Topics', 'wp-blog-agent'),
            __('Topics', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent-topics',
            array($this, 'render_topics_page')
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Series', 'wp-blog-agent'),
            __('Series', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent-series',
            array($this, 'render_series_page')
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Generated Posts', 'wp-blog-agent'),
            __('Generated Posts', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent-posts',
            array($this, 'render_posts_page')
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Queue', 'wp-blog-agent'),
            __('Queue', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent-queue',
            array($this, 'render_queue_page')
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Logs', 'wp-blog-agent'),
            __('Logs', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent-logs',
            array($this, 'render_logs_page')
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Image Generation', 'wp-blog-agent'),
            __('Image Generation', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent-image-gen',
            array($this, 'render_image_gen_page')
        );
        
        add_submenu_page(
            'wp-blog-agent',
            __('Health Check', 'wp-blog-agent'),
            __('Health Check', 'wp-blog-agent'),
            'manage_options',
            'wp-blog-agent-health-check',
            array($this, 'render_health_check_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        // API Credentials group - separate to prevent accidental reset
        register_setting('wp_blog_agent_api_settings', 'wp_blog_agent_ai_provider');
        register_setting('wp_blog_agent_api_settings', 'wp_blog_agent_openai_api_key');
        register_setting('wp_blog_agent_api_settings', 'wp_blog_agent_openai_base_url');
        register_setting('wp_blog_agent_api_settings', 'wp_blog_agent_gemini_api_key');
        register_setting('wp_blog_agent_api_settings', 'wp_blog_agent_gemini_image_api_key');
        register_setting('wp_blog_agent_api_settings', 'wp_blog_agent_ollama_base_url');
        
        // General Settings group
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_openai_model');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_openai_max_tokens');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_openai_system_prompt');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_gemini_model');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_gemini_max_tokens');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_gemini_system_prompt');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_ollama_model');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_ollama_system_prompt');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_schedule_enabled');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_schedule_frequency');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_auto_publish');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_auto_generate_image');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_auto_generate_seo');
        register_setting('wp_blog_agent_general_settings', 'wp_blog_agent_auto_generate_inline_images');
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'wp-blog-agent') === false) {
            return;
        }
        
        wp_enqueue_style('wp-blog-agent-admin', WP_BLOG_AGENT_PLUGIN_URL . 'assets/css/admin.css', array(), WP_BLOG_AGENT_VERSION);
        wp_enqueue_script('wp-blog-agent-admin', WP_BLOG_AGENT_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), WP_BLOG_AGENT_VERSION, true);
        
        // Pass AJAX URL and nonces to JavaScript
        wp_localize_script('wp-blog-agent-admin', 'wpBlogAgent', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'seoNonce' => wp_create_nonce('wp_blog_agent_seo_nonce'),
            'imageNonce' => wp_create_nonce('wp_blog_agent_image_nonce')
        ));
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Save API Credentials settings
        if (isset($_POST['wp_blog_agent_api_settings_nonce']) && 
            wp_verify_nonce($_POST['wp_blog_agent_api_settings_nonce'], 'wp_blog_agent_api_settings')) {
            
            update_option('wp_blog_agent_ai_provider', sanitize_text_field($_POST['ai_provider']));
            update_option('wp_blog_agent_openai_api_key', sanitize_text_field($_POST['openai_api_key']));
            update_option('wp_blog_agent_openai_base_url', esc_url_raw($_POST['openai_base_url']));
            update_option('wp_blog_agent_gemini_api_key', sanitize_text_field($_POST['gemini_api_key']));
            update_option('wp_blog_agent_gemini_image_api_key', sanitize_text_field($_POST['gemini_image_api_key']));
            update_option('wp_blog_agent_ollama_base_url', esc_url_raw($_POST['ollama_base_url']));
            
            echo '<div class="notice notice-success"><p>' . __('API Credentials saved successfully!', 'wp-blog-agent') . '</p></div>';
        }
        
        // Save General Settings
        if (isset($_POST['wp_blog_agent_general_settings_nonce']) && 
            wp_verify_nonce($_POST['wp_blog_agent_general_settings_nonce'], 'wp_blog_agent_general_settings')) {
            
            update_option('wp_blog_agent_openai_model', sanitize_text_field($_POST['openai_model']));
            update_option('wp_blog_agent_openai_max_tokens', !empty($_POST['openai_max_tokens']) ? absint($_POST['openai_max_tokens']) : '');
            update_option('wp_blog_agent_openai_system_prompt', sanitize_textarea_field($_POST['openai_system_prompt']));
            update_option('wp_blog_agent_gemini_model', sanitize_text_field($_POST['gemini_model']));
            update_option('wp_blog_agent_gemini_max_tokens', !empty($_POST['gemini_max_tokens']) ? absint($_POST['gemini_max_tokens']) : '');
            update_option('wp_blog_agent_gemini_system_prompt', sanitize_textarea_field($_POST['gemini_system_prompt']));
            update_option('wp_blog_agent_ollama_model', sanitize_text_field($_POST['ollama_model']));
            update_option('wp_blog_agent_ollama_system_prompt', sanitize_textarea_field($_POST['ollama_system_prompt']));
            update_option('wp_blog_agent_schedule_enabled', sanitize_text_field($_POST['schedule_enabled']));
            update_option('wp_blog_agent_auto_publish', sanitize_text_field($_POST['auto_publish']));
            update_option('wp_blog_agent_auto_generate_image', sanitize_text_field($_POST['auto_generate_image']));
            update_option('wp_blog_agent_auto_generate_seo', isset($_POST['auto_generate_seo']) ? sanitize_text_field($_POST['auto_generate_seo']) : 'no');
            update_option('wp_blog_agent_auto_generate_inline_images', isset($_POST['auto_generate_inline_images']) ? sanitize_text_field($_POST['auto_generate_inline_images']) : 'no');
            
            $frequency = sanitize_text_field($_POST['schedule_frequency']);
            update_option('wp_blog_agent_schedule_frequency', $frequency);
            
            // Update cron schedule
            WP_Blog_Agent_Scheduler::update_schedule($frequency);
            
            echo '<div class="notice notice-success"><p>' . __('General Settings saved successfully!', 'wp-blog-agent') . '</p></div>';
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/settings-page.php';
    }
    
    /**
     * Render topics page
     */
    public function render_topics_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/topics-page.php';
    }
    
    /**
     * Render posts page
     */
    public function render_posts_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/posts-page.php';
    }
    
    /**
     * Render logs page
     */
    public function render_logs_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle clear logs action
        if (isset($_POST['clear_logs']) && check_admin_referer('wp_blog_agent_clear_logs')) {
            WP_Blog_Agent_Logger::clear_old_logs(0);
            echo '<div class="notice notice-success"><p>' . __('Logs cleared successfully!', 'wp-blog-agent') . '</p></div>';
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/logs-page.php';
    }
    
    /**
     * Render queue page
     */
    public function render_queue_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle cleanup action
        if (isset($_POST['cleanup_queue']) && check_admin_referer('wp_blog_agent_cleanup_queue')) {
            $days = isset($_POST['cleanup_days']) ? intval($_POST['cleanup_days']) : 7;
            $deleted = WP_Blog_Agent_Queue::cleanup($days);
            echo '<div class="notice notice-success"><p>' . sprintf(__('Cleaned up %d completed/failed tasks!', 'wp-blog-agent'), $deleted) . '</p></div>';
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/queue-page.php';
    }
    
    /**
     * Render image generation page
     */
    public function render_image_gen_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/image-gen-page.php';
    }
    
    /**
     * Render health check page
     */
    public function render_health_check_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/health-check-page.php';
    }
    
    /**
     * Handle add topic form submission
     */
    public function handle_add_topic() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        check_admin_referer('wp_blog_agent_add_topic');
        
        // Validate topic data
        $validated = WP_Blog_Agent_Validator::validate_topic(
            $_POST['topic'],
            $_POST['keywords'],
            $_POST['hashtags']
        );
        
        if (is_wp_error($validated)) {
            WP_Blog_Agent_Logger::warning('Topic validation failed', array('error' => $validated->get_error_message()));
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&error=' . urlencode($validated->get_error_message())));
            exit;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'blog_agent_topics';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'topic' => $validated['topic'],
                'keywords' => $validated['keywords'],
                'hashtags' => $validated['hashtags'],
                'status' => 'active',
            )
        );
        
        if ($result) {
            WP_Blog_Agent_Logger::success('Topic added', array('topic' => $validated['topic']));
        } else {
            WP_Blog_Agent_Logger::error('Failed to add topic', array('topic' => $validated['topic']));
        }
        
        wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&added=1'));
        exit;
    }
    
    /**
     * Handle delete topic
     */
    public function handle_delete_topic() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        check_admin_referer('wp_blog_agent_delete_topic_' . $_GET['id']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'blog_agent_topics';
        
        $topic_id = intval($_GET['id']);
        
        $wpdb->delete(
            $table_name,
            array('id' => $topic_id)
        );
        
        WP_Blog_Agent_Logger::info('Topic deleted', array('topic_id' => $topic_id));
        
        wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&deleted=1'));
        exit;
    }
    
    /**
     * Handle generate now action
     */
    public function handle_generate_now() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        check_admin_referer('wp_blog_agent_generate_now');
        
        WP_Blog_Agent_Logger::info('Manual generation triggered');
        
        $topic_id = isset($_GET['topic_id']) ? intval($_GET['topic_id']) : null;
        
        // Add to queue
        $queue_id = WP_Blog_Agent_Queue::enqueue($topic_id, 'manual');
        
        if ($queue_id === false) {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&error=' . urlencode('Failed to add generation task to queue')));
        } else {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&queued=' . $queue_id));
        }
        exit;
    }
    
    /**
     * Handle manual topic generation
     */
    public function handle_generate_manual() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        check_admin_referer('wp_blog_agent_generate_manual');
        
        // Validate topic data
        $validated = WP_Blog_Agent_Validator::validate_topic(
            $_POST['manual_topic'],
            $_POST['manual_keywords'],
            isset($_POST['manual_hashtags']) ? $_POST['manual_hashtags'] : ''
        );
        
        if (is_wp_error($validated)) {
            WP_Blog_Agent_Logger::warning('Manual topic validation failed', array('error' => $validated->get_error_message()));
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&error=' . urlencode($validated->get_error_message())));
            exit;
        }
        
        WP_Blog_Agent_Logger::info('Manual generation with custom topic triggered', array('topic' => $validated['topic']));
        
        // Parse keywords and hashtags
        $keywords = array_filter(array_map('trim', explode(',', $validated['keywords'])));
        $hashtags = array_filter(array_map('trim', explode(',', $validated['hashtags'])));
        
        // Add # prefix to hashtags if not present
        $hashtags = array_map(function($tag) {
            return strpos($tag, '#') === 0 ? $tag : '#' . $tag;
        }, $hashtags);
        
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
        
        $content = $ai->generate_content($validated['topic'], $keywords, $hashtags);
        
        if (is_wp_error($content)) {
            WP_Blog_Agent_Logger::error('Manual content generation failed', array(
                'error' => $content->get_error_message(),
                'provider' => $provider
            ));
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&error=' . urlencode($content->get_error_message())));
            exit;
        }
        
        WP_Blog_Agent_Logger::info('Manual content generated successfully', array('provider' => $provider));
        
        // Parse content to extract title and body
        $parsed = $this->parse_generated_content($content);
        
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
            'post_excerpt' => $this->generate_excerpt($parsed['content']),
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            WP_Blog_Agent_Logger::error('Post creation failed', array('error' => $post_id->get_error_message()));
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-topics&error=' . urlencode($post_id->get_error_message())));
            exit;
        }
        
        WP_Blog_Agent_Logger::success('Manual post created successfully', array(
            'post_id' => $post_id,
            'title' => $parsed['title'],
            'status' => $post_status
        ));
        
        // Add metadata
        update_post_meta($post_id, '_wp_blog_agent_generated', true);
        update_post_meta($post_id, '_wp_blog_agent_topic_id', 0); // 0 indicates manual generation
        update_post_meta($post_id, '_wp_blog_agent_keywords', implode(', ', $keywords));
        update_post_meta($post_id, '_wp_blog_agent_hashtags', implode(' ', $hashtags));
        update_post_meta($post_id, '_wp_blog_agent_provider', $provider);
        
        // Auto-generate featured image if enabled
        $auto_generate_image = get_option('wp_blog_agent_auto_generate_image', 'no');
        if ($auto_generate_image === 'yes') {
            $this->auto_generate_featured_image($post_id, $parsed['title'], $validated['topic']);
        }
        
        // Auto-generate RankMath SEO meta if enabled
        $auto_generate_seo = get_option('wp_blog_agent_auto_generate_seo', 'no');
        if ($auto_generate_seo === 'yes') {
            $this->auto_generate_rankmath_seo($post_id);
        }
        
        wp_redirect(admin_url('admin.php?page=wp-blog-agent-posts&generated=' . $post_id));
        exit;
    }
    
    /**
     * Generate excerpt from content
     */
    private function generate_excerpt($content) {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);
        if (strlen($text) > 150) {
            $text = substr($text, 0, 147) . '...';
        }
        return $text;
    }
    
    /**
     * Parse content to extract title and body
     */
    private function parse_generated_content($content) {
        // Try to extract title from h1 tag
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $matches)) {
            $title = strip_tags($matches[1]);
            $content = preg_replace('/<h1[^>]*>.*?<\/h1>/is', '', $content, 1);
        } else {
            // Try to get first line as title
            $lines = explode("\n", strip_tags($content));
            $title = trim($lines[0]);
            if (strlen($title) > 100) {
                $title = substr($title, 0, 97) . '...';
            }
        }
        
        return array(
            'title' => $title ?: 'Untitled Post',
            'content' => trim($content)
        );
    }
    
    /**
     * Handle image generation
     */
    public function handle_generate_image() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        check_admin_referer('wp_blog_agent_generate_image');
        
        // Validate input
        if (empty($_POST['image_prompt'])) {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-image-gen&error=' . urlencode('Image prompt is required.')));
            exit;
        }
        
        $prompt = sanitize_textarea_field($_POST['image_prompt']);
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $aspect_ratio = isset($_POST['aspect_ratio']) ? sanitize_text_field($_POST['aspect_ratio']) : '16:9';
        $image_size = isset($_POST['image_size']) ? sanitize_text_field($_POST['image_size']) : '1K';
        $set_featured = isset($_POST['set_featured']) && $_POST['set_featured'] === '1';
        
        WP_Blog_Agent_Logger::info('Image generation triggered', array(
            'prompt' => substr($prompt, 0, 100),
            'post_id' => $post_id,
            'aspect_ratio' => $aspect_ratio,
            'image_size' => $image_size
        ));
        
        // Generate and save image
        $image_generator = new WP_Blog_Agent_Image_Generator();
        
        $params = array(
            'aspectRatio' => $aspect_ratio,
            'imageSize' => $image_size,
            'sampleCount' => 1,
            'outputMimeType' => 'image/jpeg',
            'personGeneration' => 'ALLOW_ALL'
        );
        
        // If set_featured is false or post_id is 0, don't attach to post
        $attach_post_id = ($set_featured && $post_id > 0) ? $post_id : 0;
        
        $attachment_id = $image_generator->generate_and_save($prompt, $attach_post_id, $params);
        
        if (is_wp_error($attachment_id)) {
            WP_Blog_Agent_Logger::error('Image generation failed', array(
                'error' => $attachment_id->get_error_message()
            ));
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-image-gen&error=' . urlencode($attachment_id->get_error_message())));
            exit;
        }
        
        // Store metadata
        update_post_meta($attachment_id, '_wp_blog_agent_generated_image', true);
        update_post_meta($attachment_id, '_wp_blog_agent_image_prompt', $prompt);
        update_post_meta($attachment_id, '_wp_blog_agent_image_params', array(
            'aspect_ratio' => $aspect_ratio,
            'image_size' => $image_size
        ));
        
        if ($post_id > 0) {
            update_post_meta($attachment_id, '_wp_blog_agent_attached_post', $post_id);
        }
        
        WP_Blog_Agent_Logger::success('Image generated and saved successfully', array(
            'attachment_id' => $attachment_id,
            'post_id' => $post_id
        ));
        
        wp_redirect(admin_url('admin.php?page=wp-blog-agent-image-gen&generated=' . $attachment_id));
        exit;
    }
    
    /**
     * Auto-generate and set featured image for a post
     */
    private function auto_generate_featured_image($post_id, $title, $topic) {
        try {
            // Create image prompt based on post title and topic
            $prompt = sprintf(
                'Create a professional, eye-catching blog header image for a blog post titled "%s" about %s. The image should be visually appealing, modern, and relevant to the topic.',
                $title,
                $topic
            );
            
            WP_Blog_Agent_Logger::info('Auto-generating featured image', array(
                'post_id' => $post_id,
                'prompt' => substr($prompt, 0, 100)
            ));
            
            // Initialize image generator
            $image_generator = new WP_Blog_Agent_Image_Generator();
            
            // Set parameters for featured image
            $params = array(
                'aspectRatio' => '16:9', // Best for blog headers
                'imageSize' => '1K',
                'sampleCount' => 1,
                'outputMimeType' => 'image/jpeg',
                'personGeneration' => 'ALLOW_ALL'
            );
            
            // Generate and save image
            $attachment_id = $image_generator->generate_and_save($prompt, $post_id, $params);
            
            if (is_wp_error($attachment_id)) {
                WP_Blog_Agent_Logger::error('Failed to auto-generate featured image', array(
                    'post_id' => $post_id,
                    'error' => $attachment_id->get_error_message()
                ));
                return false;
            }
            
            // Store metadata
            update_post_meta($attachment_id, '_wp_blog_agent_generated_image', true);
            update_post_meta($attachment_id, '_wp_blog_agent_image_prompt', $prompt);
            update_post_meta($attachment_id, '_wp_blog_agent_attached_post', $post_id);
            update_post_meta($attachment_id, '_wp_blog_agent_auto_generated', true);
            
            WP_Blog_Agent_Logger::success('Featured image auto-generated successfully', array(
                'post_id' => $post_id,
                'attachment_id' => $attachment_id
            ));
            
            return $attachment_id;
        } catch (Exception $e) {
            WP_Blog_Agent_Logger::error('Exception during auto-image generation', array(
                'post_id' => $post_id,
                'error' => $e->getMessage()
            ));
            return false;
        }
    }
    
    /**
     * Auto-generate RankMath SEO meta for a post
     */
    private function auto_generate_rankmath_seo($post_id) {
        try {
            WP_Blog_Agent_Logger::info('Auto-generating RankMath SEO meta', array(
                'post_id' => $post_id
            ));
            
            $rankmath = new WP_Blog_Agent_RankMath();
            
            // Generate description
            $description = $rankmath->generate_seo_description($post_id);
            if (is_wp_error($description)) {
                WP_Blog_Agent_Logger::error('Failed to auto-generate SEO description', array(
                    'post_id' => $post_id,
                    'error' => $description->get_error_message()
                ));
                return false;
            }
            
            // Generate keyword
            $keyword = $rankmath->generate_focus_keyword($post_id);
            if (is_wp_error($keyword)) {
                WP_Blog_Agent_Logger::error('Failed to auto-generate focus keyword', array(
                    'post_id' => $post_id,
                    'error' => $keyword->get_error_message()
                ));
                return false;
            }
            
            WP_Blog_Agent_Logger::success('RankMath SEO meta generated', array(
                'post_id' => $post_id,
                'description' => $description,
                'keyword' => $keyword
            ));
            
            return true;
        } catch (Exception $e) {
            WP_Blog_Agent_Logger::error('Exception during RankMath SEO generation', array(
                'post_id' => $post_id,
                'error' => $e->getMessage()
            ));
            return false;
        }
    }
    
    /**
     * AJAX handler for generating RankMath SEO meta
     */
    public function ajax_generate_seo() {
        check_ajax_referer('wp_blog_agent_seo_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
            return;
        }
        
        $post_id = intval($_POST['post_id']);
        
        if (!$post_id) {
            wp_send_json_error(array('message' => 'Invalid post ID'));
            return;
        }
        
        WP_Blog_Agent_Logger::info('Manual SEO generation triggered', array('post_id' => $post_id));
        
        try {
            $rankmath = new WP_Blog_Agent_RankMath();
            
            // Generate description
            $description = $rankmath->generate_seo_description($post_id);
            if (is_wp_error($description)) {
                wp_send_json_error(array('message' => 'Failed to generate description: ' . $description->get_error_message()));
                return;
            }
            
            // Generate keyword
            $keyword = $rankmath->generate_focus_keyword($post_id);
            if (is_wp_error($keyword)) {
                wp_send_json_error(array('message' => 'Failed to generate keyword: ' . $keyword->get_error_message()));
                return;
            }
            
            wp_send_json_success(array(
                'message' => 'SEO meta generated successfully!',
                'description' => $description,
                'keyword' => $keyword
            ));
        } catch (Exception $e) {
            WP_Blog_Agent_Logger::error('Exception during SEO generation', array(
                'post_id' => $post_id,
                'error' => $e->getMessage()
            ));
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
    
    /**
     * AJAX handler for generating post image
     */
    public function ajax_generate_post_image() {
        check_ajax_referer('wp_blog_agent_image_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
            return;
        }
        
        $post_id = intval($_POST['post_id']);
        
        if (!$post_id) {
            wp_send_json_error(array('message' => 'Invalid post ID'));
            return;
        }
        
        $post = get_post($post_id);
        
        if (!$post) {
            wp_send_json_error(array('message' => 'Post not found'));
            return;
        }
        
        WP_Blog_Agent_Logger::info('Manual image generation triggered', array('post_id' => $post_id));
        
        // Create image prompt based on post title
        $prompt = sprintf(
            'Create a professional, eye-catching blog header image for a blog post titled "%s". The image should be visually appealing, modern, and relevant to the topic.',
            $post->post_title
        );
        
        try {
            $image_generator = new WP_Blog_Agent_Image_Generator();
            
            $params = array(
                'aspectRatio' => '16:9',
                'imageSize' => '1K',
                'sampleCount' => 1,
                'outputMimeType' => 'image/jpeg',
                'personGeneration' => 'ALLOW_ALL'
            );
            
            $attachment_id = $image_generator->generate_and_save($prompt, $post_id, $params);
            
            if (is_wp_error($attachment_id)) {
                WP_Blog_Agent_Logger::error('Image generation failed', array(
                    'post_id' => $post_id,
                    'error' => $attachment_id->get_error_message()
                ));
                wp_send_json_error(array('message' => $attachment_id->get_error_message()));
                return;
            }
            
            // Store metadata
            update_post_meta($attachment_id, '_wp_blog_agent_generated_image', true);
            update_post_meta($attachment_id, '_wp_blog_agent_image_prompt', $prompt);
            update_post_meta($attachment_id, '_wp_blog_agent_attached_post', $post_id);
            
            $image_url = wp_get_attachment_url($attachment_id);
            
            WP_Blog_Agent_Logger::success('Image generated successfully', array(
                'post_id' => $post_id,
                'attachment_id' => $attachment_id
            ));
            
            wp_send_json_success(array(
                'message' => 'Featured image generated successfully!',
                'attachment_id' => $attachment_id,
                'image_url' => $image_url
            ));
        } catch (Exception $e) {
            WP_Blog_Agent_Logger::error('Exception during image generation', array(
                'post_id' => $post_id,
                'error' => $e->getMessage()
            ));
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
    
    /**
     * Render series page
     */
    public function render_series_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        include WP_BLOG_AGENT_PLUGIN_DIR . 'admin/series-page.php';
    }
    
    /**
     * Handle create series
     */
    public function handle_create_series() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to perform this action.'));
        }
        
        check_admin_referer('wp_blog_agent_create_series');
        
        $name = isset($_POST['series_name']) ? sanitize_text_field($_POST['series_name']) : '';
        $description = isset($_POST['series_description']) ? sanitize_textarea_field($_POST['series_description']) : '';
        
        if (empty($name)) {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&error=empty_name'));
            exit;
        }
        
        $series_id = WP_Blog_Agent_Series::create_series($name, $description);
        
        if ($series_id) {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&created=' . $series_id));
        } else {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&error=creation_failed'));
        }
        exit;
    }
    
    /**
     * Handle delete series
     */
    public function handle_delete_series() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to perform this action.'));
        }
        
        check_admin_referer('wp_blog_agent_delete_series');
        
        $series_id = isset($_GET['series_id']) ? intval($_GET['series_id']) : 0;
        
        if ($series_id > 0) {
            WP_Blog_Agent_Series::delete_series($series_id);
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&deleted=1'));
        } else {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&error=invalid_id'));
        }
        exit;
    }
    
    /**
     * Handle add post to series
     */
    public function handle_add_post_to_series() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to perform this action.'));
        }
        
        check_admin_referer('wp_blog_agent_add_post_to_series');
        
        $series_id = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        
        if ($series_id > 0 && $post_id > 0) {
            WP_Blog_Agent_Series::add_post_to_series($series_id, $post_id);
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&added_post=1'));
        } else {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&error=invalid_data'));
        }
        exit;
    }
    
    /**
     * Handle remove post from series
     */
    public function handle_remove_post_from_series() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to perform this action.'));
        }
        
        check_admin_referer('wp_blog_agent_remove_post_from_series');
        
        $series_id = isset($_GET['series_id']) ? intval($_GET['series_id']) : 0;
        $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
        
        if ($series_id > 0 && $post_id > 0) {
            WP_Blog_Agent_Series::remove_post_from_series($series_id, $post_id);
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&removed_post=1'));
        } else {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&error=invalid_data'));
        }
        exit;
    }
    
    /**
     * Handle generate from suggestion
     */
    public function handle_generate_from_suggestion() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to perform this action.'));
        }
        
        check_admin_referer('wp_blog_agent_generate_from_suggestion');
        
        $series_id = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
        $topics = isset($_POST['topics']) ? $_POST['topics'] : array();
        
        // Support both single topic (backward compatibility) and multiple topics
        if (empty($topics) && !empty($_POST['topic'])) {
            $topics = array(sanitize_text_field($_POST['topic']));
        } elseif (is_array($topics)) {
            $topics = array_map('sanitize_text_field', $topics);
        }
        
        if (empty($topics)) {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&error=empty_topic'));
            exit;
        }
        
        WP_Blog_Agent_Logger::info('Enqueueing series posts for generation', array(
            'series_id' => $series_id,
            'topics_count' => count($topics)
        ));
        
        $queued_count = 0;
        $failed_count = 0;
        
        // Enqueue each topic as a separate task
        foreach ($topics as $topic) {
            if (empty($topic)) {
                continue;
            }
            
            $queue_id = WP_Blog_Agent_Queue::enqueue(
                null, // topic_id is null for series generation
                'series',
                array(
                    'topic_text' => $topic,
                    'series_id' => $series_id
                )
            );
            
            if ($queue_id) {
                $queued_count++;
                WP_Blog_Agent_Logger::info('Series post enqueued', array(
                    'queue_id' => $queue_id,
                    'topic' => $topic,
                    'series_id' => $series_id
                ));
            } else {
                $failed_count++;
                WP_Blog_Agent_Logger::error('Failed to enqueue series post', array(
                    'topic' => $topic,
                    'series_id' => $series_id
                ));
            }
        }
        
        // Auto-generate RankMath SEO meta if enabled
        $auto_generate_seo = get_option('wp_blog_agent_auto_generate_seo', 'no');
        if ($auto_generate_seo === 'yes' && !empty($post_id)) {
            $this->auto_generate_rankmath_seo($post_id);
        }

        if ($queued_count > 0) {
            $message = sprintf(
                _n(
                    '%d post has been added to the generation queue.',
                    '%d posts have been added to the generation queue.',
                    $queued_count,
                    'wp-blog-agent'
                ),
                $queued_count
            );

            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&queued=' . $queued_count));
        } else {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&error=queue_failed'));
        }

        exit;
    }
    
    /**
     * Handle rewrite post
     */
    public function handle_rewrite_post() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to perform this action.'));
        }
        
        check_admin_referer('wp_blog_agent_rewrite_post');
        
        $series_id = isset($_GET['series_id']) ? intval($_GET['series_id']) : 0;
        $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
        
        if ($post_id <= 0) {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&error=invalid_data'));
            exit;
        }
        
        // Get the post to rewrite
        $post = get_post($post_id);
        if (!$post) {
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&error=post_not_found'));
            exit;
        }
        
        WP_Blog_Agent_Logger::info('Rewrite post queued', array(
            'post_id' => $post_id,
            'series_id' => $series_id,
            'post_title' => $post->post_title
        ));
        
        // Enqueue rewrite task
        $queue_id = WP_Blog_Agent_Queue::enqueue(
            null, // topic_id is null for rewrite
            'rewrite',
            array(
                'post_id' => $post_id,
                'series_id' => $series_id,
                'topic_text' => $post->post_title // Use existing title as topic
            )
        );
        
        if ($queue_id) {
            WP_Blog_Agent_Logger::info('Post rewrite enqueued successfully', array(
                'queue_id' => $queue_id,
                'post_id' => $post_id
            ));
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&rewrite_queued=' . $post_id));
        } else {
            WP_Blog_Agent_Logger::error('Failed to enqueue post rewrite', array(
                'post_id' => $post_id,
                'series_id' => $series_id
            ));
            wp_redirect(admin_url('admin.php?page=wp-blog-agent-series&view=' . $series_id . '&error=rewrite_failed'));
        }
        
        exit;
    }
    
    /**
     * AJAX handler for getting topic suggestions
     */
    public function ajax_get_suggestions() {
        check_ajax_referer('wp_blog_agent_suggestions', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
            return;
        }
        
        $series_id = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;
        
        if ($series_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid series ID'));
            return;
        }
        
        $suggestions = WP_Blog_Agent_Series::generate_topic_suggestions($series_id);
        
        if (is_wp_error($suggestions)) {
            wp_send_json_error(array('message' => $suggestions->get_error_message()));
            return;
        }
        
        wp_send_json_success(array('suggestions' => $suggestions));
    }
}

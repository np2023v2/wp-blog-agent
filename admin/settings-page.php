<div class="wrap">
    <h1><?php echo esc_html__('WP Blog Agent - Settings', 'wp-blog-agent'); ?></h1>
    
    <form method="post" action="" name="wp_blog_agent_settings">
        <?php wp_nonce_field('wp_blog_agent_settings', 'wp_blog_agent_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="ai_provider"><?php echo esc_html__('AI Provider', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <select name="ai_provider" id="ai_provider" class="regular-text">
                        <option value="openai" <?php selected(get_option('wp_blog_agent_ai_provider', 'openai'), 'openai'); ?>>OpenAI (GPT)</option>
                        <option value="gemini" <?php selected(get_option('wp_blog_agent_ai_provider'), 'gemini'); ?>>Google Gemini</option>
                        <option value="ollama" <?php selected(get_option('wp_blog_agent_ai_provider'), 'ollama'); ?>>Ollama (Local)</option>
                    </select>
                    <p class="description"><?php echo esc_html__('Choose which AI service to use for generating content.', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="openai_api_key"><?php echo esc_html__('OpenAI API Key', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="password" name="openai_api_key" id="openai_api_key" value="<?php echo esc_attr(get_option('wp_blog_agent_openai_api_key', '')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Enter your OpenAI API key. Get one at https://platform.openai.com/api-keys', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="openai_base_url"><?php echo esc_html__('OpenAI Base URL', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="text" name="openai_base_url" id="openai_base_url" value="<?php echo esc_attr(get_option('wp_blog_agent_openai_base_url', 'https://api.openai.com/v1/chat/completions')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Custom OpenAI API base URL. Default: https://api.openai.com/v1/chat/completions (useful for OpenAI-compatible APIs)', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="openai_model"><?php echo esc_html__('OpenAI Model', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="text" name="openai_model" id="openai_model" value="<?php echo esc_attr(get_option('wp_blog_agent_openai_model', 'gpt-3.5-turbo')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Model to use (e.g., gpt-3.5-turbo, gpt-4, gpt-4-turbo). Default: gpt-3.5-turbo', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="openai_max_tokens"><?php echo esc_html__('OpenAI Max Output Tokens', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="number" name="openai_max_tokens" id="openai_max_tokens" value="<?php echo esc_attr(get_option('wp_blog_agent_openai_max_tokens', '')); ?>" class="regular-text" min="1" placeholder="Unlimited" />
                    <p class="description"><?php echo esc_html__('Maximum number of tokens to generate. Leave empty for unlimited (model default). Default: unlimited', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="openai_system_prompt"><?php echo esc_html__('OpenAI System Prompt', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <textarea name="openai_system_prompt" id="openai_system_prompt" class="large-text" rows="3"><?php echo esc_textarea(get_option('wp_blog_agent_openai_system_prompt', 'You are a professional blog writer who creates SEO-optimized, engaging content.')); ?></textarea>
                    <p class="description"><?php echo esc_html__('Custom system prompt for the AI. Default: "You are a professional blog writer who creates SEO-optimized, engaging content."', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="gemini_api_key"><?php echo esc_html__('Gemini API Key', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="password" name="gemini_api_key" id="gemini_api_key" value="<?php echo esc_attr(get_option('wp_blog_agent_gemini_api_key', '')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Enter your Google Gemini API key. Get one at https://makersuite.google.com/app/apikey', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="gemini_model"><?php echo esc_html__('Gemini Model', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="text" name="gemini_model" id="gemini_model" value="<?php echo esc_attr(get_option('wp_blog_agent_gemini_model', 'gemini-pro')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Model to use (e.g., gemini-pro, gemini-pro-vision). Default: gemini-pro', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="gemini_max_tokens"><?php echo esc_html__('Gemini Max Output Tokens', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="number" name="gemini_max_tokens" id="gemini_max_tokens" value="<?php echo esc_attr(get_option('wp_blog_agent_gemini_max_tokens', '')); ?>" class="regular-text" min="1" placeholder="Unlimited" />
                    <p class="description"><?php echo esc_html__('Maximum number of tokens to generate. Leave empty for unlimited (model default). Default: unlimited', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="gemini_system_prompt"><?php echo esc_html__('Gemini System Prompt', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <textarea name="gemini_system_prompt" id="gemini_system_prompt" class="large-text" rows="3"><?php echo esc_textarea(get_option('wp_blog_agent_gemini_system_prompt', 'You are a professional blog writer who creates SEO-optimized, engaging content.')); ?></textarea>
                    <p class="description"><?php echo esc_html__('Custom system prompt for the AI. This will be prepended to the user prompt. Default: "You are a professional blog writer who creates SEO-optimized, engaging content."', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="gemini_image_api_key"><?php echo esc_html__('Gemini Image API Key', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="password" name="gemini_image_api_key" id="gemini_image_api_key" value="<?php echo esc_attr(get_option('wp_blog_agent_gemini_image_api_key', '')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Enter your Google Gemini API key for image generation (Imagen API). Can be the same as or different from the content generation API key.', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="ollama_base_url"><?php echo esc_html__('Ollama Base URL', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="text" name="ollama_base_url" id="ollama_base_url" value="<?php echo esc_attr(get_option('wp_blog_agent_ollama_base_url', 'http://localhost:11434/api/generate')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Ollama API endpoint. Default: http://localhost:11434/api/generate', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="ollama_model"><?php echo esc_html__('Ollama Model', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <input type="text" name="ollama_model" id="ollama_model" value="<?php echo esc_attr(get_option('wp_blog_agent_ollama_model', 'llama2')); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html__('Ollama model to use (e.g., llama2, mistral, codellama). Default: llama2', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="ollama_system_prompt"><?php echo esc_html__('Ollama System Prompt', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <textarea name="ollama_system_prompt" id="ollama_system_prompt" class="large-text" rows="3"><?php echo esc_textarea(get_option('wp_blog_agent_ollama_system_prompt', 'You are a professional blog writer who creates SEO-optimized, engaging content.')); ?></textarea>
                    <p class="description"><?php echo esc_html__('Custom system prompt for the AI. This will be prepended to the user prompt. Default: "You are a professional blog writer who creates SEO-optimized, engaging content."', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="schedule_enabled"><?php echo esc_html__('Enable Scheduling', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <select name="schedule_enabled" id="schedule_enabled" class="regular-text">
                        <option value="yes" <?php selected(get_option('wp_blog_agent_schedule_enabled', 'no'), 'yes'); ?>><?php echo esc_html__('Yes', 'wp-blog-agent'); ?></option>
                        <option value="no" <?php selected(get_option('wp_blog_agent_schedule_enabled', 'no'), 'no'); ?>><?php echo esc_html__('No', 'wp-blog-agent'); ?></option>
                    </select>
                    <p class="description"><?php echo esc_html__('Enable automatic post generation on schedule.', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="schedule_frequency"><?php echo esc_html__('Schedule Frequency', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <select name="schedule_frequency" id="schedule_frequency" class="regular-text">
                        <option value="hourly" <?php selected(get_option('wp_blog_agent_schedule_frequency', 'daily'), 'hourly'); ?>><?php echo esc_html__('Hourly', 'wp-blog-agent'); ?></option>
                        <option value="twicedaily" <?php selected(get_option('wp_blog_agent_schedule_frequency', 'daily'), 'twicedaily'); ?>><?php echo esc_html__('Twice Daily', 'wp-blog-agent'); ?></option>
                        <option value="daily" <?php selected(get_option('wp_blog_agent_schedule_frequency', 'daily'), 'daily'); ?>><?php echo esc_html__('Daily', 'wp-blog-agent'); ?></option>
                        <option value="weekly" <?php selected(get_option('wp_blog_agent_schedule_frequency', 'daily'), 'weekly'); ?>><?php echo esc_html__('Weekly', 'wp-blog-agent'); ?></option>
                    </select>
                    <p class="description"><?php echo esc_html__('How often to generate new posts.', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="auto_publish"><?php echo esc_html__('Auto Publish', 'wp-blog-agent'); ?></label>
                </th>
                <td>
                    <select name="auto_publish" id="auto_publish" class="regular-text">
                        <option value="yes" <?php selected(get_option('wp_blog_agent_auto_publish', 'yes'), 'yes'); ?>><?php echo esc_html__('Yes', 'wp-blog-agent'); ?></option>
                        <option value="no" <?php selected(get_option('wp_blog_agent_auto_publish', 'yes'), 'no'); ?>><?php echo esc_html__('No (Save as Draft)', 'wp-blog-agent'); ?></option>
                    </select>
                    <p class="description"><?php echo esc_html__('Automatically publish generated posts or save as drafts for review.', 'wp-blog-agent'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Save Settings', 'wp-blog-agent')); ?>
    </form>
    
    <hr>
    
    <h2><?php echo esc_html__('Quick Actions', 'wp-blog-agent'); ?></h2>
    <p>
        <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=wp_blog_agent_generate_now'), 'wp_blog_agent_generate_now'); ?>" class="button button-primary">
            <?php echo esc_html__('Generate Post Now', 'wp-blog-agent'); ?>
        </a>
    </p>
    
    <hr>
    
    <h2><?php echo esc_html__('Next Scheduled Generation', 'wp-blog-agent'); ?></h2>
    <?php
    $timestamp = wp_next_scheduled('wp_blog_agent_generate_post');
    if ($timestamp) {
        echo '<p>' . sprintf(
            esc_html__('Next generation: %s', 'wp-blog-agent'),
            '<strong>' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp) . '</strong>'
        ) . '</p>';
    } else {
        echo '<p>' . esc_html__('No scheduled generation.', 'wp-blog-agent') . '</p>';
    }
    ?>
</div>

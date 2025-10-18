<?php
/**
 * RankMath SEO Integration
 * Automatically generate SEO metadata for posts using AI
 */
class WP_Blog_Agent_RankMath {
    
    /**
     * Generate SEO description/snippet for a post
     */
    public function generate_seo_description($post_id) {
        $post = get_post($post_id);
        
        if (!$post) {
            return new WP_Error('invalid_post', 'Invalid post ID.');
        }
        
        // Get AI provider
        $provider = get_option('wp_blog_agent_ai_provider', 'openai');
        
        // Initialize AI client
        if ($provider === 'gemini') {
            $ai = new WP_Blog_Agent_Gemini();
        } elseif ($provider === 'ollama') {
            $ai = new WP_Blog_Agent_Ollama();
        } else {
            $ai = new WP_Blog_Agent_OpenAI();
        }
        
        // Build prompt for SEO description
        $prompt = $this->build_seo_description_prompt($post->post_title, $post->post_content);
        
        // Generate using the AI's generate_content method but without keywords/hashtags
        $full_prompt = "Task: Generate an SEO meta description (snippet) for the following blog post.\n\n" . $prompt;
        $full_prompt .= "\n\nRequirements:\n";
        $full_prompt .= "1. Maximum 155-160 characters\n";
        $full_prompt .= "2. Include primary keywords naturally\n";
        $full_prompt .= "3. Compelling and click-worthy\n";
        $full_prompt .= "4. Accurately describes the content\n";
        $full_prompt .= "5. NO quotation marks, just return the plain description text\n";
        $full_prompt .= "\nReturn ONLY the meta description text, nothing else.";
        
        // We'll use a simplified method call
        $description = $this->generate_with_ai($ai, $full_prompt, $provider);
        
        if (is_wp_error($description)) {
            WP_Blog_Agent_Logger::error('SEO description generation failed', array(
                'error' => $description->get_error_message(),
                'post_id' => $post_id
            ));
            return $description;
        }
        
        // Clean up the description
        $description = trim($description);
        $description = str_replace(array('"', "'", "\n", "\r"), '', $description);
        
        // Truncate if too long
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }
        
        // Update RankMath meta
        update_post_meta($post_id, 'rank_math_description', $description);
        
        WP_Blog_Agent_Logger::success('SEO description generated', array(
            'post_id' => $post_id,
            'description' => $description
        ));
        
        return $description;
    }
    
    /**
     * Generate focus keyword for a post
     */
    public function generate_focus_keyword($post_id) {
        $post = get_post($post_id);
        
        if (!$post) {
            return new WP_Error('invalid_post', 'Invalid post ID.');
        }
        
        // Get AI provider
        $provider = get_option('wp_blog_agent_ai_provider', 'openai');
        
        // Initialize AI client
        if ($provider === 'gemini') {
            $ai = new WP_Blog_Agent_Gemini();
        } elseif ($provider === 'ollama') {
            $ai = new WP_Blog_Agent_Ollama();
        } else {
            $ai = new WP_Blog_Agent_OpenAI();
        }
        
        // Build prompt for focus keyword
        $prompt = $this->build_focus_keyword_prompt($post->post_title, $post->post_content);
        
        $full_prompt = "Task: Identify the primary focus keyword/phrase for the following blog post.\n\n" . $prompt;
        $full_prompt .= "\n\nRequirements:\n";
        $full_prompt .= "1. Should be 1-4 words maximum\n";
        $full_prompt .= "2. Most relevant to the post content\n";
        $full_prompt .= "3. Good for SEO targeting\n";
        $full_prompt .= "4. High search intent\n";
        $full_prompt .= "5. NO quotation marks or extra text\n";
        $full_prompt .= "\nReturn ONLY the keyword/phrase, nothing else.";
        
        $keyword = $this->generate_with_ai($ai, $full_prompt, $provider);
        
        if (is_wp_error($keyword)) {
            WP_Blog_Agent_Logger::error('Focus keyword generation failed', array(
                'error' => $keyword->get_error_message(),
                'post_id' => $post_id
            ));
            return $keyword;
        }
        
        // Clean up the keyword
        $keyword = trim($keyword);
        $keyword = str_replace(array('"', "'", "\n", "\r"), '', $keyword);
        $keyword = strtolower($keyword);
        
        // Update RankMath meta
        update_post_meta($post_id, 'rank_math_focus_keyword', $keyword);
        
        WP_Blog_Agent_Logger::success('Focus keyword generated', array(
            'post_id' => $post_id,
            'keyword' => $keyword
        ));
        
        return $keyword;
    }
    
    /**
     * Generate both SEO description and focus keyword
     */
    public function generate_all_seo_meta($post_id) {
        $results = array();
        
        $description = $this->generate_seo_description($post_id);
        $results['description'] = is_wp_error($description) ? $description->get_error_message() : $description;
        
        $keyword = $this->generate_focus_keyword($post_id);
        $results['keyword'] = is_wp_error($keyword) ? $keyword->get_error_message() : $keyword;
        
        return $results;
    }
    
    /**
     * Build prompt for SEO description generation
     */
    private function build_seo_description_prompt($title, $content) {
        $content_excerpt = strip_tags($content);
        $content_excerpt = substr($content_excerpt, 0, 500); // Limit to first 500 chars
        
        return "Blog Post Title: {$title}\n\nContent Preview: {$content_excerpt}";
    }
    
    /**
     * Build prompt for focus keyword generation
     */
    private function build_focus_keyword_prompt($title, $content) {
        $content_excerpt = strip_tags($content);
        $content_excerpt = substr($content_excerpt, 0, 500); // Limit to first 500 chars
        
        return "Blog Post Title: {$title}\n\nContent Preview: {$content_excerpt}";
    }
    
    /**
     * Generate content using AI provider
     */
    private function generate_with_ai($ai, $prompt, $provider) {
        if ($provider === 'gemini') {
            return $this->generate_with_gemini($ai, $prompt);
        } elseif ($provider === 'ollama') {
            return $this->generate_with_ollama($ai, $prompt);
        } else {
            return $this->generate_with_openai($ai, $prompt);
        }
    }
    
    /**
     * Generate using OpenAI
     */
    private function generate_with_openai($ai, $prompt) {
        try {
            $reflection = new ReflectionClass($ai);
            $api_key_prop = $reflection->getProperty('api_key');
            $api_key_prop->setAccessible(true);
            $api_key = $api_key_prop->getValue($ai);
            
            if (empty($api_key)) {
                return new WP_Error('no_api_key', 'OpenAI API key is not configured.');
            }
            
            $api_url_prop = $reflection->getProperty('api_url');
            $api_url_prop->setAccessible(true);
            $api_url = $api_url_prop->getValue($ai);
            
            $model_prop = $reflection->getProperty('model');
            $model_prop->setAccessible(true);
            $model = $model_prop->getValue($ai);
            
            $request_body = array(
                'model' => $model,
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'temperature' => 0.7,
                'max_tokens' => 100
            );
            
            $response = wp_remote_post($api_url, array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ),
                'body' => json_encode($request_body),
                'timeout' => 30,
            ));
            
            if (is_wp_error($response)) {
                return $response;
            }
            
            $response_code = wp_remote_retrieve_response_code($response);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            // Check for API errors
            if ($response_code !== 200) {
                $error_message = 'OpenAI API error';
                if (isset($body['error']['message'])) {
                    $error_message = $body['error']['message'];
                } elseif (isset($body['error'])) {
                    $error_message = is_string($body['error']) ? $body['error'] : json_encode($body['error']);
                }
                return new WP_Error('api_error', $error_message);
            }
            
            // Validate response structure
            if (!is_array($body)) {
                WP_Blog_Agent_Logger::error('OpenAI SEO Invalid Response Format', array('body' => $body));
                return new WP_Error('invalid_response', 'Invalid response format from OpenAI API: Response is not an array.');
            }
            
            if (!isset($body['choices']) || !is_array($body['choices']) || empty($body['choices'])) {
                WP_Blog_Agent_Logger::error('OpenAI SEO Missing Choices', array('body' => $body));
                return new WP_Error('invalid_response', 'Invalid response from OpenAI API: No choices returned.');
            }
            
            if (!isset($body['choices'][0]['message']['content'])) {
                WP_Blog_Agent_Logger::error('OpenAI SEO Missing Content', array('choice' => isset($body['choices'][0]) ? $body['choices'][0] : 'N/A'));
                return new WP_Error('invalid_response', 'Invalid response from OpenAI API: No content in message.');
            }
            
            $content = $body['choices'][0]['message']['content'];
            
            if (empty($content)) {
                WP_Blog_Agent_Logger::error('OpenAI SEO Empty Content', array('choice' => $body['choices'][0]));
                return new WP_Error('invalid_response', 'Invalid response from OpenAI API: Content is empty.');
            }
            
            return $content;
        } catch (Exception $e) {
            return new WP_Error('exception', 'Exception in OpenAI generation: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate using Gemini
     */
    private function generate_with_gemini($ai, $prompt) {
        try {
            $reflection = new ReflectionClass($ai);
            $api_key_prop = $reflection->getProperty('api_key');
            $api_key_prop->setAccessible(true);
            $api_key = $api_key_prop->getValue($ai);
            
            if (empty($api_key)) {
                return new WP_Error('no_api_key', 'Gemini API key is not configured.');
            }
            
            $model_prop = $reflection->getProperty('model');
            $model_prop->setAccessible(true);
            $model = $model_prop->getValue($ai);
            
            $api_url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$api_key}";
            
            $request_body = array(
                'contents' => array(
                    array(
                        'parts' => array(
                            array('text' => $prompt)
                        )
                    )
                ),
                'generationConfig' => array(
                    'temperature' => 0.7,
                    'maxOutputTokens' => 100
                )
            );
            
            $response = wp_remote_post($api_url, array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode($request_body),
                'timeout' => 30,
            ));
            
            if (is_wp_error($response)) {
                return $response;
            }
            
            $response_code = wp_remote_retrieve_response_code($response);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            // Check for API errors
            if ($response_code !== 200) {
                $error_message = 'Gemini API error';
                if (isset($body['error']['message'])) {
                    $error_message = $body['error']['message'];
                } elseif (isset($body['error'])) {
                    $error_message = is_string($body['error']) ? $body['error'] : json_encode($body['error']);
                }
                return new WP_Error('api_error', $error_message);
            }
            
            // Validate response structure
            if (!is_array($body)) {
                WP_Blog_Agent_Logger::error('Gemini SEO Invalid Response Format', array('body' => $body));
                return new WP_Error('invalid_response', 'Invalid response format from Gemini API: Response is not an array.');
            }
            
            if (!isset($body['candidates']) || !is_array($body['candidates']) || empty($body['candidates'])) {
                WP_Blog_Agent_Logger::error('Gemini SEO Missing Candidates', array('body' => $body));
                return new WP_Error('invalid_response', 'Invalid response from Gemini API: No candidates returned.');
            }
            
            if (!isset($body['candidates'][0]['content']['parts'][0]['text'])) {
                WP_Blog_Agent_Logger::error('Gemini SEO Missing Text', array('candidate' => isset($body['candidates'][0]) ? $body['candidates'][0] : 'N/A'));
                return new WP_Error('invalid_response', 'Invalid response from Gemini API: No text in candidate.');
            }
            
            $content = $body['candidates'][0]['content']['parts'][0]['text'];
            
            if (empty($content)) {
                WP_Blog_Agent_Logger::error('Gemini SEO Empty Content', array('candidate' => $body['candidates'][0]));
                return new WP_Error('invalid_response', 'Invalid response from Gemini API: Content is empty.');
            }
            
            return $content;
        } catch (Exception $e) {
            return new WP_Error('exception', 'Exception in Gemini generation: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate using Ollama
     */
    private function generate_with_ollama($ai, $prompt) {
        try {
            $reflection = new ReflectionClass($ai);
            $api_url_prop = $reflection->getProperty('api_url');
            $api_url_prop->setAccessible(true);
            $api_url = $api_url_prop->getValue($ai);
            
            $model_prop = $reflection->getProperty('model');
            $model_prop->setAccessible(true);
            $model = $model_prop->getValue($ai);
            
            $request_body = array(
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => array(
                    'temperature' => 0.7,
                    'num_predict' => 100
                )
            );
            
            $response = wp_remote_post($api_url, array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode($request_body),
                'timeout' => 30,
            ));
            
            if (is_wp_error($response)) {
                return $response;
            }
            
            $response_code = wp_remote_retrieve_response_code($response);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            // Check for API errors
            if ($response_code !== 200) {
                $error_message = 'Ollama API error';
                if (isset($body['error'])) {
                    $error_message = is_string($body['error']) ? $body['error'] : json_encode($body['error']);
                }
                return new WP_Error('api_error', $error_message);
            }
            
            // Validate response structure
            if (!is_array($body)) {
                WP_Blog_Agent_Logger::error('Ollama SEO Invalid Response Format', array('body' => $body));
                return new WP_Error('invalid_response', 'Invalid response format from Ollama API: Response is not an array.');
            }
            
            if (!isset($body['response'])) {
                WP_Blog_Agent_Logger::error('Ollama SEO Missing Response', array('body' => $body));
                return new WP_Error('invalid_response', 'Invalid response from Ollama API: No response field.');
            }
            
            $content = $body['response'];
            
            if (empty($content)) {
                WP_Blog_Agent_Logger::error('Ollama SEO Empty Content', array('body' => $body));
                return new WP_Error('invalid_response', 'Invalid response from Ollama API: Content is empty.');
            }
            
            return $content;
        } catch (Exception $e) {
            return new WP_Error('exception', 'Exception in Ollama generation: ' . $e->getMessage());
        }
    }
}

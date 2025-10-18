/* WP Blog Agent Admin JavaScript */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Toggle API key fields based on selected provider
        $('#ai_provider').on('change', function() {
            const provider = $(this).val();
            
            if (provider === 'openai') {
                $('#openai_api_key').closest('tr').show();
                $('#openai_base_url').closest('tr').show();
                $('#openai_model').closest('tr').show();
                $('#openai_max_tokens').closest('tr').show();
                $('#openai_system_prompt').closest('tr').show();
                $('#gemini_api_key').closest('tr').hide();
                $('#gemini_model').closest('tr').hide();
                $('#gemini_max_tokens').closest('tr').hide();
                $('#gemini_system_prompt').closest('tr').hide();
                $('#ollama_base_url').closest('tr').hide();
                $('#ollama_model').closest('tr').hide();
                $('#ollama_system_prompt').closest('tr').hide();
            } else if (provider === 'gemini') {
                $('#openai_api_key').closest('tr').hide();
                $('#openai_base_url').closest('tr').hide();
                $('#openai_model').closest('tr').hide();
                $('#openai_max_tokens').closest('tr').hide();
                $('#openai_system_prompt').closest('tr').hide();
                $('#gemini_api_key').closest('tr').show();
                $('#gemini_model').closest('tr').show();
                $('#gemini_max_tokens').closest('tr').show();
                $('#gemini_system_prompt').closest('tr').show();
                $('#ollama_base_url').closest('tr').hide();
                $('#ollama_model').closest('tr').hide();
                $('#ollama_system_prompt').closest('tr').hide();
            } else if (provider === 'ollama') {
                $('#openai_api_key').closest('tr').hide();
                $('#openai_base_url').closest('tr').hide();
                $('#openai_model').closest('tr').hide();
                $('#openai_max_tokens').closest('tr').hide();
                $('#openai_system_prompt').closest('tr').hide();
                $('#gemini_api_key').closest('tr').hide();
                $('#gemini_model').closest('tr').hide();
                $('#gemini_max_tokens').closest('tr').hide();
                $('#gemini_system_prompt').closest('tr').hide();
                $('#ollama_base_url').closest('tr').show();
                $('#ollama_model').closest('tr').show();
                $('#ollama_system_prompt').closest('tr').show();
            }
        }).trigger('change');
        
        // Toggle schedule frequency based on enabled status
        $('#schedule_enabled').on('change', function() {
            const enabled = $(this).val();
            
            if (enabled === 'yes') {
                $('#schedule_frequency').closest('tr').show();
            } else {
                $('#schedule_frequency').closest('tr').hide();
            }
        }).trigger('change');
        
        // Settings form validation - only apply to settings page
        $('form[name="wp_blog_agent_settings"]').on('submit', function(e) {
            const provider = $('#ai_provider').val();
            let apiKey = '';
            
            if (provider === 'openai') {
                apiKey = $('#openai_api_key').val();
            } else if (provider === 'gemini') {
                apiKey = $('#gemini_api_key').val();
            }
            // Ollama doesn't require an API key
            
            if (!apiKey && provider !== 'ollama' && $('#schedule_enabled').val() === 'yes') {
                e.preventDefault();
                alert('Please enter an API key for the selected AI provider before enabling scheduling.');
                return false;
            }
        });
        
        // Image generation form validation
        $('input[name="action"][value="wp_blog_agent_generate_image"]').closest('form').on('submit', function(e) {
            const prompt = $('#image_prompt').val().trim();
            
            if (!prompt) {
                e.preventDefault();
                alert('Image prompt is required. Please enter a description of the image you want to generate.');
                $('#image_prompt').focus();
                return false;
            }
        });
        
        // Auto-dismiss notices after 5 seconds
        setTimeout(function() {
            $('.notice.is-dismissible').fadeOut();
        }, 5000);
        
    });
    
})(jQuery);

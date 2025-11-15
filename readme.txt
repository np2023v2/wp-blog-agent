=== WP Blog Agent ===
Contributors: np2023v2
Tags: ai, content generation, openai, gemini, automation, blog, seo, scheduling
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.2
License: MIT
License URI: https://opensource.org/licenses/MIT

Automated blog post generation using OpenAI GPT or Google Gemini AI with hashtags, keywords, SEO optimization, and scheduled publishing.

== Description ==

WP Blog Agent is a powerful WordPress plugin that automates blog post generation using AI. Create SEO-optimized content with hashtags, keywords, scheduling, and auto-publishing capabilities.

= Key Features =

* **AI-Powered Content Generation**: Choose between OpenAI (GPT), Google Gemini, or Ollama (local) for content creation
* **Post Series Management**: Create series of related posts and get AI-powered topic suggestions for continuation
* **RankMath SEO Integration**: Automatically generate SEO meta descriptions and focus keywords
* **Task Queue System**: Asynchronous generation with automatic retry on failure (up to 3 attempts)
* **AI Image Generation**: Generate images using Gemini Imagen API and save to WordPress media library
* **Inline Image Generation**: Automatically add illustrative images throughout blog post content
* **Health Check Module**: Comprehensive system diagnostics for database, API, queue, and version
* **Custom OpenAI Base URL**: Configure custom API endpoints for OpenAI-compatible services
* **SEO Optimization**: Automatically include keywords with proper density for search engine optimization
* **Scheduled Publishing**: Automate post generation with flexible scheduling options (hourly, twice daily, daily, weekly)
* **Activity Logging**: Track all plugin activities with detailed logs
* **Security First**: Nonce verification, capability checks, and sanitized inputs

= AI Providers Supported =

* OpenAI GPT (GPT-3.5, GPT-4)
* Google Gemini AI
* Ollama (local self-hosted models)
* Any OpenAI-compatible API endpoint

= Perfect For =

* Content marketers who need consistent blog posts
* Bloggers who want to maintain a regular posting schedule
* SEO professionals looking to scale content creation
* Agencies managing multiple client blogs
* Anyone who wants to automate their content workflow

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/wp-blog-agent/`, or install through the WordPress plugins screen
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Navigate to **Blog Agent** → **Settings** to configure your AI API key
4. Add topics in **Blog Agent** → **Topics**
5. Configure scheduling preferences in **Blog Agent** → **Settings**

== Frequently Asked Questions ==

= What AI providers are supported? =

The plugin supports OpenAI (GPT-3.5, GPT-4), Google Gemini, Ollama (local), and any OpenAI-compatible API endpoint.

= Do I need an API key? =

Yes, you need an API key from either OpenAI or Google Gemini. You can get:
* OpenAI API key from https://platform.openai.com/api-keys
* Google Gemini API key from https://makersuite.google.com/app/apikey

= How does the scheduling work? =

The plugin uses WordPress Cron to schedule automatic post generation. You can choose from hourly, twice daily, daily, or weekly frequencies.

= Can I generate posts manually? =

Yes! You can generate posts manually at any time from the Topics page or use the Quick Generate feature for one-off posts.

= Does it support SEO plugins? =

Yes, the plugin has built-in integration with RankMath SEO, automatically generating meta descriptions and focus keywords.

= Can I review posts before publishing? =

Yes, you can disable auto-publish in settings, and all generated posts will be saved as drafts for your review.

= How does the image generation work? =

The plugin can automatically generate AI images using Google's Gemini Imagen API and set them as featured images or inline content images.

= Is there a queue system? =

Yes, all post generation tasks are processed through an asynchronous queue system with automatic retry logic for failed tasks.

== Screenshots ==

1. Settings page - Configure AI provider, API keys, and automation preferences
2. Topics management - Add and manage topics with keywords and hashtags
3. Queue monitoring - View task status and generation progress
4. Generated posts - Browse all AI-generated content
5. Series management - Create and manage related post series
6. Health check - Comprehensive system diagnostics
7. Image generation - AI-powered image creation for blog posts

== Changelog ==

= 1.0.2 =
* Added Custom OpenAI base URL configuration for OpenAI-compatible API services
* Added Quick manual topic generation without saving to database
* Added Ollama (local AI) support for self-hosted models
* Added Image Generation feature using Gemini Imagen API
* Added Task Queue System for asynchronous post generation
* Added Auto Generate Inline Images feature for blog posts
* Added Post Series Management feature
* Added AI-powered topic suggestions based on existing posts
* Improved error handling for all AI providers with detailed error messages
* Enhanced API request and response logging for better debugging
* Updated Gemini Imagen model to version 4.0 for improved quality

= 1.0.0 =
* Initial release
* OpenAI GPT integration
* Google Gemini integration
* Topic management system
* Automated scheduling
* SEO optimization with keywords
* Hashtag support
* Auto-publish functionality
* Activity logging system
* Input validation and security

== Upgrade Notice ==

= 1.0.2 =
Major update with queue system, image generation, series management, and improved error handling. Recommended for all users.

== Privacy Policy ==

This plugin sends content to third-party AI services (OpenAI, Google Gemini, or Ollama) for content generation. Please review their privacy policies:
* OpenAI: https://openai.com/policies/privacy-policy
* Google: https://policies.google.com/privacy

The plugin stores API keys securely in your WordPress database and does not transmit them to any service other than the respective AI providers.

== Support ==

For support, bug reports, or feature requests, please visit:
https://github.com/np2023v2/wp-blog-agent/issues

== Credits ==

Developed by NP2023

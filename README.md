# WP Blog Agent

[![Plugin Check](https://github.com/npsg02/wp-blog-agent/actions/workflows/plugin-check.yml/badge.svg)](https://github.com/npsg02/wp-blog-agent/actions/workflows/plugin-check.yml)
[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.0%2B-blue)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

A powerful WordPress plugin that automates blog post generation using OpenAI GPT or Google Gemini AI. Create SEO-optimized content with hashtags, keywords, scheduling, and auto-publishing capabilities.

## Features

- **AI-Powered Content Generation**: Choose between OpenAI (GPT), Google Gemini, or Ollama (local) for content creation
- **Post Series Management**: Create series of related posts and get AI-powered topic suggestions for continuation
- **RankMath SEO Integration**: Automatically generate SEO meta descriptions and focus keywords for RankMath SEO plugin
- **Task Queue System**: Asynchronous generation with automatic retry on failure (up to 3 attempts)
- **AI Image Generation**: Generate images using Gemini Imagen API and save to WordPress media library
- **Inline Image Generation**: Automatically add illustrative images throughout blog post content with AI-generated placeholders
- **Manual SEO & Image Generation**: Generate SEO metadata and featured images for existing posts with one click
- **Health Check Module**: Comprehensive system diagnostics for database, API, queue, version, and image generation
- **Custom OpenAI Base URL**: Configure custom API endpoints for OpenAI-compatible services (supports any OpenAI-compatible API)
- **Quick Manual Generation**: Generate posts immediately without saving topics to the database
- **Flexible Content Generation**: Generate posts from title only, or optionally include keywords and hashtags
- **SEO Optimization**: Automatically include keywords with proper density for search engine optimization (optional)
- **Hashtag Support**: Add relevant hashtags to boost social media visibility (optional)
- **Topic Management**: Create and manage multiple topics for varied content
- **Scheduled Publishing**: Automate post generation with flexible scheduling options (hourly, twice daily, daily, weekly)
- **Auto-Publish**: Automatically publish generated posts or save as drafts for review
- **Queue Monitoring**: View queue status, statistics, and task history
- **Enhanced API Logging**: Comprehensive logging of API requests and responses for debugging and monitoring
- **Robust Error Handling**: Better error messages and handling for invalid API responses
- **Activity Logging**: Track all plugin activities with detailed logs
- **Input Validation**: Comprehensive validation for all user inputs
- **Security First**: Nonce verification, capability checks, and sanitized inputs
- **User-Friendly Admin Interface**: Easy-to-use settings and management pages
- **Clean Uninstall**: Remove all plugin data when uninstalled

## Installation

1. Download or clone this repository to your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/np2023v2/wp-blog-agent.git
   ```

2. Activate the plugin through the WordPress admin panel:
   - Go to **Plugins** → **Installed Plugins**
   - Find "WP Blog Agent" and click **Activate**

3. Configure the plugin:
   - Go to **Blog Agent** → **Settings**
   - Enter your OpenAI or Gemini API key
   - Configure your preferences

## Configuration

### 1. Get API Keys

#### OpenAI API Key
- Visit [OpenAI Platform](https://platform.openai.com/api-keys)
- Sign up or log in to your account
- Create a new API key
- Copy the key for use in the plugin

#### Google Gemini API Key
- Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
- Sign up or log in with your Google account
- Create a new API key
- Copy the key for use in the plugin

### 2. Plugin Settings

Navigate to **Blog Agent** → **Settings** to configure:

- **AI Provider**: Choose between OpenAI (GPT) or Google Gemini
- **API Keys**: Enter your API key for the selected provider
- **OpenAI Base URL** (OpenAI only): Custom API endpoint URL for OpenAI-compatible services (default: https://api.openai.com/v1/chat/completions)
- **Enable Scheduling**: Turn on/off automated post generation
- **Schedule Frequency**: Choose how often to generate posts (hourly, twice daily, daily, weekly)
- **Auto Publish**: Decide whether to publish posts automatically or save as drafts
- **Auto Generate Featured Image**: Automatically create and set a featured image for each post using AI
- **Auto Generate RankMath SEO**: Automatically generate SEO metadata for RankMath plugin
- **Auto Generate Inline Images**: Enable AI to automatically add illustrative images throughout the blog post content

#### Inline Images Feature
When **Auto Generate Inline Images** is enabled:
- The AI will add 2-4 image placeholders in the generated content at strategic locations
- Each placeholder describes what the image should show
- After content generation, the plugin automatically generates AI images for each placeholder
- Placeholders are replaced with actual images embedded in the content
- Images are saved to your WordPress media library with proper metadata
- This creates visually rich, professional blog posts with relevant illustrations

### 3. Manage Topics

Navigate to **Blog Agent** → **Topics** to:

- **Quick Generate (Manual Topic)**: Generate a post immediately without saving the topic to the database
  - Enter topic (required)
  - Optionally add keywords for SEO optimization
  - Optionally add hashtags for social media visibility
  - Click "Generate Now" for instant content creation
  - Perfect for one-off posts or testing
- **Add Topics**: Create new topics with optional keywords and hashtags for scheduled generation
- **View Topics**: See all configured topics
- **Generate**: Manually generate a post for any specific topic
- **Delete**: Remove topics you no longer need

**Example Topic Configuration:**
- **Topic**: "WordPress SEO Best Practices" (required)
- **Keywords**: wordpress, SEO, search engine optimization, ranking, plugins (optional)
- **Hashtags**: #WordPress, #SEO, #WebDev, #BlogTips (optional)

### 4. Manage Post Series

Navigate to **Blog Agent** → **Series** to:

- **Create Series**: Create a new post series with a name and description
- **View Series**: See all your post series and their statistics
- **Add Posts to Series**: Add existing blog posts to a series
  - **NEW**: Add ANY post to a series (AI-generated or manually created)
  - Filter posts by type: All Posts, AI Generated, or Manual
  - Posts are organized by type for easy selection
- **AI Topic Suggestions**: Get AI-powered topic suggestions for the next post in a series
  - Click "Get AI Suggestions" on any series with at least one post
  - AI analyzes existing post titles in the series
  - Suggests relevant topics that follow the theme
  - **Select one or multiple suggestions** using checkboxes
  - Click "Generate Selected Topics" to add them to the generation queue
  - Posts are generated asynchronously in the background
  - The new posts are automatically added to the series
- **Rewrite Posts**: Regenerate content for any post in a series
  - Click "Rewrite" button on any post
  - AI generates fresh content while preserving the post ID
  - Confirmation dialog prevents accidental rewrites
  - Rewrite happens asynchronously in the queue
- **Manage Series Posts**: View, reorder, or remove posts from a series

**Example Workflow:**
1. Create a series called "WordPress Security Guide"
2. Add your first post about "WordPress Security Basics"
3. Click "Get AI Suggestions" - AI might suggest topics like:
   - "Advanced WordPress Security Plugins"
   - "Implementing Two-Factor Authentication in WordPress"
   - "WordPress Security Best Practices for 2024"
   - "Securing WordPress Database and Files"
   - "WordPress Backup and Recovery Strategies"
4. **Select multiple suggestions** (e.g., check 3 topics)
5. Click "Generate Selected Topics" - all 3 posts are queued for generation
6. Continue working while posts generate in the background
7. Check **Blog Agent** → **Queue** to monitor progress
8. Repeat the process to continue building your series

### 5. View Generated Posts

Navigate to **Blog Agent** → **Generated Posts** to:

- View all posts created by the plugin
- Check post status (published, draft, etc.)
- See which AI provider was used
- Edit or view posts directly
- **Generate SEO metadata** for posts with one click
- **Generate featured images** for posts with AI

### 6. RankMath SEO Integration

The plugin includes automatic SEO optimization for the RankMath SEO plugin:

#### Auto-Generate SEO Metadata
- Navigate to **Blog Agent** → **Settings** → **General Settings**
- Enable **Auto Generate RankMath SEO**
- All newly generated posts will automatically have:
  - **SEO Meta Description**: AI-generated 155-160 character description
  - **Focus Keyword**: Primary keyword (1-4 words) identified from content

#### Manual SEO Generation
- Go to **Blog Agent** → **Generated Posts**
- Click **Generate SEO** button on any post
- SEO metadata will be generated and saved instantly

See [docs/features/RANKMATH_SEO_FEATURE.md](docs/features/RANKMATH_SEO_FEATURE.md) for detailed documentation.

### 7. Monitor Queue

Navigate to **Blog Agent** → **Queue** to:

- View queue statistics (pending, processing, completed, failed)
- Monitor real-time task processing
- See task details and history
- View error messages for failed tasks
- Cleanup old completed/failed tasks

### 8. Monitor Activity

Navigate to **Blog Agent** → **Logs** to:

- View recent plugin activity
- Check for errors or warnings
- Monitor content generation success
- Debug issues
- Clear old logs

### 9. Generate Images

Navigate to **Blog Agent** → **Image Generation** to:

- Generate images using Gemini Imagen API
- Save images directly to WordPress media library
- Attach images to specific blog posts
- Set images as featured images automatically
- Configure aspect ratio (16:9, 4:3, 1:1, 3:4)
- Choose image resolution (1K, 2K, 4K)
- View recently generated images

## Usage

### Quick Manual Generation

1. Go to **Blog Agent** → **Topics**
2. Find the "Quick Generate (Manual Topic)" section at the top
3. Enter your topic, keywords, and hashtags
4. Click **Generate Now**
5. The task will be added to the queue and processed asynchronously
6. Check **Blog Agent** → **Queue** to monitor progress

### Scheduled Automatic Generation

1. Go to **Blog Agent** → **Settings**
2. Set **Enable Scheduling** to "Yes"
3. Choose your preferred **Schedule Frequency**
4. Save settings
5. Posts will be automatically added to the queue based on your schedule
6. The queue processor will generate them asynchronously

### Topic-Specific Generation

1. Go to **Blog Agent** → **Topics**
2. Find the topic you want to generate content for
3. Click the **Generate** button next to that topic
4. A task will be added to the queue for that specific topic
5. Monitor progress in **Blog Agent** → **Queue**

### Series-Based Generation (Batch Support)

1. Go to **Blog Agent** → **Series**
2. Create a new series or select an existing one
3. Add at least one post to the series (or generate one manually)
4. Click **Get AI Suggestions** to analyze existing posts
5. AI will suggest 5 relevant topics for continuation
6. **Select one or multiple topics** using checkboxes
7. Click **Generate Selected Topics** to add them to the queue
8. Posts are generated asynchronously - no need to wait!
9. Monitor progress in **Blog Agent** → **Queue**
10. Repeat to continue building your content series

**Example Series Workflow:**
- Create a series: "Complete Guide to WordPress Security"
- Add/generate first post: "WordPress Security Basics"
- Get suggestions: AI suggests 5 topics like "WordPress Plugin Security", "Hardening wp-config.php", "Two-Factor Authentication", etc.
- **Select multiple topics**: Check 3 topics you want to generate
- Click "Generate Selected Topics": All 3 posts are queued
- Continue working while posts generate in background
- Check queue page to see progress
- Once completed, all posts are automatically added to the series

### Image Generation for Blog Posts

1. Go to **Blog Agent** → **Image Generation**
2. Enter a detailed prompt describing the image you want (e.g., "Create image visualization docker work")
3. Optionally specify a post ID to attach the image to
4. Choose aspect ratio (16:9 recommended for blog posts)
5. Select image size (1K for faster generation, 4K for high quality)
6. Check "Set as Featured Image" if you want it automatically set as post's featured image
7. Click **Generate Image**
8. The image will be saved to your media library and optionally attached to the post

**Note**: Image generation uses the same Gemini API key configured in Settings.

### 10. Monitor System Health

Navigate to **Blog Agent** → **Health Check** to:

- **Check Database Health**: Verify all tables exist and have correct schema
- **Test LLM APIs**: Test connectivity to OpenAI and Gemini with response time tracking
- **Monitor Queue**: View queue statistics, failure rates, and stuck tasks
- **Verify Version**: Check plugin, database, WordPress, and PHP versions
- **Validate Image Generation**: Ensure image generation API is configured correctly
- **View System Info**: See server, MySQL, and PHP configuration details

The health check provides:
- Color-coded status indicators (healthy, warning, error, not configured)
- Detailed diagnostics for each component
- Actionable recommendations for fixing issues
- One-click refresh for updated status

See [docs/features/HEALTH_CHECK_FEATURE.md](docs/features/HEALTH_CHECK_FEATURE.md) for detailed documentation.

## How It Works

1. **Task Enqueueing**: When triggered (manually or scheduled), a task is added to the queue
2. **Queue Processing**: WordPress cron processes the queue asynchronously
3. **Topic Selection**: The plugin selects a topic from your configured list (or uses specified topic)
4. **Prompt Building**: Creates an optimized prompt with the topic, keywords, and hashtags
5. **AI Generation**: Sends the prompt to your selected AI provider (OpenAI, Gemini, or Ollama)
6. **Content Processing**: Parses the response to extract title and content
7. **Post Creation**: Creates a WordPress post with the generated content
8. **Publishing**: Publishes the post or saves as draft based on your settings
9. **Queue Update**: Marks task as completed or failed (with retry logic)

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Active internet connection
- Valid API key for OpenAI or Google Gemini

## Database Tables

The plugin creates the following custom tables:

- `wp_blog_agent_topics`: Stores topics, keywords, and hashtags
- `wp_blog_agent_queue`: Stores generation task queue with status tracking
- `wp_blog_agent_series`: Stores post series information (name, description)
- `wp_blog_agent_series_posts`: Stores relationships between series and posts

## File Storage

The plugin creates log files in:

- `wp-content/uploads/wp-blog-agent-logs/`: Activity logs (protected with .htaccess)

## Cron Jobs

The plugin uses WordPress Cron to schedule automated generation:

- Hook: `wp_blog_agent_generate_post` - Adds tasks to queue based on schedule
- Hook: `wp_blog_agent_process_queue` - Processes queued tasks asynchronously
- Frequencies: hourly, twicedaily, daily, weekly

## Security

- API keys are stored securely in WordPress options
- All user inputs are sanitized and validated
- Nonce verification for all form submissions
- Capability checks for admin actions
- Log files protected from direct access
- Prepared statements for database queries
- Output escaping to prevent XSS

## Troubleshooting

### Health Check First

**Always start with the Health Check page** (`Blog Agent → Health Check`) when troubleshooting:
- Identifies issues across all components
- Provides actionable recommendations
- Tests API connectivity with response times
- Validates database integrity
- Monitors queue health and statistics

### Posts Not Being Generated

1. Run Health Check to identify issues
2. Check that you have entered a valid API key
3. Verify that scheduling is enabled
4. Ensure you have at least one active topic
5. Check WordPress error logs for any issues

### API Errors

- **OpenAI**: Verify your API key has sufficient credits
- **Gemini**: Ensure your API key is valid and has proper permissions
- Check your internet connection
- Use Health Check to test API connectivity

### Scheduling Issues

- Verify that WordPress Cron is working on your server
- Some hosting providers disable WP-Cron; you may need to set up a system cron job
- Check the Logs page for scheduling errors
- Use Health Check to verify cron job status

### Check Logs

- Go to **Blog Agent** → **Logs**
- Look for ERROR or WARNING entries
- Check timestamps to see when issues occurred
- Use logs to debug API or generation issues

## Development

### File Structure

```
wp-blog-agent/
├── .github/
│   └── workflows/           # CI/CD workflows
├── .wordpress-org/          # WordPress.org assets (banner, icon, screenshots)
├── admin/                   # Admin UI pages
│   ├── health-check-page.php
│   ├── image-gen-page.php
│   ├── logs-page.php
│   ├── posts-page.php
│   ├── queue-page.php
│   ├── series-page.php
│   ├── settings-page.php
│   └── topics-page.php
├── assets/
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── docs/                    # Documentation
│   ├── features/            # Feature documentation
│   ├── development/         # Development guides and test docs
│   ├── ARCHITECTURE.md
│   ├── DEPLOYMENT.md
│   └── QUICKSTART.md
├── includes/                # Core plugin classes
│   ├── class-wp-blog-agent-activator.php
│   ├── class-wp-blog-agent-admin.php
│   ├── class-wp-blog-agent-deactivator.php
│   ├── class-wp-blog-agent-gemini.php
│   ├── class-wp-blog-agent-generator.php
│   ├── class-wp-blog-agent-health-check.php
│   ├── class-wp-blog-agent-image-generator.php
│   ├── class-wp-blog-agent-logger.php
│   ├── class-wp-blog-agent-ollama.php
│   ├── class-wp-blog-agent-openai.php
│   ├── class-wp-blog-agent-queue.php
│   ├── class-wp-blog-agent-rankmath.php
│   ├── class-wp-blog-agent-scheduler.php
│   ├── class-wp-blog-agent-series.php
│   ├── class-wp-blog-agent-text-utils.php
│   └── class-wp-blog-agent-validator.php
├── .gitignore
├── CHANGELOG.md
├── CONTRIBUTING.md
├── LICENSE
├── README.md
├── readme.txt               # WordPress.org readme
├── bump-version.sh
├── create-release-zip.sh
├── uninstall.php
└── wp-blog-agent.php        # Main plugin file
```
└── wp-blog-agent.php
```

### Development Tools

#### Version Bump Script

The `bump-version.sh` script automates the version bump process:

```bash
./bump-version.sh 1.0.2
```

This script will:
1. Update the version in `wp-blog-agent.php` (both header comment and constant)
2. Create a new version section in `CHANGELOG.md`
3. Provide instructions for creating git tags

**Usage:**
```bash
# Bump to a new version
./bump-version.sh <new_version>

# Example: bump to version 1.0.3
./bump-version.sh 1.0.3
```

After running the script:
1. Review the changes with `git diff`
2. Update `CHANGELOG.md` with actual changes for the version
3. Commit the changes
4. Create and push a git tag

See [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) for complete deployment instructions including CI/CD workflows.

### Testing

The plugin includes a comprehensive PHPUnit test suite covering the core utility classes.

#### Running Tests

```bash
# Install test dependencies
composer install

# Run all tests
composer test

# Run tests with detailed output
./vendor/bin/phpunit --testdox

# Generate code coverage report
composer test-coverage
```

#### Test Coverage

- **WP_Blog_Agent_Text_Utils** (18 tests): JSON encoding, UTF-8 handling, text sanitization
- **WP_Blog_Agent_Validator** (23 tests): Input validation, API key validation, data sanitization
- **WP_Blog_Agent_Logger** (13 tests): Logging functionality, log rotation, log cleanup

See [TESTING.md](TESTING.md) for detailed testing documentation and guidelines.

### CI/CD Workflows

The plugin includes GitHub Actions workflows for automated deployment:

- **Plugin Check** (`.github/workflows/plugin-check.yml`): Validates PHP syntax and runs WordPress Plugin Checker on every push/PR
- **Deploy to WordPress.org** (`.github/workflows/deploy.yml`): Automatically deploys tagged releases to WordPress.org
- **Create Release** (`.github/workflows/release.yml`): Creates GitHub releases with downloadable ZIP files

To deploy a new version:
```bash
./bump-version.sh 1.0.3
git add .
git commit -m "Bump version to 1.0.3"
git tag -a v1.0.3 -m "Version 1.0.3"
git push origin main
git push origin v1.0.3
```

The CI/CD workflows will automatically:
1. Run plugin checks
2. Create a GitHub release with ZIP file
3. Deploy to WordPress.org (requires SVN credentials in repository secrets)

## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on:

- Code of conduct
- Development setup
- Coding standards
- Pull request process
- Testing requirements

## License

This plugin is licensed under the MIT License. See [LICENSE](LICENSE) file for details.

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/np2023v2/wp-blog-agent).

## Credits

Developed by NP2023

## Changelog

### Version 1.0.0
- Initial release
- OpenAI GPT integration
- Google Gemini integration
- Topic management system
- Automated scheduling
- SEO optimization with keywords
- Hashtag support
- Auto-publish functionality
- Activity logging system
- Input validation and security
- Logs viewer interface
- Clean uninstall process
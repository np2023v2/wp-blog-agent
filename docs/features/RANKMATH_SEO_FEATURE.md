# RankMath SEO Integration

This plugin now includes automatic SEO metadata generation for the RankMath SEO plugin.

## Features

### 1. Automatic SEO Meta Generation

When enabled in settings, the plugin will automatically generate:
- **SEO Meta Description (Snippet)**: A compelling 155-160 character description optimized for search engines
- **Focus Keyword**: The primary keyword/phrase (1-4 words) that best represents the post content

### 2. Manual Generation Buttons

Two new buttons are available in the "Generated Posts" page for each post:
- **Generate SEO**: Manually generate RankMath SEO meta description and focus keyword
- **Generate Image**: Manually generate an AI-powered featured image for the post

## Setup

### Enable Auto-Generation

1. Go to **Blog Agent → Settings → General Settings**
2. Find "Auto Generate RankMath SEO" setting
3. Select **Yes** to enable automatic generation
4. Click **Save General Settings**

When enabled, all newly generated posts will automatically have:
- SEO meta description set in RankMath
- Focus keyword set in RankMath

### Manual Generation

1. Go to **Blog Agent → Generated Posts**
2. For any post, click:
   - **Generate SEO** button to create/update SEO metadata
   - **Generate Image** button to create a featured image

## How It Works

### SEO Description Generation

The plugin uses AI to:
1. Analyze the post title and content
2. Generate a compelling meta description (155-160 characters)
3. Include primary keywords naturally
4. Store it as `rank_math_description` post meta

### Focus Keyword Generation

The plugin uses AI to:
1. Analyze the post title and content  
2. Identify the most relevant primary keyword/phrase (1-4 words)
3. Choose keywords with high search intent
4. Store it as `rank_math_focus_keyword` post meta

## AI Provider Support

The SEO generation feature works with all supported AI providers:
- **OpenAI (GPT)**: Uses your configured OpenAI API key
- **Google Gemini**: Uses your configured Gemini API key
- **Ollama**: Uses your local Ollama installation

The same AI provider configured in settings will be used for SEO generation.

## RankMath Compatibility

The plugin sets the following RankMath meta fields:
- `rank_math_description`: The SEO meta description
- `rank_math_focus_keyword`: The primary focus keyword

These are standard RankMath meta keys that will be automatically recognized by the RankMath SEO plugin if installed.

## Requirements

- WordPress 5.0 or higher
- RankMath SEO plugin (optional but recommended)
- One of the supported AI providers configured (OpenAI, Gemini, or Ollama)

## Benefits

### For SEO
- **Consistent Optimization**: Every post gets properly optimized SEO metadata
- **Time Savings**: No need to manually write meta descriptions
- **Better Rankings**: AI-generated descriptions are optimized for search engines
- **Keyword Targeting**: Automatic focus keyword identification

### For Content Creation
- **Automated Workflow**: Generate post + SEO metadata + featured image in one go
- **Manual Control**: Option to regenerate SEO metadata for existing posts
- **Flexibility**: Enable/disable as needed

## Usage Examples

### Example 1: Fully Automated
1. Enable "Auto Generate RankMath SEO" in settings
2. Enable "Auto Generate Featured Image" in settings  
3. Add topics and enable scheduling
4. Sit back - the plugin creates complete, SEO-optimized posts with images

### Example 2: Semi-Automated
1. Disable auto-generation in settings
2. Generate posts as usual
3. Review posts in "Generated Posts" page
4. Click "Generate SEO" for posts you want to optimize
5. Click "Generate Image" for posts needing featured images

### Example 3: Existing Posts
1. Go to "Generated Posts" page
2. Find existing posts that need SEO optimization
3. Click "Generate SEO" button to add/update metadata
4. SEO description and focus keyword are generated instantly

## Troubleshooting

### "Unauthorized" Error
- Check that you're logged in as an administrator
- Verify you have the "edit_posts" capability

### SEO Not Generated
- Verify your AI provider is configured correctly in settings
- Check the API key is valid and has sufficient quota
- Review the plugin logs in **Blog Agent → Logs**

### RankMath Not Detecting Metadata
- Ensure RankMath SEO plugin is installed and activated
- The metadata will still be stored even if RankMath is not installed
- You can install RankMath later and it will detect the existing metadata

## Logging

All SEO generation activities are logged. Check logs at:
**Blog Agent → Logs**

Log entries include:
- SEO generation attempts
- Generated descriptions and keywords
- Any errors or failures
- API response details

## API Usage

SEO generation uses minimal API tokens:
- Description generation: ~100 tokens max
- Keyword generation: ~100 tokens max
- Total per post: ~200 tokens

This is significantly less than full post generation (~1000-2000 tokens).

## Notes

- SEO metadata is generated in the language/style of your post content
- Descriptions are automatically truncated to 160 characters if too long
- Keywords are converted to lowercase for consistency
- Both features work independently - you can generate one without the other
- Manual generation overwrites existing metadata

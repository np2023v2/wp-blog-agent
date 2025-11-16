# Quick Start Guide - WP Blog Agent

## Installation Steps

### Step 1: Install the Plugin

**Option A: Manual Installation**
1. Download the plugin ZIP file or clone the repository
2. Upload to `/wp-content/plugins/wp-blog-agent/`
3. Go to WordPress Admin → Plugins
4. Find "WP Blog Agent" and click "Activate"

**Option B: Git Clone**
```bash
cd wp-content/plugins/
git clone https://github.com/np2023v2/wp-blog-agent.git
```
Then activate in WordPress admin.

### Step 2: Get Your API Key

**For OpenAI:**
1. Visit https://platform.openai.com/api-keys
2. Sign up or log in
3. Click "Create new secret key"
4. Copy the key (you won't see it again!)
5. **Note**: You'll need to add credits to your account

**For Google Gemini:**
1. Visit https://makersuite.google.com/app/apikey
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the key
5. **Note**: Gemini has a free tier available

### Step 3: Configure the Plugin

1. Go to **Blog Agent → Settings** in WordPress admin
2. Select your AI provider (OpenAI or Gemini)
3. Paste your API key in the appropriate field
4. Configure your preferences:
   - **Enable Scheduling**: Choose "Yes" to automate
   - **Schedule Frequency**: How often to generate posts
   - **Auto Publish**: "Yes" to publish immediately, "No" to save as drafts
5. Click **Save Settings**

### Step 4: Add Your First Topic

1. Go to **Blog Agent → Topics**
2. Fill in the form:
   - **Topic**: e.g., "WordPress Security Best Practices"
   - **Keywords**: e.g., "wordpress, security, plugins, hacking, protection"
   - **Hashtags**: e.g., "#WordPress, #Security, #WebDev"
3. Click **Add Topic**

### Step 5: Generate Your First Post

**Manual Generation:**
1. Go to **Blog Agent → Settings**
2. Click **Generate Post Now**
3. Wait a moment for the AI to generate content
4. Check **Blog Agent → Generated Posts** to see your new post

**Or Generate from Specific Topic:**
1. Go to **Blog Agent → Topics**
2. Find your topic in the list
3. Click **Generate** button next to it

### Step 6: Review the Generated Post

1. Go to **Blog Agent → Generated Posts**
2. Click **Edit** on your generated post
3. Review the content
4. Make any adjustments needed
5. If it was saved as draft, click **Publish** when ready

## Testing the Setup

### Test Manual Generation

```
1. Settings configured ✓
2. At least one topic added ✓
3. Click "Generate Post Now" ✓
4. Check for success message ✓
5. View generated post ✓
```

### Test Scheduled Generation

```
1. Enable Scheduling in settings ✓
2. Set frequency (try "hourly" for testing) ✓
3. Save settings ✓
4. Wait for scheduled time ✓
5. Check Generated Posts page ✓
```

## Common Configuration Examples

### Example 1: Daily Blog with Auto-Publish
```
AI Provider: OpenAI
Schedule: Daily
Auto Publish: Yes
Topics: 5-10 diverse topics
```

### Example 2: Content Review Before Publishing
```
AI Provider: Gemini
Schedule: Twice Daily
Auto Publish: No (save as draft)
Topics: 3-5 focused topics
```

### Example 3: High-Frequency Content
```
AI Provider: OpenAI
Schedule: Hourly
Auto Publish: Yes
Topics: 20+ varied topics
```

### Example 4: Manual Control
```
AI Provider: Gemini
Schedule: Disabled
Auto Publish: No
Topics: As needed
Manual Generation: Use "Generate" button for each topic
```

## Example Topics to Get Started

### Tech Blog
- **Topic**: "Latest JavaScript Frameworks in 2024"
- **Keywords**: javascript, frameworks, react, vue, angular, web development
- **Hashtags**: #JavaScript, #WebDev, #Programming, #Tech

### Health & Wellness
- **Topic**: "Benefits of Morning Meditation"
- **Keywords**: meditation, mindfulness, mental health, wellness, morning routine
- **Hashtags**: #Meditation, #Wellness, #MentalHealth, #SelfCare

### Business & Marketing
- **Topic**: "Social Media Marketing Strategies for Small Business"
- **Keywords**: social media, marketing, small business, instagram, facebook, strategy
- **Hashtags**: #Marketing, #SmallBusiness, #SocialMedia, #Entrepreneur

### Travel
- **Topic**: "Hidden Gems in Southeast Asia"
- **Keywords**: travel, southeast asia, tourism, adventure, hidden gems, vacation
- **Hashtags**: #Travel, #SoutheastAsia, #Adventure, #TravelTips

### Food & Cooking
- **Topic**: "Quick and Healthy Meal Prep Ideas"
- **Keywords**: meal prep, healthy eating, cooking, recipes, nutrition, time-saving
- **Hashtags**: #MealPrep, #HealthyEating, #Cooking, #FoodBlogger

## Troubleshooting Quick Fixes

### "No API Key" Error
- **Fix**: Go to Settings and enter your API key
- **Verify**: Make sure you selected the correct provider

### "No Active Topics" Error
- **Fix**: Go to Topics and add at least one topic
- **Check**: Ensure topic status is "Active"

### Posts Not Generating on Schedule
- **Fix 1**: Verify scheduling is enabled in Settings
- **Fix 2**: Check that WordPress Cron is working (some hosts disable it)
- **Fix 3**: Visit your site regularly (triggers WP-Cron on page loads)

### API Rate Limit Errors
- **OpenAI**: Check your account has sufficient credits
- **Gemini**: Verify you haven't exceeded free tier limits
- **Solution**: Reduce frequency or upgrade API plan

### Content Quality Issues
- **Improve Topics**: Be more specific in topic descriptions
- **Better Keywords**: Use relevant, specific keywords
- **Adjust Prompts**: Edit the prompt building in the code if needed

## Best Practices

### 1. Topic Management
- Start with 5-10 diverse topics
- Use specific, descriptive topic names
- Include 5-10 relevant keywords per topic
- Add 3-5 hashtags per topic

### 2. Keyword Selection
- Use a mix of broad and specific terms
- Include long-tail keywords for SEO
- Research popular keywords in your niche
- Update keywords based on performance

### 3. Scheduling Strategy
- Start with daily generation to test
- Adjust frequency based on your needs
- Consider your audience's reading habits
- Balance quantity with quality

### 4. Content Review
- Start with auto-publish OFF
- Review first few generated posts
- Once satisfied, enable auto-publish
- Periodically check generated content

### 5. API Usage
- Monitor your API usage and costs
- Start with lower frequencies
- Use the free tier when possible
- Scale up gradually

## Using Image Generation

### Generate Images for Blog Posts

1. **Access Image Generation**
   - Go to **Blog Agent → Image Generation**

2. **Generate Your First Image**
   - Enter a detailed prompt: "Create a professional blog header image showing modern web development tools"
   - Choose aspect ratio: 16:9 (best for blog headers)
   - Select size: 1K (faster) or 4K (higher quality)
   - Click **Generate Image**

3. **Attach to Existing Post**
   - Find your post ID (visible in post edit URL)
   - Enter the post ID in "Attach to Post" field
   - Check "Set as Featured Image"
   - Generate the image

4. **View Generated Images**
   - Scroll down to see recently generated images
   - Click "View Details" to access in media library
   - Use images in your blog posts

### Image Generation Tips

- **Be Specific**: Detailed prompts produce better results
  - Good: "Create a vibrant illustration of a developer coding at a modern desk with dual monitors, coffee cup, and plants"
  - Poor: "Developer working"

- **Aspect Ratios**:
  - 16:9 - Blog headers, featured images
  - 4:3 - Standard content images
  - 1:1 - Social media, thumbnails
  - 3:4 - Portrait orientation

- **Image Sizes**:
  - 1K (1024px) - Fast generation, good for most uses
  - 2K (2048px) - Higher quality, balanced
  - 4K (4096px) - Maximum quality, slower generation

## Next Steps

1. ✓ Plugin installed and activated
2. ✓ API key configured
3. ✓ First topic added
4. ✓ First post generated
5. ⏭️ Add 5-10 more topics
6. ⏭️ Enable scheduling
7. ⏭️ Monitor generated content
8. ⏭️ Adjust settings as needed
9. ⏭️ Scale up when comfortable

## Need Help?

- Check the main [README.md](README.md) for detailed documentation
- Review [ARCHITECTURE.md](ARCHITECTURE.md) for technical details
- Check WordPress error logs for issues
- Verify API credentials are correct
- Ensure WordPress Cron is functioning

## Support

For issues or questions:
- GitHub Issues: https://github.com/np2023v2/wp-blog-agent/issues
- Documentation: See README.md
- WordPress Logs: wp-content/debug.log (if enabled)

Happy Blogging! 🚀

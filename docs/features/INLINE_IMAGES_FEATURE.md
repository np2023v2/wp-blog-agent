# Inline Images Feature Documentation

## Overview

The **Auto Generate Inline Images** feature allows the WP Blog Agent to automatically create blog posts with illustrative images embedded throughout the content. This creates more engaging, visually appealing posts that enhance reader experience.

## How It Works

### 1. Content Generation with Placeholders

When the feature is enabled, the AI is instructed to include image placeholders in the generated content using this format:

```
[IMAGE: description of the image needed]
```

**Example:**
```html
<h2>Understanding WordPress Performance</h2>
<p>WordPress performance is crucial for user experience and SEO.</p>
[IMAGE: A professional workspace with laptop showing WordPress dashboard]
<p>There are several factors that affect site speed...</p>
```

The AI typically adds 2-4 placeholders at strategic locations where visual content would enhance understanding.

### 2. Image Generation

After the text content is generated, the plugin:
1. Scans the content for image placeholders using pattern matching
2. Extracts the description from each placeholder
3. Creates an enhanced image prompt combining the post topic and the description
4. Generates an AI image using the Gemini Imagen API
5. Uploads each generated image to the WordPress media library

### 3. Placeholder Replacement

Finally, the plugin replaces each placeholder with actual HTML image markup:

```html
<figure class="wp-block-image">
    <img src="[image-url]" alt="[description]" class="wp-blog-agent-inline-image" />
    <figcaption>[description]</figcaption>
</figure>
```

This creates properly formatted images with captions that integrate seamlessly with WordPress block editor.

## Configuration

### Enable the Feature

1. Go to **Blog Agent** → **Settings** → **General Settings**
2. Find **Auto Generate Inline Images** setting
3. Select **Yes** to enable
4. Click **Save General Settings**

### Requirements

- Gemini Image API key must be configured in **API Credentials** tab
- The same API key used for text generation can be used for images
- Sufficient API quota for image generation (each post may generate 2-4 images)

## How Placeholders Are Created

The AI is instructed to:
- Add 2-4 image placeholders throughout the article
- Place them where visual content would enhance understanding
- Provide clear, descriptive text about what each image should show
- Use the exact format: `[IMAGE: description]`

**Example Instructions to AI:**
```
Add 2-4 image placeholders throughout the article using this exact format: [IMAGE: description of the image needed]
- Place image placeholders where visual content would enhance understanding
- Each placeholder should have a clear, descriptive text about what the image should show
- Example: [IMAGE: A professional workspace with laptop and coffee]
```

## Image Generation Parameters

For inline images, the plugin uses:
- **Aspect Ratio**: 16:9 (optimal for blog content)
- **Image Size**: 1K (good balance of quality and file size)
- **Sample Count**: 1 (one image per placeholder)
- **Output Format**: JPEG
- **Person Generation**: Allowed

## Metadata Tracking

Each generated inline image stores metadata:
- `_wp_blog_agent_generated_image`: `true`
- `_wp_blog_agent_image_prompt`: The full prompt used to generate the image
- `_wp_blog_agent_inline_image`: `true` (distinguishes from featured images)

## Error Handling

If image generation fails for any placeholder:
- The plugin logs the error
- Replaces the placeholder with an HTML comment: `<!-- Image placeholder: [description] (generation failed) -->`
- Continues processing other placeholders
- The post is still created with the text content intact

This ensures that a failed image generation doesn't block the entire post creation.

## Benefits

### Enhanced Visual Appeal
- Posts are more engaging with relevant illustrations
- Professional appearance with properly formatted images
- Better user experience and longer time on page

### SEO Advantages
- Images with descriptive alt text improve SEO
- Proper HTML structure (figure, figcaption) follows best practices
- Visual content can help with image search rankings

### Time Savings
- No need to manually search for or create images
- Fully automated process from content to images
- Consistent quality and relevance

### Consistency
- All images are AI-generated with consistent style
- Descriptions automatically match content context
- Professional quality maintained across all posts

## Example Workflow

1. **Topic**: "10 WordPress Security Best Practices"
2. **AI Generates Content** with placeholders:
   ```
   <h1>10 WordPress Security Best Practices</h1>
   <p>Introduction to WordPress security...</p>
   [IMAGE: Secure padlock icon on computer screen representing website security]
   
   <h2>1. Use Strong Passwords</h2>
   <p>Strong passwords are your first line of defense...</p>
   [IMAGE: Password strength indicator showing strong password]
   
   <h2>2. Keep WordPress Updated</h2>
   <p>Regular updates patch security vulnerabilities...</p>
   [IMAGE: WordPress dashboard showing available updates]
   ```

3. **Plugin Generates Images**: 3 AI images created based on descriptions

4. **Final Result**: Complete blog post with 3 embedded images, each with proper alt text and captions

## Troubleshooting

### No Images Generated

**Check:**
- Gemini Image API key is configured correctly
- API key has sufficient quota
- Check the Logs page for error messages

### Placeholders Not Replaced

**Check:**
- Inline images feature is enabled in settings
- Content uses correct placeholder format: `[IMAGE: description]`
- Check logs for image generation errors

### Image Quality Issues

**Considerations:**
- Improve placeholder descriptions for better results
- More detailed descriptions produce more accurate images
- The AI prompt quality affects final image quality

## Best Practices

1. **Enable for visual-heavy topics**: Works best for topics that benefit from visual aids
2. **Monitor API usage**: Each post with 3 images uses 3 API calls
3. **Review logs regularly**: Check for any generation failures
4. **Test with manual generation**: Try manual topic generation first to see results
5. **Combine with featured images**: Use both inline images and featured images for best results

## Technical Details

### Placeholder Pattern

The regex pattern used to detect placeholders:
```php
/\[IMAGE:\s*([^\]]+)\]/i
```

This matches:
- `[IMAGE: description]` (standard format)
- `[IMAGE:description]` (without space)
- Case-insensitive matching
- Captures the description text

### Image Prompt Enhancement

Each placeholder description is enhanced into a full prompt:
```php
sprintf(
    'Create a high-quality, professional image for a blog post about "%s". The image should show: %s. Make it visually appealing, modern, and relevant.',
    $topic,
    $description
);
```

This ensures generated images are contextual and high-quality.

## Future Enhancements

Potential improvements for future versions:
- Configurable number of inline images (min/max)
- Custom aspect ratios per placeholder
- Image position control (left, right, center)
- Image size variations
- Alternative image generation providers
- Manual placeholder insertion in existing posts
- Batch regeneration of images

## Support

For issues or questions about the Inline Images feature:
1. Check the **Logs** page in Blog Agent admin
2. Review this documentation
3. Check the main README.md for general setup
4. Visit the GitHub repository for support

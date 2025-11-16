# WordPress.org Plugin Assets

This directory contains assets for the WordPress.org plugin repository.

## Directory Structure

- `banner-772x250.png` - Plugin banner image (high resolution)
- `banner-1544x500.png` - Plugin banner image (high resolution retina)
- `icon-128x128.png` - Plugin icon (standard)
- `icon-256x256.png` - Plugin icon (retina)
- `screenshot-*.png` - Plugin screenshots (numbered sequentially)

## Guidelines

### Banner Images
- banner-772x250.png: Standard banner (required)
- banner-1544x500.png: Retina banner (optional but recommended)
- Use high-quality images that represent the plugin
- Include plugin name/logo if desired
- Keep file size reasonable (under 500KB)

### Icons
- icon-128x128.png: Standard icon (required)
- icon-256x256.png: Retina icon (optional but recommended)
- Should be simple and recognizable at small sizes
- PNG format with transparency preferred
- Represents the plugin in listings

### Screenshots
- Named screenshot-1.png, screenshot-2.png, etc.
- Correspond to descriptions in readme.txt
- Should clearly show plugin features
- Recommended size: 1280x720 or larger
- PNG or JPG format

## Usage

These assets are automatically synced to WordPress.org when deploying using the GitHub Action workflow.

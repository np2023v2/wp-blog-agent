#!/bin/bash

# Create release ZIP for WP Blog Agent
# Usage: ./create-release-zip.sh [version]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to display usage
usage() {
    echo "Usage: $0 [version]"
    echo "Example: $0 1.0.3"
    echo ""
    echo "Creates a release ZIP file for distribution"
    exit 1
}

# Get version
if [ -z "$1" ]; then
    # Try to get version from wp-blog-agent.php
    VERSION=$(grep "define('WP_BLOG_AGENT_VERSION'" wp-blog-agent.php | sed -E "s/.*'([0-9]+\.[0-9]+\.[0-9]+)'.*/\1/")
    if [ -z "$VERSION" ]; then
        echo -e "${RED}Error: Could not determine version${NC}"
        usage
    fi
    echo -e "${YELLOW}Using version from wp-blog-agent.php: ${VERSION}${NC}"
else
    VERSION=$1
fi

# Validate version format
if ! [[ $VERSION =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    echo -e "${RED}Error: Invalid version format. Use semantic versioning (e.g., 1.0.3)${NC}"
    exit 1
fi

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${GREEN}Creating release ZIP for version ${VERSION}${NC}"

# Create build directory
BUILD_DIR="build"
PLUGIN_DIR="wp-blog-agent"
ZIP_NAME="wp-blog-agent-${VERSION}.zip"

echo "Cleaning up old build..."
rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR/$PLUGIN_DIR"

echo "Copying plugin files..."
rsync -av --progress . "$BUILD_DIR/$PLUGIN_DIR/" \
    --exclude .git \
    --exclude .github \
    --exclude .gitignore \
    --exclude build \
    --exclude node_modules \
    --exclude vendor \
    --exclude .wordpress-org \
    --exclude '*.md' \
    --exclude bump-version.sh \
    --exclude create-release-zip.sh \
    --exclude docs \
    --exclude '*.log' \
    --exclude '*.swp' \
    --exclude '*.swo' \
    --exclude '*~' \
    --exclude .DS_Store \
    --exclude Thumbs.db \
    --exclude .idea \
    --exclude .vscode

# Keep essential markdown files
cp README.md "$BUILD_DIR/$PLUGIN_DIR/"
cp CHANGELOG.md "$BUILD_DIR/$PLUGIN_DIR/"
cp LICENSE "$BUILD_DIR/$PLUGIN_DIR/"

echo "Creating ZIP file..."
cd "$BUILD_DIR"
zip -r "../$ZIP_NAME" "$PLUGIN_DIR/" -q

cd ..
rm -rf "$BUILD_DIR"

if [ -f "$ZIP_NAME" ]; then
    FILE_SIZE=$(du -h "$ZIP_NAME" | cut -f1)
    echo -e "${GREEN}✓ Successfully created: ${ZIP_NAME} (${FILE_SIZE})${NC}"
    echo ""
    echo "Next steps:"
    echo "  1. Test the plugin by installing the ZIP on a WordPress site"
    echo "  2. Upload to GitHub release if needed"
    echo ""
else
    echo -e "${RED}✗ Failed to create ZIP file${NC}"
    exit 1
fi

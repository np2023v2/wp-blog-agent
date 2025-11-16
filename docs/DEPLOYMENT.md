# Deployment Guide

This guide covers how to deploy WP Blog Agent to WordPress.org and create GitHub releases.

## Prerequisites

### For WordPress.org Deployment

1. **WordPress.org SVN Credentials**
   - You need an account on WordPress.org
   - Your plugin must be approved and listed on WordPress.org
   - Set up repository secrets in GitHub:
     - `SVN_USERNAME`: Your WordPress.org username
     - `SVN_PASSWORD`: Your WordPress.org password

2. **Plugin Assets**
   - Add banner images to `.wordpress-org/` directory
   - Add icon images to `.wordpress-org/` directory
   - Add screenshots to `.wordpress-org/` directory
   - See `.wordpress-org/README.md` for specifications

### For GitHub Releases

- GitHub repository with appropriate permissions
- Repository must have tagging enabled

## Deployment Process

### 1. Prepare for Release

1. **Update version numbers:**
   ```bash
   ./bump-version.sh 1.0.3
   ```
   This updates:
   - Version in `wp-blog-agent.php` (header and constant)
   - Creates new version section in `CHANGELOG.md`

2. **Update CHANGELOG.md:**
   - Fill in the changes for the new version
   - Follow Keep a Changelog format
   - Include Added, Changed, Fixed, Security sections as needed

3. **Update readme.txt:**
   - Update the "Tested up to" version if needed
   - Update the changelog section
   - Ensure version matches `wp-blog-agent.php`

4. **Test the plugin:**
   - Test all major features
   - Check compatibility with latest WordPress version
   - Verify no PHP errors or warnings
   - Run plugin checker locally if possible

### 2. Commit and Tag

1. **Commit all changes:**
   ```bash
   git add .
   git commit -m "Bump version to 1.0.3"
   git push origin main
   ```

2. **Create and push a tag:**
   ```bash
   git tag -a v1.0.3 -m "Version 1.0.3"
   git push origin v1.0.3
   ```

### 3. Automated Deployment

When you push a tag (e.g., `v1.0.3`), the following happens automatically:

#### GitHub Release (`.github/workflows/release.yml`)
1. Creates a clean build of the plugin
2. Excludes development files (docs, .git, etc.)
3. Creates a ZIP file: `wp-blog-agent-1.0.3.zip`
4. Creates a GitHub release with the ZIP attached
5. Extracts relevant changelog entries for release notes

#### WordPress.org Deploy (`.github/workflows/deploy.yml`)
1. Checks out the code
2. Deploys to WordPress.org SVN repository
3. Syncs `.wordpress-org/` assets to WordPress.org
4. Creates a plugin ZIP and attaches to GitHub release

#### Plugin Check (`.github/workflows/plugin-check.yml`)
- Runs automatically on pushes to main/develop
- Validates PHP syntax
- Runs WordPress Plugin Checker
- Reports any issues

### 4. Verify Deployment

1. **Check GitHub Release:**
   - Visit: https://github.com/np2023v2/wp-blog-agent/releases
   - Verify the new release is listed
   - Download and test the ZIP file

2. **Check WordPress.org:**
   - Visit: https://wordpress.org/plugins/wp-blog-agent/
   - Verify the new version is available
   - Check that assets (banner, icon, screenshots) display correctly
   - Test the download

3. **Monitor for Issues:**
   - Check GitHub Actions logs for any errors
   - Monitor WordPress.org support forums
   - Test installation on a clean WordPress site

## Manual Deployment (Fallback)

If automated deployment fails, you can deploy manually:

### Manual GitHub Release

1. Create the release ZIP:
   ```bash
   ./create-release-zip.sh 1.0.3
   ```

2. Go to GitHub → Releases → "Draft a new release"
3. Choose the tag (v1.0.3)
4. Upload the ZIP file
5. Add release notes from CHANGELOG.md
6. Publish release

### Manual WordPress.org Deployment

1. **Checkout the SVN repository:**
   ```bash
   svn co https://plugins.svn.wordpress.org/wp-blog-agent/
   cd wp-blog-agent
   ```

2. **Update trunk:**
   ```bash
   rsync -av --delete /path/to/plugin/ trunk/ \
     --exclude='.git*' --exclude='node_modules' --exclude='vendor' \
     --exclude='.wordpress-org' --exclude='docs'
   ```

3. **Copy assets:**
   ```bash
   cp /path/to/plugin/.wordpress-org/* assets/
   ```

4. **Create tag:**
   ```bash
   svn cp trunk tags/1.0.3
   ```

5. **Commit changes:**
   ```bash
   svn ci -m "Release version 1.0.3"
   ```

## Rollback

If a release has critical issues:

1. **GitHub Release:**
   - Edit the release and mark as "pre-release"
   - Or delete the release (keep the tag)

2. **WordPress.org:**
   - Revert to previous tag in SVN:
     ```bash
     svn cp tags/1.0.2 trunk
     svn ci -m "Revert to version 1.0.2"
     ```

## Troubleshooting

### Deployment Action Fails

1. Check GitHub Actions logs for errors
2. Verify SVN credentials are correct in repository secrets
3. Ensure tag format is correct (vX.Y.Z)
4. Check that WordPress.org plugin is approved

### Plugin Check Fails

1. Review PHP syntax errors
2. Check for WordPress coding standards violations
3. Update code to fix issues
4. Push fixes and create new tag

### Assets Not Showing on WordPress.org

1. Verify files are in `.wordpress-org/` directory
2. Check file names match requirements
3. Ensure file sizes are reasonable (< 1MB each)
4. Wait a few minutes for WordPress.org cache to clear

## Version Numbering

Follow Semantic Versioning (semver):
- **Major (1.0.0 → 2.0.0)**: Breaking changes
- **Minor (1.0.0 → 1.1.0)**: New features, backward compatible
- **Patch (1.0.0 → 1.0.1)**: Bug fixes, backward compatible

## Release Checklist

- [ ] Version bumped in all files
- [ ] CHANGELOG.md updated with changes
- [ ] readme.txt updated
- [ ] All tests passing
- [ ] PHP syntax check passes
- [ ] Compatible with latest WordPress version
- [ ] Assets (banner, icon, screenshots) ready
- [ ] Changes committed to main branch
- [ ] Tag created and pushed
- [ ] GitHub release created successfully
- [ ] WordPress.org deployment successful
- [ ] Release verified on both platforms
- [ ] Documentation updated if needed

## Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Plugin Directory Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [Keep a Changelog](https://keepachangelog.com/)
- [Semantic Versioning](https://semver.org/)
- [10up WordPress Plugin Deploy Action](https://github.com/10up/action-wordpress-plugin-deploy)

# Health Check Module

## Overview

The Health Check module provides comprehensive diagnostics for the WP Blog Agent plugin, helping administrators monitor and troubleshoot system health.

## Features

### 1. Database Health Check
- **Table Verification**: Verifies all 4 database tables exist
  - `blog_agent_topics`: Topic management
  - `blog_agent_queue`: Task queue
  - `blog_agent_series`: Post series
  - `blog_agent_series_posts`: Series-post relationships
- **Schema Validation**: Checks for required columns in each table
- **Row Count**: Displays number of records in each table
- **Status Indicators**: Shows which tables have issues

### 2. LLM API Health Check
- **Provider Testing**: Tests both OpenAI and Gemini APIs
- **Active Provider Status**: Highlights which provider is currently active
- **Response Time Tracking**: Measures API response time in milliseconds
- **Configuration Validation**: Verifies API keys are configured
- **Connection Testing**: Performs lightweight API calls to verify connectivity
- **Error Reporting**: Displays detailed error messages for failed connections

### 3. Queue Health Check
- **Statistics Dashboard**: Shows counts of pending, processing, completed, and failed tasks
- **Failure Rate Monitoring**: Calculates and displays failure percentage
- **Stuck Task Detection**: Identifies tasks stuck in processing state for over 1 hour
- **Cron Job Status**: Verifies queue processing cron job is scheduled
- **Next Run Information**: Shows when the next queue processing will occur

### 4. Version & Updates
- **Plugin Version**: Current plugin version
- **Database Version**: Database schema version
- **WordPress Version**: Current WordPress version with compatibility check
- **PHP Version**: Current PHP version with compatibility check
- **Upgrade Status**: Indicates if database needs upgrading
- **Minimum Requirements**: 
  - WordPress 5.0+
  - PHP 7.4+

### 5. Image Generation Health
- **Auto-Generate Status**: Shows if automatic image generation is enabled
- **API Key Configuration**: Verifies Gemini Image API key is configured
- **Upload Directory Check**: Ensures WordPress upload directory is writable
- **API Validation**: Validates API key format
- **Cost Protection**: Skips actual image generation in health checks to avoid API costs

### 6. System Information
- **Server Software**: Web server information
- **MySQL Version**: Database version
- **Memory Limit**: PHP memory limit
- **Max Execution Time**: PHP execution time limit
- **Upload Size Limits**: Max upload and post sizes

## Accessing Health Check

Navigate to **Blog Agent** → **Health Check** in the WordPress admin panel.

## Status Indicators

The health check uses color-coded status indicators:

- 🟢 **Healthy** (Green): Component is working correctly
- 🟡 **Warning** (Yellow): Component has minor issues but is functional
- 🔴 **Error** (Red): Component has critical issues requiring attention
- 🔵 **Not Configured** (Blue): Component is not configured

## Overall Status

The overall system status is calculated from all component checks:
- **Healthy**: All components are working correctly
- **Warning**: At least one component has warnings but no critical errors
- **Error**: At least one component has critical errors

## Common Issues and Solutions

### Database Errors

**Issue**: Table does not exist
- **Solution**: Deactivate and reactivate the plugin to recreate tables

**Issue**: Missing columns in table
- **Solution**: Update the plugin to the latest version and refresh the health check

### API Errors

**Issue**: OpenAI API not working
- **Solution**: 
  1. Verify API key is correct
  2. Check if API key has sufficient credits
  3. Ensure internet connectivity

**Issue**: Gemini API not working
- **Solution**:
  1. Verify API key is correct
  2. Check API key permissions
  3. Ensure the API is enabled in Google Cloud Console

### Queue Issues

**Issue**: High failure rate
- **Solution**:
  1. Check API connectivity
  2. Review logs for error patterns
  3. Verify API keys and credits

**Issue**: Stuck tasks
- **Solution**:
  1. Check if WordPress cron is working
  2. Review server logs for PHP errors
  3. Clear stuck tasks from queue page

**Issue**: Cron job not scheduled
- **Solution**: 
  1. Deactivate and reactivate the plugin
  2. Check if WP-Cron is disabled on your server
  3. Set up system cron if needed

### Version Issues

**Issue**: Database version older than plugin version
- **Solution**: The plugin will automatically upgrade the database on next admin page load

**Issue**: WordPress or PHP version too old
- **Solution**: Upgrade to meet minimum requirements (WordPress 5.0+, PHP 7.4+)

### Image Generation Issues

**Issue**: API key not configured
- **Solution**: Go to Settings → Image Generation and enter Gemini Image API key

**Issue**: Upload directory not writable
- **Solution**: 
  1. Check directory permissions (should be 755 or 775)
  2. Contact hosting provider if permissions cannot be changed

## Performance Considerations

- **API Tests**: Health checks make minimal API calls to test connectivity
- **Cost Impact**: Tests generate small content (~10 tokens) to minimize API costs
- **Image API**: Full image generation tests are skipped to avoid costs
- **Caching**: Results are not cached; refresh triggers new checks
- **Execution Time**: Full health check typically completes in 5-15 seconds

## Monitoring Best Practices

1. **Regular Checks**: Run health check weekly or after making configuration changes
2. **Before Production**: Always run health check before deploying to production
3. **After Updates**: Run health check immediately after plugin updates
4. **Troubleshooting**: Use health check as first step when issues arise
5. **Documentation**: Take screenshots of errors for support tickets

## Integration with Other Features

- **Logs**: Health check works alongside the Logs page for detailed diagnostics
- **Queue**: Queue statistics link to the Queue page for detailed task management
- **Settings**: Issues often point to Settings page for configuration fixes
- **Generated Posts**: Database checks ensure post data integrity

## Security

- **Admin Only**: Health check is only accessible to users with `manage_options` capability
- **No Data Modification**: Health checks are read-only operations
- **Safe API Tests**: Minimal API calls with non-sensitive test data
- **No Credentials Display**: API keys are never displayed in results

## Technical Details

### Health Check Class

**File**: `includes/class-wp-blog-agent-health-check.php`

**Main Methods**:
- `run_all_checks()`: Executes all health checks and returns results
- `check_database()`: Validates database tables and schema
- `check_llm_api()`: Tests LLM API connectivity
- `check_queue()`: Analyzes queue health and statistics
- `check_version()`: Verifies version compatibility
- `check_image_generation()`: Validates image generation setup

### Admin Page

**File**: `admin/health-check-page.php`

Displays health check results in a user-friendly dashboard with:
- Status cards for each component
- Detailed tables with diagnostics
- Issue lists with actionable information
- System information panel

### Registration

The health check page is registered in `class-wp-blog-agent-admin.php`:
```php
add_submenu_page(
    'wp-blog-agent',
    __('Health Check', 'wp-blog-agent'),
    __('Health Check', 'wp-blog-agent'),
    'manage_options',
    'wp-blog-agent-health-check',
    array($this, 'render_health_check_page')
);
```

## Future Enhancements

Potential improvements for future versions:
- Scheduled automatic health checks
- Email notifications for critical issues
- Health check history and trends
- Export health check reports
- API endpoint for programmatic access
- Integration with WordPress Site Health

## Related Documentation

- [ARCHITECTURE.md](ARCHITECTURE.md): Plugin architecture overview
- [README.md](README.md): General plugin documentation
- [TROUBLESHOOTING.md]: Troubleshooting guide (if available)
- [CHANGELOG.md](CHANGELOG.md): Version history

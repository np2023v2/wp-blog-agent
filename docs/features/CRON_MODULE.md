# Cron Management Module

## Overview

The Cron Management Module (`WP_Blog_Agent_Cron`) provides centralized management of all WordPress cron jobs used by the WP Blog Agent plugin. This module consolidates cron-related functionality that was previously scattered across multiple classes.

## Features

- **Centralized Cron Management**: All cron operations in one place
- **Custom Schedules**: Defines custom intervals (hourly, twice daily, weekly)
- **Automatic Scheduling**: Schedules and unschedules cron jobs automatically
- **Status Monitoring**: Get real-time status of all cron jobs
- **Error Handling**: Robust error logging for scheduling failures
- **WordPress Cron Check**: Verify if WordPress cron is functioning properly

## Architecture

### Cron Hooks

The module manages two main cron hooks:

1. **`wp_blog_agent_generate_post`**: Recurring cron job for scheduled post generation
   - Runs based on user-configured frequency (hourly, twice daily, daily, weekly)
   - Adds tasks to the queue for processing

2. **`wp_blog_agent_process_queue`**: One-time event for queue processing
   - Processes pending tasks in the generation queue
   - Automatically rescheduled when new tasks are available

### Custom Schedules

The module defines the following custom WordPress cron intervals:

- **Hourly**: Every 3,600 seconds (1 hour)
- **Twice Daily**: Every 43,200 seconds (12 hours)
- **Weekly**: Every 604,800 seconds (7 days)

## Usage

### Initialization

The cron module is automatically initialized when the plugin loads:

```php
WP_Blog_Agent_Cron::init();
```

This registers the custom schedules and hooks the event handlers.

### Scheduling Post Generation

Schedule recurring post generation:

```php
// Schedule daily post generation
WP_Blog_Agent_Cron::schedule_post_generation('daily');

// Schedule hourly post generation
WP_Blog_Agent_Cron::schedule_post_generation('hourly');

// Disable scheduled generation
WP_Blog_Agent_Cron::schedule_post_generation('none');
```

### Unscheduling Post Generation

Remove scheduled post generation:

```php
WP_Blog_Agent_Cron::unschedule_post_generation();
```

### Scheduling Queue Processing

Schedule a one-time queue processing event:

```php
// Schedule immediate processing
WP_Blog_Agent_Cron::schedule_queue_processing();

// Schedule processing with 5-minute delay
WP_Blog_Agent_Cron::schedule_queue_processing(300);
```

### Unscheduling All Cron Jobs

Remove all plugin cron jobs (useful during deactivation):

```php
WP_Blog_Agent_Cron::unschedule_all();
```

### Getting Cron Status

Retrieve current status of all cron jobs:

```php
$status = WP_Blog_Agent_Cron::get_status();

// Returns:
// array(
//     'post_generation' => array(
//         'scheduled' => true,
//         'next_run' => '2025-10-31 12:00:00',
//         'timestamp' => 1730380800
//     ),
//     'queue_processing' => array(
//         'scheduled' => true,
//         'next_run' => '2025-10-31 10:30:00',
//         'timestamp' => 1730375400
//     )
// )
```

### Checking WordPress Cron

Verify if WordPress cron is functioning:

```php
$is_working = WP_Blog_Agent_Cron::is_cron_working();

if (!$is_working) {
    // WordPress cron is disabled or not working
    // May need to set up system cron
}
```

## Integration

### Activator

The activator uses the cron module to schedule initial cron jobs:

```php
// In class-wp-blog-agent-activator.php
$frequency = get_option('wp_blog_agent_schedule_frequency', 'daily');
WP_Blog_Agent_Cron::schedule_post_generation($frequency);
```

### Deactivator

The deactivator uses the cron module to clean up all cron jobs:

```php
// In class-wp-blog-agent-deactivator.php
WP_Blog_Agent_Cron::unschedule_all();
```

### Scheduler

The scheduler delegates to the cron module:

```php
// In class-wp-blog-agent-scheduler.php
public static function update_schedule($frequency) {
    WP_Blog_Agent_Cron::schedule_post_generation($frequency);
}
```

### Queue

The queue uses the cron module to schedule processing:

```php
// In class-wp-blog-agent-queue.php
WP_Blog_Agent_Cron::schedule_queue_processing();
WP_Blog_Agent_Cron::schedule_queue_processing(300); // Retry in 5 minutes
```

### Health Check

The health check uses the cron module to get status:

```php
// In class-wp-blog-agent-health-check.php
$cron_status = WP_Blog_Agent_Cron::get_status();
```

## Event Handlers

### Post Generation Handler

```php
WP_Blog_Agent_Cron::handle_generate_post()
```

Triggered by the `wp_blog_agent_generate_post` cron hook:
- Checks if scheduling is enabled
- Enqueues a new generation task
- Logs success or failure

### Queue Processing Handler

```php
WP_Blog_Agent_Cron::handle_process_queue()
```

Triggered by the `wp_blog_agent_process_queue` cron hook:
- Delegates to `WP_Blog_Agent_Queue::process_queue()`
- Processes the next pending task in the queue

## Logging

The cron module uses `WP_Blog_Agent_Logger` for comprehensive logging:

- **Info**: Normal operations (scheduling, unscheduling)
- **Debug**: Detailed scheduling information
- **Error**: Scheduling failures

Example log entries:
```
[INFO] Post generation cron scheduled (frequency: daily, next_run: 2025-10-31 12:00:00)
[INFO] Post generation cron unscheduled
[DEBUG] Queue processing scheduled (delay: 300, scheduled_time: 2025-10-31 10:35:00)
[ERROR] Failed to schedule post generation cron (frequency: invalid)
```

## Backward Compatibility

The cron module maintains backward compatibility with existing code:

- The `WP_Blog_Agent_Scheduler` class still exists and delegates to the cron module
- All cron hooks remain the same
- Existing integrations continue to work without modification

## Troubleshooting

### Cron Jobs Not Running

1. **Check if WordPress cron is enabled**:
   ```php
   $is_working = WP_Blog_Agent_Cron::is_cron_working();
   ```

2. **Check cron status**:
   ```php
   $status = WP_Blog_Agent_Cron::get_status();
   ```

3. **Check if DISABLE_WP_CRON is set**:
   ```php
   if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
       // Set up system cron instead
   }
   ```

### Cron Jobs Not Scheduled

If cron jobs are not being scheduled:

1. Check the activity logs in **Blog Agent** → **Logs**
2. Look for error messages related to cron scheduling
3. Verify that the frequency is valid (hourly, twicedaily, daily, weekly)

### Setting Up System Cron

If WordPress cron is disabled on your server, set up a system cron job:

```bash
# Add to crontab
*/5 * * * * curl -s https://yoursite.com/wp-cron.php > /dev/null 2>&1
```

Or using wget:
```bash
*/5 * * * * wget -q -O - https://yoursite.com/wp-cron.php > /dev/null 2>&1
```

## API Reference

### Methods

#### `init()`
Initialize the cron module. Registers custom schedules and event handlers.

#### `schedule_post_generation($frequency)`
Schedule recurring post generation.
- **Parameters**: 
  - `$frequency` (string): Frequency (hourly, twicedaily, daily, weekly, none)
- **Returns**: (bool) Success status

#### `unschedule_post_generation()`
Unschedule post generation cron.
- **Returns**: (bool) Success status

#### `schedule_queue_processing($delay = 0)`
Schedule one-time queue processing event.
- **Parameters**: 
  - `$delay` (int): Delay in seconds before processing
- **Returns**: (bool) Success status

#### `unschedule_queue_processing()`
Unschedule queue processing cron.
- **Returns**: (bool) Success status

#### `unschedule_all()`
Unschedule all plugin cron jobs.
- **Returns**: (bool) Success status

#### `handle_generate_post()`
Handle post generation cron event. Called automatically by WordPress cron.

#### `handle_process_queue()`
Handle queue processing cron event. Called automatically by WordPress cron.

#### `get_status()`
Get status of all cron jobs.
- **Returns**: (array) Status information

#### `is_cron_working()`
Check if WordPress cron is functioning.
- **Returns**: (bool) True if working, false otherwise

### Constants

#### `WP_Blog_Agent_Cron::HOOK_GENERATE_POST`
Cron hook name for post generation: `'wp_blog_agent_generate_post'`

#### `WP_Blog_Agent_Cron::HOOK_PROCESS_QUEUE`
Cron hook name for queue processing: `'wp_blog_agent_process_queue'`

## Benefits

### Centralized Management
- All cron-related code in one place
- Easier to maintain and update
- Consistent error handling and logging

### Better Organization
- Clear separation of concerns
- Reduced code duplication
- Improved code readability

### Enhanced Monitoring
- Centralized status checking
- Comprehensive logging
- Better debugging capabilities

### Improved Reliability
- Robust error handling
- Automatic rescheduling
- WordPress cron health checks

## Related Documentation

- [Architecture Documentation](../ARCHITECTURE.md)
- [Health Check Feature](HEALTH_CHECK_FEATURE.md)
- [Queue System Overview](QUEUE_UI_OVERVIEW.md)

# WP Blog Agent - Plugin Architecture

## Component Overview

### Core Files

#### wp-blog-agent.php
- Main plugin file
- Defines plugin metadata and constants
- Loads all required classes
- Registers activation/deactivation hooks
- Initializes the plugin

### Classes

#### WP_Blog_Agent_Activator
- **Purpose**: Handles plugin activation
- **Responsibilities**:
  - Creates database tables (blog_agent_topics, blog_agent_queue, blog_agent_series, blog_agent_series_posts)
  - Sets default options
  - Schedules initial cron events using WP_Blog_Agent_Cron

#### WP_Blog_Agent_Deactivator
- **Purpose**: Handles plugin deactivation
- **Responsibilities**:
  - Clears scheduled cron events using WP_Blog_Agent_Cron
  - Cleanup operations

#### WP_Blog_Agent_Admin
- **Purpose**: Manages admin interface
- **Responsibilities**:
  - Registers admin menu pages
  - Handles settings and configuration
  - Processes form submissions (add/delete topics, generate posts)
  - Enqueues admin assets (CSS/JS)

#### WP_Blog_Agent_OpenAI
- **Purpose**: OpenAI API integration
- **Responsibilities**:
  - Communicates with OpenAI API
  - Builds prompts for content generation
  - Processes API responses
  - Error handling

#### WP_Blog_Agent_Gemini
- **Purpose**: Google Gemini API integration
- **Responsibilities**:
  - Communicates with Gemini API
  - Builds prompts for content generation
  - Processes API responses
  - Error handling

#### WP_Blog_Agent_Image_Generator
- **Purpose**: Gemini Imagen API integration for image generation
- **Responsibilities**:
  - Generates images using Gemini Imagen API
  - Decodes base64 image data
  - Uploads images to WordPress media library
  - Attaches images to posts
  - Sets featured images
  - Manages image metadata

#### WP_Blog_Agent_Generator
- **Purpose**: Content generation orchestration
- **Responsibilities**:
  - Selects topics from database
  - Calls appropriate AI provider
  - Parses generated content
  - Creates WordPress posts
  - Adds metadata to posts

#### WP_Blog_Agent_Cron
- **Purpose**: Centralized cron job management
- **Responsibilities**:
  - Registers and manages WordPress cron hooks
  - Defines custom cron schedules (hourly, twice daily, weekly)
  - Schedules and unschedules cron events
  - Handles post generation cron events
  - Handles queue processing cron events
  - Provides cron status monitoring
  - Checks WordPress cron health

#### WP_Blog_Agent_Scheduler
- **Purpose**: Automated scheduling (legacy wrapper)
- **Responsibilities**:
  - Maintains backward compatibility
  - Delegates to WP_Blog_Agent_Cron for all cron operations
  - Updates cron schedules via WP_Blog_Agent_Cron

#### WP_Blog_Agent_Queue
- **Purpose**: Task queue management
- **Responsibilities**:
  - Enqueues generation tasks
  - Processes pending tasks
  - Manages task status (pending, processing, completed, failed)
  - Implements retry logic (up to 3 attempts)
  - Provides queue statistics
  - Cleanup old completed/failed tasks
  - Uses WP_Blog_Agent_Cron for scheduling queue processing

#### WP_Blog_Agent_Series
- **Purpose**: Post series management and AI topic suggestions
- **Responsibilities**:
  - Create, read, update, delete series
  - Manage series-post relationships
  - Add and remove posts from series
  - Generate AI-powered topic suggestions based on existing posts
  - Track series statistics (post count, etc.)
  - Coordinate with AI providers for topic suggestions

#### WP_Blog_Agent_Logger
- **Purpose**: Activity logging and debugging
- **Responsibilities**:
  - Write logs to file system
  - Log levels (info, warning, error)
  - Format and timestamp log entries
  - Rotate log files
  - Provide log viewing interface

#### WP_Blog_Agent_Validator
- **Purpose**: Input validation and sanitization
- **Responsibilities**:
  - Validate API keys
  - Validate topic data
  - Sanitize user inputs
  - Test API connections
  - Validate email lists and settings

#### WP_Blog_Agent_Health_Check
- **Purpose**: System health monitoring and diagnostics
- **Responsibilities**:
  - Verify database tables exist and validate schema
  - Test LLM API connectivity (OpenAI, Gemini)
  - Monitor queue health and statistics
  - Check version compatibility (plugin, database, WordPress, PHP)
  - Validate image generation configuration
  - Provide comprehensive health status dashboard
  - Calculate overall system health status
  - Detect issues like stuck tasks and high failure rates

## Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│                     User Configuration                       │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              Settings Page (Admin Interface)                │
│  - AI Provider Selection (OpenAI/Gemini)                    │
│  - API Key Configuration                                     │
│  - Schedule Settings                                         │
│  - Auto-Publish Options                                      │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│                    Topics Management                         │
│  - Add Topics with Keywords/Hashtags                        │
│  - View/Edit/Delete Topics                                   │
│  - Manual Generation Trigger                                 │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│          Trigger (Manual or Scheduled via WP-Cron)          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              WP_Blog_Agent_Queue                             │
│                                                              │
│  1. Task added to queue (blog_agent_queue table)            │
│  2. Status: pending                                          │
│  3. Schedule immediate processing event                      │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│          wp_blog_agent_process_queue (WP-Cron)              │
│                                                              │
│  1. Get next pending task                                    │
│  2. Mark as processing                                       │
│  3. Call WP_Blog_Agent_Generator                            │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              WP_Blog_Agent_Generator                         │
│                                                              │
│  1. Select random active topic OR specific topic            │
│  2. Parse keywords and hashtags                              │
│  3. Determine AI provider from settings                      │
└───────────────────────┬─────────────────────────────────────┘
                        │
           ┌────────────┴────────────┐
           │                         │
           ▼                         ▼
┌──────────────────┐      ┌──────────────────┐
│ WP_Blog_Agent_   │      │ WP_Blog_Agent_   │
│     OpenAI       │      │     Gemini       │
│                  │      │                  │
│ - Build prompt   │      │ - Build prompt   │
│ - Call API       │      │ - Call API       │
│ - Parse response │      │ - Parse response │
└────────┬─────────┘      └────────┬─────────┘
         │                         │
         └────────────┬────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              Generated Content (HTML)                        │
│  - Title (from H1 or first line)                            │
│  - Body content with proper formatting                       │
│  - Keywords integrated naturally                             │
│  - Hashtags appended                                         │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              WordPress Post Creation                         │
│                                                              │
│  - Extract title and content                                │
│  - Generate excerpt                                          │
│  - Set post status (publish or draft)                       │
│  - Add metadata:                                             │
│    * _wp_blog_agent_generated                               │
│    * _wp_blog_agent_topic_id                                │
│    * _wp_blog_agent_keywords                                │
│    * _wp_blog_agent_hashtags                                │
│    * _wp_blog_agent_provider                                │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              Published/Draft Post                            │
│  - Viewable in Generated Posts page                         │
│  - Standard WordPress post with metadata                     │
└─────────────────────────────────────────────────────────────┘
```

## Admin Pages

### 1. Settings Page (wp-blog-agent)
- Configure AI provider and API keys
- Enable/disable scheduling
- Set schedule frequency
- Configure auto-publish behavior
- Quick action: Generate post now
- View next scheduled generation

### 2. Topics Page (wp-blog-agent-topics)
- Add new topics with keywords and hashtags
- List all existing topics
- Generate posts for specific topics
- Delete topics

### 3. Series Page (wp-blog-agent-series)
- Create and manage post series
- View all series with statistics
- AI-powered topic suggestions based on existing posts
- Add existing posts to series
- Remove posts from series
- Generate posts from AI suggestions
- View all posts in a series

### 4. Generated Posts Page (wp-blog-agent-posts)
- View all posts created by the plugin
- See post status and metadata
- Quick links to edit or view posts
- Shows which AI provider was used

### 5. Queue Page (wp-blog-agent-queue)
- View queue statistics (pending, processing, completed, failed)
- List recent queue items with status
- See task details including topic, trigger, attempts
- View error messages for failed tasks
- Cleanup old completed/failed tasks
- Monitor background processing

### 6. Logs Page (wp-blog-agent-logs)
- View plugin activity logs
- Filter by log level (info, warning, error)
- Debug generation issues
- Clear old logs

### 7. Image Generation Page (wp-blog-agent-image-gen)
- Generate images using Gemini Imagen API
- Configure image parameters (aspect ratio, size)
- Attach images to specific posts
- Set as featured image automatically
- View recently generated images
- Direct access to media library

## Database Schema

### Table: wp_blog_agent_topics

```sql
CREATE TABLE wp_blog_agent_topics (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    topic varchar(255) NOT NULL,
    keywords text NOT NULL,
    hashtags text NOT NULL,
    status varchar(20) DEFAULT 'active',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

### Table: wp_blog_agent_queue

```sql
CREATE TABLE wp_blog_agent_queue (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    topic_id mediumint(9) DEFAULT NULL,
    status varchar(20) DEFAULT 'pending',
    trigger varchar(50) DEFAULT 'manual',
    post_id bigint(20) DEFAULT NULL,
    attempts int DEFAULT 0,
    error_message text DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    started_at datetime DEFAULT NULL,
    completed_at datetime DEFAULT NULL,
    PRIMARY KEY (id),
    KEY status (status),
    KEY created_at (created_at)
);
```

#### Queue Status Values
- `pending`: Task is waiting to be processed
- `processing`: Task is currently being processed
- `completed`: Task completed successfully
- `failed`: Task failed after maximum retry attempts (3)

### Table: wp_blog_agent_series

```sql
CREATE TABLE wp_blog_agent_series (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    description text DEFAULT NULL,
    status varchar(20) DEFAULT 'active',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

### Table: wp_blog_agent_series_posts

```sql
CREATE TABLE wp_blog_agent_series_posts (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    series_id mediumint(9) NOT NULL,
    post_id bigint(20) NOT NULL,
    position int DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY series_id (series_id),
    KEY post_id (post_id),
    UNIQUE KEY series_post (series_id, post_id)
);
```

### Post Metadata

Posts generated by the plugin include the following metadata:
- `_wp_blog_agent_generated`: Boolean flag (true)
- `_wp_blog_agent_topic_id`: ID of the topic used
- `_wp_blog_agent_keywords`: Comma-separated keywords
- `_wp_blog_agent_hashtags`: Space-separated hashtags
- `_wp_blog_agent_provider`: AI provider used (openai/gemini)
- `_wp_blog_agent_series_id`: ID of the series (if post belongs to a series)

## WordPress Options

The plugin uses the following options:
- `wp_blog_agent_ai_provider`: Selected AI provider (openai/gemini)
- `wp_blog_agent_openai_api_key`: OpenAI API key
- `wp_blog_agent_gemini_api_key`: Gemini API key
- `wp_blog_agent_schedule_enabled`: Whether scheduling is enabled (yes/no)
- `wp_blog_agent_schedule_frequency`: Schedule frequency (hourly/twicedaily/daily/weekly)
- `wp_blog_agent_auto_publish`: Whether to auto-publish (yes/no)

## Cron System

### Hook: wp_blog_agent_generate_post
- Triggered based on configured frequency
- Calls WP_Blog_Agent_Scheduler::scheduled_generation()
- Enqueues a generation task instead of executing directly

### Hook: wp_blog_agent_process_queue
- Triggered when tasks are added to queue
- Calls WP_Blog_Agent_Queue::process_queue()
- Processes one task at a time
- Automatically schedules next task if more are pending
- Implements retry logic (up to 3 attempts with 5-minute delay)

### Custom Schedules
- **hourly**: Every 3600 seconds (1 hour)
- **twicedaily**: Every 43200 seconds (12 hours)
- **daily**: WordPress default (24 hours)
- **weekly**: Every 604800 seconds (7 days)

## Security Measures

1. **Nonce Verification**: All form submissions require valid nonces
2. **Capability Checks**: Admin functions require 'manage_options' capability
3. **Data Sanitization**: All user inputs are sanitized
4. **Data Validation**: Input validation before processing
5. **Direct Access Prevention**: Files check for ABSPATH constant
6. **Secure API Key Storage**: Keys stored in WordPress options table

## Error Handling

### API Errors
- Missing API keys return WP_Error
- Invalid API responses return WP_Error
- Network errors are caught and logged
- User-friendly error messages displayed in admin

### Content Generation Errors
- No active topics returns error
- Failed API calls are logged
- Error messages displayed in admin notices

## Extensibility

The plugin is designed with extensibility in mind:

### Hooks for Developers
Future versions can add WordPress hooks:
- Before/after content generation
- Filter generated content
- Custom AI providers
- Custom post types

### Class Structure
All classes are self-contained and can be extended:
- Add new AI providers by creating similar classes
- Extend generator for custom content processing
- Add custom admin pages following existing pattern

## Performance Considerations

1. **API Timeouts**: Set to 60 seconds for long-running API calls
2. **Cron Scheduling**: Uses WordPress Cron (non-blocking)
3. **Database Queries**: Optimized with proper indexes
4. **Asset Loading**: Admin assets only load on plugin pages
5. **Lazy Loading**: Classes loaded only when needed

## Future Enhancement Ideas

1. **Multi-language Support**: i18n/l10n implementation
2. **Custom Post Types**: Support for custom content types
3. **Category Assignment**: Auto-assign posts to categories
4. **Featured Images**: AI-generated images integration
5. **Content Templates**: Customizable content templates
6. **Analytics Dashboard**: Track generation success rates
7. **Bulk Generation**: Generate multiple posts at once
8. **Content Calendar**: Visual calendar for scheduled posts
9. **A/B Testing**: Test different prompts and AI providers
10. **Export/Import**: Topic configuration export/import

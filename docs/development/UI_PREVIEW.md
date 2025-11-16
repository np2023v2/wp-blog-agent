# Settings UI Preview

## Overview
This document provides a visual representation of the new tabbed settings interface.

## Settings Page - API Credentials Tab

```
┌──────────────────────────────────────────────────────────────────────┐
│  WP Blog Agent - Settings                                            │
├──────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ┌─────────────────┐  ┌─────────────────┐                          │
│  │ API Credentials │  │ General Settings │                          │
│  └─────────────────┘  └─────────────────┘                          │
│  ═══════════════════                                                │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │                                                                 │ │
│  │  AI Provider                                                    │ │
│  │  [ OpenAI (GPT) ▼ ]                                            │ │
│  │  Choose which AI service to use for generating content.        │ │
│  │                                                                 │ │
│  │  OpenAI API Key                                                 │ │
│  │  [●●●●●●●●●●●●●●●●●●●●]                                       │ │
│  │  Enter your OpenAI API key. Get one at ...                     │ │
│  │                                                                 │ │
│  │  OpenAI Base URL                                                │ │
│  │  [https://api.openai.com/v1/chat/completions               ]   │ │
│  │  Custom OpenAI API base URL. Default: ...                      │ │
│  │                                                                 │ │
│  │  Gemini API Key                        (Hidden when OpenAI)    │ │
│  │  Gemini Image API Key                  (Hidden when OpenAI)    │ │
│  │  Ollama Base URL                       (Hidden when OpenAI)    │ │
│  │                                                                 │ │
│  │  [ Save API Credentials ]                                       │ │
│  │                                                                 │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

## Settings Page - General Settings Tab

```
┌──────────────────────────────────────────────────────────────────────┐
│  WP Blog Agent - Settings                                            │
├──────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ┌─────────────────┐  ┌─────────────────┐                          │
│  │ API Credentials │  │ General Settings │                          │
│  └─────────────────┘  └─────────────────┘                          │
│                        ═══════════════════                          │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │                                                                 │ │
│  │  AI Model Configuration                                         │ │
│  │  ════════════════════                                          │ │
│  │                                                                 │ │
│  │  OpenAI Model                                                   │ │
│  │  [gpt-3.5-turbo                                             ]   │ │
│  │  Model to use (e.g., gpt-3.5-turbo, gpt-4, gpt-4-turbo)       │ │
│  │                                                                 │ │
│  │  OpenAI Max Output Tokens                                       │ │
│  │  [            ]  (Unlimited)                                    │ │
│  │  Maximum number of tokens to generate. Leave empty for ...     │ │
│  │                                                                 │ │
│  │  OpenAI System Prompt                                           │ │
│  │  ┌───────────────────────────────────────────────────────────┐ │ │
│  │  │You are a professional blog writer who creates           │ │ │
│  │  │SEO-optimized, engaging content.                         │ │ │
│  │  └───────────────────────────────────────────────────────────┘ │ │
│  │                                                                 │ │
│  │  Post Generation Settings                                       │ │
│  │  ═══════════════════════                                       │ │
│  │                                                                 │ │
│  │  Enable Scheduling                                              │ │
│  │  [ Yes ▼ ]                                                      │ │
│  │  Enable automatic post generation on schedule.                 │ │
│  │                                                                 │ │
│  │  Schedule Frequency                                             │ │
│  │  [ Daily ▼ ]                                                    │ │
│  │  How often to generate new posts.                              │ │
│  │                                                                 │ │
│  │  Auto Publish                                                   │ │
│  │  [ Yes ▼ ]                                                      │ │
│  │  Automatically publish generated posts or save as drafts       │ │
│  │                                                                 │ │
│  │  Auto Generate Featured Image                                   │ │
│  │  [ No ▼ ]                                                       │ │
│  │  Automatically generate and set featured image for posts       │ │
│  │                                                                 │ │
│  │  [ Save General Settings ]                                      │ │
│  │                                                                 │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

## Success Messages

### After Saving API Credentials:
```
┌──────────────────────────────────────────────────────────────────┐
│  ✓  API Credentials saved successfully!                   [×]    │
└──────────────────────────────────────────────────────────────────┘
```

### After Saving General Settings:
```
┌──────────────────────────────────────────────────────────────────┐
│  ✓  General Settings saved successfully!                  [×]    │
└──────────────────────────────────────────────────────────────────┘
```

## Image Generation Page - With Warning

```
┌──────────────────────────────────────────────────────────────────────┐
│  Image Generation                                                    │
├──────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │  ⚠  Gemini Image API key is not configured.                   │ │
│  │     Please configure it in API Credentials.                     │ │
│  │                          ^^^^^^^^^^^^^^^^^^^^^                  │ │
│  │                          (clickable link)                       │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                       │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │  Generate Image                                                 │ │
│  │  ════════════                                                  │ │
│  │  Generate images using Gemini Imagen API and save them to      │ │
│  │  your media library.                                            │ │
│  │                                                                 │ │
│  │  Image Prompt *                                                 │ │
│  │  ┌───────────────────────────────────────────────────────────┐ │ │
│  │  │                                                            │ │ │
│  │  │                                                            │ │ │
│  │  └───────────────────────────────────────────────────────────┘ │ │
│  │  ...                                                            │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

## User Flow Examples

### Scenario 1: Updating Schedule Without Affecting API Keys

1. User navigates to **Settings** → **General Settings** tab
2. Changes "Schedule Frequency" from "Daily" to "Hourly"
3. Clicks "Save General Settings"
4. ✓ Success: "General Settings saved successfully!"
5. API credentials remain unchanged (not even touched)

### Scenario 2: Updating API Key Without Affecting Other Settings

1. User navigates to **Settings** → **API Credentials** tab
2. Updates OpenAI API Key
3. Clicks "Save API Credentials"
4. ✓ Success: "API Credentials saved successfully!"
5. Model settings, prompts, scheduling remain unchanged

### Scenario 3: Switching AI Provider

1. User navigates to **Settings** → **API Credentials** tab
2. Changes "AI Provider" from "OpenAI" to "Gemini"
3. JavaScript automatically:
   - Hides OpenAI fields
   - Shows Gemini fields
4. User enters Gemini API key
5. Clicks "Save API Credentials"
6. User goes to **General Settings** tab
7. JavaScript automatically:
   - Hides OpenAI model settings
   - Shows Gemini model settings
8. User configures Gemini model
9. Clicks "Save General Settings"
10. ✓ Both tabs saved independently!

## Key Features Illustrated

✅ **Tabbed Navigation**: Clear, standard WordPress tabs
✅ **Independent Forms**: Each tab has its own save button
✅ **Field Visibility**: Provider-specific fields shown/hidden automatically
✅ **Success Messages**: Specific to what was saved
✅ **Helpful Links**: Warning notices link to relevant settings
✅ **Professional UI**: Consistent with WordPress admin design

## Browser Compatibility

The interface uses standard WordPress admin CSS classes:
- `nav-tab-wrapper` for tab container
- `nav-tab` for individual tabs
- `nav-tab-active` for active tab highlight
- `form-table` for form layout
- `notice` for messages

This ensures compatibility across all browsers and WordPress versions.

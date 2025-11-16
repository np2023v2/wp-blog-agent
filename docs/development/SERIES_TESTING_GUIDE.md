# Post Series Feature - Testing Guide

## Overview
This document provides a comprehensive testing guide for the new Post Series feature in WP Blog Agent.

## Feature Description
The Post Series feature allows users to:
1. Create series of related blog posts
2. Get AI-powered topic suggestions based on existing posts in a series
3. Generate posts from suggestions that automatically join the series
4. Manage posts within series (add, remove, reorder)

## Prerequisites
- WordPress 5.0 or higher
- PHP 7.4 or higher
- Active OpenAI or Gemini API key configured
- WP Blog Agent plugin activated

## Test Scenarios

### 1. Database Setup (Automatic on Activation)
**Test**: Verify database tables are created
- [ ] Activate the plugin
- [ ] Check that `wp_blog_agent_series` table exists
- [ ] Check that `wp_blog_agent_series_posts` table exists
- [ ] Verify table structure matches schema in ARCHITECTURE.md

**Expected Result**: Tables created successfully with correct structure

### 2. Series Management

#### 2.1 Create Series
**Test**: Create a new series
- [ ] Navigate to Blog Agent → Series
- [ ] Fill in Series Name: "WordPress Security Guide"
- [ ] Fill in Description: "Complete guide to securing WordPress websites"
- [ ] Click "Create Series"
- [ ] Verify success message appears
- [ ] Verify series appears in the list

**Expected Result**: Series created and listed successfully

#### 2.2 View Series
**Test**: View series details
- [ ] Click "View" on a series
- [ ] Verify series name and description are displayed
- [ ] Verify "Total Posts: 0" is shown for new series
- [ ] Verify "Get AI Suggestions" button is disabled or shows appropriate message

**Expected Result**: Series details page loads correctly

#### 2.3 Delete Series
**Test**: Delete a series
- [ ] Create a test series
- [ ] Click "Delete" on the series
- [ ] Confirm deletion
- [ ] Verify success message
- [ ] Verify series is removed from list

**Expected Result**: Series deleted successfully (posts remain in WordPress)

### 3. Adding Posts to Series

#### 3.1 Add Existing Post
**Test**: Add an existing generated post to series
- [ ] Navigate to a series detail page
- [ ] Scroll to "Add Existing Post to Series" section
- [ ] Select a post from dropdown
- [ ] Click "Add to Series"
- [ ] Verify success message
- [ ] Verify post appears in series posts list

**Expected Result**: Post added to series successfully

#### 3.2 Remove Post from Series
**Test**: Remove a post from series
- [ ] View a series with at least one post
- [ ] Click "Remove" on a post
- [ ] Confirm removal
- [ ] Verify success message
- [ ] Verify post is removed from series list
- [ ] Verify post still exists in WordPress (check Blog Agent → Generated Posts)

**Expected Result**: Post removed from series but not deleted from WordPress

### 4. AI Topic Suggestions

#### 4.1 Get Suggestions (No Posts)
**Test**: Try to get suggestions for empty series
- [ ] Create a new empty series
- [ ] Try to get AI suggestions
- [ ] Verify appropriate message: "Add at least one post to this series to get AI topic suggestions"

**Expected Result**: Clear message indicating at least one post is needed

#### 4.2 Get Suggestions (With Posts)
**Test**: Get AI suggestions for series with posts
- [ ] View a series with at least one post
- [ ] Click "Get AI Suggestions" button
- [ ] Verify button shows "Loading..." during request
- [ ] Wait for AI response
- [ ] Verify 5 topic suggestions are displayed
- [ ] Verify each suggestion has a radio button
- [ ] Verify suggestions are relevant to existing posts

**Expected Result**: AI generates relevant topic suggestions based on series theme

#### 4.3 Generate from Suggestion
**Test**: Generate a post from suggested topic
- [ ] Get AI suggestions for a series
- [ ] Select one of the suggested topics
- [ ] Click "Generate Selected Topic"
- [ ] Verify redirect to series page
- [ ] Verify success message with post edit/view links
- [ ] Verify new post appears in series posts list
- [ ] Verify post content is generated
- [ ] Check that post metadata includes series_id

**Expected Result**: Post generated and automatically added to series

### 5. Integration Tests

#### 5.1 Series Metadata on Posts
**Test**: Verify series metadata is stored correctly
- [ ] Generate a post from series suggestion
- [ ] Go to Blog Agent → Generated Posts
- [ ] Edit the generated post in WordPress
- [ ] Check custom fields for `_wp_blog_agent_series_id`
- [ ] Verify it matches the series ID

**Expected Result**: Series ID stored in post metadata

#### 5.2 Multi-Series Workflow
**Test**: Create multiple series and manage them
- [ ] Create 3 different series with different themes
- [ ] Add posts to each series
- [ ] Get suggestions for each series
- [ ] Generate posts from suggestions
- [ ] Verify each series maintains its own posts
- [ ] Verify suggestions are different for each series

**Expected Result**: Multiple series work independently

#### 5.3 Auto-Publish Setting
**Test**: Verify auto-publish setting is respected
- [ ] Set auto-publish to "No" in settings
- [ ] Generate a post from series suggestion
- [ ] Verify post is created as draft
- [ ] Change auto-publish to "Yes"
- [ ] Generate another post
- [ ] Verify post is published

**Expected Result**: Auto-publish setting affects series-generated posts

### 6. Error Handling

#### 6.1 Empty Topic Selection
**Test**: Try to generate without selecting a topic
- [ ] Get AI suggestions
- [ ] Click "Generate Selected Topic" without selecting a radio button
- [ ] Verify HTML5 validation prevents submission

**Expected Result**: Form validation prevents empty submission

#### 6.2 API Error Handling
**Test**: Handle API errors gracefully
- [ ] Temporarily set invalid API key
- [ ] Try to get AI suggestions
- [ ] Verify error message is displayed
- [ ] Verify no JavaScript errors in console
- [ ] Restore valid API key

**Expected Result**: Clear error message shown to user

#### 6.3 Invalid Series ID
**Test**: Handle invalid series access
- [ ] Try to access series with invalid ID (e.g., ?view=99999)
- [ ] Verify error message: "Series not found"
- [ ] Verify back button to series list

**Expected Result**: Graceful error handling for invalid IDs

### 7. UI/UX Tests

#### 7.1 Series List Display
**Test**: Verify series list is user-friendly
- [ ] Create series with varying description lengths
- [ ] Check that long descriptions are truncated
- [ ] Verify post counts are displayed
- [ ] Verify creation dates are formatted correctly

**Expected Result**: Clean, readable series list

#### 7.2 AJAX Interaction
**Test**: Verify smooth AJAX interactions
- [ ] Click "Get AI Suggestions"
- [ ] Verify button is disabled during loading
- [ ] Verify loading text appears
- [ ] Verify suggestions appear without page reload
- [ ] Check browser console for errors

**Expected Result**: Smooth AJAX experience without page reloads

#### 7.3 Responsive Design
**Test**: Check mobile responsiveness
- [ ] View series page on mobile device or responsive mode
- [ ] Verify forms are usable
- [ ] Verify tables are readable
- [ ] Verify buttons are clickable

**Expected Result**: Mobile-friendly interface

### 8. Security Tests

#### 8.1 Nonce Verification
**Test**: Verify nonce protection
- [ ] Try to submit forms without nonce (using browser dev tools)
- [ ] Verify WordPress nonce verification prevents submission

**Expected Result**: All forms protected by nonces

#### 8.2 Capability Checks
**Test**: Verify permission requirements
- [ ] Log in as user without 'manage_options' capability
- [ ] Try to access series page
- [ ] Verify access denied

**Expected Result**: Only administrators can manage series

#### 8.3 Input Sanitization
**Test**: Verify input sanitization
- [ ] Try to create series with HTML/JavaScript in name
- [ ] Verify HTML is escaped when displayed
- [ ] Try SQL injection patterns in forms
- [ ] Verify no database errors

**Expected Result**: All inputs properly sanitized

### 9. Performance Tests

#### 9.1 Large Series
**Test**: Handle series with many posts
- [ ] Create series with 50+ posts
- [ ] View series detail page
- [ ] Verify page loads in reasonable time
- [ ] Verify all posts are displayed

**Expected Result**: Good performance even with many posts

#### 9.2 AI Suggestion Speed
**Test**: Measure AI suggestion response time
- [ ] Click "Get AI Suggestions"
- [ ] Time the response
- [ ] Verify reasonable response time (under 30 seconds)

**Expected Result**: Suggestions generated within acceptable timeframe

## Known Limitations

1. AI suggestions quality depends on:
   - Quality of existing post titles
   - Number of posts in series (minimum 1 required)
   - AI provider's capabilities

2. Post position tracking:
   - Posts are ordered by addition time
   - Manual reordering not yet implemented

3. Series deletion:
   - Deletes series metadata only
   - Posts remain in WordPress

## Troubleshooting

### Issue: Suggestions Not Generated
**Check**:
- Valid API key configured
- At least one post in series
- Check logs (Blog Agent → Logs) for errors
- Verify internet connection

### Issue: Post Not Added to Series
**Check**:
- Check post metadata for `_wp_blog_agent_series_id`
- Check database `wp_blog_agent_series_posts` table
- Review logs for errors

### Issue: AJAX Not Working
**Check**:
- Browser console for JavaScript errors
- WordPress admin AJAX URL is correct
- Nonce is generated correctly

## Conclusion

This feature enables content creators to build comprehensive, AI-assisted content series efficiently. All tests should pass before considering the feature production-ready.

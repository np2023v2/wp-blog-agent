# Test Plan: Manual Post Selection and Rewrite Features

## Overview
This document provides a comprehensive test plan for the new features added to the Post Series functionality:
1. Allow users to add ANY post to a series (manual or AI-generated)
2. Allow users to rewrite posts using AI

## Prerequisites
- WordPress installation with WP Blog Agent plugin activated
- Valid API key configured (OpenAI, Gemini, or Ollama)
- At least one post series created
- Mix of AI-generated and manually created posts in WordPress

## Test Cases

### 1. Add Manual Post to Series

#### Test 1.1: View All Posts Option
**Steps:**
1. Navigate to Blog Agent → Series
2. Click "View" on an existing series
3. Scroll to "Add Existing Post to Series" section
4. In the "Filter Posts" dropdown, select "All Posts"
5. Click the "Select Post" dropdown

**Expected Result:**
- Both AI-generated and manual posts should be visible
- Posts should be organized into two optgroups: "AI Generated Posts" and "Manual Posts"
- Manual posts should be clearly visible and selectable

#### Test 1.2: Filter AI-Generated Posts
**Steps:**
1. In the "Filter Posts" dropdown, select "AI Generated Posts"
2. Check the "Select Post" dropdown

**Expected Result:**
- Only AI-generated posts should be visible
- The "Manual Posts" optgroup should be hidden
- Filter should work without page reload

#### Test 1.3: Filter Manual Posts
**Steps:**
1. In the "Filter Posts" dropdown, select "Manual Posts"
2. Check the "Select Post" dropdown

**Expected Result:**
- Only manually created posts should be visible
- The "AI Generated Posts" optgroup should be hidden
- Filter should work without page reload

#### Test 1.4: Add Manual Post
**Steps:**
1. Set filter to "All Posts" or "Manual Posts"
2. Select a manually created post from the dropdown
3. Click "Add to Series" button

**Expected Result:**
- Success message: "Post added to series!"
- Manual post should appear in the series posts list
- Post should be added at the end of the series

#### Test 1.5: Verify Manual Post in Series
**Steps:**
1. Check the "Posts in this Series" table
2. Find the manually added post

**Expected Result:**
- Post should be listed with correct title
- Status should be displayed correctly
- Edit, View, Rewrite, and Remove buttons should be available

### 2. Post Rewrite Functionality

#### Test 2.1: Rewrite Post - Confirmation Dialog
**Steps:**
1. Navigate to a series detail page with posts
2. Click the "Rewrite" button on any post
3. Observe the confirmation dialog

**Expected Result:**
- Confirmation dialog appears with message: "Are you sure you want to rewrite this post? The existing content will be replaced with new AI-generated content."
- User can cancel or confirm

#### Test 2.2: Rewrite Post - Queue Success
**Steps:**
1. Click "Rewrite" button on a post
2. Confirm the dialog
3. Wait for page reload

**Expected Result:**
- Success message: "Post rewrite has been queued! The post will be regenerated asynchronously."
- Link to "View Queue" should be present
- Original post should still be visible in the series

#### Test 2.3: Rewrite Post - Queue Processing
**Steps:**
1. After queueing a rewrite, navigate to Blog Agent → Queue
2. Check for the rewrite task

**Expected Result:**
- Task should be visible with trigger_source = "rewrite"
- Task status should progress from "pending" to "processing" to "completed"
- Topic text should show the original post title

#### Test 2.4: Rewrite Post - Content Updated
**Steps:**
1. Note the original post title and content
2. After rewrite completes, view the post in WordPress
3. Compare title and content

**Expected Result:**
- Post ID remains the same
- Title may have changed (new AI-generated title)
- Content has been completely regenerated
- Post excerpt has been updated
- Post status remains unchanged
- Featured image may be regenerated if auto-generate is enabled

#### Test 2.5: Rewrite Post - Metadata
**Steps:**
1. After rewrite completes, check post metadata
2. Look for `_wp_blog_agent_rewritten` and `_wp_blog_agent_rewrite_date` meta keys

**Expected Result:**
- `_wp_blog_agent_generated` = true
- `_wp_blog_agent_provider` = current AI provider
- `_wp_blog_agent_rewritten` = true
- `_wp_blog_agent_rewrite_date` = current timestamp
- Series association remains intact

#### Test 2.6: Rewrite Post - Auto Features
**Steps:**
1. Enable "Auto Generate Featured Image" and "Auto Generate RankMath SEO" in settings
2. Rewrite a post
3. After completion, check the post

**Expected Result:**
- New featured image generated if enabled
- RankMath SEO metadata generated if enabled
- Inline images processed if enabled

### 3. Error Handling

#### Test 3.1: Invalid Post ID
**Steps:**
1. Manually construct URL with invalid post ID
2. Navigate to: `/wp-admin/admin-post.php?action=wp_blog_agent_rewrite_post&series_id=1&post_id=999999&_wpnonce=[valid_nonce]`

**Expected Result:**
- Error message: "Post not found"
- User redirected back to series page

#### Test 3.2: Queue Failure
**Steps:**
1. Simulate queue failure (e.g., database issue)
2. Attempt to rewrite a post

**Expected Result:**
- Error message: "Failed to queue post rewrite"
- User redirected back to series page
- Error logged

#### Test 3.3: Rewrite Non-Existent Post
**Steps:**
1. Delete a post from WordPress
2. Try to rewrite the deleted post from series

**Expected Result:**
- Error message: "Post not found"
- Series should handle gracefully

### 4. Security Testing

#### Test 4.1: Nonce Verification
**Steps:**
1. Try to access rewrite action without valid nonce
2. Navigate to: `/wp-admin/admin-post.php?action=wp_blog_agent_rewrite_post&series_id=1&post_id=1`

**Expected Result:**
- WordPress nonce verification fails
- Access denied

#### Test 4.2: Capability Check
**Steps:**
1. Log in as a user without 'manage_options' capability
2. Try to access series page and rewrite functionality

**Expected Result:**
- Access denied
- "You do not have sufficient permissions" message

#### Test 4.3: XSS Prevention
**Steps:**
1. Try to inject JavaScript in post titles
2. Rewrite a post with malicious title
3. View series page

**Expected Result:**
- All output properly escaped
- No JavaScript execution
- Post title displayed as plain text

### 5. Integration Testing

#### Test 5.1: Rewrite with Auto-Publish Disabled
**Steps:**
1. Set "Auto Publish" to "No" in settings
2. Rewrite a published post

**Expected Result:**
- Post status remains "publish" (existing post)
- Content updated successfully

#### Test 5.2: Rewrite with Scheduling
**Steps:**
1. Enable scheduling
2. Rewrite multiple posts
3. Check queue processing

**Expected Result:**
- Queue processes rewrite tasks correctly
- No interference with scheduled generation
- Tasks processed in order

#### Test 5.3: Add Manual Post and Rewrite
**Steps:**
1. Add a manual post to series
2. Immediately rewrite that post
3. Verify completion

**Expected Result:**
- Manual post added successfully
- Rewrite queued and processed
- Post now has AI-generated content
- Post marked as `_wp_blog_agent_generated`

### 6. UI/UX Testing

#### Test 6.1: Filter Performance
**Steps:**
1. Create series with 50+ posts
2. Use post filter to switch between options

**Expected Result:**
- Filter responds instantly
- No page reload required
- Smooth user experience

#### Test 6.2: Button States
**Steps:**
1. Check all buttons on series detail page
2. Verify button labels and styles

**Expected Result:**
- "Rewrite" button clearly visible
- Consistent button styling
- Clear separation between actions

#### Test 6.3: Mobile Responsiveness
**Steps:**
1. Access series page on mobile device
2. Try to add manual post and rewrite

**Expected Result:**
- Filter dropdown works on mobile
- Buttons accessible and clickable
- Confirmation dialogs display properly

## Test Matrix

| Feature | Test Case | Status | Notes |
|---------|-----------|--------|-------|
| Manual Post Selection | View All Posts | ⏳ Pending | |
| Manual Post Selection | Filter AI Posts | ⏳ Pending | |
| Manual Post Selection | Filter Manual Posts | ⏳ Pending | |
| Manual Post Selection | Add Manual Post | ⏳ Pending | |
| Post Rewrite | Confirmation Dialog | ⏳ Pending | |
| Post Rewrite | Queue Success | ⏳ Pending | |
| Post Rewrite | Content Updated | ⏳ Pending | |
| Post Rewrite | Metadata Updated | ⏳ Pending | |
| Error Handling | Invalid Post ID | ⏳ Pending | |
| Error Handling | Queue Failure | ⏳ Pending | |
| Security | Nonce Verification | ⏳ Pending | |
| Security | Capability Check | ⏳ Pending | |
| Integration | Auto Features | ⏳ Pending | |
| UI/UX | Filter Performance | ⏳ Pending | |

## Known Limitations

1. **No Undo**: Once a post is rewritten, there's no built-in undo feature. Users should backup important content.
2. **Async Only**: Rewrite is asynchronous - users must wait for queue processing.
3. **Title Changes**: AI may generate a different title than expected.
4. **Content Loss**: Original content is completely replaced.

## Recommendations

1. **Backup First**: Users should backup posts before rewriting
2. **Monitor Queue**: Check queue page during rewrite operations
3. **Test Provider**: Ensure AI provider is working before bulk rewrites
4. **Review Content**: Always review rewritten content before publishing

## Conclusion

All test cases should be executed and pass before considering the feature production-ready. Any failures should be documented and addressed.

---

**Test Plan Version:** 1.0  
**Created:** 2024-11-09  
**Last Updated:** 2024-11-09  
**Created By:** GitHub Copilot

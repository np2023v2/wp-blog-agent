# Settings Optimization Test Plan

## Overview
This document outlines the test plan for verifying that the settings optimization works correctly and that API credentials and general settings can be updated independently without affecting each other.

## Test Cases

### Test Case 1: API Credentials Tab
**Objective**: Verify that API credentials can be saved independently

**Steps**:
1. Navigate to WP Blog Agent > Settings
2. Verify that "API Credentials" tab is active by default
3. Update the following fields:
   - AI Provider (change to different provider)
   - OpenAI API Key
   - OpenAI Base URL
   - Gemini API Key
   - Gemini Image API Key
   - Ollama Base URL
4. Click "Save API Credentials"
5. Verify success message appears
6. Navigate to "General Settings" tab
7. Verify that general settings (models, prompts, scheduling) remain unchanged

**Expected Result**: Only API credentials are updated, general settings remain unchanged

---

### Test Case 2: General Settings Tab
**Objective**: Verify that general settings can be saved independently

**Steps**:
1. Navigate to WP Blog Agent > Settings
2. Click on "General Settings" tab
3. Update the following fields:
   - OpenAI Model
   - OpenAI Max Tokens
   - OpenAI System Prompt
   - Gemini Model
   - Schedule Enabled
   - Schedule Frequency
   - Auto Publish
   - Auto Generate Featured Image
4. Click "Save General Settings"
5. Verify success message appears
6. Navigate to "API Credentials" tab
7. Verify that API credentials remain unchanged

**Expected Result**: Only general settings are updated, API credentials remain unchanged

---

### Test Case 3: Provider Field Visibility
**Objective**: Verify that fields are shown/hidden based on selected AI provider

**Steps**:
1. Navigate to WP Blog Agent > Settings (API Credentials tab)
2. Select "OpenAI (GPT)" from AI Provider dropdown
3. Verify OpenAI API Key and Base URL are visible
4. Verify Gemini and Ollama fields are hidden
5. Navigate to "General Settings" tab
6. Verify OpenAI Model, Max Tokens, and System Prompt are visible
7. Verify Gemini and Ollama model fields are hidden
8. Return to "API Credentials" tab
9. Select "Google Gemini" from AI Provider dropdown
10. Verify Gemini API Keys are visible, OpenAI fields are hidden
11. Navigate to "General Settings" tab
12. Verify Gemini model fields are visible, OpenAI/Ollama fields are hidden

**Expected Result**: Correct fields are shown/hidden based on provider across both tabs

---

### Test Case 4: Image Generation Page Integration
**Objective**: Verify that Image Generation page no longer has redundant settings

**Steps**:
1. Navigate to WP Blog Agent > Image Generation
2. Verify there is NO settings form for Gemini Image API Key
3. Verify there is a warning/notice with a link to API Credentials tab if API key is not configured
4. Click the link in the notice
5. Verify it navigates to Settings > API Credentials tab

**Expected Result**: No redundant settings form, helpful link to API Credentials tab

---

### Test Case 5: Settings Persistence
**Objective**: Verify that settings are properly saved and loaded

**Steps**:
1. Save API credentials in API Credentials tab
2. Navigate away from the page (e.g., to Topics page)
3. Return to Settings > API Credentials tab
4. Verify all API credentials are still present and correct
5. Navigate to Settings > General Settings tab
6. Save general settings
7. Navigate away and return
8. Verify all general settings are still present and correct

**Expected Result**: All settings persist correctly across page navigation

---

## Manual Verification Steps

### Visual Inspection
1. Check that tab navigation is visible and clear
2. Verify that active tab is highlighted
3. Ensure form layout is consistent and professional
4. Verify that field descriptions are helpful and accurate

### JavaScript Functionality
1. Open browser console (F12)
2. Navigate between tabs
3. Change AI provider dropdown
4. Verify no JavaScript errors in console
5. Verify smooth field visibility transitions

### Security
1. Verify that nonce fields are present in both forms
2. Check that API keys are properly sanitized on save
3. Ensure URL fields use esc_url_raw()
4. Verify that only users with 'manage_options' capability can access settings

---

## Success Criteria

All test cases pass with the following results:
- ✅ Settings can be saved independently in each tab
- ✅ Saving one tab does not affect the other tab's settings
- ✅ Provider-based field visibility works correctly across tabs
- ✅ No redundant settings on Image Generation page
- ✅ Settings persist correctly
- ✅ No JavaScript errors
- ✅ All security measures in place
- ✅ Professional UI/UX with clear tab navigation

---

## Notes

- This optimization addresses the issue of API credentials being reset when updating other settings
- The WordPress Settings API is properly utilized with separate option groups
- Each form has its own nonce for security
- The implementation maintains backward compatibility with existing settings

# Fix Screenshot Upload to Invoi Tasks - Complete Prompt

## Problem Summary

I have a script that captures screenshots of web pages using Playwright and attempts to upload them to tasks in the Invoi project management system via the Agent API. The screenshots are being captured successfully and saved locally, but the upload to tasks isn't working properly.

## What Happened

I created a script (`capture-and-upload-screenshots.mjs`) that:
1. ✅ Successfully captures full-page screenshots of 5 service pages using Playwright
2. ✅ Saves screenshots locally to `./screenshots/` directory (5 PNG files, 1.4MB - 2.1MB each)
3. ❌ Attempts to upload screenshots to tasks via API comments with base64 data URIs

## Current Implementation

The script currently tries to embed screenshots in task comments using markdown image syntax with base64 data URIs:

```javascript
await agentFetch(`/tasks/${taskUuid}/comments`, {
  method: 'POST',
  body: {
    comment_text: `📸 **Page Screenshot**\n\n![Screenshot](data:image/png;base64,${base64Image})`
  }
});
```

## Why It Didn't Work

The base64 image embedding in markdown comments doesn't work - the images don't display in the task interface. This is likely because:
- The Invoi Agent API doesn't support base64 data URIs in markdown comments
- The comment system doesn't render inline images from data URIs
- File attachments need to be uploaded via a different endpoint/method
- Base64 images in markdown are often stripped or not supported by many systems

## Current State

- ✅ Screenshots are successfully captured and saved locally in `./screenshots/` directory
- ✅ Comments were added to tasks (but images probably didn't display)
- ❌ Screenshots are not visible in the task interface

## What I Need

Please help me fix the screenshot upload functionality. I need you to:

1. **Check the Invoi Agent API documentation** for:
   - File upload endpoints (if they exist)
   - Task attachment endpoints
   - Proper way to attach files/images to tasks
   - Multipart/form-data upload support

2. **Implement one of these solutions:**
   - **Option A**: If API supports file uploads, implement proper multipart/form-data file upload
   - **Option B**: If API doesn't support direct uploads, integrate with an image hosting service (S3, Cloudinary, Imgur, etc.) and link to hosted images
   - **Option C**: If neither works, create a solution that makes it easy to manually attach the screenshots (e.g., generate a summary with file paths and instructions)

3. **Update the script** (`capture-and-upload-screenshots.mjs`) to successfully attach screenshots to tasks so they're visible in the Invoi interface

## API Details

- **Base URL**: `https://app.noteware.dev/api/agent`
- **Authentication**: Headers `x-agent-key` and `x-user-id` (from `.env` file)
- **Current endpoints used**:
  - `POST /tasks/{uuid}/comments` - Adding comments
  - `GET /projects/{id}/tasks` - Fetching tasks
  - `PATCH /tasks/{uuid}` - Updating task status

## Files Involved

- `capture-and-upload-screenshots.mjs` - The current script that needs to be fixed
- `./screenshots/` - Directory with 5 PNG files:
  - `task-335-*.png` (SSHO Services) - 1.4MB
  - `task-336-*.png` (Lead Compliance Plan) - 1.6MB
  - `task-337-*.png` (Caltrans Construction Safety) - 1.7MB
  - `task-338-*.png` (Federal Contracting) - 504KB
  - `task-339-*.png` (Construction Safety Consulting) - 2.1MB
- Tasks are in Project ID 6, Task IDs: 335, 336, 337, 338, 339

## Requirements

- Screenshots should be visible/accessible in the task interface
- Solution should be automated (not require manual steps)
- Should handle 5 screenshots (1.4MB - 2.1MB each)
- Should work with the existing Invoi Agent API
- Script should use environment variables from `.env` file for API credentials

## Questions to Answer

1. Does the Invoi Agent API have a file upload endpoint?
2. What's the proper way to attach files/images to tasks?
3. Should I use a third-party image hosting service?
4. What's the best approach given the API limitations?

## Expected Outcome

After the fix, when I run the script:
- Screenshots should be captured (already working)
- Screenshots should be successfully uploaded/attached to their respective tasks
- Screenshots should be visible in the Invoi task interface
- Each task should have a visible screenshot that can be viewed/downloaded

Please investigate the API capabilities and implement a working solution for uploading screenshots to tasks.

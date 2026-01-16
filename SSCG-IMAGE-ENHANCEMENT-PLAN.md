# SSCG Plugin - Image Search Enhancement Plan

## Objective
Enhance image gathering to use both Pixabay and Pexels APIs, increase image choices, and ensure proper SEO attributes (alt text, title tags) are added to all images.

## Current State
- **Pexels**: Implemented, returns 6 images per search (per_page = 6)
- **Pixabay**: API key configured and tested, but NOT used in actual image search
- **Alt Text**: Set via `_wp_attachment_image_alt` meta, but may need SEO enhancement
- **Title Tags**: Not currently set on images

## Implementation Tasks

### 1. Create Pixabay Image Search Class
**File**: `includes/class-sscg-pixabay.php` (NEW)
- Mirror structure of `class-sscg-pexels.php`
- Implement `search($query, $per_page = 15)` method
- Implement `upload_image($image_url, $post_id, $alt_text = '')` method
- Implement `set_featured_image($attachment_id, $post_id)` method
- Parse Pixabay API response format
- Extract/generate SEO-friendly alt text

### 2. Enhance Image Search to Use Both APIs
**File**: `includes/class-sscg-ajax.php`
- Modify `sscg_search_images()` AJAX handler
- Search both Pexels (15 images) AND Pixabay (15 images) in parallel
- Combine results = 30 total image choices
- Mark each result with source for user visibility

### 3. Improve Alt Text Generation
**Files**: Both Pexels and Pixabay classes
- Use page context/keywords to generate descriptive alt text
- Format: "[Context] - [Description] - [Source]"
- Ensure 125-150 characters, includes keywords naturally
- Fallback to API-provided alt text

### 4. Add SEO Attributes to Images
**Files**: Both Pexels and Pixabay upload_image methods
- `_wp_attachment_image_alt` - Enhanced with keywords
- `_wp_attachment_image_title` - Add title attribute
- SEO-friendly filenames: `pexels-{id}-{keyword}.jpg`

### 5. Update Plugin Initialization
**File**: `sscg.php`
- Require new `class-sscg-pixabay.php` file

## Expected Outcome
- 30 image choices (15 from each API)
- All images have proper alt text with keywords
- All images have title attributes
- Better SEO for generated content

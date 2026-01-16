# Featured Image Improvements

**Date:** January 15, 2026
**Status:** ✅ Complete

## Changes Made

### 1. Enhanced Featured Image Retrieval

**File:** `inc/frontend/home-page-functions.php`

**Function:** `ehs_get_latest_posts()`

**Improvements:**
- ✅ **Size Priority:** Attempts to get `large` size first, falls back to `medium`
- ✅ **Placeholder Fallback:** Uses SVG placeholder if no featured image is set
- ✅ **Better Alt Text:** More descriptive alt text for accessibility
- ✅ **Lazy Loading:** Added `loading="lazy"` attribute for performance

**Code:**
```php
// Get featured image (post thumbnail) - try large size first, fallback to medium
$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'large');
if (!$thumbnail) {
    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
}
// Fallback to a placeholder if no featured image is set
if (!$thumbnail) {
    $thumbnail = get_stylesheet_directory_uri() . '/assets/images/placeholder-blog.svg';
}
```

### 2. Updated Article Card Rendering

**Function:** `ehs_homepage_render_article_card()`

**Improvements:**
- ✅ **Always Shows Image:** Removed conditional check - always displays image div
- ✅ **Better Alt Text:** "Featured image for [Post Title]" instead of just title
- ✅ **Lazy Loading:** Performance optimization for below-fold images

**Before:**
```php
<?php if (!empty($post['thumbnail'])): ?>
    <div class="article-card__image">
        <img src="..." alt="<?php echo esc_attr($post['title']); ?>">
    </div>
<?php endif; ?>
```

**After:**
```php
<div class="article-card__image">
    <img src="<?php echo esc_url($post['thumbnail']); ?>"
         alt="Featured image for <?php echo esc_attr($post['title']); ?>"
         loading="lazy">
</div>
```

### 3. Created Placeholder Image

**File:** `assets/images/placeholder-blog.svg`

**Features:**
- ✅ Navy (#003366) and Gold (#FFB81C) brand colors
- ✅ Document/blog icon design
- ✅ "EHS Analytical Blog Post" text
- ✅ Responsive SVG format (800×500px viewBox)
- ✅ Lightweight (< 1KB)

**Design:**
- Light gray background (#F5F5F5)
- Navy accent bars top and bottom
- Centered document icon with gold circle accent
- Professional, on-brand appearance

## How It Works

### Blog Posts WITH Featured Images
1. WordPress retrieves the post's featured image
2. Tries to get `large` size (typically 1024×1024px or larger)
3. Falls back to `medium` size (300×300px) if large not available
4. Displays image in 180px tall container with `object-fit: cover`
5. Image scales to 105% on hover for visual interest

### Blog Posts WITHOUT Featured Images
1. No featured image set in WordPress
2. Function detects empty thumbnail
3. Automatically uses `placeholder-blog.svg`
4. Placeholder displays in same 180px container
5. Maintains consistent layout across all posts

## CSS Styling (Already Implemented)

**Desktop (style.css:896-918):**
```css
.article-card__image {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
}

.article-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.article-card:hover .article-card__image img {
    transform: scale(1.05);
}
```

**Mobile (style.css:1156-1158):**
```css
@media (max-width: 768px) {
    .article-card__image {
        height: 160px;
    }
}
```

## Benefits

### Performance
- ✅ **Lazy Loading:** Images only load when scrolling into viewport
- ✅ **Optimized Sizes:** Uses appropriate WordPress image size (large vs medium)
- ✅ **SVG Fallback:** Tiny placeholder (~1KB) vs large stock photos

### User Experience
- ✅ **Consistent Layout:** All posts show images (no layout shift)
- ✅ **Professional Appearance:** Branded placeholder matches site design
- ✅ **Visual Hierarchy:** Featured images draw attention to latest content

### Accessibility
- ✅ **Descriptive Alt Text:** "Featured image for [Post Title]"
- ✅ **Semantic HTML:** Proper image markup with attributes
- ✅ **Screen Reader Friendly:** Context provided for visually impaired users

### Content Management
- ✅ **No Required Setup:** Works immediately for posts with/without images
- ✅ **Flexible:** Automatically uses best available image size
- ✅ **Failsafe:** Never shows broken image icons

## Testing Checklist

- [ ] Create test post WITH featured image
- [ ] Create test post WITHOUT featured image
- [ ] View home page "Latest Resources" section
- [ ] Verify featured image displays for post with image
- [ ] Verify placeholder displays for post without image
- [ ] Test lazy loading (scroll to section, images should load)
- [ ] Check mobile responsive (160px height)
- [ ] Verify hover effect (1.05x scale on desktop)
- [ ] Test screen reader (alt text should be descriptive)

## WordPress Setup (For Reference)

**To Set Featured Image on a Post:**
1. Edit post in WordPress admin
2. Right sidebar → "Featured Image" section
3. Click "Set featured image"
4. Upload or select from media library
5. Click "Set featured image" button
6. Update/Publish post

**WordPress automatically generates these sizes:**
- Thumbnail: 150×150px (cropped)
- Medium: 300×300px (max dimensions)
- Large: 1024×1024px (max dimensions)
- Full: Original upload size

## Files Modified

1. ✅ `inc/frontend/home-page-functions.php` - Enhanced image retrieval and rendering
2. ✅ `assets/images/placeholder-blog.svg` - Created placeholder image

## Files Referenced (No Changes)

- `style.css` - Article card image styling already optimal (lines 896-918, 1156-1158)

---

**Result:** Featured images now display beautifully with smart fallbacks and optimal performance! 🎨

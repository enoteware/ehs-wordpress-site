# Credential Cards Usage Guide

## Overview

Credential cards follow the EHS design system and display credentials in a responsive grid layout. Cards include featured image, acronym badge, title, issuing organization, category badge, excerpt, and link.

## CSS Classes

All credential card styles are in `style.css` following the design system:

- `.credential-card` - Main card container
- `.credential-card__image` - Image/logo container
- `.credential-card__content` - Card content wrapper
- `.credential-card__acronym` - Gold badge with acronym
- `.credential-card__title` - Card title (H3)
- `.credential-card__issuing-org` - Issuing organization text
- `.credential-card__category` - Category badge (color-coded)
- `.credential-card__excerpt` - Card excerpt text
- `.credential-card__link` - "Learn More" link
- `.credentials-grid` - Responsive grid container

## Usage Methods

### Method 1: Shortcode (Recommended for Elementor)

Add a Shortcode widget in Elementor and use:

```
[credentials_grid]
```

**Options:**
- `posts_per_page="9"` - Number of credentials to show (default: all)
- `featured="true"` - Show only featured credentials
- `category="Professional Certification"` - Filter by category
- `orderby="title"` - Order by title, date, etc.
- `order="ASC"` - Sort order

**Examples:**
```
[credentials_grid posts_per_page="6" featured="true"]
[credentials_grid category="Professional Certification"]
[credentials_grid posts_per_page="9" orderby="title" order="ASC"]
```

### Method 2: PHP Function

In a page template or PHP widget:

```php
<?php
// Render all credentials
ehs_render_credentials_grid();

// Render with custom query
ehs_render_credentials_grid(array(
    'posts_per_page' => 6,
    'meta_query' => array(
        array(
            'key' => 'credential_featured',
            'value' => '1',
            'compare' => '='
        )
    )
));
?>
```

### Method 3: Individual Card

```php
<?php
// Render single credential card
$credential = get_post(3360); // Post ID
ehs_render_credential_card($credential);
?>
```

### Method 4: Get Featured Credentials Array

```php
<?php
$featured = ehs_get_featured_credentials(6);
echo '<div class="credentials-grid">';
foreach ($featured as $credential) {
    ehs_render_credential_card($credential);
}
echo '</div>';
?>
```

## Design System Compliance

✅ **Colors:** Uses CSS variables (`var(--ehs-navy)`, `var(--ehs-gold)`, etc.)  
✅ **Typography:** Maven Pro font family, proper weights and sizes  
✅ **Spacing:** Follows spacing scale (20px, 24px, 30px, etc.)  
✅ **Shadows:** Consistent box-shadow values  
✅ **Hover Effects:** Lift animation with enhanced shadow  
✅ **Responsive:** Mobile-first grid layout  

## Card Structure

Each card displays:
1. **Featured Image** - Logo/certification badge (180px height)
2. **Acronym Badge** - Gold background, navy text (e.g., "CIH", "CSP")
3. **Title** - Full credential name (H3, navy blue)
4. **Issuing Organization** - Organization name (small gray text)
5. **Category Badge** - Color-coded by type:
   - Professional Certification (blue)
   - Business Designation (red)
   - License (green)
   - Affiliation (purple)
6. **Excerpt** - Brief description
7. **Learn More Link** - Link to full credential page with arrow icon

## Responsive Behavior

- **Desktop (992px+):** 3-4 columns, full grid
- **Tablet (576px-991px):** 2 columns, adjusted spacing
- **Mobile (<576px):** Single column, stacked cards

## File Locations

- **CSS:** `wordpress/wp-content/themes/hello-elementor-child/style.css`
- **Functions:** `wordpress/wp-content/themes/hello-elementor-child/inc/frontend/credential-cards.php`
- **Example:** `wordpress/wp-content/themes/hello-elementor-child/credential-cards-example.php`

## Next Steps

1. Upload featured images (logos) for each credential in WordPress admin
2. Publish credential posts (currently drafts)
3. Add shortcode to Elementor page: `[credentials_grid]`
4. Customize grid as needed with shortcode parameters

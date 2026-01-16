# Style Guide Assets

This folder contains assets for the EHS Design System style guide documentation.

## Folder Structure

```
style-guide-assets/
├── js/
│   └── mega-menu.js          # Mega menu JavaScript functionality
├── demos/
│   ├── mega-menu-demo.html   # Standalone mega menu demo page
│   └── service-template-demo.html  # Standalone service template demo page
├── navigation.html            # Navigation component (reference)
└── README.md                  # This file
```

## Navigation System

All design system pages include a consistent navigation bar at the top:

**Navigation Items:**
- **Style Guide** - Main style guide page
- **Demos** (dropdown) - Links to demo pages
  - Mega Menu
  - Service Template
- **Quick Reference** - Link to DESIGN_SYSTEM.md

**Features:**
- Sticky navigation (stays at top when scrolling)
- Active page highlighting (current page shown in gold)
- Responsive design (mobile-friendly dropdown)
- Consistent branding (Navy #003366, Gold #FFB81C)
- Keyboard accessible

**Implementation:**
The navigation is embedded directly in each HTML file to ensure it works without server-side includes. All pages share the same navigation structure and styling.

## JavaScript Files

### `js/mega-menu.js`

Complete JavaScript implementation for the mega menu navigation component.

**Features:**
- Desktop hover/click interactions
- Mobile accordion functionality
- Keyboard navigation support
- Click outside to close
- Responsive breakpoint handling
- Accessibility (ARIA attributes, focus management)

**Usage:**
Include in your WordPress theme's `functions.php` or header template:

```php
wp_enqueue_script(
    'ehs-mega-menu',
    get_stylesheet_directory_uri() . '/style-guide-assets/js/mega-menu.js',
    array(),
    '1.0.0',
    true
);
```

Or include directly in HTML:

```html
<script src="style-guide-assets/js/mega-menu.js"></script>
```

**Dependencies:**
- None (vanilla JavaScript)
- Requires mega menu HTML structure with classes: `.menu-item-has-children`, `.mega-menu`, `.mega-menu-column`

**Browser Support:**
- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with polyfills if needed)

## Adding New Assets

When adding new JavaScript files or other assets:

1. Place JavaScript files in `js/` folder
2. Place CSS files in `css/` folder (if needed)
3. Place images in `images/` folder (if needed)
4. Update this README with documentation
5. Reference in `style-guide.html` if applicable

## File Locations

## Demo Pages

### `demos/mega-menu-demo.html`

Standalone interactive demo of the mega menu component.

**Features:**
- Complete header with mega menu
- All CSS and JavaScript self-contained
- Responsive design demonstration
- Interactive hover/click functionality
- No conflicts with main style guide

**Usage:**
Open directly in browser or link from style guide.

### `demos/service-template-demo.html`

Standalone demo of the service page template structure.

**Features:**
- Complete service page layout
- Hero section with overlay
- Two-column layout (sidebar + content)
- Service meta cards
- Table of contents navigation
- Call-to-action section
- All CSS self-contained

**Usage:**
Open directly in browser or link from style guide.

## File Locations

- **Style Guide HTML:** `../style-guide.html`
- **Design System MD:** `../DESIGN_SYSTEM.md`
- **Mega Menu Guide:** `../../../MEGA_MENU_DESIGN_RECOMMENDATIONS.md`

---

**Last Updated:** January 2025

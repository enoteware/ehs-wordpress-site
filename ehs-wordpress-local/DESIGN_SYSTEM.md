# EHS Design System - Quick Reference

**Last Updated:** January 2025  
**Purpose:** Quick reference guide for developers working with the EHS design system

---

## Architecture

**Elementor = Structure | Theme CSS = Styling**

- **Elementor:** Layout, widgets, content organization, responsive breakpoints
- **Theme CSS:** All colors, typography, spacing, effects, visual styling
- **Integration:** Apply CSS classes via Elementor's "Advanced → CSS Classes" field

---

## Brand Colors

### Primary Colors
- **Navy Primary:** `#003366` / `var(--ehs-navy)`
- **Navy Secondary:** `#1E405A`
- **Gold Primary:** `#FFB81C` / `var(--ehs-gold)`
- **Gold Secondary:** `#F6A90F`
- **Gold Dark:** `#d28c0e`
- **Green Accent:** `#157537`

### Neutral Colors
- **White:** `#FFFFFF` / `var(--ehs-white)`
- **Light Gray:** `#F5F5F5` / `var(--ehs-light-gray)`
- **Dark Gray:** `#333333` / `var(--ehs-dark-gray)`

### Usage
```css
/* Use CSS variables when possible */
color: var(--ehs-navy);
background-color: var(--ehs-gold);
```

---

## Typography

### Font Family
- **Headings & Body:** Maven Pro, sans-serif
- **Weights:** 400 (Regular), 600 (Semi-bold), 700 (Bold)

### Heading Hierarchy
- **H1:** 3rem (48px), Weight 700, Color #003366
- **H2:** 2.5rem (40px), Weight 700, Color #003366
- **H3:** 1.75rem (28px), Weight 700, Color #003366
- **H4:** 1.5rem (24px), Weight 700, Color #003366
- **H5:** 1.25rem (20px), Weight 600, Color #003366
- **H6:** 1rem (16px), Weight 600, Color #003366

### Body Text
- **Size:** 1.1rem (18px)
- **Weight:** 400
- **Line Height:** 1.8
- **Color:** #333333

---

## Buttons

### CSS Classes
- `.btn.btn-primary` - Navy background, green hover
- `.btn.btn-secondary` - Gold background, green hover
- `.btn.btn-outline` - Transparent with navy border
- `.btn-sm` - Small size (10px 20px padding, 13px font)
- `.btn-md` - Medium size (15px 30px padding, 15px font)
- `.btn-lg` - Large size (20px 40px padding, 18px font)

### Elementor Usage
1. Add Button widget
2. Go to Advanced → CSS Classes
3. Add: `btn btn-primary btn-md`
4. Leave Style tab colors empty

---

## Forms

### Input Fields
- **Padding:** 12px 16px
- **Border:** 2px solid #e0e0e0
- **Border Radius:** 5px
- **Focus:** Border #003366 with shadow
- **Error:** Border #dc3545
- **Success:** Border #157537

### CSS Classes
- `.form-input` - Text/email inputs
- `.form-textarea` - Textarea fields
- `.form-select` - Select dropdowns
- `.form-label` - Form labels
- `.form-error` - Error state
- `.form-success` - Success state

---

## Cards & Containers

### Card Styles
- **Background:** White (#FFFFFF)
- **Border Radius:** 8px
- **Box Shadow:** 0 2px 12px rgba(0, 0, 0, 0.08)
- **Padding:** 30px
- **Hover:** Lift effect with enhanced shadow

### Container Widths
- **Standard:** max-width: 1200px
- **Narrow:** max-width: 800px (text-heavy)
- **Wide:** max-width: 1400px (full-width sections)

### CSS Classes
- `.card` - Standard card container
- `.card-title` - Card heading
- `.card-content` - Card body text

---

## Spacing System

### Spacing Scale
- **4px** - Tight spacing, icon padding
- **8px** - Small gaps, tight layouts
- **12px** - Form input padding
- **16px** - Standard padding, small margins
- **20px** - Button padding, card gaps
- **24px** - Medium spacing, section gaps
- **32px** - Large spacing, element separation
- **40px** - Section padding, major gaps
- **60px** - Large section spacing
- **80px** - Hero section padding, major sections

### Usage
Always use values from the spacing scale. Avoid arbitrary values like 13px, 17px, etc.

---

## Layout & Grid

### Responsive Breakpoints
- **Mobile:** < 576px (single column, stacked)
- **Tablet:** 576px - 991px (2 columns, adjusted spacing)
- **Desktop:** 992px - 1199px (3-4 columns, standard layout)
- **Large Desktop:** 1200px+ (full grid, maximum width)

### Grid System
- **Columns:** 12-column grid (Elementor default)
- **Gap:** 10px-40px depending on context
- **Container:** max-width: 1200px (standard)

---

## Navigation & Mega Menu

### Design Inspiration

**Recommended Examples:**
- **Asana** (https://asana.com) - Multi-column with featured content, hover over "Product"
- **Segment** (https://segment.com) - Clean category-based navigation, hover over "Product"
- **Plaid** (https://plaid.com) - Minimalist two-panel layout, hover over "Products"
- **HubSpot** (https://www.hubspot.com) - Services organized by category, hover over "Products"
- **Webflow** (https://webflow.com) - Balanced columns with icons, hover over "Product"

### Recommended Design: Multi-Column Services Grid

**Layout Structure:**
- 4-column grid organizing services by category
- Navy background (#003366) with gold (#FFB81C) category headers
- White text links with gold hover states
- Clean, scannable layout perfect for 12+ services

**Category Organization:**
1. **Consulting Services** - EHS Consulting, EHS Staff Outsourcing
2. **Testing & Assessment** - Indoor Air Quality, Mold, Asbestos, Water Damage, Fire & Smoke
3. **Construction Safety** - SSHO Services, Lead Compliance, Caltrans Safety
4. **Federal Services** - Federal Contracting Services

### Mega Menu CSS Classes

```css
/* Mega Menu Container */
.mega-menu {
    background-color: var(--ehs-navy);
    padding: 40px 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border-radius: 0 0 8px 8px;
}

/* Mega Menu Grid (4 columns) */
.mega-menu-content {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Category Headers */
.mega-menu-column-title {
    color: var(--ehs-gold);
    font-family: 'Maven Pro', sans-serif;
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(255, 184, 28, 0.3);
    padding-bottom: 8px;
    white-space: nowrap;
}

/* Menu Links */
.mega-menu-column .sub-menu a {
    color: #FFFFFF;
    font-family: 'Maven Pro', sans-serif;
    font-size: 15px;
    padding: 8px 0;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.mega-menu-column .sub-menu a:hover {
    color: var(--ehs-gold);
    padding-left: 8px;
}
```

### Responsive Behavior

- **Desktop (992px+):** Full 4-column mega menu display
- **Tablet (768px-991px):** 2-column layout, stacked
- **Mobile (<768px):** Accordion-style dropdown, single column

### Implementation Options

1. **Custom CSS/JavaScript** (Recommended)
   - Works with existing `wp_nav_menu()` setup
   - Full control, no plugins required
   - See `MEGA_MENU_DESIGN_RECOMMENDATIONS.md` for complete code

2. **Max Mega Menu Plugin** (Easiest)
   - Visual editor, no coding
   - Responsive by default
   - Free version fully functional

3. **Custom WordPress Walker**
   - Programmatic control
   - Custom HTML structure
   - See `MEGA_MENU_DESIGN_RECOMMENDATIONS.md` for walker class code

### Design Specifications

**Typography:**
- **Category Headers:** Maven Pro, SemiBold (600), 16px, Gold (#FFB81C), `white-space: nowrap`
- **Menu Links:** Maven Pro, Regular (400), 15px, White (#FFFFFF), `white-space: nowrap`
- **Hover State:** Gold (#FFB81C) text with left padding animation
- **No Text Wrapping:** All navigation text uses `white-space: nowrap` to prevent two-line text

**Spacing:**
- **Menu Padding:** 40px vertical, 30px horizontal
- **Column Gap:** 40px
- **Link Spacing:** 8px vertical padding
- **Category Header Margin:** 16px bottom

**Colors:**
- **Background:** Navy (#003366)
- **Text:** White (#FFFFFF)
- **Category Headers:** Gold (#FFB81C)
- **Hover:** Gold (#FFB81C)
- **Border:** Gold with 30% opacity for category dividers

**Animation:**
- **Open/Close:** Slide down with fade (0.3s ease)
- **Hover:** Smooth color transition (0.2s ease)
- **Mobile Accordion:** Smooth height transition (0.3s ease)

### Reference Documentation

- **Complete Guide:** `/MEGA_MENU_DESIGN_RECOMMENDATIONS.md` - Full design options, code examples, and implementation guide
- **Live Examples:** See links above for real-world implementations

---

## Elementor Integration

### Best Practices
1. **Structure in Elementor:**
   - Use Elementor for layout, sections, columns
   - Organize content hierarchy
   - Set responsive breakpoints

2. **Styling in CSS:**
   - Apply CSS classes via "Advanced → CSS Classes"
   - Leave Style tab colors/typography empty
   - Use Style tab only for layout (width, alignment)

3. **CSS Class Application:**
   - Button: `btn btn-primary btn-md`
   - Card: `card`
   - Form input: `form-input`
   - Custom: Add to `style.css` and reference here

### Avoid
- ❌ Setting colors in Elementor Style tab
- ❌ Setting typography in Elementor Style tab
- ❌ Using inline styles
- ❌ Creating one-off styles

### Do
- ✅ Use CSS classes from this guide
- ✅ Add new styles to theme `style.css`
- ✅ Document new CSS classes here
- ✅ Follow spacing scale and color palette

---

## CSS Variables

Available in `:root`:
```css
--ehs-navy: #003366;
--ehs-gold: #FFB81C;
--ehs-light-gray: #F5F5F5;
--ehs-dark-gray: #333333;
--ehs-white: #FFFFFF;
```

---

## File Locations

- **Style Guide (HTML):** `ehs-wordpress-local/style-guide.html`
- **Theme CSS:** `wordpress/wp-content/themes/hello-elementor-child/style.css`
- **Functions:** `wordpress/wp-content/themes/hello-elementor-child/functions.php`
- **Clear Elementor Settings:** `ehs-wordpress-local/clear-elementor-site-settings.php`

---

## Quick Commands

### Clear Elementor Site Settings
```bash
cd ehs-wordpress-local
ddev exec wp eval-file clear-elementor-site-settings.php --path=/var/www/html/wordpress
```

### Regenerate Elementor CSS
```bash
cd ehs-wordpress-local
./regen-css.sh
```

---

## Adding New Styles

1. Add CSS to `style.css` with clear comments
2. Document CSS class in this file
3. Add example to `style-guide.html` if needed
4. Test in Elementor by applying CSS class
5. Update this reference document

---

**For complete visual examples and detailed specifications, see `style-guide.html`**

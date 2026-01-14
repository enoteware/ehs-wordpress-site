# Elementor Font Override Scan Report

## Executive Summary

Scanned all Elementor pages and templates in the EHS WordPress site and identified **33 items** that use widget-based font overrides instead of global fonts. This prevents global font changes from applying properly.

- **Pages with overrides:** 16
- **Templates with overrides:** 17
- **Total items needing updates:** 33

## What Are Font Overrides?

Font overrides occur when Elementor widgets have `typography_typography: "custom"` in their settings, along with specific font properties like:
- `typography_font_size`
- `typography_font_weight`
- `typography_text_transform`
- `typography_font_style`
- `typography_line_height`
- `typography_letter_spacing`
- And responsive variants (`typography_font_size_mobile`, `typography_font_size_tablet`)

## Impact

When widgets use custom typography, they ignore global font settings defined in:
- Elementor → Custom Fonts
- Elementor → Global Fonts
- Theme typography settings

This means changes to global fonts won't apply to these overridden widgets.

## Pages Needing Updates

### High Priority (Many Overrides)
1. **Construction Safety Consulting (ID: 2344)** - 20 overrides
2. **About Us (ID: 115)** - 11 overrides
3. **Thank you (ID: 2546)** - 10 overrides
4. **Home (ID: 92)** - 8 overrides

### Medium Priority (5-7 Overrides)
5. **California Fire and Smoke Assessments (ID: 1200)** - 7 overrides
6. **Industrial Hygiene Services (ID: 655)** - 7 overrides
7. **Environmental Health and Safety (EHS) Consulting (ID: 653)** - 5 overrides
8. **Environmental Health and Safety (EHS) Consulting (ID: 2493)** - 5 overrides

### Low Priority (3-4 Overrides)
9. **Construction Safety Consulting (ID: 2165)** - 4 overrides
10. **Water Damage Assessments (ID: 1198)** - 4 overrides
11. **Contact (ID: 107)** - 3 overrides
12. **EHS Staff Outsourcing (ID: 633)** - 3 overrides
13. **Ergonomic Evaluations (ID: 663)** - 3 overrides
14. **Indoor Air Quality Testing (ID: 657)** - 3 overrides
15. **Mold Testing (ID: 659)** - 3 overrides

### Single Override Pages
16. **Asbestos Testing (ID: 661)** - 6 overrides

## Templates Needing Updates

### High Priority
1. **EHS Footer (ID: 859)** - 12 overrides
2. **our services (ID: 1786)** - 11 overrides
3. **backup of more than 20 yrs (ID: 2523)** - 9 overrides

### Medium Priority
4. **certification_section (ID: 1922)** - 5 overrides
5. **new_certification_section (ID: 1952)** - 5 overrides
6. **Homepage (ID: 2367)** - 7 overrides
7. **ehs-single-post (ID: 1750)** - 4 overrides

### Low Priority
8. **Looking for X (ID: 1989)** - 3 overrides
9. **footer beed a (ID: 2004)** - 3 overrides
10. **bottom three services (ID: 1450)** - 3 overrides
11. **services block (ID: 804)** - 4 overrides
12. **services block2 (ID: 815)** - 4 overrides
13. **Services Page (ID: 2169)** - 3 overrides

### Single Override Templates
14. **Global Header (ID: 37)** - 2 overrides
15. **contact button (ID: 2323)** - 1 override
16. **phone number (ID: 2320)** - 1 override
17. **badges/icons (ID: 2056)** - 1 override

## Most Common Widget Types Affected

1. **heading** - Most affected widget type
2. **text-editor** - Second most affected
3. **button** - Third most affected
4. **theme-post-title**, **breadcrumbs**, **theme-post-excerpt**, **theme-post-content** - Theme widgets

## Most Common Override Properties

1. `typography_font_size` - Font size overrides
2. `typography_text_transform` - Text transform (uppercase, capitalize)
3. `typography_font_weight` - Font weight (bold, 400, 800)
4. `typography_letter_spacing` - Letter spacing
5. `typography_font_style` - Font style (italic, oblique)
6. `typography_line_height` - Line height
7. `typography_font_family` - Font family overrides

## Recommended Fix Process

### Option 1: Manual Fix (Recommended for Precision)
1. Open each page/template in Elementor editor
2. For each widget with custom typography:
   - Go to widget settings → Style → Typography
   - Change "Typography" from "Custom" to "Default"
   - Remove any custom font size, weight, transform, etc. settings
3. Test the page to ensure it looks correct with global fonts

### Option 2: Database Update Script (Advanced)
Create a script to programmatically remove typography overrides from Elementor JSON data.

### Option 3: Global Font Strategy
1. First, ensure global fonts are properly configured in Elementor
2. Then systematically remove overrides from high-priority pages first
3. Test thoroughly after each change

## Global Font Setup Verification

Before removing overrides, ensure these are properly configured:
- Elementor → Custom Fonts → Global Fonts
- Elementor → Settings → Global Fonts
- Theme customizer → Typography settings

## Priority Order for Updates

1. **Templates first** (Global Header, EHS Footer, Homepage) - affects multiple pages
2. **High-traffic pages** (Home, About Us, Contact)
3. **Service pages** - consistent branding across services
4. **Low-priority items** - thank you pages, etc.

## Estimated Effort

- **High priority items (4 pages + 3 templates):** 2-3 hours
- **Medium priority items (4 pages + 4 templates):** 2 hours
- **Low priority items (8 pages + 7 templates):** 1-2 hours
- **Testing and verification:** 1 hour

**Total estimated time:** 6-8 hours

## Next Steps

1. Review current global font settings
2. Start with Global Header template (ID: 37)
3. Test changes on a staging environment first
4. Update pages systematically by priority
5. Verify all changes work correctly with global fonts

---

*Report generated on: January 14, 2026*
*Script: scan-elementor-fonts.js*
*Database: 56 posts scanned, 33 with overrides found*
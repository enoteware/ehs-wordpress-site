# Elementor Button Variants Setup Guide

## Overview

Based on your selection of the outline button style, I've created 8 variants that can be used throughout your site. All variants follow the same base style:
- **Transparent background**
- **2px border**
- **16px font, 600 weight, uppercase**
- **15px 30px padding (medium)**
- **5px border radius**

## Button Variants

### Solid Buttons (Colored Backgrounds)

#### 1. **EHS Solid Primary**
- Background: `#003366` (Navy)
- Text: `#ffffff` (White)
- Hover Background: `#157537` (Green)
- Hover Text: `#ffffff` (White)
- Font: 16px, 600, uppercase
- Padding: 15px 30px
- Box Shadow: Yes
- **Use for:** Primary CTAs, main actions

#### 2. **EHS Solid Secondary**
- Background: `#FFB81C` (Gold)
- Text: `#003366` (Navy)
- Hover Background: `#157537` (Green)
- Hover Text: `#ffffff` (White)
- Font: 16px, 600, uppercase
- Padding: 20px 30px
- Box Shadow: Yes
- **Use for:** Secondary CTAs, footer buttons

#### 3. **EHS Solid Secondary (White Text)**
- Background: `#FFB81C` (Gold)
- Text: `#ffffff` (White)
- Hover Background: `#157537` (Green)
- Font: 16px, 600, uppercase
- Padding: 20px 30px
- Box Shadow: Yes
- **Use for:** Secondary CTAs with better contrast

#### 4. **EHS Solid Green**
- Background: `#157537` (Green)
- Text: `#ffffff` (White)
- Hover Background: `#0d4d24` (Darker Green)
- Font: 16px, 600, uppercase
- Padding: 15px 30px
- Box Shadow: Yes
- **Use for:** Success actions, positive CTAs

#### 5. **EHS Solid Gold (Darker)**
- Background: `#d28c0e` (Darker Gold)
- Text: `#ffffff` (White)
- Hover Background: `#157537` (Green)
- Font: 16px, 600, uppercase
- Padding: 20px 30px
- Box Shadow: Yes
- **Use for:** Alternative secondary button

### Outline Buttons

#### 6. **EHS Outline Primary** (Navy Hover)
- Border: `#003366` (Navy)
- Text: `#003366`
- Hover Background: `#003366`
- Hover Text: `#ffffff`
- **Use for:** Primary CTAs on light backgrounds

### 2. **EHS Outline Primary (Green Hover)**
- Border: `#003366` (Navy)
- Text: `#003366`
- Hover Background: `#157537` (Green)
- Hover Text: `#ffffff`
- **Use for:** Primary CTAs with green brand accent

### 3. **EHS Outline Secondary** (Gold Hover)
- Border: `#FFB81C` (Gold)
- Text: `#003366`
- Hover Background: `#FFB81C`
- Hover Text: `#003366`
- **Use for:** Secondary actions on light backgrounds

### 4. **EHS Outline Secondary (Green Hover)**
- Border: `#FFB81C` (Gold)
- Text: `#FFB81C`
- Hover Background: `#157537` (Green)
- Hover Text: `#ffffff`
- **Use for:** Secondary actions with green brand accent

### 5. **EHS Outline White** (White Hover)
- Border: `#ffffff` (White)
- Text: `#ffffff`
- Hover Background: `#ffffff`
- Hover Text: `#003366`
- **Use for:** Dark/hero backgrounds

### 6. **EHS Outline White (Green Hover)**
- Border: `#ffffff` (White)
- Text: `#ffffff`
- Hover Background: `#157537` (Green)
- Hover Text: `#ffffff`
- **Use for:** Dark backgrounds with green brand accent

### 7. **EHS Outline Small**
- Same as Primary but smaller
- Padding: `10px 20px`
- Font: `14px`
- **Use for:** Compact spaces, sidebars, cards

### 8. **EHS Outline Large**
- Same as Primary but larger
- Padding: `20px 40px`
- Font: `18px`
- **Use for:** Hero sections, prominent CTAs

## How to Apply in Elementor

### Option 1: Create Global Widgets (Recommended)

1. **Create a Button Widget:**
   - In Elementor editor, add a Button widget
   - Configure it with the settings from `elementor-button-variants.json`
   - Click the widget settings → Advanced → Save as Global Widget
   - Name it (e.g., "EHS Outline Primary")

2. **Use Global Widgets:**
   - When adding buttons, use the Global Widget instead of regular Button widget
   - This ensures consistency across all pages

### Option 2: Manual Application

For each button, apply these settings:

**Base Settings (All Variants):**
- Size: `md` (or `sm`/`lg` for size variants)
- Background Color: `transparent`
- Border Width: `2px` (all sides)
- Border Radius: `5px` (all sides)
- Typography: Custom
  - Font Size: `16px` (or `14px`/`18px` for size variants)
  - Font Weight: `600`
  - Text Transform: `uppercase`
- Padding: `15px 30px` (or adjust for size variants)

**Then set variant-specific colors:**
- See `elementor-button-variants.json` for exact color values

### Option 3: Use Elementor Site Settings

1. Go to **Elementor → Settings → Style**
2. Create custom button styles
3. Apply globally to all buttons

## Quick Reference: Elementor Settings

### Solid Primary
```
Size: md
Background: #003366
Hover Background: #157537
Text Color: #ffffff
Hover Text: #ffffff
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
Box Shadow: Yes (0 5px 10px rgba(0, 51, 102, 0.3))
```

### Solid Secondary (Gold)
```
Size: md
Background: #FFB81C
Hover Background: #157537
Text Color: #003366 (or #ffffff)
Hover Text: #ffffff
Typography: 16px, 600, uppercase
Padding: 20px 30px
Border Radius: 5px
Box Shadow: Yes
```

### Solid Green
```
Size: md
Background: #157537
Hover Background: #0d4d24
Text Color: #ffffff
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
Box Shadow: Yes
```

### Primary Outline (Navy Hover)
```
Size: md
Background: transparent
Hover Background: #003366
Text Color: #003366
Hover Text: #ffffff
Border: 2px solid #003366
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
```

### Primary Outline (Green Hover)
```
Size: md
Background: transparent
Hover Background: #157537
Text Color: #003366
Hover Text: #ffffff
Border: 2px solid #003366
Hover Border: #157537
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
```

### Secondary Outline (Gold Hover)
```
Size: md
Background: transparent
Hover Background: #FFB81C
Text Color: #003366
Hover Text: #003366
Border: 2px solid #FFB81C
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
```

### Secondary Outline (Green Hover)
```
Size: md
Background: transparent
Hover Background: #157537
Text Color: #FFB81C
Hover Text: #ffffff
Border: 2px solid #FFB81C
Hover Border: #157537
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
```

### White Outline (White Hover)
```
Size: md
Background: transparent
Hover Background: #ffffff
Text Color: #ffffff
Hover Text: #003366
Border: 2px solid #ffffff
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
```

### White Outline (Green Hover)
```
Size: md
Background: transparent
Hover Background: #157537
Text Color: #ffffff
Hover Text: #ffffff
Border: 2px solid #ffffff
Hover Border: #157537
Typography: 16px, 600, uppercase
Padding: 15px 30px
Border Radius: 5px
```

## Files Created

1. **`elementor-button-variants.json`** - Complete JSON definitions for all variants
2. **`button-variants-preview.html`** - Visual preview of all variants (open in browser)
3. **`ELEMENTOR_BUTTON_SETUP.md`** - This setup guide

## Next Steps

1. **Review the preview:** Open `button-variants-preview.html` in your browser
2. **Choose your variants:** Decide which variants you'll use most
3. **Create Global Widgets:** Set up 3-4 most common variants as Global Widgets
4. **Update existing buttons:** Use the extraction script to find buttons, then update them
5. **Document exceptions:** If any buttons need custom styles, document why

## Brand Colors Reference

- **Navy:** `#003366` or `#1e405a`
- **Gold:** `#FFB81C` or `#d28c0e`
- **Green:** `#157537`
- **White:** `#ffffff`

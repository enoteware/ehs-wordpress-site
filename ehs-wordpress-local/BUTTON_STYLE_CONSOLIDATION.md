# Button Style Consolidation Report

## Summary

**Total Buttons Found:** 32  
**Unique Style Variations:** 17  
**Consolidation Needed:** Yes - Too many one-off styles

## Current Button Style Variations

### Primary Button Styles (Keep These)

#### 1. **Primary CTA Button** (Variation #13 - Header)
- **Background:** `#1e405a` (Navy)
- **Hover:** `#157537` (Green)
- **Size:** `md`
- **Font:** 15px, uppercase, italic
- **Padding:** 15px all sides
- **Shadow:** 5px 10px rgba(30,64,90,0.5)
- **Usage:** Header "get started" button
- **Status:** ✅ **KEEP - This is the primary brand button**

#### 2. **Secondary CTA Button** (Variation #9 - Most Common)
- **Background:** `#d28c0e` (Gold/Orange)
- **Hover:** `rgba(255,255,255,0)` (transparent)
- **Size:** `md`
- **Font:** 16px, uppercase
- **Padding:** 30px all sides
- **Usage:** 6 instances - "Contact Us" buttons
- **Status:** ✅ **KEEP - Standard secondary button**

#### 3. **Footer/Contact Button** (Variation #5)
- **Background:** `#f6a90f` (Gold)
- **Hover:** `#f6a90f` (same) or `#157537` (green)
- **Size:** `md`
- **Font:** Default, uppercase
- **Padding:** 20px all sides
- **Shadow:** 5px 10px rgba(30,64,90,0.5)
- **Usage:** Footer and service pages
- **Status:** ⚠️ **CONSOLIDATE** - Should match Variation #9 or #13

### Problematic One-Off Styles (Remove/Consolidate)

#### Variation #1: Green "Get a Free Quote"
- Background: `#157537` (green)
- No hover state
- **Action:** Consolidate to primary button style

#### Variation #2-3: Default/Inherit Styles
- No background color set
- **Action:** These should use a defined button style

#### Variation #4: Header Button (Duplicate)
- Same as Variation #13 but missing font styling
- **Action:** Remove duplicate, use Variation #13

#### Variation #6: Service Page Button
- Background: `#1e405a`, Hover: `#f6a90f`
- **Action:** Consolidate to primary button (Variation #13)

#### Variation #7: Footer Button (Different Hover)
- Background: `#f6a90f`, Hover: `#157537`
- **Action:** Standardize hover to match brand guidelines

#### Variation #8: Old Template Style
- Background: `#d28c0e`, Font: 14px
- **Action:** Update to match Variation #9 (16px)

#### Variation #10-12: Transparent/Outline Buttons
- Various transparent backgrounds
- **Action:** Create one standard outline button style

#### Variation #14-17: Template Demo Styles
- Medical/Law firm template demo buttons
- Colors: `#d61747`, `#1b80f3`, etc.
- **Action:** **REMOVE** - These are from demo templates, not brand colors

## Recommended Button Style System

### Primary Button (Primary CTA)
```css
Background: #1e405a (Navy)
Hover: #157537 (Green)
Text: White, 15px, uppercase, italic
Padding: 15px all sides
Shadow: 5px 10px rgba(30,64,90,0.5)
Border Radius: 5px
```

### Secondary Button (Secondary CTA)
```css
Background: #d28c0e or #f6a90f (Gold)
Hover: #157537 (Green) or transparent
Text: White, 16px, uppercase
Padding: 20-30px all sides
Shadow: 5px 10px rgba(30,64,90,0.5) (optional)
Border Radius: 5px
```

### Tertiary/Outline Button (Text Links)
```css
Background: Transparent
Hover: #1e405a (Navy) or #157537 (Green)
Text: #1e405a (Navy), 16-18px
Padding: 0px or minimal
Border: Optional 2px solid
Border Radius: 5px
```

## Consolidation Action Plan

### Phase 1: Define Standard Styles
1. ✅ Create 3 standard button styles in Elementor Global Styles
2. ✅ Document button usage guidelines
3. ✅ Create button style presets

### Phase 2: Update Existing Buttons
1. **Header Button** - Already correct (Variation #13)
2. **Footer Buttons** - Update to match secondary style
3. **Service Page Buttons** - Update to primary style
4. **Homepage CTAs** - Update to primary/secondary as appropriate
5. **Remove Demo Template Buttons** - Delete or replace Variation #14-17

### Phase 3: Create Elementor Global Styles
Create these as Elementor Global Widget Styles:
- `EHS Primary Button`
- `EHS Secondary Button`  
- `EHS Outline Button`

### Phase 4: Audit and Replace
1. Run button extraction script again
2. Identify buttons not using global styles
3. Replace one-offs with standard styles
4. Document exceptions (if any)

## Brand Color Reference

- **Navy:** `#1e405a` or `#003366`
- **Gold:** `#f6a90f` or `#d28c0e` or `#FFB81C`
- **Green:** `#157537`
- **White:** `#ffffff`

## Next Steps

1. Review this report with design team
2. Finalize 3 button style definitions
3. Create Elementor Global Styles
4. Begin systematic replacement
5. Update style guide documentation

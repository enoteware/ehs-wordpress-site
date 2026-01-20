# Impactful Checklist Components

Reusable CSS classes for creating visually striking checklists on service pages.

## Available Components

### 1. Primary Checklist (Orange Checkboxes)

**Class:** `.service-checklist-primary`

Orange/gold checkboxes on white background with subtle shadow.

**Usage:**
```html
<p class="service-checklist-intro">Below are some examples of industrial hygiene services we provide.</p>

<ul class="service-checklist-primary">
  <li>Chemical exposure air monitoring (solvents, acids, fumes, metals, dusts, asbestos, lead, etc.)</li>
  <li>Noise Study and sound level mapping</li>
  <li>Job safety analysis (JSA) for any or all operations</li>
  <li>Airborne allergen exposure monitoring</li>
  <li>Potent compound exposure monitoring (surrogate sampling)</li>
</ul>
```

**Features:**
- Orange checkmark in rounded square
- Gold shadow for depth
- Clean white background
- Perfect for services lists, features, benefits

---

### 2. Highlight Checklist (Navy Box with Gold Border)

**Class:** `.service-checklist-highlight`

Navy blue box with gold border containing white text and circular gold bullets.

**Usage:**
```html
<div class="service-checklist-highlight">
  <h3 class="service-checklist-highlight-heading">These assessments can be used for the following benefits:</h3>
  <ul style="list-style: none; padding: 0; margin: 0;">
    <li style="position: relative; padding-left: 40px; margin-bottom: 16px;">
      <span style="position: absolute; left: 0; top: 0; color: #FFB81C; font-size: 1.5rem; font-weight: 700;">⊕</span>
      Demonstrate compliance with a particular Cal/OSHA standard
    </li>
    <li style="position: relative; padding-left: 40px; margin-bottom: 16px;">
      <span style="position: absolute; left: 0; top: 0; color: #FFB81C; font-size: 1.5rem; font-weight: 700;">⊕</span>
      Verification of effectiveness of engineering controls
    </li>
    <li style="position: relative; padding-left: 40px; margin-bottom: 0;">
      <span style="position: absolute; left: 0; top: 0; color: #FFB81C; font-size: 1.5rem; font-weight: 700;">⊕</span>
      Cost saving opportunities
    </li>
  </ul>
</div>
```

**Features:**
- Navy background (#003366) with gold border (#FFB81C)
- White text for high contrast
- Gold circular bullets (⊕)
- Optional centered heading inside box
- Perfect for key benefits, important points, call-to-action items

---

### 3. Optional Intro Text

**Class:** `.service-checklist-intro`

Bold intro text before a checklist.

**Usage:**
```html
<p class="service-checklist-intro">Below are some examples of industrial hygiene services we provide.</p>
```

---

## Design Specifications

### Colors
- Navy: `#003366` (`var(--ehs-navy)`)
- Gold: `#FFB81C` (`var(--ehs-gold)`)
- White: `#FFFFFF` (`var(--ehs-white)`)
- Dark Gray: `#333333` (`var(--ehs-dark-gray)`)

### Typography
- Font: Maven Pro
- Primary list: 1.05rem
- Highlight list: 1.1rem
- Intro text: 1.1rem, font-weight: 600

### Spacing
- List margins: 24px top/bottom, 32px bottom
- List item spacing: 14-16px between items
- Highlight box padding: 32px vertical, 40px horizontal

### Responsive
- Mobile (< 768px):
  - Font size reduces to 1rem
  - Padding adjusts to 24px/20px
  - Maintains visual hierarchy

---

## Live Example

See Industrial Hygiene Services page (ID: 3285) for a complete implementation.

---

## When to Use Each

**Use `.service-checklist-primary` for:**
- Standard service lists
- Feature lists
- Process steps
- General benefits

**Use `.service-checklist-highlight` for:**
- Key benefits that need emphasis
- Important compliance points
- Call-to-action lists
- High-value propositions
- Content that should stand out on the page

---

## Adding to Other Service Pages

**Via WP-CLI:**
```bash
# Create content file with checklist classes
cat > /tmp/service-content.html << 'EOF'
<p class="service-checklist-intro">Our services include:</p>
<ul class="service-checklist-primary">
  <li>Service item 1</li>
  <li>Service item 2</li>
</ul>
EOF

# Update post
ddev exec 'wp post update POST_ID --post_content="$(cat /tmp/service-content.html)" --path=/var/www/html/wordpress'
```

**Via WordPress Admin:**
1. Edit service post in Text mode (not Visual)
2. Add HTML with checklist classes
3. Save/update

---

## CSS Location

**File:** `wordpress/wp-content/themes/hello-elementor-child/style.css`
**Lines:** 1093-1212

All styling is theme-based (not Elementor). CSS uses design system variables for consistency.

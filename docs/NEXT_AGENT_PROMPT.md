# Agent Prompt: Build About, Services Archive, and Contact Pages

## Context

We've just completed rebuilding the EHS Analytical home page as a pure PHP template using a modern biotech-inspired design system. The new home page uses:
- Pure PHP (no Elementor dependency)
- Maven Pro typography site-wide
- Navy (#003366) and Gold (#FFB81C) brand colors
- Clean, professional layout with proper spacing
- Biotech design principles: minimalism, trust signals, clear hierarchy

**Completed Work:**
- ✅ Home page: `front-page-new.php` (6 sections, fully responsive)
- ✅ Design system CSS in `style.css` (hero, credentials, metrics, badges, etc.)
- ✅ Helper functions in `inc/frontend/home-page-functions.php`
- ✅ Full documentation in `DESIGN_SYSTEM.md`

**Current Status:**
- Site: http://ehs-mini.ddev.site
- WordPress admin: http://ehs-mini.ddev.site/wp-admin
- Credentials: `a509f58b_admin` / `EHS-Local-Dev-2024!`

---

## Task: Build 3 New Page Templates

Create pure PHP templates for the following pages, reusing existing content from the current Elementor-based pages:

### 1. About Page (`page-about.php`)
**Current URL:** http://ehs-mini.ddev.site/about
**Goal:** Showcase company story, team, credentials, veteran ownership

**Required Sections:**
- Hero section (company mission/vision)
- Company story (3-4 paragraphs about EHS Analytical)
- Veteran ownership highlight (USMC & Navy background, SDVOSB/DVBE)
- Team/leadership section (if content exists)
- Certifications & credentials (CIH, CSP, CHST, PMP, etc.)
- Why choose us / differentiators
- Call-to-action (contact us)

**Design Notes:**
- Use hero section pattern from home page
- Include credentials badge grid (already built, see home page)
- Highlight veteran ownership prominently (key differentiator)
- Professional, trustworthy tone

---

### 2. Services Archive Page (`archive-services.php`)
**Current URL:** http://ehs-mini.ddev.site/services
**Goal:** Display all services in organized, scannable grid layout

**Required Elements:**
- Hero section ("Our EHS Services" headline)
- Service category organization (group by: Consulting, Testing, Construction, Federal)
- Grid of all published services (use service-card component)
- Each card shows: icon, title, short description, "Learn More" link
- Mobile responsive (3 cols → 2 cols → 1 col)

**Design Notes:**
- Reuse `.service-related__grid` and `.service-card` CSS classes
- Query all published services: `post_type=services, post_status=publish`
- Use `ehs_get_fallback_service_icon()` for icons if not set
- Optional: Add filter/category tabs if content warrants

---

### 3. Contact Page (`page-contact.php`)
**Current URL:** http://ehs-mini.ddev.site/contact
**Goal:** Make it easy to contact EHS Analytical

**Required Sections:**
- Hero section ("Contact Us" / "Get Started")
- Contact information display:
  - Phone: (619) 288-3094
  - Email: (extract from current contact page)
  - Address: San Diego, CA
  - Hours: (if available)
- Contact form (use existing WordPress form or create simple PHP form)
- Map/location (optional, if currently on page)
- Service areas highlight (California, Federal)
- Certifications badge (SDVOSB/DVBE prominently displayed)

**Design Notes:**
- 2-column layout: left = contact info, right = form
- Use existing contact form from current page if available
- Include trust signals (certifications, veteran-owned)
- Clear CTAs for phone and email

---

## Design System Reference

### Available CSS Classes

**Layout & Containers:**
- `.container` - Max-width 1200px, centered
- `.ehs-hero-section` - Full-width hero with background image
- `.hero-overlay` - Navy overlay (70% opacity)
- `.hero-content` - Centered content wrapper

**Sections:**
- `.ehs-services-section` - Services grid section (80px padding)
- `.ehs-credentials-section` - Light gray background, credentials display
- `.ehs-about-section` - Full-width with background image

**Components:**
- `.service-card` - Service card with icon, title, excerpt, link
- `.service-related__grid` - 3-column responsive grid for services
- `.badge-grid` - 3×3 certification badge grid
- `.badge-item` - Individual badge card with hover
- `.credentials-grid` - 2-column grid (credentials section)
- `.metrics-row` - Horizontal metrics display
- `.metric-value` - Large metric number (3rem, bold)
- `.metric-label` - Metric label text

**Buttons:**
- `.ehs-btn.ehs-btn-solid-primary` - Navy background
- `.ehs-btn.ehs-btn-solid-secondary` - Gold background
- `.ehs-btn.ehs-btn-outline-white` - White outline
- `.ehs-btn-lg` - Large button

**Forms (if needed):**
- `.form-input` - Text/email inputs
- `.form-textarea` - Textarea fields
- `.form-select` - Select dropdowns
- `.form-label` - Form labels

### Helper Functions Available

**Service Queries:**
```php
// Get all published services
$args = array(
    'post_type' => 'services',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC'
);
$services = new WP_Query($args);

// Get service icon (custom field or SVG fallback)
$icon_url = ehs_get_fallback_service_icon($post_slug);

// Render service card
ehs_homepage_render_service_card($service_array);
```

**Certification Badges:**
```php
ehs_render_certification_badges(); // Outputs complete badge grid
```

### Typography (Maven Pro)

**Already Applied Globally:**
- All text uses Maven Pro font family
- Headings: 700 weight, Navy color (#003366)
- Body: 400 weight, Dark gray (#333333)
- Subheadings: 600 weight

**Heading Sizes:**
- H1: 3rem (48px)
- H2: 2.5rem (40px)
- H3: 1.75rem (28px)
- Body: 1.1rem (18px)

### Color Palette

**CSS Variables (use these):**
```css
var(--ehs-navy)       /* #003366 - Primary */
var(--ehs-gold)       /* #FFB81C - Accent */
var(--ehs-light-gray) /* #F5F5F5 - Backgrounds */
var(--ehs-dark-gray)  /* #333333 - Text */
var(--ehs-white)      /* #FFFFFF - White */
```

### Spacing Scale
Use consistently: 4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 60px, 80px

---

## Implementation Guidelines

### 1. Extracting Existing Content

**Access Current Pages:**
```bash
# About page
curl http://ehs-mini.ddev.site/about

# Services archive
curl http://ehs-mini.ddev.site/services

# Contact page
curl http://ehs-mini.ddev.site/contact
```

**Or use WordPress Admin:**
- Login: http://ehs-mini.ddev.site/wp-admin
- View pages in "Pages" section
- Check Elementor content for text, headings, structure

**Key Content to Preserve:**
- All text content (headlines, paragraphs, lists)
- Contact information (phone, email, address)
- Company story and mission statements
- Team member information (if exists)
- Any unique service descriptions

### 2. Template Structure

**Follow WordPress Template Hierarchy:**

**About Page (`page-about.php`):**
```php
<?php
/**
 * Template Name: About Page
 * Description: About EHS Analytical page template
 */

get_header();
?>

<!-- Hero Section -->
<section class="ehs-hero-section" style="background-image: url('...');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>About EHS Analytical</h1>
        <p class="hero-subheadline">Subheadline here</p>
    </div>
</section>

<!-- Company Story Section -->
<section class="ehs-about-section">
    <!-- Content here -->
</section>

<!-- More sections... -->

<?php get_footer(); ?>
```

**Services Archive (`archive-services.php`):**
```php
<?php
/**
 * Archive Template: Services
 * Displays all published services
 */

get_header();
?>

<!-- Hero Section -->
<section class="ehs-hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Our EHS Services</h1>
        <p class="hero-subheadline">Comprehensive solutions for California and federal projects</p>
    </div>
</section>

<!-- Services Grid -->
<section class="ehs-services-section">
    <div class="container">
        <div class="service-related__grid">
            <?php
            // Query all services
            $args = array(
                'post_type' => 'services',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC'
            );

            $services_query = new WP_Query($args);

            if ($services_query->have_posts()) {
                while ($services_query->have_posts()) {
                    $services_query->the_post();

                    // Get service data
                    $service_icon = get_post_meta(get_the_ID(), 'service_icon', true);
                    $icon_url = '';
                    if ($service_icon) {
                        $icon_url = wp_get_attachment_url($service_icon);
                    }
                    if (!$icon_url) {
                        $icon_url = ehs_get_fallback_service_icon(get_post_field('post_name', get_the_ID()));
                    }

                    $service = array(
                        'title' => get_the_title(),
                        'excerpt' => get_post_meta(get_the_ID(), 'service_short_description', true) ?: wp_trim_words(get_the_excerpt(), 20),
                        'permalink' => get_permalink(),
                        'icon' => $icon_url
                    );

                    // Render service card
                    ehs_homepage_render_service_card($service);
                }
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
```

**Contact Page (`page-contact.php`):**
```php
<?php
/**
 * Template Name: Contact Page
 * Description: Contact EHS Analytical page template
 */

get_header();
?>

<!-- Hero Section -->
<section class="ehs-hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Contact Us</h1>
        <p class="hero-subheadline">Get started with EHS Analytical today</p>
    </div>
</section>

<!-- Contact Section -->
<section class="ehs-services-section">
    <div class="container">
        <div class="credentials-grid">
            <!-- Left: Contact Info -->
            <div class="credentials-content">
                <h2>Get In Touch</h2>
                <p>Contact information here...</p>
                <!-- Phone, email, address -->
            </div>

            <!-- Right: Contact Form -->
            <div>
                <!-- Form here -->
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
```

### 3. File Locations

**Create templates in:**
```
wordpress/wp-content/themes/hello-elementor-child/
├── page-about.php
├── archive-services.php
├── page-contact.php
```

**Reference existing files:**
- Design system: `style-guide/DESIGN_SYSTEM.md`
- Home page: `front-page-new.php`
- Helper functions: `inc/frontend/home-page-functions.php`
- CSS: `style.css`

### 4. Testing Checklist

**For Each Page:**
- [ ] Template file created
- [ ] Content extracted from current Elementor page
- [ ] All sections render correctly
- [ ] Maven Pro typography applied
- [ ] Responsive design works (mobile, tablet, desktop)
- [ ] All links work correctly
- [ ] Images/icons display properly
- [ ] CSS classes from design system used
- [ ] No inline styles (use CSS classes)
- [ ] Clear caches after creation

**Test URLs:**
- About: http://ehs-mini.ddev.site/about
- Services: http://ehs-mini.ddev.site/services
- Contact: http://ehs-mini.ddev.site/contact

### 5. Activation Steps

**After templates are created and tested:**

1. **Assign Templates (if custom page templates):**
   - Go to WordPress Admin → Pages
   - Edit "About" page → Page Attributes → Template → Select "About Page"
   - Edit "Contact" page → Page Attributes → Template → Select "Contact Page"
   - Save/Update pages

2. **Archive Template (automatic):**
   - `archive-services.php` automatically applies to `/services/` URL
   - No manual assignment needed

3. **Clear Caches:**
```bash
cd ehs-wordpress-local
ddev exec wp cache flush --path=/var/www/html/wordpress
ddev exec wp elementor flush-css --path=/var/www/html/wordpress
```

4. **Verify Live:**
   - Visit each page URL
   - Check all sections render
   - Test responsive design
   - Verify all links and CTAs work

---

## Design Principles to Follow

Based on biotech company research (same principles used for home page):

1. **Minimalism** - Less copy, clear visual hierarchy
2. **Trust Signals** - Certifications, credentials, veteran ownership prominently displayed
3. **Clear CTAs** - Every page has obvious next action
4. **Professional Tone** - Matches EHS industry standards
5. **Consistent Spacing** - Use spacing scale (80px sections, 60px mobile)
6. **Color Discipline** - Navy/gold palette maintained throughout
7. **Mobile-First** - All layouts responsive across breakpoints

---

## Expected Deliverables

1. **3 Template Files:**
   - `page-about.php` - About page template
   - `archive-services.php` - Services archive template
   - `page-contact.php` - Contact page template

2. **Content Reused:** All text, images, and information from current Elementor pages

3. **Additional CSS (if needed):** Any new CSS classes added to `style.css` with proper comments

4. **Documentation:** Brief summary of changes and any new CSS classes created

5. **Testing Complete:** All pages verified at desktop, tablet, and mobile breakpoints

---

## Reference Documentation

**Design System:**
- `style-guide/DESIGN_SYSTEM.md` - Complete CSS class reference
- `style-guide/style-guide.html` - Visual style guide
- `docs/HOME_PAGE_IMPLEMENTATION_SUMMARY.md` - Home page implementation details

**Existing Templates:**
- `front-page-new.php` - Home page (reference for structure)
- `single-services.php` - Individual service page template

**WordPress Info:**
- Site: http://ehs-mini.ddev.site
- Admin: http://ehs-mini.ddev.site/wp-admin
- Theme location: `wordpress/wp-content/themes/hello-elementor-child/`

---

## Success Criteria

✅ All 3 pages display correctly with new design
✅ Content preserved from current Elementor pages
✅ Maven Pro typography applied consistently
✅ Navy/gold brand colors used throughout
✅ All CSS classes from design system reused
✅ Mobile responsive at all breakpoints
✅ No Elementor dependency
✅ Professional, trustworthy appearance
✅ Fast page load (<3 seconds)
✅ All links and CTAs functional

---

**Start with extracting content from the current pages, then build the templates following the home page structure and design system patterns. Reuse CSS classes wherever possible - avoid creating new styles unless absolutely necessary.**

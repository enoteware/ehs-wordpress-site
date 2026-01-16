# Contact Form Usage Guide

A lightweight, secure contact form with Resend API integration and bot protection.

## Features

- ✅ **Lightweight** - No heavy dependencies, pure custom code
- ✅ **Resend API** - Reliable email delivery via Resend
- ✅ **Bot Protection** - reCAPTCHA v3 + honeypot field
- ✅ **Modal Ready** - Built-in modal functionality
- ✅ **AJAX Submission** - No page reloads
- ✅ **Rate Limiting** - Prevents spam submissions
- ✅ **Fully Customizable** - Show/hide fields, custom styling

## Setup

### 1. Configure API Keys

Go to **Settings → Contact Form** in WordPress admin and configure:

- **Resend API Key** - Get from [resend.com/api-keys](https://resend.com/api-keys)
- **From Email** - Must be verified in your Resend account
- **To Email** - Where form submissions will be sent
- **reCAPTCHA Keys** (Optional) - Get from [Google reCAPTCHA](https://www.google.com/recaptcha/admin)

### 2. Basic Usage

#### Shortcode
```
[ehs_contact_form]
```

With options:
```
[ehs_contact_form show_phone="true" show_company="false" submit_text="Send Message"]
```

#### PHP Function
```php
<?php
echo ehs_render_contact_form(array(
    'show_name' => true,
    'show_phone' => true,
    'show_company' => false,
    'submit_text' => 'Send Message',
    'class' => 'custom-class',
));
?>
```

## Modal Usage

### Simple Modal Example

```html
<!-- Trigger Button -->
<button onclick="ehsOpenContactModal('contact-modal')">Contact Us</button>

<!-- Modal Structure -->
<div id="contact-modal" class="ehs-modal-overlay">
    <div class="ehs-modal">
        <div class="ehs-modal-header">
            <h3 class="ehs-modal-title">Contact Us</h3>
            <button class="ehs-modal-close" onclick="ehsCloseContactModal('contact-modal')">&times;</button>
        </div>
        <div class="ehs-modal-body">
            <?php echo ehs_render_contact_form(); ?>
        </div>
    </div>
</div>
```

### Elementor Button to Modal

1. Add a Button widget in Elementor
2. Set button link to: `#contact-modal`
3. Add custom CSS class: `ehs-modal-trigger`
4. Add this JavaScript to your page:

```javascript
jQuery(document).ready(function($) {
    $('.ehs-modal-trigger').on('click', function(e) {
        e.preventDefault();
        ehsOpenContactModal('contact-modal');
    });
});
```

### Multiple Modals

You can create multiple modals with different IDs:

```html
<!-- Contact Form Modal -->
<div id="contact-modal" class="ehs-modal-overlay">
    <!-- ... -->
</div>

<!-- Quote Request Modal -->
<div id="quote-modal" class="ehs-modal-overlay">
    <div class="ehs-modal">
        <div class="ehs-modal-header">
            <h3 class="ehs-modal-title">Request a Quote</h3>
            <button class="ehs-modal-close" onclick="ehsCloseContactModal('quote-modal')">&times;</button>
        </div>
        <div class="ehs-modal-body">
            <?php echo ehs_render_contact_form(array('show_company' => true)); ?>
        </div>
    </div>
</div>
```

## Customization

### Form Fields

Control which fields are shown:

```php
ehs_render_contact_form(array(
    'show_name' => true,      // Name field
    'show_phone' => true,     // Phone field
    'show_company' => false,   // Company field
));
```

### Styling

The form uses CSS classes that you can override:

- `.ehs-contact-form` - Main form container
- `.ehs-form-field` - Individual field wrapper
- `.ehs-submit-btn` - Submit button
- `.ehs-form-messages` - Success/error messages

Add custom CSS to your theme's `style.css`:

```css
.ehs-contact-form .ehs-submit-btn {
    background-color: #003366; /* Your brand color */
}
```

### Modal Styling

Modal classes:

- `.ehs-modal-overlay` - Full-screen overlay
- `.ehs-modal` - Modal container
- `.ehs-modal-header` - Header with title and close button
- `.ehs-modal-body` - Form content area

## Security Features

### Bot Protection

1. **Honeypot Field** - Hidden field that bots fill (silently rejected)
2. **reCAPTCHA v3** - Invisible bot detection (optional but recommended)
3. **Rate Limiting** - Max 3 submissions per hour per IP
4. **Nonce Verification** - WordPress security token

### Email Security

- All input is sanitized
- Email addresses validated
- HTML emails use proper escaping
- IP address logging for abuse tracking

## Troubleshooting

### Form Not Submitting

1. Check browser console for JavaScript errors
2. Verify AJAX URL is correct (should be `/wp-admin/admin-ajax.php`)
3. Check that jQuery is loaded

### Emails Not Sending

1. Verify Resend API key is correct
2. Check that "From Email" is verified in Resend dashboard
3. Check WordPress debug log for errors
4. Test API key directly with Resend API

### reCAPTCHA Not Working

1. Verify Site Key and Secret Key are correct
2. Check that reCAPTCHA script is loading (check Network tab)
3. Ensure domain is registered in reCAPTCHA settings

### Modal Not Opening

1. Ensure JavaScript is loaded (check console)
2. Verify modal ID matches in trigger and modal
3. Check for JavaScript errors

## File Structure

```
hello-elementor-child/
├── inc/
│   ├── frontend/
│   │   ├── contact-form.php          # Form rendering
│   │   └── contact-form-handler.php  # AJAX handler & Resend API
│   └── admin/
│       └── contact-form-settings.php # Admin settings page
├── assets/
│   ├── css/
│   │   └── contact-form.css          # Form & modal styles
│   └── js/
│       └── contact-form.js            # Form submission & modal
└── functions.php                     # Integration
```

## API Reference

### JavaScript Functions

- `ehsOpenContactModal(modalId)` - Open modal by ID
- `ehsCloseContactModal(modalId)` - Close modal by ID

### PHP Functions

- `ehs_render_contact_form($args)` - Render form HTML
- `ehs_handle_contact_form_submission()` - AJAX handler (hooked)

### WordPress Options

- `ehs_resend_api_key` - Resend API key
- `ehs_resend_from_email` - From email address
- `ehs_resend_to_email` - To email address
- `ehs_resend_from_name` - From name
- `ehs_recaptcha_site_key` - reCAPTCHA site key
- `ehs_recaptcha_secret_key` - reCAPTCHA secret key

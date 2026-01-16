<?php
/**
 * Custom Contact Form Component
 * Lightweight, reusable contact form with Resend API integration
 * 
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Contact Form HTML
 * 
 * @param array $args Form configuration
 * @return string Form HTML
 */
function ehs_render_contact_form($args = array()) {
    $defaults = array(
        'form_id' => 'ehs-contact-form-' . uniqid(),
        'show_name' => true,
        'show_phone' => true,
        'show_company' => false,
        'submit_text' => 'Send Message',
        'class' => '',
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Generate nonce for security
    $nonce = wp_create_nonce('ehs_contact_form_nonce');
    
    ob_start();
    ?>
    <form id="<?php echo esc_attr($args['form_id']); ?>" class="ehs-contact-form <?php echo esc_attr($args['class']); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
        <div class="ehs-form-messages"></div>
        
        <?php if ($args['show_name']): ?>
        <div class="ehs-form-field">
            <label for="<?php echo esc_attr($args['form_id']); ?>-name">Name <span class="required">*</span></label>
            <input 
                type="text" 
                id="<?php echo esc_attr($args['form_id']); ?>-name" 
                name="name" 
                required 
                aria-required="true"
                placeholder="Your name"
            />
        </div>
        <?php endif; ?>
        
        <div class="ehs-form-field">
            <label for="<?php echo esc_attr($args['form_id']); ?>-email">Email <span class="required">*</span></label>
            <input 
                type="email" 
                id="<?php echo esc_attr($args['form_id']); ?>-email" 
                name="email" 
                required 
                aria-required="true"
                placeholder="your.email@example.com"
            />
        </div>
        
        <?php if ($args['show_phone']): ?>
        <div class="ehs-form-field">
            <label for="<?php echo esc_attr($args['form_id']); ?>-phone">Phone</label>
            <input 
                type="tel" 
                id="<?php echo esc_attr($args['form_id']); ?>-phone" 
                name="phone" 
                placeholder="(555) 123-4567"
            />
        </div>
        <?php endif; ?>
        
        <?php if ($args['show_company']): ?>
        <div class="ehs-form-field">
            <label for="<?php echo esc_attr($args['form_id']); ?>-company">Company</label>
            <input 
                type="text" 
                id="<?php echo esc_attr($args['form_id']); ?>-company" 
                name="company" 
                placeholder="Company name"
            />
        </div>
        <?php endif; ?>
        
        <div class="ehs-form-field">
            <label for="<?php echo esc_attr($args['form_id']); ?>-subject">Subject <span class="required">*</span></label>
            <input 
                type="text" 
                id="<?php echo esc_attr($args['form_id']); ?>-subject" 
                name="subject" 
                required 
                aria-required="true"
                placeholder="What is this regarding?"
            />
        </div>
        
        <div class="ehs-form-field">
            <label for="<?php echo esc_attr($args['form_id']); ?>-message">Message <span class="required">*</span></label>
            <textarea 
                id="<?php echo esc_attr($args['form_id']); ?>-message" 
                name="message" 
                rows="5" 
                required 
                aria-required="true"
                placeholder="Tell us how we can help..."
            ></textarea>
        </div>
        
        <!-- Honeypot field (hidden from users, visible to bots) -->
        <div class="ehs-honeypot" style="position: absolute; left: -9999px; opacity: 0;">
            <label for="<?php echo esc_attr($args['form_id']); ?>-website">Website</label>
            <input 
                type="text" 
                id="<?php echo esc_attr($args['form_id']); ?>-website" 
                name="website" 
                tabindex="-1" 
                autocomplete="off"
            />
        </div>
        
        <!-- reCAPTCHA v3 token will be added here -->
        <input type="hidden" name="recaptcha_token" id="<?php echo esc_attr($args['form_id']); ?>-recaptcha-token" />
        
        <div class="ehs-form-field ehs-form-submit">
            <button type="submit" class="ehs-submit-btn">
                <span class="btn-text"><?php echo esc_html($args['submit_text']); ?></span>
                <span class="btn-loader" style="display: none;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                            <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416;0 31.416" repeatCount="indefinite"/>
                            <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416;-31.416" repeatCount="indefinite"/>
                        </circle>
                    </svg>
                </span>
            </button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode to display contact form
 * Usage: [ehs_contact_form show_phone="true" show_company="false"]
 */
function ehs_contact_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_name' => 'true',
        'show_phone' => 'true',
        'show_company' => 'false',
        'submit_text' => 'Send Message',
        'class' => '',
    ), $atts);
    
    $args = array(
        'show_name' => $atts['show_name'] === 'true',
        'show_phone' => $atts['show_phone'] === 'true',
        'show_company' => $atts['show_company'] === 'true',
        'submit_text' => $atts['submit_text'],
        'class' => $atts['class'],
    );
    
    return ehs_render_contact_form($args);
}
add_shortcode('ehs_contact_form', 'ehs_contact_form_shortcode');

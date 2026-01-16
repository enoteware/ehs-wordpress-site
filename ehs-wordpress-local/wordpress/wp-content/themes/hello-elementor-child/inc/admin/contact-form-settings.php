<?php
/**
 * Contact Form Settings Page
 * Admin interface for configuring Resend API and reCAPTCHA
 * 
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings page to admin menu
 */
function ehs_add_contact_form_settings_page() {
    add_options_page(
        'Contact Form Settings',
        'Contact Form',
        'manage_options',
        'ehs-contact-form-settings',
        'ehs_render_contact_form_settings_page'
    );
}
add_action('admin_menu', 'ehs_add_contact_form_settings_page');

/**
 * Render settings page
 */
function ehs_render_contact_form_settings_page() {
    // Save settings
    if (isset($_POST['ehs_contact_form_settings']) && check_admin_referer('ehs_contact_form_settings_nonce')) {
        update_option('ehs_resend_api_key', sanitize_text_field($_POST['ehs_resend_api_key']));
        update_option('ehs_resend_from_email', sanitize_email($_POST['ehs_resend_from_email']));
        update_option('ehs_resend_to_email', sanitize_email($_POST['ehs_resend_to_email']));
        update_option('ehs_resend_from_name', sanitize_text_field($_POST['ehs_resend_from_name']));
        update_option('ehs_recaptcha_site_key', sanitize_text_field($_POST['ehs_recaptcha_site_key']));
        update_option('ehs_recaptcha_secret_key', sanitize_text_field($_POST['ehs_recaptcha_secret_key']));
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    // Get current settings
    $resend_api_key = get_option('ehs_resend_api_key', '');
    $resend_from_email = get_option('ehs_resend_from_email', get_option('admin_email'));
    $resend_to_email = get_option('ehs_resend_to_email', get_option('admin_email'));
    $resend_from_name = get_option('ehs_resend_from_name', get_bloginfo('name'));
    $recaptcha_site_key = get_option('ehs_recaptcha_site_key', '');
    $recaptcha_secret_key = get_option('ehs_recaptcha_secret_key', '');
    ?>
    <div class="wrap">
        <h1>Contact Form Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('ehs_contact_form_settings_nonce'); ?>
            
            <h2>Resend API Configuration</h2>
            <p>Configure your Resend API settings for email delivery. Get your API key from <a href="https://resend.com/api-keys" target="_blank">resend.com/api-keys</a></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_api_key">Resend API Key</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="ehs_resend_api_key" 
                            name="ehs_resend_api_key" 
                            value="<?php echo esc_attr($resend_api_key); ?>" 
                            class="regular-text"
                            placeholder="re_xxxxxxxxxxxxx"
                        />
                        <p class="description">Your Resend API key (starts with "re_")</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_from_email">From Email</label>
                    </th>
                    <td>
                        <input 
                            type="email" 
                            id="ehs_resend_from_email" 
                            name="ehs_resend_from_email" 
                            value="<?php echo esc_attr($resend_from_email); ?>" 
                            class="regular-text"
                        />
                        <p class="description">Email address to send from (must be verified in Resend)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_from_name">From Name</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="ehs_resend_from_name" 
                            name="ehs_resend_from_name" 
                            value="<?php echo esc_attr($resend_from_name); ?>" 
                            class="regular-text"
                        />
                        <p class="description">Display name for sent emails</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_to_email">To Email</label>
                    </th>
                    <td>
                        <input 
                            type="email" 
                            id="ehs_resend_to_email" 
                            name="ehs_resend_to_email" 
                            value="<?php echo esc_attr($resend_to_email); ?>" 
                            class="regular-text"
                        />
                        <p class="description">Email address to receive contact form submissions</p>
                    </td>
                </tr>
            </table>

            <h2>reCAPTCHA v3 Configuration (Optional)</h2>
            <p>Add bot protection using Google reCAPTCHA v3. Get your keys from <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA</a></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="ehs_recaptcha_site_key">Site Key</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="ehs_recaptcha_site_key" 
                            name="ehs_recaptcha_site_key" 
                            value="<?php echo esc_attr($recaptcha_site_key); ?>" 
                            class="regular-text"
                            placeholder="6Lcxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                        />
                        <p class="description">reCAPTCHA v3 Site Key (public)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_recaptcha_secret_key">Secret Key</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="ehs_recaptcha_secret_key" 
                            name="ehs_recaptcha_secret_key" 
                            value="<?php echo esc_attr($recaptcha_secret_key); ?>" 
                            class="regular-text"
                            placeholder="6Lcxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                        />
                        <p class="description">reCAPTCHA v3 Secret Key (private)</p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="ehs_contact_form_settings" class="button button-primary" value="Save Settings" />
            </p>
        </form>

        <hr>

        <h2>Usage</h2>
        <h3>Shortcode</h3>
        <p>Add the contact form anywhere using the shortcode:</p>
        <code>[ehs_contact_form]</code>
        
        <h3>PHP Function</h3>
        <p>Use in templates:</p>
        <pre><code>&lt;?php
echo ehs_render_contact_form(array(
    'show_phone' => true,
    'show_company' => false,
    'submit_text' => 'Send Message'
));
?&gt;</code></pre>

        <h3>Modal Window</h3>
        <p>To use in a modal, create a modal structure and include the form:</p>
        <pre><code>&lt;div id="contact-modal" class="ehs-modal-overlay"&gt;
    &lt;div class="ehs-modal"&gt;
        &lt;div class="ehs-modal-header"&gt;
            &lt;h3 class="ehs-modal-title"&gt;Contact Us&lt;/h3&gt;
            &lt;button class="ehs-modal-close" onclick="ehsCloseContactModal('contact-modal')"&gt;&times;&lt;/button&gt;
        &lt;/div&gt;
        &lt;div class="ehs-modal-body"&gt;
            &lt;?php echo ehs_render_contact_form(); ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;!-- Trigger button --&gt;
&lt;button onclick="ehsOpenContactModal('contact-modal')"&gt;Open Contact Form&lt;/button&gt;</code></pre>
    </div>
    <?php
}

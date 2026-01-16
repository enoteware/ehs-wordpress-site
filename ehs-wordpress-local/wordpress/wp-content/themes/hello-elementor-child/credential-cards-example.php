<?php
/**
 * Credential Cards Usage Example
 * 
 * This file demonstrates how to use the credential card functions.
 * You can include this in a page template or use the functions directly.
 * 
 * Usage in Elementor:
 * 1. Add an HTML widget
 * 2. Use PHP code: <?php ehs_render_credentials_grid(); ?>
 * 
 * Or use shortcode (if you create one):
 * [credentials_grid]
 */

// Example 1: Render all credentials in a grid
ehs_render_credentials_grid();

// Example 2: Render only featured credentials
// $featured = ehs_get_featured_credentials(6);
// echo '<div class="credentials-grid">';
// foreach ($featured as $credential) {
//     ehs_render_credential_card($credential);
// }
// echo '</div>';

// Example 3: Custom query
// ehs_render_credentials_grid(array(
//     'posts_per_page' => 9,
//     'meta_query' => array(
//         array(
//             'key' => 'credential_category',
//             'value' => 'Professional Certification',
//             'compare' => '='
//         )
//     )
// ));

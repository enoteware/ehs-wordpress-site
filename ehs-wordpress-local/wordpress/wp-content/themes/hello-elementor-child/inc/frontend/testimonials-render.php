<?php
/**
 * Google Review Testimonials
 *
 * Loads Adam's testimonials.json and renders cards/sections for homepage
 * and service pages. Quotes stay verbatim. No Review schema markup.
 *
 * @package HelloElementorChild
 * @since 1.0.3
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Path to testimonials data file.
 *
 * @return string
 */
function ehs_testimonials_data_path() {
    return get_stylesheet_directory() . '/data/testimonials.json';
}

/**
 * Load and cache testimonials dataset.
 *
 * @return array
 */
function ehs_get_testimonials_data() {
    static $data = null;
    if ($data !== null) {
        return $data;
    }

    $path = ehs_testimonials_data_path();
    if (!is_readable($path)) {
        $data = array(
            'aggregate'     => array('rating' => 4.7, 'review_count' => 14),
            'google_reviews_url' => 'https://www.google.com/search?q=EHS+Analytical+Solutions+Inc+San+Diego+reviews',
            'testimonials'  => array(),
        );
        return $data;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    if (!is_array($decoded)) {
        $data = array('testimonials' => array());
        return $data;
    }

    $data = $decoded;
    return $data;
}

/**
 * Google reviews profile URL used on cards and badge.
 *
 * @return string
 */
function ehs_testimonials_google_url() {
    $data = ehs_get_testimonials_data();
    $url  = isset($data['google_reviews_url']) ? $data['google_reviews_url'] : '';
    if ($url === '') {
        $url = 'https://www.google.com/search?q=EHS+Analytical+Solutions+Inc+San+Diego+reviews';
    }
    /**
     * Filter the public Google reviews URL used by testimonial cards.
     *
     * @param string $url Absolute URL.
     */
    return apply_filters('ehs_testimonials_google_url', $url);
}

/**
 * Index testimonials by id.
 *
 * @return array<string,array>
 */
function ehs_get_testimonials_by_id() {
    static $by_id = null;
    if ($by_id !== null) {
        return $by_id;
    }
    $by_id = array();
    $data  = ehs_get_testimonials_data();
    foreach ((array) ($data['testimonials'] ?? array()) as $item) {
        if (!empty($item['id'])) {
            $by_id[$item['id']] = $item;
        }
    }
    return $by_id;
}

/**
 * Get testimonials for a placement key or path.
 *
 * @param string $placement Placement string from JSON (e.g. homepage or /construction-safety-consulting/).
 * @return array
 */
function ehs_get_testimonials_for_placement($placement) {
    $placement = trim((string) $placement);
    if ($placement === '') {
        return array();
    }

    // Normalize path placements to trailing-slash form.
    if ($placement[0] === '/') {
        $placement = '/' . trim($placement, '/') . '/';
    }

    $matched = array();
    $data    = ehs_get_testimonials_data();
    foreach ((array) ($data['testimonials'] ?? array()) as $item) {
        $placements = isset($item['placements']) && is_array($item['placements'])
            ? $item['placements']
            : array();
        foreach ($placements as $p) {
            $p = (string) $p;
            if ($p === $placement) {
                $matched[] = $item;
                break;
            }
            if ($p !== '' && $p[0] === '/') {
                $norm = '/' . trim($p, '/') . '/';
                if ($norm === $placement) {
                    $matched[] = $item;
                    break;
                }
            }
        }
    }
    return $matched;
}

/**
 * Ordered testimonials by id list.
 *
 * @param string[] $ids
 * @return array
 */
function ehs_get_testimonials_by_ids(array $ids) {
    $by_id  = ehs_get_testimonials_by_id();
    $result = array();
    foreach ($ids as $id) {
        if (isset($by_id[$id])) {
            $result[] = $by_id[$id];
        }
    }
    return $result;
}

/**
 * Display name for a reviewer.
 *
 * @param array $item Testimonial row.
 * @return string
 */
function ehs_testimonial_display_name(array $item) {
    if (!empty($item['display_name'])) {
        return (string) $item['display_name'];
    }
    return (string) ($item['reviewer'] ?? '');
}

/**
 * Quote text for a placement context.
 *
 * @param array  $item    Testimonial row.
 * @param string $context homepage|service
 * @return string
 */
function ehs_testimonial_quote_text(array $item, $context = 'service') {
    if ($context === 'homepage' && !empty($item['homepage_short_version'])) {
        return (string) $item['homepage_short_version'];
    }
    return (string) ($item['quote'] ?? '');
}

/**
 * URL for the multicolor Google "G" mark (dashboardicons / Homarr set).
 *
 * Source: https://dashboardicons.com/icons/google
 * SVG: https://cdn.jsdelivr.net/gh/homarr-labs/dashboard-icons/svg/google.svg
 *
 * @return string
 */
function ehs_testimonials_google_logo_url() {
    $path = get_stylesheet_directory() . '/assets/images/icons/google.svg';
    if (is_readable($path)) {
        return get_stylesheet_directory_uri() . '/assets/images/icons/google.svg';
    }
    return 'https://cdn.jsdelivr.net/gh/homarr-labs/dashboard-icons/svg/google.svg';
}

/**
 * URL for Google Maps mark (optional secondary asset from same set).
 *
 * @return string
 */
function ehs_testimonials_google_maps_logo_url() {
    $path = get_stylesheet_directory() . '/assets/images/icons/google-maps.svg';
    if (is_readable($path)) {
        return get_stylesheet_directory_uri() . '/assets/images/icons/google-maps.svg';
    }
    return 'https://cdn.jsdelivr.net/gh/homarr-labs/dashboard-icons/svg/google-maps.svg';
}

/**
 * Img markup for the Google logo (decorative; paired with text).
 *
 * @param string $class Extra CSS classes.
 * @return string
 */
function ehs_render_google_logo_img($class = 'ehs-google-logo') {
    $url = ehs_testimonials_google_logo_url();
    return sprintf(
        '<img class="%s" src="%s" alt="" width="18" height="18" loading="lazy" decoding="async" aria-hidden="true" />',
        esc_attr($class),
        esc_url($url)
    );
}

/**
 * Render gold star row (visual only, not schema).
 *
 * @param int $stars Number of filled stars (1-5).
 * @return string
 */
function ehs_render_testimonial_stars($stars = 5) {
    $stars   = max(1, min(5, (int) $stars));
    $out     = '<div class="ehs-testimonial-card__stars" aria-label="' . esc_attr($stars . ' out of 5 stars') . '">';
    for ($i = 0; $i < 5; $i++) {
        $filled = $i < $stars ? ' is-filled' : '';
        $out   .= '<span class="ehs-testimonial-card__star' . $filled . '" aria-hidden="true">★</span>';
    }
    $out .= '</div>';
    return $out;
}

/**
 * Render a single testimonial card.
 *
 * @param array  $item    Testimonial row.
 * @param array  $args    {
 *     @type string $context   homepage|service
 *     @type string $layout    grid|full
 *     @type bool   $read_more Enable expand for long quotes.
 * }
 * @return string
 */
function ehs_render_testimonial_card(array $item, $args = array()) {
    $args = wp_parse_args($args, array(
        'context'   => 'service',
        'layout'    => 'grid',
        'read_more' => true,
    ));

    $quote = ehs_testimonial_quote_text($item, $args['context']);
    if ($quote === '') {
        return '';
    }

    $name      = ehs_testimonial_display_name($item);
    $stars     = isset($item['stars']) ? (int) $item['stars'] : 5;
    $google    = ehs_testimonials_google_url();
    $word_count = str_word_count(wp_strip_all_tags($quote));
    $use_more  = !empty($args['read_more']) && $word_count > 60;
    $layout    = $args['layout'] === 'full' ? ' ehs-testimonial-card--full' : '';
    $id_attr   = !empty($item['id']) ? ' data-testimonial-id="' . esc_attr($item['id']) . '"' : '';

    ob_start();
    ?>
    <article class="ehs-testimonial-card<?php echo esc_attr($layout); ?>"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
        <?php echo ehs_render_testimonial_stars($stars); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php if ($use_more) : ?>
            <blockquote class="ehs-testimonial-card__quote ehs-testimonial-card__quote--collapsible">
                <p class="ehs-testimonial-card__quote-text"><?php echo esc_html($quote); ?></p>
            </blockquote>
            <button type="button" class="ehs-testimonial-card__read-more" aria-expanded="false">
                Read more
            </button>
        <?php else : ?>
            <blockquote class="ehs-testimonial-card__quote">
                <p class="ehs-testimonial-card__quote-text"><?php echo esc_html($quote); ?></p>
            </blockquote>
        <?php endif; ?>

        <footer class="ehs-testimonial-card__footer">
            <?php if ($name !== '') : ?>
                <cite class="ehs-testimonial-card__name"><?php echo esc_html($name); ?></cite>
            <?php endif; ?>
            <a class="ehs-testimonial-card__google-label"
               href="<?php echo esc_url($google); ?>"
               target="_blank"
               rel="noopener noreferrer">
                <?php echo ehs_render_google_logo_img('ehs-google-logo ehs-google-logo--sm'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <span class="ehs-testimonial-card__google-text">Google Review</span>
            </a>
        </footer>
    </article>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render a grid or stack of testimonial cards.
 *
 * @param array $items Testimonials.
 * @param array $args  Render args (context, layout, class).
 * @return string
 */
function ehs_render_testimonial_cards(array $items, $args = array()) {
    if (empty($items)) {
        return '';
    }

    $args = wp_parse_args($args, array(
        'context' => 'service',
        'layout'  => 'grid',
        'class'   => '',
    ));

    $grid_mod = $args['layout'] === 'full'
        ? ' ehs-testimonials__grid--full'
        : ' ehs-testimonials__grid--cols-' . min(3, count($items));

    $extra = $args['class'] !== '' ? ' ' . sanitize_html_class($args['class']) : '';

    $html  = '<div class="ehs-testimonials__grid' . esc_attr($grid_mod . $extra) . '">';
    foreach ($items as $item) {
        $html .= ehs_render_testimonial_card($item, array(
            'context' => $args['context'],
            'layout'  => $args['layout'],
        ));
    }
    $html .= '</div>';
    return $html;
}

/**
 * Section header (title + optional Google rating badge).
 *
 * @param array $args {
 *     @type string $title
 *     @type bool   $show_badge
 *     @type string $heading_tag
 * }
 * @return string
 */
function ehs_render_testimonials_section_header($args = array()) {
    $args = wp_parse_args($args, array(
        'title'       => 'What Our Clients Say',
        'show_badge'  => true,
        'heading_tag' => 'h2',
    ));

    $data   = ehs_get_testimonials_data();
    $rating = isset($data['aggregate']['rating']) ? $data['aggregate']['rating'] : 4.7;
    $google = ehs_testimonials_google_url();
    $tag    = in_array($args['heading_tag'], array('h2', 'h3'), true) ? $args['heading_tag'] : 'h2';

    ob_start();
    ?>
    <div class="ehs-testimonials__header">
        <<?php echo $tag; ?> class="ehs-testimonials__title"><?php echo esc_html($args['title']); ?></<?php echo $tag; ?>>
        <?php if (!empty($args['show_badge'])) : ?>
            <a class="ehs-testimonials__badge"
               href="<?php echo esc_url($google); ?>"
               target="_blank"
               rel="noopener noreferrer">
                <?php echo ehs_render_google_logo_img('ehs-google-logo ehs-google-logo--badge'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <span class="ehs-testimonials__badge-star" aria-hidden="true">★</span>
                <?php echo esc_html(number_format((float) $rating, 1)); ?> on Google
            </a>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Full homepage testimonials section HTML.
 *
 * @return string
 */
function ehs_render_homepage_testimonials_section() {
    $ids   = array('chris-mcandrew', 'jessica-perer', 'dan-treiber');
    $items = ehs_get_testimonials_by_ids($ids);
    if (empty($items)) {
        $items = ehs_get_testimonials_for_placement('homepage');
    }
    if (empty($items)) {
        return '';
    }

    ob_start();
    ?>
    <section class="ehs-testimonials-section ehs-testimonials-section--home" aria-label="Client testimonials">
        <div class="container">
            <?php
            echo ehs_render_testimonials_section_header(array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'title'      => 'What Our Clients Say',
                'show_badge' => true,
            ));
            echo ehs_render_testimonial_cards($items, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'context' => 'homepage',
                'layout'  => 'grid',
            ));
            ?>
        </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

/**
 * Echo homepage section (template helper).
 *
 * @return void
 */
function ehs_homepage_testimonials() {
    echo ehs_render_homepage_testimonials_section(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Service page placement config keyed by post slug.
 *
 * @return array
 */
function ehs_service_testimonial_placements() {
    return array(
        'construction-safety-consulting' => array(
            'path'         => '/construction-safety-consulting/',
            'layout'       => 'full',
            'ids'          => array('chris-mcandrew'),
            // Default placement (after intro section). Anchoring on the
            // 'Recent Construction Projects' heading dropped the card inside
            // the project-timeline markup because the next h3 lives within it.
            'after_heading' => null,
            'title'        => 'What Our Clients Say',
            'show_badge'   => false,
        ),
        'industrial-hygiene-san-diego' => array(
            'path'          => '/industrial-hygiene-san-diego/',
            'layout'        => 'grid',
            'ids'           => array('mark-hayes', 'darryl-gurholt'),
            'after_heading' => null, // after main body (early inject)
            'title'         => 'What Our Clients Say',
            'show_badge'    => false,
        ),
        'environmental-health-and-safety-ehs-consulting' => array(
            'path'          => '/environmental-health-and-safety-ehs-consulting/',
            'layout'        => 'grid',
            'ids'           => array('jessica-perer', 'joe-nuccio'),
            'after_heading' => null,
            'title'         => 'What Our Clients Say',
            'show_badge'    => false,
        ),
        'san-diego-asbestos-testing' => array(
            'path'          => '/san-diego-asbestos-testing/',
            'layout'        => 'full',
            'ids'           => array('jordan-karney-chaim'),
            'after_heading' => null,
            'title'         => 'What Our Clients Say',
            'show_badge'    => false,
        ),
    );
}

/**
 * Build service testimonials block HTML for a slug.
 *
 * @param string $slug Service post slug.
 * @return string
 */
function ehs_render_service_testimonials_block($slug) {
    $map = ehs_service_testimonial_placements();
    if (!isset($map[$slug])) {
        return '';
    }

    $cfg   = $map[$slug];
    $items = !empty($cfg['ids'])
        ? ehs_get_testimonials_by_ids($cfg['ids'])
        : ehs_get_testimonials_for_placement($cfg['path']);

    if (empty($items)) {
        return '';
    }

    ob_start();
    ?>
    <section class="ehs-testimonials-section ehs-testimonials-section--service" aria-label="Client testimonials">
        <?php
        echo ehs_render_testimonials_section_header(array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            'title'       => $cfg['title'],
            'show_badge'  => !empty($cfg['show_badge']),
            'heading_tag' => 'h2',
        ));
        echo ehs_render_testimonial_cards($items, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            'context' => 'service',
            'layout'  => $cfg['layout'],
        ));
        ?>
    </section>
    <?php
    return (string) ob_get_clean();
}

/**
 * Inject service testimonials into post content at the right heading, or append.
 *
 * @param string $content Post content HTML.
 * @return string
 */
function ehs_inject_service_testimonials($content) {
    if (is_admin() || !is_singular('services') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $slug = get_post_field('post_name', get_queried_object_id());
    $map  = ehs_service_testimonial_placements();
    if (!isset($map[$slug])) {
        return $content;
    }

    // Avoid double-injection.
    if (strpos($content, 'ehs-testimonials-section--service') !== false) {
        return $content;
    }

    $block = ehs_render_service_testimonials_block($slug);
    if ($block === '') {
        return $content;
    }

    $cfg     = $map[$slug];
    $heading = isset($cfg['after_heading']) ? $cfg['after_heading'] : null;

    if ($heading) {
        // Insert after the heading block that matches the configured title.
        $pattern = '/(<h[2-4][^>]*>\s*' . preg_quote($heading, '/') . '\s*<\/h[2-4]>)/iu';
        if (preg_match($pattern, $content, $m, PREG_OFFSET_CAPTURE)) {
            $insert_at = $m[0][1] + strlen($m[0][0]);

            // Prefer placing after the whole section that follows the heading:
            // walk forward until next h2/h3 or end, then insert before that next heading.
            $after = substr($content, $insert_at);
            if (preg_match('/<h[2-3]\b/i', $after, $nm, PREG_OFFSET_CAPTURE)) {
                $insert_at = $insert_at + $nm[0][1];
                return substr($content, 0, $insert_at) . $block . substr($content, $insert_at);
            }

            return substr($content, 0, $insert_at) . $block . substr($content, $insert_at);
        }
    }

    // Default: after first substantial section (first h2 block + following content until next h2), else prepend after first paragraph block.
    if (preg_match_all('/<h2\b[^>]*>.*?<\/h2>/is', $content, $matches, PREG_OFFSET_CAPTURE) && count($matches[0]) >= 1) {
        // Insert before second h2 if present (after intro), else after first h2 section end (before first h2 of "FAQ" style).
        if (count($matches[0]) >= 2) {
            // For IH / EHS consulting: after main description = before second major h2 often works poorly.
            // Insert before the last FAQ-like heading if found, else after first section.
            $faq_pos = false;
            if (preg_match('/<h[2-3][^>]*>\s*(FAQ|Frequently Asked|Why Choose|Related)/iu', $content, $fm, PREG_OFFSET_CAPTURE)) {
                $faq_pos = $fm[0][1];
            }
            if ($faq_pos !== false) {
                return substr($content, 0, $faq_pos) . $block . substr($content, $faq_pos);
            }
            // After content under first h2: position of second h2.
            $second = $matches[0][1][1];
            return substr($content, 0, $second) . $block . substr($content, $second);
        }
    }

    return $content . $block;
}
add_filter('the_content', 'ehs_inject_service_testimonials', 25);

/**
 * Shortcode: [ehs_testimonials placement="homepage"] or ids="a,b"
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function ehs_testimonials_shortcode($atts) {
    $atts = shortcode_atts(array(
        'placement' => '',
        'ids'       => '',
        'layout'    => 'grid',
        'context'   => 'service',
        'title'     => '',
        'badge'     => '0',
    ), $atts, 'ehs_testimonials');

    if ($atts['placement'] === 'homepage' && $atts['ids'] === '' && $atts['title'] === '') {
        return ehs_render_homepage_testimonials_section();
    }

    if ($atts['ids'] !== '') {
        $ids   = array_filter(array_map('trim', explode(',', $atts['ids'])));
        $items = ehs_get_testimonials_by_ids($ids);
    } elseif ($atts['placement'] !== '') {
        $items = ehs_get_testimonials_for_placement($atts['placement']);
    } else {
        return '';
    }

    if (empty($items)) {
        return '';
    }

    $html = '<section class="ehs-testimonials-section ehs-testimonials-section--shortcode">';
    if ($atts['title'] !== '') {
        $html .= ehs_render_testimonials_section_header(array(
            'title'      => $atts['title'],
            'show_badge' => $atts['badge'] === '1' || $atts['badge'] === 'true',
        ));
    }
    $html .= ehs_render_testimonial_cards($items, array(
        'context' => $atts['context'],
        'layout'  => $atts['layout'],
    ));
    $html .= '</section>';
    return $html;
}
add_shortcode('ehs_testimonials', 'ehs_testimonials_shortcode');

/**
 * Inline script for Read more toggles (small, no separate file needed).
 *
 * @return void
 */
function ehs_testimonials_enqueue_script() {
    if (is_admin()) {
        return;
    }

    $needs = is_front_page() || is_page_template('front-page-new.php') || is_singular('services');
    if (!$needs) {
        return;
    }

    $js = <<<'JS'
(function () {
  function init(root) {
    root.querySelectorAll('.ehs-testimonial-card__read-more').forEach(function (btn) {
      if (btn.dataset.bound) return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', function () {
        var card = btn.closest('.ehs-testimonial-card');
        if (!card) return;
        var open = card.classList.toggle('is-expanded');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.textContent = open ? 'Read less' : 'Read more';
      });
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { init(document); });
  } else {
    init(document);
  }
})();
JS;

    wp_register_script('ehs-testimonials', false, array(), '1.0.3', true);
    wp_enqueue_script('ehs-testimonials');
    wp_add_inline_script('ehs-testimonials', $js);
}
add_action('wp_enqueue_scripts', 'ehs_testimonials_enqueue_script', 30);

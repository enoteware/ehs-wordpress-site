<?php
/**
 * Content Icons – replace emojis with theme SVGs in post content
 *
 * Provides ehs_get_content_icon_svg() and a the_content/the_excerpt filter
 * that replaces emoji characters and WordPress emoji <img> tags with inline SVGs.
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Icon slug to filename mapping (under assets/service-icons/).
 *
 * @var array<string, string>
 */
function ehs_content_icon_files() {
    return array(
        'target'    => 'target.svg',
        'road'      => 'road.svg',
        'gear'      => 'gear.svg',
        'clipboard' => 'clipboard-check.svg',
        'rocket'    => 'rocket.svg',
        'briefcase' => 'briefcase.svg',
        'clock'     => 'clock.svg',
        'check'     => 'check.svg',
        'calendar'  => 'calendar.svg',
    );
}

/**
 * WordPress emoji codepoint (hex in URL, e.g. 1f3af) to icon slug.
 *
 * @var array<string, string>
 */
function ehs_emoji_codepoint_to_slug() {
    return array(
        '1f3af' => 'target',   // 🎯
        '1f6e3' => 'road',     // 🛣️
        '2699'  => 'gear',     // ⚙️
        '1f4cb' => 'clipboard', // 📋
        '1f680' => 'rocket',   // 🚀
        '1f4bc' => 'briefcase', // 💼
        '23f1'  => 'clock',    // ⏱️
        '2713'  => 'check',    // ✓
        '1f4c5' => 'calendar', // 📅
    );
}

/**
 * Get inline SVG markup for a content icon by slug.
 *
 * @param string $slug Icon slug: target, road, gear, clipboard, rocket, briefcase, clock, check.
 * @return string SVG markup or empty string if slug unknown / file missing.
 */
function ehs_get_content_icon_svg($slug) {
    $files = ehs_content_icon_files();
    if (!isset($files[$slug])) {
        return '';
    }
    $dir = get_stylesheet_directory() . '/assets/service-icons/';
    $path = $dir . $files[$slug];
    if (!is_readable($path)) {
        return '';
    }
    $svg = file_get_contents($path);
    if ($svg === false) {
        return '';
    }
    // Strip XML comments so we can use in post content
    $svg = preg_replace('/<!--.*?-->\s*/s', '', $svg);
    $svg = trim($svg);
    return $svg;
}

/**
 * Wrap SVG in span for content replacement.
 *
 * @param string $svg Inline SVG markup.
 * @return string Markup with wrapper.
 */
function ehs_content_icon_wrap($svg) {
    if ($svg === '') {
        return '';
    }
    return '<span class="ehs-content-icon" aria-hidden="true">' . $svg . '</span>';
}

/**
 * Replace emoji <img> tags and raw emoji characters with theme SVGs.
 *
 * @param string $content Post content or excerpt.
 * @return string Filtered content.
 */
function ehs_replace_emoji_with_svg($content) {
    if ($content === '') {
        return $content;
    }
    $has_emoji = (strpos($content, 'emoji') !== false || strpos($content, '🎯') !== false || strpos($content, '🛣') !== false || strpos($content, '⚙') !== false || strpos($content, '📋') !== false || strpos($content, '🚀') !== false || strpos($content, '💼') !== false || strpos($content, '⏱') !== false || strpos($content, '✓') !== false || strpos($content, '📅') !== false);
    if (!$has_emoji) {
        return $content;
    }

    $codepoint_to_slug = ehs_emoji_codepoint_to_slug();
    $slug_to_svg = array();
    foreach (array_unique($codepoint_to_slug) as $slug) {
        $slug_to_svg[$slug] = ehs_content_icon_wrap(ehs_get_content_icon_svg($slug));
    }

    // Replace WordPress emoji <img> tags (class="emoji" or src contains s.w.org/emoji)
    $content = preg_replace_callback(
        '/<img(?=[^>]*\b(?:class="[^"]*emoji[^"]*"|src="[^"]*s\.w\.org[^"]*emoji[^"]*"))[^>]*>/i',
        function ($m) use ($codepoint_to_slug, $slug_to_svg) {
            $tag = $m[0];
            if (preg_match('/src="[^"]*\/([a-f0-9]+)\.svg/i', $tag, $src_match)) {
                $codepoint = strtolower($src_match[1]);
                if (isset($codepoint_to_slug[$codepoint]) && isset($slug_to_svg[$codepoint_to_slug[$codepoint]])) {
                    return $slug_to_svg[$codepoint_to_slug[$codepoint]];
                }
            }
            return $tag;
        },
        $content
    );

    // Replace raw emoji characters (UTF-8)
    $char_to_slug = array(
        '🎯' => 'target',
        '🛣️' => 'road',
        '🛣'  => 'road',
        '⚙️' => 'gear',
        '⚙'  => 'gear',
        '📋' => 'clipboard',
        '🚀' => 'rocket',
        '💼' => 'briefcase',
        '⏱️' => 'clock',
        '⏱'  => 'clock',
        '✓'  => 'check',
        '📅' => 'calendar',
    );
    foreach ($char_to_slug as $char => $slug) {
        if (isset($slug_to_svg[$slug])) {
            $content = str_replace($char, $slug_to_svg[$slug], $content);
        }
    }

    return $content;
}

add_filter('the_content', 'ehs_replace_emoji_with_svg', 15);
add_filter('the_excerpt', 'ehs_replace_emoji_with_svg', 15);

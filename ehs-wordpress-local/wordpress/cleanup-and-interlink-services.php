<?php
/**
 * Service Pages Cleanup and Interlinking
 * 1. Remove "Contact Us" sections
 * 2. Clean up excessive whitespace
 * 3. Add internal links to related services
 */

// Get all service posts
$posts = get_posts([
    'post_type' => 'services',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'ID',
    'order' => 'ASC'
]);

echo "Found " . count($posts) . " service posts\n\n";

// Service URL mapping for internal linking
$service_links = [
    'industrial hygiene' => '/industrial-hygiene-san-diego/',
    'construction safety' => '/construction-safety-consulting/',
    'asbestos testing' => '/san-diego-asbestos-testing/',
    'mold testing' => '/mold-testing/',
    'indoor air quality' => '/san-diego-indoor-air-quality-testing/',
    'fire and smoke' => '/california-fire-and-smoke-assessments/',
    'ergonomic' => '/san-diego-ergonomic-evaluations/',
    'ehs consulting' => '/environmental-health-and-safety-ehs-consulting/',
    'ehs staff outsourcing' => '/ehs-staff-outsourcing/',
    'ssho' => '/ssho-services-california/',
    'caltrans' => '/caltrans-construction-safety-services/',
    'federal contracting' => '/federal-contracting-sdvosb/',
    'lead compliance' => '/lead-compliance-plan-services/',
    'water damage' => '/water-damage-assessments/',
    'fume hood' => '/fume-hood-local-exhaust-certifications/',
    'osha 30' => '/osha-30-hour-training-certification-in-ca/',
];

foreach ($posts as $post) {
    $content = $post->post_content;
    $original = $content;
    $changes = [];

    // 1. Remove "Contact Us" section
    // Pattern matches: Contact Us heading + list with phone/email/hours
    $content = preg_replace(
        '/<h3[^>]*>Contact Us<\/h3>\s*<ul[^>]*>\s*<li>\(619\) 288-3094<\/li>\s*<li>\[email protected\]<\/li>\s*<li><strong>Hours:<\/strong>[^<]*<\/li>\s*<\/ul>/is',
        '',
        $content
    );

    if ($content !== $original) {
        $changes[] = "Removed Contact Us section";
        $original = $content;
    }

    // 2. Clean up excessive whitespace and line breaks

    // Remove multiple consecutive blank lines (more than 2 newlines)
    $content = preg_replace("/\n{3,}/", "\n\n", $content);

    // Remove whitespace before closing tags
    $content = preg_replace("/\s+<\/(p|h[1-6]|li|ul|ol|div)>/", '</$1>', $content);

    // Remove whitespace after opening tags
    $content = preg_replace("/<(p|h[1-6]|li|ul|ol|div)([^>]*)>\s+/", '<$1$2>', $content);

    // Normalize spaces (multiple spaces to single space)
    $content = preg_replace('/ {2,}/', ' ', $content);

    // Remove trailing spaces at end of lines
    $content = preg_replace('/ +$/m', '', $content);

    if ($content !== $original) {
        $changes[] = "Cleaned up whitespace";
        $original = $content;
    }

    // 3. Add internal links to related services
    // Only add links if they don't already exist
    $current_slug = $post->post_name;

    foreach ($service_links as $keyword => $url) {
        // Skip linking to self
        if (strpos($current_slug, str_replace([' ', '/'], ['', ''], $keyword)) !== false) {
            continue;
        }

        // Check if this service is mentioned in the text (case insensitive)
        // and not already linked
        $pattern = '/(?<!href=")(?<!\/)\b(' . preg_quote($keyword, '/') . ')\b(?![^<]*<\/a>)/i';

        // Only link the first occurrence
        $count = 0;
        $content = preg_replace_callback(
            $pattern,
            function($matches) use ($url, &$count) {
                if ($count > 0) {
                    return $matches[0]; // Don't link subsequent occurrences
                }
                $count++;
                return '<a href="' . $url . '">' . $matches[1] . '</a>';
            },
            $content,
            1 // Only replace first occurrence
        );

        if ($count > 0) {
            $changes[] = "Added link to: $keyword";
        }
    }

    // Update post if changed
    if ($content !== $post->post_content) {
        wp_update_post([
            'ID' => $post->ID,
            'post_content' => $content
        ]);

        echo "✓ Updated: " . $post->post_title . " (ID: {$post->ID})\n";
        foreach ($changes as $change) {
            echo "  - $change\n";
        }
        echo "\n";
    } else {
        echo "○ No changes: " . $post->post_title . " (ID: {$post->ID})\n";
    }
}

echo "\n✅ Done!\n";

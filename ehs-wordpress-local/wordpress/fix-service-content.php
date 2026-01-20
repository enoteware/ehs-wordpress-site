<?php
/**
 * Fix service post content:
 * 1. Remove certification badge images
 * 2. Wrap bare paragraphs in proper <p> tags
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

foreach ($posts as $post) {
    $content = $post->post_content;
    $original = $content;
    $changes = [];

    // 1. Remove certification badge images
    $cert_patterns = [
        'CSP', 'CIH', 'PMP', 'SDVOSB', 'DVBE', 'CUSP', 'IOSH', 'USOLN',
        'certification', 'certified', 'logo', 'credential',
        'download-1\.png', 'cve_completed', 'project-management-professional',
        'new_iaq_certification', 'DVBE-LOGO', 'Board of Certified Safety'
    ];

    $cert_pattern = '/(' . implode('|', $cert_patterns) . ')/i';

    // Remove image containers with certification badges
    $content = preg_replace_callback(
        '/<div class="service-image-container">\s*<img[^>]*>/is',
        function($matches) use ($cert_pattern, &$changes) {
            if (preg_match($cert_pattern, $matches[0])) {
                $changes[] = "Removed certification badge image";
                return '';
            }
            return $matches[0];
        },
        $content
    );

    // Clean up empty image containers
    $content = preg_replace('/<div class="service-image-container"><\/div>\s*/i', '', $content);

    // 2. Wrap bare paragraphs (text not in any tag)
    // Split by newlines, find lines that are bare text
    $lines = explode("\n", $content);
    $fixed_lines = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // Skip empty lines
        if (empty($trimmed)) {
            $fixed_lines[] = $line;
            continue;
        }

        // Check if line is bare text (starts with capital letter, no opening tag)
        if (preg_match('/^[A-Z]/', $trimmed) && !preg_match('/^</', $trimmed)) {
            // This is bare text, wrap it
            $fixed_lines[] = '<p class="service-text">' . $trimmed . '</p>';
            $changes[] = "Wrapped bare paragraph: " . substr($trimmed, 0, 50) . "...";
        } else {
            $fixed_lines[] = $line;
        }
    }

    $content = implode("\n", $fixed_lines);

    // Clean up multiple blank lines
    $content = preg_replace("/\n{3,}/", "\n\n", $content);

    // 3. Add service-text class to <p> tags that don't have it
    $before_p_fix = $content;
    $content = preg_replace('/<p>/', '<p class="service-text">', $content);
    if ($content !== $before_p_fix) {
        $changes[] = "Added service-text class to plain <p> tags";
    }

    // Update post if changed
    if ($content !== $original) {
        wp_update_post([
            'ID' => $post->ID,
            'post_content' => $content
        ]);

        echo "✓ Updated: " . $post->post_title . " (ID: {$post->ID})\n";
        foreach (array_unique($changes) as $change) {
            echo "  - $change\n";
        }
        echo "\n";
    } else {
        echo "○ No changes: " . $post->post_title . " (ID: {$post->ID})\n";
    }
}

echo "\n✅ Done!\n";

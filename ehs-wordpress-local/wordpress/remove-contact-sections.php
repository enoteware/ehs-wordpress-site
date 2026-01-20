<?php
/**
 * Remove all "Contact Us" sections from service pages
 * Handles different formatting variations
 */

$posts = get_posts([
    'post_type' => 'services',
    'post_status' => 'publish',
    'posts_per_page' => -1
]);

echo "Checking " . count($posts) . " service posts\n\n";

foreach ($posts as $post) {
    $content = $post->post_content;
    $original = $content;

    // Pattern 1: Contact Us with any class variations
    $content = preg_replace(
        '/<h3[^>]*>Contact Us<\/h3>\s*<ul[^>]*>.*?<\/ul>/is',
        '',
        $content
    );

    // Pattern 2: Get in touch variations
    $content = preg_replace(
        '/<h[23][^>]*>Get [Ii]n [Tt]ouch<\/h[23]>\s*<ul[^>]*>.*?<\/ul>/is',
        '',
        $content
    );

    // Pattern 3: Contact info lists with phone/email/hours
    $content = preg_replace(
        '/<ul[^>]*>\s*<li>\(619\)\s*288-3094<\/li>.*?<\/ul>/is',
        '',
        $content
    );

    // Pattern 4: Standalone contact headings
    $content = preg_replace(
        '/<h[23][^>]*>\s*Contact\s*(Us|Information)?<\/h[23]>/is',
        '',
        $content
    );

    // Clean up any resulting multiple blank lines
    $content = preg_replace("/\n{3,}/", "\n\n", $content);

    if ($content !== $original) {
        wp_update_post([
            'ID' => $post->ID,
            'post_content' => $content
        ]);
        echo "✓ Removed contact section from: " . $post->post_title . " (ID: {$post->ID})\n";
    } else {
        echo "○ No contact section found: " . $post->post_title . "\n";
    }
}

echo "\n✅ Done!\n";

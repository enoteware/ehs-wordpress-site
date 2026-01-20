<?php
/**
 * Report on internal links in service pages
 */

$posts = get_posts([
    'post_type' => 'services',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC'
]);

echo "Internal Links Report\n";
echo "=====================\n\n";

$total_links = 0;

foreach ($posts as $post) {
    $content = $post->post_content;

    // Count internal links (starting with /)
    preg_match_all('/<a href="\/[^"]*">([^<]*)<\/a>/', $content, $matches);
    $link_count = count($matches[0]);
    $total_links += $link_count;

    echo str_pad($post->post_title, 50) . " | " . $link_count . " link" . ($link_count != 1 ? 's' : '') . "\n";

    if ($link_count > 0) {
        foreach ($matches[1] as $index => $link_text) {
            $url = $matches[0][$index];
            preg_match('/href="([^"]*)"/', $url, $url_match);
            echo "  → " . $link_text . " (" . $url_match[1] . ")\n";
        }
    }
    echo "\n";
}

echo "=====================\n";
echo "Total internal links: $total_links\n";

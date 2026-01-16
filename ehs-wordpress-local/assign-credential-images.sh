#!/bin/bash
# Assign Featured Images to Credentials
# Matches images from media library to credentials based on filename

echo "Finding credentials and matching images..."

# Get all credentials
CREDENTIALS=$(ddev exec wp post list --post_type=credentials --format=json --path=/var/www/html/wordpress)

# Process each credential
echo "$CREDENTIALS" | ddev exec wp eval '
$credentials = json_decode(file_get_contents("php://stdin"), true);
if (!$credentials) {
    echo "No credentials found.\n";
    exit;
}

// Get all images
$images = get_posts(array(
    "post_type" => "attachment",
    "post_mime_type" => "image",
    "posts_per_page" => -1,
    "post_status" => "inherit",
));

// Build image lookup
$image_lookup = array();
foreach ($images as $img) {
    $file = get_attached_file($img->ID);
    if ($file) {
        $image_lookup[] = array(
            "id" => $img->ID,
            "filename" => basename($file),
            "title" => get_the_title($img->ID),
        );
    }
}

// Process each credential
foreach ($credentials as $cred) {
    $cred_id = $cred["ID"];
    $title = $cred["post_title"];
    $acronym = get_post_meta($cred_id, "credential_acronym", true);
    
    echo "Processing: {$title}";
    if ($acronym) echo " ({$acronym})";
    echo "\n";
    
    // Check if already has featured image
    $current = get_post_thumbnail_id($cred_id);
    if ($current) {
        $current_file = get_attached_file($current);
        echo "  Already has: " . basename($current_file) . "\n";
        continue;
    }
    
    // Find matching image
    $acronym_lower = strtolower($acronym);
    $best_match = null;
    
    foreach ($image_lookup as $img) {
        $filename_lower = strtolower($img["filename"]);
        
        // Exact acronym match
        if ($acronym && strpos($filename_lower, $acronym_lower) !== false) {
            $best_match = $img;
            break;
        }
    }
    
    if ($best_match) {
        set_post_thumbnail($cred_id, $best_match["id"]);
        echo "  ✅ Assigned: {$best_match["filename"]}\n";
    } else {
        echo "  ⚠️  No match found\n";
    }
    echo "\n";
}
' --path=/var/www/html/wordpress

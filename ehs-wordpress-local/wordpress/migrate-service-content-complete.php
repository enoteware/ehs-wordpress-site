<?php
/**
 * Complete Service Content Migration Script
 *
 * Migrates content from live ehsanalytical.com to dev site
 * - Extracts structured content (not raw HTML)
 * - Downloads and uploads images to dev media library
 * - Transforms to dev design system CSS classes
 * - Updates WordPress posts with styled content
 *
 * Usage: ddev exec wp eval-file migrate-service-content-complete.php --path=/var/www/html/wordpress
 *
 * Options:
 *   --dry-run     Preview changes without saving
 *   --service=X   Migrate only specific service slug
 *   --verbose     Show detailed output
 */

// Prevent timeout
set_time_limit(0);

// ===========================================
// CONFIGURATION - Edit these settings
// ===========================================
$dry_run = false;  // Set to false to actually save changes
$verbose = true;  // Show detailed output
$single_service = 'construction-safety-consulting';  // Set to null for all services, or specific slug
// ===========================================

// Service mapping: slug => [live_url, dev_post_id]
$services = [
    'construction-safety-consulting' => [
        'live_url' => 'https://ehsanalytical.com/construction-safety-consulting/',
        'dev_post_id' => 3277
    ],
    'caltrans-construction-safety-services' => [
        'live_url' => 'https://ehsanalytical.com/caltrans-construction-safety-services/',
        'dev_post_id' => 3273
    ],
    'ssho-services-california' => [
        'live_url' => 'https://ehsanalytical.com/ssho-services-california/',
        'dev_post_id' => 3269
    ],
    'lead-compliance-plan-services' => [
        'live_url' => 'https://ehsanalytical.com/lead-compliance-plan-services/',
        'dev_post_id' => 3271
    ],
    'federal-contracting-sdvosb' => [
        'live_url' => 'https://ehsanalytical.com/federal-contracting-sdvosb/',
        'dev_post_id' => 3275
    ],
    'environmental-health-and-safety-ehs-consulting' => [
        'live_url' => 'https://ehsanalytical.com/environmental-health-and-safety-ehs-consulting/',
        'dev_post_id' => 3286
    ],
    'ehs-staff-outsourcing' => [
        'live_url' => 'https://ehsanalytical.com/ehs-staff-outsourcing/',
        'dev_post_id' => 3287
    ],
    'industrial-hygiene-san-diego' => [
        'live_url' => 'https://ehsanalytical.com/industrial-hygiene-san-diego/',
        'dev_post_id' => 3285
    ],
    'indoor-air-quality-testing' => [
        'live_url' => 'https://ehsanalytical.com/san-diego-indoor-air-quality-testing/',
        'dev_post_id' => 3284
    ],
    'mold-testing' => [
        'live_url' => 'https://ehsanalytical.com/mold-testing/',
        'dev_post_id' => 3283
    ],
    'asbestos-testing' => [
        'live_url' => 'https://ehsanalytical.com/san-diego-asbestos-testing/',
        'dev_post_id' => 3282
    ],
    'ergonomic-evaluations' => [
        'live_url' => 'https://ehsanalytical.com/san-diego-ergonomic-evaluations/',
        'dev_post_id' => 3281
    ],
    'water-damage-assessments' => [
        'live_url' => 'https://ehsanalytical.com/water-damage-assessments/',
        'dev_post_id' => 3280
    ],
    'california-fire-and-smoke-assessments' => [
        'live_url' => 'https://ehsanalytical.com/california-fire-and-smoke-assessments/',
        'dev_post_id' => 3279
    ],
    'fume-hood-local-exhaust-certifications' => [
        'live_url' => 'https://ehsanalytical.com/fume-hood-local-exhaust-certifications/',
        'dev_post_id' => 3288
    ],
];

// Filter to single service if specified
if ($single_service && isset($services[$single_service])) {
    $services = [$single_service => $services[$single_service]];
} elseif ($single_service) {
    echo "Error: Service '$single_service' not found in mapping.\n";
    exit(1);
}

/**
 * Fetch HTML from URL
 */
function fetch_html($url) {
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: Mozilla/5.0 (compatible; MigrationBot/1.0)\r\n",
            'timeout' => 30
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);

    $html = @file_get_contents($url, false, $context);

    if ($html === false) {
        // Try with cURL as fallback
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MigrationBot/1.0)',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        $html = curl_exec($ch);
        curl_close($ch);
    }

    return $html;
}

/**
 * Create DOMDocument from HTML
 */
function create_dom_document($html) {
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    // Add HTML5 charset meta to handle encoding properly
    $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html;
    $doc->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
    libxml_clear_errors();
    return $doc;
}

/**
 * Get innerHTML of a DOMNode (preserves child tags like <strong>, <em>, etc.)
 */
function get_inner_html($node) {
    $innerHTML = '';
    foreach ($node->childNodes as $child) {
        $innerHTML .= $node->ownerDocument->saveHTML($child);
    }
    // Clean up inline styles but keep semantic tags
    $innerHTML = preg_replace('/\s*style="[^"]*"/i', '', $innerHTML);
    // Convert font-weight:400 spans to normal text, font-weight:700 to strong
    $innerHTML = preg_replace('/<span[^>]*font-weight:\s*700[^>]*>(.*?)<\/span>/is', '<strong>$1</strong>', $innerHTML);
    $innerHTML = preg_replace('/<span[^>]*font-weight:\s*400[^>]*>(.*?)<\/span>/is', '$1', $innerHTML);
    // Remove empty spans
    $innerHTML = preg_replace('/<span[^>]*>\s*<\/span>/i', '', $innerHTML);
    $innerHTML = preg_replace('/<span>([^<]*)<\/span>/i', '$1', $innerHTML);
    return trim($innerHTML);
}

/**
 * Extract main content area from Elementor page
 * Returns DOMNode from the provided document
 */
function extract_main_content($doc, $xpath) {
    // Find main content - try various selectors used by Elementor
    $selectors = [
        '//div[contains(@class, "elementor-widget-theme-post-content")]//div[contains(@class, "elementor-widget-container")]',
        '//article//div[contains(@class, "entry-content")]',
        '//div[contains(@class, "elementor-section-wrap")]',
        '//main//article',
        '//div[contains(@data-elementor-type, "wp-post")]'
    ];

    foreach ($selectors as $selector) {
        $elements = $xpath->query($selector);
        if ($elements->length > 0) {
            return $elements->item(0);
        }
    }

    // Fallback: get body content
    $body = $xpath->query('//body')->item(0);
    return $body;
}

/**
 * Extract structured content from DOM
 * Returns array of sections with headings, paragraphs, lists, images
 */
function extract_structured_content($node, $xpath) {
    $sections = [];
    $current_section = null;

    // Get all text widgets and headings
    $widgets = $xpath->query('.//div[contains(@class, "elementor-widget")]', $node);

    foreach ($widgets as $widget) {
        $classes = $widget->getAttribute('class');

        // Skip navigation, menu, footer widgets
        if (preg_match('/(nav-menu|sidebar|footer|header|breadcrumb)/i', $classes)) {
            continue;
        }

        // Also check parent section for sticky/header indicators
        $parent_section = $xpath->query('ancestor::*[contains(@class, "elementor-section")][1]', $widget)->item(0);
        $parent_classes = $parent_section ? $parent_section->getAttribute('class') : '';
        $parent_settings = $parent_section ? $parent_section->getAttribute('data-settings') : '';
        if (strpos($parent_settings, '"sticky"') !== false || strpos($parent_classes, 'elementor-hidden') !== false) {
            continue;
        }

        // Heading widget
        if (strpos($classes, 'elementor-widget-heading') !== false) {
            $heading_el = $xpath->query('.//h1|.//h2|.//h3|.//h4', $widget)->item(0);
            if ($heading_el) {
                $text = trim($heading_el->textContent);
                // Skip navigation-like headings (all lowercase, short generic text)
                if (strtolower($text) === $text && strlen($text) < 30) {
                    continue; // Skip "our services" type nav headings
                }
                // Skip duplicate headings
                static $seen_headings = [];
                $text_lower = strtolower($text);
                if (isset($seen_headings[$text_lower])) {
                    continue;
                }
                $seen_headings[$text_lower] = true;

                if (!empty($text) && strlen($text) > 2) {
                    // Start new section
                    if ($current_section) {
                        $sections[] = $current_section;
                    }
                    $current_section = [
                        'heading' => $text,
                        'level' => $heading_el->nodeName,
                        'content' => []
                    ];
                }
            }
            continue;
        }

        // Text editor widget
        if (strpos($classes, 'elementor-widget-text-editor') !== false) {
            $container = $xpath->query('.//div[contains(@class, "elementor-widget-container")]', $widget)->item(0);
            if ($container) {
                $content = extract_text_content($container, $xpath);
                if (!empty($content) && $current_section) {
                    $current_section['content'] = array_merge($current_section['content'], $content);
                } elseif (!empty($content)) {
                    // No section yet, create intro section
                    if (!$current_section) {
                        $current_section = ['heading' => '', 'level' => '', 'content' => []];
                    }
                    $current_section['content'] = array_merge($current_section['content'], $content);
                }
            }
            continue;
        }

        // Image widget
        if (strpos($classes, 'elementor-widget-image') !== false) {
            $img = $xpath->query('.//img', $widget)->item(0);
            if ($img) {
                // Check multiple src attributes (for lazy loading)
                $src = $img->getAttribute('data-lazy-src')
                    ?: $img->getAttribute('data-src')
                    ?: $img->getAttribute('src');
                $alt = $img->getAttribute('alt') ?: '';

                // Skip small icons, arrows, buttons
                $width = $img->getAttribute('width');
                if ($width && intval($width) < 50) continue;
                if (preg_match('/(icon|arrow|button|placeholder|spinner)/i', $src)) continue;

                // Skip certification/credential badges (these belong in footer, not content)
                if (preg_match('/(CSP|CIH|PMP|SDVOSB|DVBE|CUSP|IOSH|USOLN|certification|certified|logo|credential|download-1\.png|cve_completed|project-management-professional)/i', $alt . ' ' . $src)) {
                    continue;
                }

                if ($current_section) {
                    $current_section['content'][] = [
                        'type' => 'image',
                        'src' => $src,
                        'alt' => $alt
                    ];
                } else {
                    // Image before any section - create an intro section
                    if (!$current_section) {
                        $current_section = ['heading' => '', 'level' => '', 'content' => []];
                    }
                    $current_section['content'][] = [
                        'type' => 'image',
                        'src' => $src,
                        'alt' => $alt
                    ];
                }
            }
            continue;
        }

        // Icon list widget
        if (strpos($classes, 'elementor-widget-icon-list') !== false) {
            $items = $xpath->query('.//li//span[contains(@class, "elementor-icon-list-text")]', $widget);
            if ($items->length > 0) {
                $list_items = [];
                foreach ($items as $item) {
                    $text = trim($item->textContent);
                    if (!empty($text)) {
                        $list_items[] = ['text' => $text, 'sub_items' => []];
                    }
                }
                if (!empty($list_items) && $current_section) {
                    $current_section['content'][] = [
                        'type' => 'list',
                        'items' => $list_items
                    ];
                }
            }
            continue;
        }

        // Jet Timeline widget
        if (strpos($classes, 'elementor-widget-jet-timeline') !== false) {
            $timeline_items = $xpath->query('.//div[contains(@class, "jet-timeline-item")]', $widget);
            if ($timeline_items->length > 0) {
                $projects = [];
                foreach ($timeline_items as $timeline_item) {
                    $title_el = $xpath->query('.//h5[contains(@class, "timeline-item__card-title")]', $timeline_item)->item(0);
                    $desc_el = $xpath->query('.//div[contains(@class, "timeline-item__card-desc")]', $timeline_item)->item(0);
                    $meta_el = $xpath->query('.//div[contains(@class, "timeline-item__meta-content")]', $timeline_item)->item(0);

                    if ($title_el) {
                        $project = [
                            'title' => trim($title_el->textContent),
                            'description' => $desc_el ? trim($desc_el->textContent) : '',
                            'meta' => $meta_el ? trim($meta_el->textContent) : ''
                        ];
                        $projects[] = $project;
                    }
                }
                if (!empty($projects) && $current_section) {
                    $current_section['content'][] = [
                        'type' => 'timeline',
                        'projects' => $projects
                    ];
                }
            }
            continue;
        }

        // Image box widget (certifications, feature boxes)
        if (strpos($classes, 'elementor-widget-image-box') !== false) {
            $img = $xpath->query('.//img', $widget)->item(0);
            $title_el = $xpath->query('.//h3 | .//h4 | .//h5', $widget)->item(0);
            $desc_el = $xpath->query('.//p', $widget)->item(0);

            if ($title_el || $img) {
                // Create a section for this image-box if it has a title
                if ($title_el) {
                    $title = trim($title_el->textContent);
                    if (!empty($title)) {
                        if ($current_section) {
                            $sections[] = $current_section;
                        }
                        $current_section = [
                            'heading' => $title,
                            'level' => $title_el->nodeName,
                            'content' => []
                        ];
                    }
                }

                // Add image if present
                if ($img) {
                    $src = $img->getAttribute('data-lazy-src')
                        ?: $img->getAttribute('data-src')
                        ?: $img->getAttribute('src');
                    $alt = $img->getAttribute('alt') ?: '';

                    if ($src && $current_section) {
                        $current_section['content'][] = [
                            'type' => 'image',
                            'src' => $src,
                            'alt' => $alt
                        ];
                    }
                }

                // Add description if present
                if ($desc_el && $current_section) {
                    $desc = trim($desc_el->textContent);
                    if (!empty($desc)) {
                        $current_section['content'][] = [
                            'type' => 'text',
                            'text' => $desc
                        ];
                    }
                }
            }
            continue;
        }

        // Image gallery/carousel widget (old style)
        if (strpos($classes, 'elementor-widget-image-gallery') !== false ||
            strpos($classes, 'elementor-widget-image-carousel') !== false) {
            $images = $xpath->query('.//img', $widget);
            foreach ($images as $img) {
                $src = $img->getAttribute('src') ?: $img->getAttribute('data-src');
                $alt = $img->getAttribute('alt') ?: '';
                if ($src && !preg_match('/(icon|logo|arrow|button|placeholder)/i', $src)) {
                    if ($current_section) {
                        $current_section['content'][] = [
                            'type' => 'image',
                            'src' => $src,
                            'alt' => $alt
                        ];
                    }
                }
            }
            continue;
        }

        // Elementor Gallery widget (new Pro style with data-thumbnail)
        if (strpos($classes, 'elementor-widget-gallery') !== false) {
            // Get images from e-gallery-item elements with data-thumbnail
            $gallery_items = $xpath->query('.//*[@data-thumbnail]', $widget);
            if ($gallery_items->length > 0) {
                $gallery_images = [];
                foreach ($gallery_items as $item) {
                    $src = $item->getAttribute('data-thumbnail');
                    // Get full size from href if available
                    $full_src = $item->getAttribute('href') ?: $src;
                    // Use full size URL, remove -1024x... size suffix to get original
                    $full_src = preg_replace('/-\d+x\d+(\.[^.]+)$/', '$1', $full_src);
                    if ($src && !preg_match('/(icon|logo|arrow|button|placeholder)/i', $src)) {
                        $gallery_images[] = [
                            'type' => 'image',
                            'src' => $full_src,
                            'alt' => ''
                        ];
                    }
                }
                if (!empty($gallery_images) && $current_section) {
                    // Add as image gallery type for grid display
                    $current_section['content'][] = [
                        'type' => 'gallery',
                        'images' => $gallery_images
                    ];
                }
            }
            continue;
        }
    }

    // Add last section
    if ($current_section && (!empty($current_section['heading']) || !empty($current_section['content']))) {
        $sections[] = $current_section;
    }

    return $sections;
}

/**
 * Extract text content from container (paragraphs, lists)
 */
function extract_text_content($container, $xpath) {
    $content = [];

    // Process child nodes
    foreach ($container->childNodes as $child) {
        if ($child->nodeType !== XML_ELEMENT_NODE) continue;

        $nodeName = strtolower($child->nodeName);

        // Paragraph
        if ($nodeName === 'p') {
            // Get innerHTML to preserve bold, italic, underline formatting
            $innerHTML = get_inner_html($child);
            $plainText = trim($child->textContent);
            if (!empty($plainText) && strlen($plainText) > 5) {
                $content[] = [
                    'type' => 'paragraph',
                    'text' => $innerHTML  // Preserve HTML formatting
                ];
            }
            continue;
        }

        // Lists (ul, ol)
        if ($nodeName === 'ul' || $nodeName === 'ol') {
            $items = extract_list_items($child, $xpath);
            if (!empty($items)) {
                $content[] = [
                    'type' => 'list',
                    'items' => $items
                ];
            }
            continue;
        }

        // Headings within text editor
        if (preg_match('/^h[2-6]$/', $nodeName)) {
            $text = trim($child->textContent);
            if (!empty($text)) {
                $content[] = [
                    'type' => 'subheading',
                    'text' => $text,
                    'level' => $nodeName
                ];
            }
            continue;
        }

        // Divs - recurse
        if ($nodeName === 'div') {
            $nested = extract_text_content($child, $xpath);
            $content = array_merge($content, $nested);
        }
    }

    return $content;
}

/**
 * Extract list items with potential sub-items
 */
function extract_list_items($list, $xpath) {
    $items = [];

    foreach ($list->childNodes as $child) {
        if ($child->nodeType !== XML_ELEMENT_NODE || strtolower($child->nodeName) !== 'li') {
            continue;
        }

        $item = ['text' => '', 'sub_items' => []];

        // Get text content (excluding nested lists)
        foreach ($child->childNodes as $li_child) {
            if ($li_child->nodeType === XML_TEXT_NODE) {
                $item['text'] .= $li_child->textContent;
            } elseif ($li_child->nodeType === XML_ELEMENT_NODE) {
                $childName = strtolower($li_child->nodeName);
                if ($childName === 'ul' || $childName === 'ol') {
                    // Nested list
                    $item['sub_items'] = extract_list_items($li_child, $xpath);
                } elseif ($childName !== 'ul' && $childName !== 'ol') {
                    // Other elements (strong, em, span)
                    $item['text'] .= $li_child->textContent;
                }
            }
        }

        $item['text'] = trim($item['text']);
        if (!empty($item['text'])) {
            $items[] = $item;
        }
    }

    return $items;
}

/**
 * Extract all images from HTML
 */
function extract_all_images($html) {
    $images = [];

    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);

    foreach ($matches[1] as $src) {
        // Skip small icons, data URIs, SVGs
        if (strpos($src, 'data:') === 0) continue;
        if (preg_match('/\.(svg|ico)$/i', $src)) continue;
        if (preg_match('/(icon|logo|arrow|button|placeholder)/i', $src)) continue;

        // Get alt text
        preg_match('/<img[^>]+src=["\']' . preg_quote($src, '/') . '["\'][^>]+alt=["\']([^"\']*)["\'][^>]*>/i', $html, $alt_match);
        $alt = $alt_match[1] ?? '';

        $images[] = [
            'src' => $src,
            'alt' => $alt
        ];
    }

    return array_unique($images, SORT_REGULAR);
}

/**
 * Download and upload image to WordPress
 */
function download_and_upload_image($url, $post_id) {
    // Ensure URL is absolute
    if (strpos($url, '//') === 0) {
        $url = 'https:' . $url;
    } elseif (strpos($url, '/') === 0) {
        $url = 'https://ehsanalytical.com' . $url;
    }

    // Check if image already exists in media library
    $filename = basename(parse_url($url, PHP_URL_PATH));
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_query' => [
            [
                'key' => '_wp_attached_file',
                'value' => $filename,
                'compare' => 'LIKE'
            ]
        ],
        'posts_per_page' => 1
    ]);

    if (!empty($existing)) {
        return wp_get_attachment_url($existing[0]->ID);
    }

    // Download the file
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $tmp = download_url($url);

    if (is_wp_error($tmp)) {
        echo "    Warning: Failed to download $url - " . $tmp->get_error_message() . "\n";
        return null;
    }

    $file_array = [
        'name' => $filename,
        'tmp_name' => $tmp
    ];

    $id = media_handle_sideload($file_array, $post_id);

    if (is_wp_error($id)) {
        @unlink($tmp);
        echo "    Warning: Failed to upload $filename - " . $id->get_error_message() . "\n";
        return null;
    }

    return wp_get_attachment_url($id);
}

/**
 * Transform structured content to dev design system HTML
 */
function build_dev_html($sections, $image_mapping) {
    $html = '';

    foreach ($sections as $section) {
        // Section heading
        if (!empty($section['heading'])) {
            $html .= '<h2 class="service-section-heading">' . esc_html($section['heading']) . '</h2>' . "\n\n";
        }

        // Section content
        foreach ($section['content'] as $item) {
            switch ($item['type']) {
                case 'paragraph':
                    // Use wp_kses_post to allow safe HTML (strong, em, a, etc.)
                    $html .= '<p class="service-text">' . wp_kses_post($item['text']) . '</p>' . "\n\n";
                    break;

                case 'subheading':
                    $tag = $item['level'] ?: 'h3';
                    $html .= '<' . $tag . ' class="service-section-heading">' . esc_html($item['text']) . '</' . $tag . '>' . "\n\n";
                    break;

                case 'list':
                    $html .= build_dev_list($item['items']);
                    break;

                case 'image':
                    $dev_url = $image_mapping[$item['src']] ?? $item['src'];
                    $html .= '<div class="service-image-container">' . "\n";
                    $html .= '  <img src="' . esc_url($dev_url) . '" alt="' . esc_attr($item['alt']) . '" class="service-image">' . "\n";
                    $html .= '</div>' . "\n\n";
                    break;

                case 'timeline':
                    // Use existing project timeline component
                    $html .= build_project_timeline($item['projects']);
                    break;

                case 'gallery':
                    // Render image gallery as grid
                    $html .= '<div class="service-image-gallery">' . "\n";
                    foreach ($item['images'] as $gallery_img) {
                        $dev_url = $image_mapping[$gallery_img['src']] ?? $gallery_img['src'];
                        $html .= '  <div class="service-gallery-item">' . "\n";
                        $html .= '    <img src="' . esc_url($dev_url) . '" alt="' . esc_attr($gallery_img['alt']) . '" class="service-gallery-image">' . "\n";
                        $html .= '  </div>' . "\n";
                    }
                    $html .= '</div>' . "\n\n";
                    break;
            }
        }
    }

    return $html;
}

/**
 * Build project timeline HTML using dev design system
 */
function build_project_timeline($projects) {
    if (empty($projects)) return '';

    // Parse project data into structured format
    $parsed_projects = [];
    foreach ($projects as $project) {
        // Extract year from title (e.g., "2010 - Deepwater Horizon Oil Spill")
        $year = '';
        $title = $project['title'];
        if (preg_match('/^(\d{4}(?:-\d{4})?)\s*[-–]\s*(.+)$/', $title, $matches)) {
            $year = $matches[1];
            $title = $matches[2];
        }

        // Extract value from description (e.g., "$65 billion project")
        $value = '';
        $description = $project['description'];
        if (preg_match('/\(\$[\d.]+\s*(?:billion|million|B|M)\s*project\)/i', $description, $value_match)) {
            $value = trim($value_match[0], '()');
            $description = str_replace($value_match[0], '', $description);
        }

        // Extract client from description
        $client = '';
        if (preg_match('/for\s+([^–\-]+?)(?:\s*[-–]|$)/i', $description, $client_match)) {
            $client = trim($client_match[1]);
        }

        $parsed_projects[] = [
            'year' => $year,
            'title' => trim($title),
            'value' => $value,
            'client' => $client,
            'description' => trim($description),
            'service_type' => $project['meta'] ?? ''
        ];
    }

    // Use the existing render function
    return ehs_render_project_timeline('', $parsed_projects);
}

/**
 * Build list HTML with proper dev classes
 */
function build_dev_list($items) {
    if (empty($items)) return '';

    $html = '<ul class="service-content-list">' . "\n";

    foreach ($items as $item) {
        $html .= '  <li>';

        // Check if item has bold prefix (common pattern: "Bold text: description")
        if (preg_match('/^([^:]+):\s*(.+)$/', $item['text'], $matches)) {
            $html .= '<strong>' . esc_html($matches[1]) . ':</strong> ' . esc_html($matches[2]);
        } else {
            $html .= esc_html($item['text']);
        }

        // Sub-items
        if (!empty($item['sub_items'])) {
            $html .= "\n" . '    <ul class="service-sublist">' . "\n";
            foreach ($item['sub_items'] as $sub) {
                $html .= '      <li>' . esc_html($sub['text']) . '</li>' . "\n";
            }
            $html .= '    </ul>' . "\n  ";
        }

        $html .= '</li>' . "\n";
    }

    $html .= '</ul>' . "\n\n";

    return $html;
}

/**
 * Verify migration results
 */
function verify_migration($post_id, $sections) {
    $post = get_post($post_id);
    $dev_content = $post->post_content;

    $live_sections = count($sections);
    $dev_headings = substr_count($dev_content, 'service-section-heading');

    $live_paragraphs = 0;
    foreach ($sections as $section) {
        foreach ($section['content'] as $item) {
            if ($item['type'] === 'paragraph') $live_paragraphs++;
        }
    }
    $dev_paragraphs = substr_count($dev_content, 'service-text');

    $dev_images = substr_count($dev_content, 'service-image-container');

    return [
        'sections' => ['live' => $live_sections, 'dev' => $dev_headings],
        'paragraphs' => ['live' => $live_paragraphs, 'dev' => $dev_paragraphs],
        'images' => ['dev' => $dev_images],
        'word_count' => str_word_count(strip_tags($dev_content))
    ];
}

// ===========================================
// MAIN MIGRATION PROCESS
// ===========================================

echo "===========================================\n";
echo "Service Content Migration\n";
echo "===========================================\n\n";

if ($dry_run) {
    echo "*** DRY RUN MODE - No changes will be saved ***\n\n";
}

$results = [];
$total_services = count($services);
$current = 0;

foreach ($services as $slug => $config) {
    $current++;
    echo "[$current/$total_services] Migrating: $slug\n";
    echo "  Live URL: {$config['live_url']}\n";
    echo "  Dev Post ID: {$config['dev_post_id']}\n";

    // Fetch live HTML
    echo "  Fetching HTML...\n";
    $html = fetch_html($config['live_url']);

    if (!$html) {
        echo "  ERROR: Failed to fetch HTML\n\n";
        $results[$slug] = ['status' => 'error', 'message' => 'Failed to fetch HTML'];
        continue;
    }

    echo "  HTML fetched: " . strlen($html) . " bytes\n";

    // Parse and extract content
    echo "  Extracting structured content...\n";
    $doc = create_dom_document($html);
    $xpath = new DOMXPath($doc);
    $main_content = extract_main_content($doc, $xpath);

    if (!$main_content) {
        echo "  ERROR: Failed to extract main content\n\n";
        $results[$slug] = ['status' => 'error', 'message' => 'Failed to extract content'];
        continue;
    }

    $sections = extract_structured_content($main_content, $xpath);
    echo "  Found " . count($sections) . " sections\n";

    if ($verbose) {
        foreach ($sections as $i => $section) {
            echo "    Section $i: " . substr($section['heading'], 0, 50) . "...\n";
            echo "      Content items: " . count($section['content']) . "\n";
        }
    }

    // Extract and download images
    echo "  Processing images...\n";
    $all_images = extract_all_images($html);
    $image_mapping = [];

    if (!$dry_run) {
        foreach ($all_images as $img) {
            $dev_url = download_and_upload_image($img['src'], $config['dev_post_id']);
            if ($dev_url) {
                $image_mapping[$img['src']] = $dev_url;
            }
        }
    }
    echo "  Processed " . count($image_mapping) . "/" . count($all_images) . " images\n";

    // Build dev HTML
    echo "  Building dev design system HTML...\n";
    $dev_html = build_dev_html($sections, $image_mapping);

    if ($verbose) {
        echo "  Generated HTML preview:\n";
        echo "  " . substr($dev_html, 0, 500) . "...\n";
    }

    // Update WordPress post
    if (!$dry_run) {
        echo "  Updating WordPress post...\n";

        $update_result = wp_update_post([
            'ID' => $config['dev_post_id'],
            'post_content' => $dev_html
        ], true);

        if (is_wp_error($update_result)) {
            echo "  ERROR: " . $update_result->get_error_message() . "\n\n";
            $results[$slug] = ['status' => 'error', 'message' => $update_result->get_error_message()];
            continue;
        }

        // Verify
        $verification = verify_migration($config['dev_post_id'], $sections);
        echo "  Verification:\n";
        echo "    Sections: {$verification['sections']['dev']} (from {$verification['sections']['live']} live)\n";
        echo "    Paragraphs: {$verification['paragraphs']['dev']} (from {$verification['paragraphs']['live']} live)\n";
        echo "    Images: {$verification['images']['dev']}\n";
        echo "    Word count: {$verification['word_count']}\n";

        $results[$slug] = [
            'status' => 'success',
            'verification' => $verification
        ];
    } else {
        echo "  [DRY RUN] Would update post with " . strlen($dev_html) . " bytes of HTML\n";
        $results[$slug] = ['status' => 'dry_run', 'html_size' => strlen($dev_html)];
    }

    echo "  Done.\n\n";
}

// Summary
echo "===========================================\n";
echo "Migration Summary\n";
echo "===========================================\n\n";

$success = 0;
$errors = 0;

foreach ($results as $slug => $result) {
    $status = $result['status'];
    echo "$slug: $status";

    if ($status === 'success') {
        $success++;
        echo " (words: {$result['verification']['word_count']})";
    } elseif ($status === 'error') {
        $errors++;
        echo " - {$result['message']}";
    }
    echo "\n";
}

echo "\nTotal: $success success, $errors errors\n";

if ($dry_run) {
    echo "\n*** DRY RUN COMPLETE - No changes were made ***\n";
    echo "Run without --dry-run to apply changes.\n";
}

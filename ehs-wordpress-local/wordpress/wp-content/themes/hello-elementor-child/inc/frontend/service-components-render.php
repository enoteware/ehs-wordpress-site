<?php
/**
 * Service Components Render Functions
 *
 * Individual render functions for each component type
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Minify component HTML to avoid wpautop corrupting shortcode output.
 *
 * Some environments/plugins end up applying wpautop after shortcode expansion.
 * Removing inter-tag whitespace/newlines prevents stray <p></p> tags from being
 * injected into complex component markup (e.g., timelines).
 *
 * @param string $html Raw HTML
 * @return string Minified HTML (whitespace between tags removed)
 */
function ehs_service_component_minify_html($html) {
    if (!is_string($html) || $html === '') {
        return '';
    }

    $html = trim($html);
    return preg_replace('/>\\s+</', '><', $html);
}

/**
 * Normalize project timeline data to avoid duplicated/mismatched years.
 *
 * Editors sometimes include the year prefix in the title (e.g. "2023 – Project"),
 * which can then disagree with the separate `year` field. Prefer a year prefix
 * embedded in the title when present and strip it from the visible title.
 *
 * @param array $project Project data
 * @return array Normalized project data
 */
function ehs_project_timeline_normalize_project($project) {
    if (!is_array($project)) {
        return array();
    }

    $year = isset($project['year']) ? trim((string) $project['year']) : '';
    $title = isset($project['title']) ? trim((string) $project['title']) : '';

    if ($title !== '') {
        $decoded_title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        if (preg_match('/^\\s*(\\d{4})\\s*[-–—]\\s*(.+)\\s*$/u', $decoded_title, $matches)) {
            $title_year = $matches[1];
            $title_rest = trim($matches[2]);

            if ($title_rest !== '') {
                $project['title'] = $title_rest;
            }
            if ($title_year !== '' && $title_year !== $year) {
                $project['year'] = $title_year;
            }
        }
    }

    return $project;
}

/**
 * Render video component
 *
 * @param array $component Component data
 * @return string HTML markup
 */
function ehs_render_service_video($component) {
    if (empty($component['video_url'])) {
        return '';
    }

    $video_url = esc_url($component['video_url']);
    $caption = isset($component['video_caption']) ? $component['video_caption'] : '';
    $thumbnail_id = isset($component['video_thumbnail']) ? absint($component['video_thumbnail']) : 0;

    // Detect video platform and generate embed URL
    $embed_url = ehs_get_video_embed_url($video_url);
    
    if (!$embed_url) {
        return '';
    }

    ob_start();
    ?>
    <div class="service-component service-component-video">
        <div class="service-component-video__embed">
            <iframe 
                src="<?php echo esc_url($embed_url); ?>" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                loading="lazy">
            </iframe>
        </div>
        <?php if (!empty($caption)) : ?>
            <div class="service-component-video__caption">
                <?php echo esc_html($caption); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ehs_service_component_minify_html(ob_get_clean());
}

/**
 * Get video embed URL from various video platform URLs
 *
 * @param string $url Video URL
 * @return string|false Embed URL or false if not supported
 */
function ehs_get_video_embed_url($url) {
    // YouTube
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }

    // Vimeo
    if (preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $url, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1];
    }

    // If already an embed URL, return as-is
    if (strpos($url, 'youtube.com/embed') !== false || strpos($url, 'vimeo.com/video') !== false) {
        return $url;
    }

    return false;
}

/**
 * Render checklist component
 *
 * @param array $component Component data
 * @return string HTML markup
 */
function ehs_render_service_checklist($component) {
    if (empty($component['checklist_items']) || !is_array($component['checklist_items'])) {
        return '';
    }

    $title = isset($component['checklist_title']) ? $component['checklist_title'] : '';
    $items = array_filter(array_map('trim', $component['checklist_items']));

    if (empty($items)) {
        return '';
    }

    ob_start();
    ?>
    <div class="service-component service-component-checklist">
        <?php if (!empty($title)) : ?>
            <h3 class="service-component-checklist__title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <ul class="service-component-checklist__list">
            <?php foreach ($items as $item) : ?>
                <li class="service-component-checklist__item">
                    <span class="service-component-checklist__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <span class="service-component-checklist__text"><?php echo esc_html($item); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ehs_service_component_minify_html(ob_get_clean());
}

/**
 * Render timeline component
 *
 * @param array $component Component data
 * @return string HTML markup
 */
function ehs_render_service_timeline($component) {
    if (empty($component['timeline_items']) || !is_array($component['timeline_items'])) {
        return '';
    }

    $title = isset($component['timeline_title']) ? $component['timeline_title'] : '';
    $items = array_filter($component['timeline_items'], function($item) {
        return !empty($item['step']) || !empty($item['description']);
    });

    if (empty($items)) {
        return '';
    }

    ob_start();
    ?>
    <div class="service-component service-component-timeline">
        <?php if (!empty($title)) : ?>
            <h3 class="service-component-timeline__title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <div class="service-component-timeline__container">
            <?php foreach ($items as $index => $item) : ?>
                <div class="service-component-timeline__item">
                    <div class="service-component-timeline__step">
                        <span class="service-component-timeline__number"><?php echo esc_html($index + 1); ?></span>
                    </div>
                    <div class="service-component-timeline__content">
                        <?php if (!empty($item['step'])) : ?>
                            <h4 class="service-component-timeline__step-title"><?php echo esc_html($item['step']); ?></h4>
                        <?php endif; ?>
                        <?php if (!empty($item['description'])) : ?>
                            <p class="service-component-timeline__step-description"><?php echo esc_html($item['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ehs_service_component_minify_html(ob_get_clean());
}

/**
 * Render project timeline/portfolio component
 *
 * @param string $title Section title
 * @param array $projects Array of project data
 * @return string HTML markup
 */
function ehs_render_project_timeline($title, $projects) {
    if (empty($projects) || !is_array($projects)) {
        return '';
    }

    ob_start();
    ?>
    <div class="service-component service-component-projects">
        <?php if (!empty($title)) : ?>
            <h2 class="service-component-projects__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <div class="project-timeline">
            <div class="project-timeline__line">
                <div class="project-timeline__progress"></div>
            </div>
            <?php foreach ($projects as $index => $project) : ?>
                <?php $project = ehs_project_timeline_normalize_project($project); ?>
                <div class="project-timeline__item" data-index="<?php echo $index; ?>">
                    <div class="project-timeline__marker">
                        <div class="project-timeline__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="project-timeline__card">
                        <div class="project-timeline__card-header">
                            <?php if (!empty($project['year'])) : ?>
                                <span class="project-timeline__card-year"><?php echo esc_html($project['year']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($project['title'])) : ?>
                                <h3 class="project-timeline__card-title"><?php echo esc_html($project['title']); ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($project['value'])) : ?>
                                <span class="project-timeline__card-value"><?php echo esc_html($project['value']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="project-timeline__card-body">
                            <?php if (!empty($project['client'])) : ?>
                                <p class="project-timeline__card-client">
                                    <strong>Client:</strong> <?php echo esc_html($project['client']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($project['description'])) : ?>
                                <p class="project-timeline__card-description"><?php echo esc_html($project['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($project['service'])) : ?>
                            <div class="project-timeline__card-footer">
                                <span class="project-timeline__card-service"><?php echo esc_html($project['service']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ehs_service_component_minify_html(ob_get_clean());
}

/**
 * Render all service components from meta field
 *
 * @param int $post_id Post ID (optional, defaults to current post)
 * @return string HTML markup for all components
 */
function ehs_render_service_components($post_id = 0) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (!$post_id) {
        return '';
    }

    $components_json = get_post_meta($post_id, 'service_components', true);
    if (empty($components_json)) {
        return '';
    }

    $components = json_decode($components_json, true);
    if (!is_array($components) || empty($components)) {
        return '';
    }

    $output = '';
    foreach ($components as $component) {
        if (!isset($component['type'])) {
            continue;
        }

        switch ($component['type']) {
            case 'video':
                $output .= ehs_render_service_video($component);
                break;
            case 'checklist':
                $output .= ehs_render_service_checklist($component);
                break;
            case 'timeline':
                $output .= ehs_render_service_timeline($component);
                break;
        }
    }

    return $output;
}

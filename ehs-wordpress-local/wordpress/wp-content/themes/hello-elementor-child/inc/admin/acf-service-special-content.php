<?php
/**
 * ACF Field Definitions for Service Special Content
 *
 * Registers accordion and video fields for services post type
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_service_special_content',
        'title' => 'Service Special Content',
        'fields' => [
            [
                'key' => 'field_service_accordions',
                'label' => 'Service Accordions',
                'name' => 'service_accordions',
                'type' => 'repeater',
                'instructions' => 'Add expandable accordion sections with lists of items',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '100',
                    'class' => '',
                    'id' => '',
                ],
                'layout' => 'block',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => '',
                'button_label' => 'Add Accordion',
                'rows' => 2,
                'sub_fields' => [
                    [
                        'key' => 'field_accordion_title',
                        'label' => 'Accordion Title',
                        'name' => 'accordion_title',
                        'type' => 'text',
                        'instructions' => 'The title/heading for this accordion section',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '100',
                            'class' => '',
                            'id' => '',
                        ],
                        'default_value' => '',
                        'maxlength' => 255,
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ],
                    [
                        'key' => 'field_accordion_items',
                        'label' => 'Accordion Items',
                        'name' => 'accordion_items',
                        'type' => 'textarea',
                        'instructions' => 'Enter one item per line. Each line becomes a list item in the accordion.',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '100',
                            'class' => '',
                            'id' => '',
                        ],
                        'default_value' => '',
                        'placeholder' => 'Item 1\nItem 2\nItem 3',
                        'maxlength' => 5000,
                        'rows' => 8,
                        'new_lines' => 'br',
                    ],
                ],
            ],
            [
                'key' => 'field_service_youtube_video',
                'label' => 'Service YouTube Video',
                'name' => 'service_youtube_video',
                'type' => 'text',
                'instructions' => 'Paste the YouTube video URL (supports youtube.com/watch?v=ID, youtu.be/ID, or youtube.com/embed/ID)',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '100',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'placeholder' => 'https://www.youtube.com/watch?v=...',
                'maxlength' => 500,
                'prepend' => '',
                'append' => '',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'services',
                ],
            ],
        ],
        'menu_order' => 10,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ]);
}

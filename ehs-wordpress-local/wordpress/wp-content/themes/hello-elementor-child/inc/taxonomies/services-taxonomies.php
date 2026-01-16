<?php
/**
 * Services Taxonomies
 *
 * Replaces free-form "Service Details" meta fields with checkbox-based taxonomies.
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

function ehs_register_services_taxonomies() {
	$post_types = array('services');

	$taxonomies = array(
		'service_category' => array(
			'singular' => __('Service Category', 'hello-elementor-child'),
			'plural'   => __('Service Categories', 'hello-elementor-child'),
			'slug'     => 'service-category',
		),
		'service_area' => array(
			'singular' => __('Service Area', 'hello-elementor-child'),
			'plural'   => __('Service Areas', 'hello-elementor-child'),
			'slug'     => 'service-area',
		),
		'service_certification' => array(
			'singular' => __('Certification', 'hello-elementor-child'),
			'plural'   => __('Certifications', 'hello-elementor-child'),
			'slug'     => 'service-certification',
		),
		'service_target_audience' => array(
			'singular' => __('Target Audience', 'hello-elementor-child'),
			'plural'   => __('Target Audiences', 'hello-elementor-child'),
			'slug'     => 'service-target-audience',
		),
	);

	foreach ($taxonomies as $taxonomy => $cfg) {
		$labels = array(
			'name'              => $cfg['plural'],
			'singular_name'     => $cfg['singular'],
			'search_items'      => __('Search', 'hello-elementor-child') . ' ' . $cfg['plural'],
			'all_items'         => __('All', 'hello-elementor-child') . ' ' . $cfg['plural'],
			'parent_item'       => __('Parent', 'hello-elementor-child') . ' ' . $cfg['singular'],
			'parent_item_colon' => __('Parent', 'hello-elementor-child') . ' ' . $cfg['singular'] . ':',
			'edit_item'         => __('Edit', 'hello-elementor-child') . ' ' . $cfg['singular'],
			'update_item'       => __('Update', 'hello-elementor-child') . ' ' . $cfg['singular'],
			'add_new_item'      => __('Add New', 'hello-elementor-child') . ' ' . $cfg['singular'],
			'new_item_name'     => __('New', 'hello-elementor-child') . ' ' . $cfg['singular'],
			'menu_name'         => $cfg['plural'],
		);

		register_taxonomy($taxonomy, $post_types, array(
			'hierarchical'      => true, // checkbox UI + "Add New" right in meta box
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'       => $cfg['slug'],
				'with_front' => false,
			),
		));
	}
}
add_action('init', 'ehs_register_services_taxonomies', 1);

/**
 * Some admin setups/plugins can hide specific taxonomy meta boxes.
 * Force the Certifications taxonomy box to appear on Services edit screens.
 */
function ehs_force_services_certifications_metabox() {
	if (!is_admin()) {
		return;
	}

	remove_meta_box('service_certificationdiv', 'services', 'side');
	add_meta_box(
		'service_certificationdiv',
		__('Certifications', 'hello-elementor-child'),
		'post_categories_meta_box',
		'services',
		'side',
		'default',
		array('taxonomy' => 'service_certification')
	);
}
add_action('add_meta_boxes_services', 'ehs_force_services_certifications_metabox', 1000);

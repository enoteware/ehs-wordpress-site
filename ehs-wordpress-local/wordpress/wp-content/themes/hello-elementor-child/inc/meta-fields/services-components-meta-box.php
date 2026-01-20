<?php
/**
 * Service Components Meta Box UI and Save Logic
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Service Components Meta Box
 */
function ehs_add_services_components_meta_box() {
    add_meta_box(
        'ehs_service_components',
        __('Service Components', 'hello-elementor-child'),
        'ehs_service_components_meta_box_callback',
        'services',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ehs_add_services_components_meta_box');

/**
 * Service Components Meta Box Callback
 */
function ehs_service_components_meta_box_callback($post) {
    wp_nonce_field('ehs_service_components_meta_box', 'ehs_service_components_meta_box_nonce');

    $components_json = get_post_meta($post->ID, 'service_components', true);
    $components = !empty($components_json) ? json_decode($components_json, true) : array();
    
    if (!is_array($components)) {
        $components = array();
    }
    ?>

    <div id="service-components-container">
        <p class="description">
            <?php _e('Add reusable components (videos, checklists, timelines) to this service page. Components will be automatically rendered in the page content.', 'hello-elementor-child'); ?>
        </p>

        <div id="service-components-list">
            <?php if (!empty($components)) : ?>
                <?php foreach ($components as $index => $component) : ?>
                    <?php ehs_render_component_editor($component, $index); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="service-components-actions">
            <button type="button" class="button" id="add-component-video">
                <?php _e('+ Add Video', 'hello-elementor-child'); ?>
            </button>
            <button type="button" class="button" id="add-component-checklist">
                <?php _e('+ Add Checklist', 'hello-elementor-child'); ?>
            </button>
            <button type="button" class="button" id="add-component-timeline">
                <?php _e('+ Add Timeline', 'hello-elementor-child'); ?>
            </button>
        </div>

        <input type="hidden" id="service_components_json" name="service_components_json" value="<?php echo esc_attr($components_json); ?>" />
    </div>

    <script type="text/template" id="component-template-video">
        <?php ehs_render_component_editor(array('type' => 'video'), '{{INDEX}}'); ?>
    </script>

    <script type="text/template" id="component-template-checklist">
        <?php ehs_render_component_editor(array('type' => 'checklist'), '{{INDEX}}'); ?>
    </script>

    <script type="text/template" id="component-template-timeline">
        <?php ehs_render_component_editor(array('type' => 'timeline'), '{{INDEX}}'); ?>
    </script>

    <style>
        .service-component-editor {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .service-component-editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .service-component-editor-title {
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            color: #2271b1;
        }
        .service-component-editor-actions {
            display: flex;
            gap: 5px;
        }
        .service-component-editor-actions button {
            padding: 4px 8px;
            font-size: 12px;
        }
        .service-component-field {
            margin-bottom: 15px;
        }
        .service-component-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .service-component-field input[type="text"],
        .service-component-field input[type="url"],
        .service-component-field textarea {
            width: 100%;
            max-width: 600px;
        }
        .service-component-field textarea {
            min-height: 60px;
        }
        .service-component-items {
            margin-top: 10px;
        }
        .service-component-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: flex-start;
        }
        .service-component-item input {
            flex: 1;
        }
        .service-component-item-remove {
            padding: 5px 10px;
        }
        .service-components-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .service-components-actions button {
            margin-right: 10px;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var componentIndex = <?php echo count($components); ?>;
        var components = <?php echo wp_json_encode($components); ?>;

        // Add Video Component
        $('#add-component-video').on('click', function() {
            var template = $('#component-template-video').html().replace(/\{\{INDEX\}\}/g, componentIndex);
            $('#service-components-list').append(template);
            componentIndex++;
            updateComponentsJSON();
        });

        // Add Checklist Component
        $('#add-component-checklist').on('click', function() {
            var template = $('#component-template-checklist').html().replace(/\{\{INDEX\}\}/g, componentIndex);
            $('#service-components-list').append(template);
            componentIndex++;
            updateComponentsJSON();
        });

        // Add Timeline Component
        $('#add-component-timeline').on('click', function() {
            var template = $('#component-template-timeline').html().replace(/\{\{INDEX\}\}/g, componentIndex);
            $('#service-components-list').append(template);
            componentIndex++;
            updateComponentsJSON();
        });

        // Remove Component
        $(document).on('click', '.remove-component', function() {
            if (confirm('<?php _e('Are you sure you want to remove this component?', 'hello-elementor-child'); ?>')) {
                $(this).closest('.service-component-editor').remove();
                updateComponentsJSON();
            }
        });

        // Move Component Up
        $(document).on('click', '.move-component-up', function() {
            var $editor = $(this).closest('.service-component-editor');
            var $prev = $editor.prev('.service-component-editor');
            if ($prev.length) {
                $editor.insertBefore($prev);
                updateComponentsJSON();
            }
        });

        // Move Component Down
        $(document).on('click', '.move-component-down', function() {
            var $editor = $(this).closest('.service-component-editor');
            var $next = $editor.next('.service-component-editor');
            if ($next.length) {
                $editor.insertAfter($next);
                updateComponentsJSON();
            }
        });

        // Add Checklist Item
        $(document).on('click', '.add-checklist-item', function() {
            var $list = $(this).siblings('.service-component-items');
            var index = $list.find('.service-component-item').length;
            var itemHtml = '<div class="service-component-item">' +
                '<input type="text" class="checklist-item-input" placeholder="<?php _e('Checklist item', 'hello-elementor-child'); ?>" />' +
                '<button type="button" class="button service-component-item-remove remove-checklist-item"><?php _e('Remove', 'hello-elementor-child'); ?></button>' +
                '</div>';
            $list.append(itemHtml);
            updateComponentsJSON();
        });

        // Remove Checklist Item
        $(document).on('click', '.remove-checklist-item', function() {
            $(this).closest('.service-component-item').remove();
            updateComponentsJSON();
        });

        // Add Timeline Item
        $(document).on('click', '.add-timeline-item', function() {
            var $list = $(this).siblings('.service-component-items');
            var itemHtml = '<div class="service-component-item">' +
                '<input type="text" class="timeline-step-input" placeholder="<?php _e('Step title', 'hello-elementor-child'); ?>" style="width: 200px;" />' +
                '<textarea class="timeline-description-input" placeholder="<?php _e('Step description', 'hello-elementor-child'); ?>" rows="2"></textarea>' +
                '<button type="button" class="button service-component-item-remove remove-timeline-item"><?php _e('Remove', 'hello-elementor-child'); ?></button>' +
                '</div>';
            $list.append(itemHtml);
            updateComponentsJSON();
        });

        // Remove Timeline Item
        $(document).on('click', '.remove-timeline-item', function() {
            $(this).closest('.service-component-item').remove();
            updateComponentsJSON();
        });

        // Update JSON on any input change
        $(document).on('input change', '.service-component-editor input, .service-component-editor textarea', function() {
            updateComponentsJSON();
        });

        // Media uploader for video thumbnail
        $(document).on('click', '.select-video-thumbnail', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $input = $button.siblings('input[type="hidden"]');
            var $preview = $button.siblings('.video-thumbnail-preview');

            var frame = wp.media({
                title: '<?php _e('Select Video Thumbnail', 'hello-elementor-child'); ?>',
                button: {
                    text: '<?php _e('Use this image', 'hello-elementor-child'); ?>'
                },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.id);
                $preview.html('<img src="' + attachment.url + '" style="max-width: 150px; margin-top: 10px;" />');
                $button.siblings('.remove-video-thumbnail').show();
                updateComponentsJSON();
            });

            frame.open();
        });

        // Remove video thumbnail
        $(document).on('click', '.remove-video-thumbnail', function(e) {
            e.preventDefault();
            var $button = $(this);
            $button.siblings('input[type="hidden"]').val('');
            $button.siblings('.video-thumbnail-preview').html('');
            $button.hide();
            updateComponentsJSON();
        });
        
        // Show remove button if thumbnail exists on load
        $('.video-thumbnail-input').each(function() {
            if ($(this).val()) {
                $(this).siblings('.remove-video-thumbnail').show();
            }
        });

        // Update components JSON
        function updateComponentsJSON() {
            var components = [];
            $('.service-component-editor').each(function() {
                var $editor = $(this);
                var type = $editor.data('component-type');
                var component = { type: type };

                if (type === 'video') {
                    component.video_url = $editor.find('.video-url-input').val() || '';
                    component.video_caption = $editor.find('.video-caption-input').val() || '';
                    component.video_thumbnail = $editor.find('.video-thumbnail-input').val() || '';
                } else if (type === 'checklist') {
                    component.checklist_title = $editor.find('.checklist-title-input').val() || '';
                    component.checklist_items = [];
                    $editor.find('.checklist-item-input').each(function() {
                        var item = $(this).val().trim();
                        if (item) {
                            component.checklist_items.push(item);
                        }
                    });
                } else if (type === 'timeline') {
                    component.timeline_title = $editor.find('.timeline-title-input').val() || '';
                    component.timeline_items = [];
                    $editor.find('.service-component-item').each(function() {
                        var $item = $(this);
                        var step = $item.find('.timeline-step-input').val().trim();
                        var description = $item.find('.timeline-description-input').val().trim();
                        if (step || description) {
                            component.timeline_items.push({
                                step: step,
                                description: description
                            });
                        }
                    });
                }

                components.push(component);
            });

            $('#service_components_json').val(JSON.stringify(components));
        }

        // Initialize with existing data
        updateComponentsJSON();
    });
    </script>
    <?php
}

/**
 * Render component editor UI
 *
 * @param array $component Component data
 * @param int|string $index Component index
 */
function ehs_render_component_editor($component, $index) {
    $type = isset($component['type']) ? $component['type'] : '';
    $type_label = ucfirst($type);
    ?>
    <div class="service-component-editor" data-component-type="<?php echo esc_attr($type); ?>">
        <div class="service-component-editor-header">
            <span class="service-component-editor-title"><?php echo esc_html($type_label); ?> Component</span>
            <div class="service-component-editor-actions">
                <button type="button" class="button move-component-up">↑</button>
                <button type="button" class="button move-component-down">↓</button>
                <button type="button" class="button remove-component"><?php _e('Remove', 'hello-elementor-child'); ?></button>
            </div>
        </div>

        <?php if ($type === 'video') : ?>
            <div class="service-component-field">
                <label><?php _e('Video URL', 'hello-elementor-child'); ?></label>
                <input type="url" class="video-url-input regular-text" 
                       value="<?php echo isset($component['video_url']) ? esc_attr($component['video_url']) : ''; ?>"
                       placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..." />
                <p class="description"><?php _e('Enter YouTube or Vimeo video URL', 'hello-elementor-child'); ?></p>
            </div>
            <div class="service-component-field">
                <label><?php _e('Video Caption (Optional)', 'hello-elementor-child'); ?></label>
                <textarea class="video-caption-input" rows="2"><?php echo isset($component['video_caption']) ? esc_textarea($component['video_caption']) : ''; ?></textarea>
            </div>
            <div class="service-component-field">
                <label><?php _e('Custom Thumbnail (Optional)', 'hello-elementor-child'); ?></label>
                <input type="hidden" class="video-thumbnail-input" 
                       value="<?php echo isset($component['video_thumbnail']) ? esc_attr($component['video_thumbnail']) : ''; ?>" />
                <button type="button" class="button select-video-thumbnail"><?php _e('Select Image', 'hello-elementor-child'); ?></button>
                <button type="button" class="button remove-video-thumbnail" style="<?php echo (isset($component['video_thumbnail']) && $component['video_thumbnail']) ? '' : 'display:none;'; ?>"><?php _e('Remove', 'hello-elementor-child'); ?></button>
                <div class="video-thumbnail-preview">
                    <?php if (isset($component['video_thumbnail']) && $component['video_thumbnail']) : ?>
                        <?php echo wp_get_attachment_image($component['video_thumbnail'], 'thumbnail'); ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($type === 'checklist') : ?>
            <div class="service-component-field">
                <label><?php _e('Checklist Title (Optional)', 'hello-elementor-child'); ?></label>
                <input type="text" class="checklist-title-input regular-text" 
                       value="<?php echo isset($component['checklist_title']) ? esc_attr($component['checklist_title']) : ''; ?>"
                       placeholder="<?php _e('Our Services Include', 'hello-elementor-child'); ?>" />
            </div>
            <div class="service-component-field">
                <label><?php _e('Checklist Items', 'hello-elementor-child'); ?></label>
                <button type="button" class="button add-checklist-item"><?php _e('+ Add Item', 'hello-elementor-child'); ?></button>
                <div class="service-component-items">
                    <?php if (isset($component['checklist_items']) && is_array($component['checklist_items'])) : ?>
                        <?php foreach ($component['checklist_items'] as $item) : ?>
                            <div class="service-component-item">
                                <input type="text" class="checklist-item-input" value="<?php echo esc_attr($item); ?>" />
                                <button type="button" class="button service-component-item-remove remove-checklist-item"><?php _e('Remove', 'hello-elementor-child'); ?></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($type === 'timeline') : ?>
            <div class="service-component-field">
                <label><?php _e('Timeline Title (Optional)', 'hello-elementor-child'); ?></label>
                <input type="text" class="timeline-title-input regular-text" 
                       value="<?php echo isset($component['timeline_title']) ? esc_attr($component['timeline_title']) : ''; ?>"
                       placeholder="<?php _e('Our Process', 'hello-elementor-child'); ?>" />
            </div>
            <div class="service-component-field">
                <label><?php _e('Timeline Steps', 'hello-elementor-child'); ?></label>
                <button type="button" class="button add-timeline-item"><?php _e('+ Add Step', 'hello-elementor-child'); ?></button>
                <div class="service-component-items">
                    <?php if (isset($component['timeline_items']) && is_array($component['timeline_items'])) : ?>
                        <?php foreach ($component['timeline_items'] as $item) : ?>
                            <div class="service-component-item">
                                <input type="text" class="timeline-step-input" 
                                       value="<?php echo isset($item['step']) ? esc_attr($item['step']) : ''; ?>"
                                       placeholder="<?php _e('Step title', 'hello-elementor-child'); ?>" 
                                       style="width: 200px;" />
                                <textarea class="timeline-description-input" 
                                          placeholder="<?php _e('Step description', 'hello-elementor-child'); ?>" 
                                          rows="2"><?php echo isset($item['description']) ? esc_textarea($item['description']) : ''; ?></textarea>
                                <button type="button" class="button service-component-item-remove remove-timeline-item"><?php _e('Remove', 'hello-elementor-child'); ?></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Save Service Components Meta Box Data
 */
function ehs_save_services_components_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['ehs_service_components_meta_box_nonce']) || !wp_verify_nonce($_POST['ehs_service_components_meta_box_nonce'], 'ehs_service_components_meta_box')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save components JSON
    if (isset($_POST['service_components_json'])) {
        $components_json = sanitize_text_field($_POST['service_components_json']);
        // Use our sanitize function
        $sanitized = ehs_sanitize_service_components($components_json);
        update_post_meta($post_id, 'service_components', $sanitized);
    } else {
        delete_post_meta($post_id, 'service_components');
    }
}
add_action('save_post_services', 'ehs_save_services_components_meta_box');

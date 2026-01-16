<?php
/**
 * Contact Form Entries Admin Page
 * View submissions and export to CSV
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add entries submenu page
 */
function ehs_add_contact_form_entries_page() {
    add_submenu_page(
        'options-general.php',
        'Contact Form Entries',
        'Form Entries',
        'manage_options',
        'ehs-contact-form-entries',
        'ehs_render_contact_form_entries_page'
    );
}
add_action('admin_menu', 'ehs_add_contact_form_entries_page');

/**
 * Handle CSV export
 */
function ehs_handle_csv_export() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'ehs-contact-form-entries') {
        return;
    }

    if (!isset($_GET['action']) || $_GET['action'] !== 'export_csv') {
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access');
    }

    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'ehs_export_entries_csv')) {
        wp_die('Security check failed');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'ehs_contact_form_entries';

    // Build query with optional date filters
    $where = array('1=1');
    $params = array();

    if (!empty($_GET['date_from'])) {
        $where[] = 'created_at >= %s';
        $params[] = sanitize_text_field($_GET['date_from']) . ' 00:00:00';
    }

    if (!empty($_GET['date_to'])) {
        $where[] = 'created_at <= %s';
        $params[] = sanitize_text_field($_GET['date_to']) . ' 23:59:59';
    }

    $where_clause = implode(' AND ', $where);
    $query = "SELECT * FROM $table_name WHERE $where_clause ORDER BY created_at DESC";

    if (!empty($params)) {
        $query = $wpdb->prepare($query, $params);
    }

    $entries = $wpdb->get_results($query, ARRAY_A);

    // Set headers for CSV download
    $filename = 'contact-form-entries-' . date('Y-m-d-His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add BOM for Excel compatibility
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // CSV headers
    fputcsv($output, array(
        'ID',
        'Name',
        'Email',
        'Phone',
        'Company',
        'Subject',
        'Message',
        'IP Address',
        'Turnstile Verified',
        'Submitted At'
    ));

    // CSV rows
    foreach ($entries as $entry) {
        fputcsv($output, array(
            $entry['id'],
            $entry['name'],
            $entry['email'],
            $entry['phone'],
            $entry['company'],
            $entry['subject'],
            $entry['message'],
            $entry['ip_address'],
            $entry['turnstile_verified'] ? 'Yes' : 'No',
            $entry['created_at']
        ));
    }

    fclose($output);
    exit;
}
add_action('admin_init', 'ehs_handle_csv_export');

/**
 * Handle bulk actions (delete)
 */
function ehs_handle_entries_bulk_actions() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'ehs-contact-form-entries') {
        return;
    }

    if (!isset($_POST['action']) || $_POST['action'] !== 'delete') {
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access');
    }

    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'ehs_bulk_entries_nonce')) {
        wp_die('Security check failed');
    }

    if (empty($_POST['entry_ids']) || !is_array($_POST['entry_ids'])) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'ehs_contact_form_entries';

    $ids = array_map('intval', $_POST['entry_ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '%d'));

    $wpdb->query($wpdb->prepare(
        "DELETE FROM $table_name WHERE id IN ($placeholders)",
        $ids
    ));

    wp_redirect(add_query_arg(array(
        'page' => 'ehs-contact-form-entries',
        'deleted' => count($ids)
    ), admin_url('options-general.php')));
    exit;
}
add_action('admin_init', 'ehs_handle_entries_bulk_actions');

/**
 * Render entries page
 */
function ehs_render_contact_form_entries_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ehs_contact_form_entries';

    // Pagination
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    // Get total count
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);

    // Get entries
    $entries = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    // Show deleted notice
    if (isset($_GET['deleted'])) {
        $deleted_count = intval($_GET['deleted']);
        echo '<div class="notice notice-success"><p>' . sprintf('%d entries deleted.', $deleted_count) . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Contact Form Entries</h1>

        <div class="tablenav top">
            <div class="alignleft actions">
                <form method="get" action="" style="display: inline-flex; gap: 8px; align-items: center;">
                    <input type="hidden" name="page" value="ehs-contact-form-entries" />
                    <input type="hidden" name="action" value="export_csv" />
                    <?php wp_nonce_field('ehs_export_entries_csv', '_wpnonce', false); ?>

                    <label for="date_from">From:</label>
                    <input type="date" id="date_from" name="date_from" value="<?php echo esc_attr($_GET['date_from'] ?? ''); ?>" />

                    <label for="date_to">To:</label>
                    <input type="date" id="date_to" name="date_to" value="<?php echo esc_attr($_GET['date_to'] ?? ''); ?>" />

                    <button type="submit" class="button">Export CSV</button>
                </form>
            </div>

            <div class="alignright">
                <span class="displaying-num"><?php echo number_format($total_items); ?> items</span>
            </div>
        </div>

        <?php if (empty($entries)): ?>
            <div class="notice notice-info">
                <p>No form submissions yet.</p>
            </div>
        <?php else: ?>
            <form method="post" action="">
                <?php wp_nonce_field('ehs_bulk_entries_nonce'); ?>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all" />
                            </td>
                            <th class="manage-column column-id">ID</th>
                            <th class="manage-column column-name">Name</th>
                            <th class="manage-column column-email">Email</th>
                            <th class="manage-column column-subject">Subject</th>
                            <th class="manage-column column-message">Message</th>
                            <th class="manage-column column-verified">Verified</th>
                            <th class="manage-column column-date">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="entry_ids[]" value="<?php echo esc_attr($entry->id); ?>" />
                                </th>
                                <td><?php echo esc_html($entry->id); ?></td>
                                <td>
                                    <strong><?php echo esc_html($entry->name); ?></strong>
                                    <?php if ($entry->company): ?>
                                        <br><small><?php echo esc_html($entry->company); ?></small>
                                    <?php endif; ?>
                                    <?php if ($entry->phone): ?>
                                        <br><small><?php echo esc_html($entry->phone); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo esc_attr($entry->email); ?>"><?php echo esc_html($entry->email); ?></a>
                                </td>
                                <td><?php echo esc_html($entry->subject); ?></td>
                                <td>
                                    <div style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo esc_html(wp_trim_words($entry->message, 20)); ?>
                                    </div>
                                    <button type="button" class="button-link" onclick="alert('<?php echo esc_js($entry->message); ?>')">View full</button>
                                </td>
                                <td>
                                    <?php if ($entry->turnstile_verified): ?>
                                        <span style="color: green;">&#10003;</span>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo esc_html(date('M j, Y', strtotime($entry->created_at))); ?>
                                    <br><small><?php echo esc_html(date('g:i a', strtotime($entry->created_at))); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <select name="action">
                            <option value="">Bulk actions</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="submit" class="button action" value="Apply" onclick="return confirm('Are you sure you want to delete the selected entries?');" />
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <div class="tablenav-pages">
                            <span class="pagination-links">
                                <?php if ($current_page > 1): ?>
                                    <a class="first-page button" href="<?php echo esc_url(add_query_arg('paged', 1)); ?>">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                    <a class="prev-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page - 1)); ?>">
                                        <span aria-hidden="true">&lsaquo;</span>
                                    </a>
                                <?php else: ?>
                                    <span class="tablenav-pages-navspan button disabled">&laquo;</span>
                                    <span class="tablenav-pages-navspan button disabled">&lsaquo;</span>
                                <?php endif; ?>

                                <span class="paging-input">
                                    <?php echo $current_page; ?> of <span class="total-pages"><?php echo $total_pages; ?></span>
                                </span>

                                <?php if ($current_page < $total_pages): ?>
                                    <a class="next-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page + 1)); ?>">
                                        <span aria-hidden="true">&rsaquo;</span>
                                    </a>
                                    <a class="last-page button" href="<?php echo esc_url(add_query_arg('paged', $total_pages)); ?>">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                <?php else: ?>
                                    <span class="tablenav-pages-navspan button disabled">&rsaquo;</span>
                                    <span class="tablenav-pages-navspan button disabled">&raquo;</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>

        <style>
            .wp-list-table .column-cb { width: 30px; }
            .wp-list-table .column-id { width: 50px; }
            .wp-list-table .column-name { width: 150px; }
            .wp-list-table .column-email { width: 180px; }
            .wp-list-table .column-subject { width: 150px; }
            .wp-list-table .column-verified { width: 70px; text-align: center; }
            .wp-list-table .column-date { width: 100px; }
            #cb-select-all { margin: 0; }
        </style>

        <script>
            document.getElementById('cb-select-all').addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('input[name="entry_ids[]"]');
                checkboxes.forEach(function(cb) {
                    cb.checked = this.checked;
                }, this);
            });
        </script>
    </div>
    <?php
}

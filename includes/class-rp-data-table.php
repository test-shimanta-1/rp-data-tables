<?php

/**
 * Plugin Main Class File.
 * Handels: Handles: Inventor(Post Type) creation and meta management
 * 
 * @since 1.0.0
 * @package RP_Data_Table
 */

class RP_Data_Table
{

    /**
     * Constructor
     * 
     * Initializes the 'our_inventors' CPT and meta boxes, and hooks save function.
     * 
     * @since 1.0.0
     * @return void
     */
    public function __construct()
    {
        add_action('init', array($this, 'sdw_init_inventor_post_type')); // registering CPT: our_inventors
        add_action('add_meta_boxes', array($this, 'sdw_add_inventor_meta_box')); // enabeling meta boxes for CPT 
        add_action('save_post', array($this, 'sdw_save_inventor_meta')); // saves records of CPT(our_inventors) 
    }

    /**
     * Initialize CPT: our_inventors.
     * Registers the custom post type with labels, supports, and arguments.
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_init_inventor_post_type()
    {
        $supports = array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'comments',
            'revisions',
            'post-formats',
        );
        $labels = array(
            'name' => _x('Inventors', 'plural'),
            'singular_name' => _x('Inventors', 'singular'),
            'menu_name' => _x('Inventors', 'admin menu'),
            'name_admin_bar' => _x('Inventors', 'admin bar'),
            'add_new' => _x('Add New', 'add new'),
            'add_new_item' => __('Add New Inventors'),
            'new_item' => __('New Inventors'),
            'edit_item' => __('Edit Inventors'),
            'view_item' => __('View Inventors'),
            'all_items' => __('All Inventors'),
            'search_items' => __('Search Inventors'),
            'not_found' => __('No Inventors found.'),
        );
        $args = array(
            'supports' => $supports,
            'labels' => $labels,
            'public' => true,
            'menu_icon' => 'dashicons-groups',
            'query_var' => true,
            'rewrite' => array('slug' => 'inventors'),
            'has_archive' => true,
            'hierarchical' => false,
            'show_in_menu' => true,
        );
        register_post_type('our_inventors', $args);
    }

    /**
     * Add meta box to 'our_inventors' CPT.
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_add_inventor_meta_box()
    {
        add_meta_box(
            'rp_inventor_meta',
            __('Inventor Details', 'rp-data-table'),
            array($this, 'sdw_render_inventor_meta_box'),
            'our_inventors',
            'normal',
            'default'
        );
    }

    /**
     * Render the meta box fields in the admin for CPT.
     * 
     * @param WP_Post $post The post object for which meta is rendered
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_render_inventor_meta_box($post)
    {
        wp_nonce_field('rp_save_inventor_meta', 'rp_inventor_nonce');

        $ipa_number = get_post_meta($post->ID, '_ipa_number', true);
        $filing_date = get_post_meta($post->ID, '_filing_date', true);
        $grant_no = get_post_meta($post->ID, '_grant_no', true);
        $grant_date = get_post_meta($post->ID, '_grant_date', true);
        $financial_year = get_post_meta($post->ID, '_financial_year', true);
        $calendar_year = get_post_meta($post->ID, '_calendar_year', true);

        $fy_from = $fy_to = '';

        if (!empty($financial_year) && strpos($financial_year, '-') !== false) {
            list($fy_from, $fy_to) = explode('-', $financial_year);
        }
        ?>

        <table class="form-table">

            <tr>
                <th>IPA Number</th>
                <td><input type="text" name="ipa_number" value="<?php echo esc_attr($ipa_number); ?>"></td>
            </tr>

            <tr>
                <th>Filing Date</th>
                <td><input type="date" name="filing_date"></td>
            </tr>

            <tr>
                <th>Grant Number</th>
                <td><input type="text" name="grant_no" value="<?php echo esc_attr($grant_no); ?>"></td>
            </tr>

            <tr>
                <th>Grant Date</th>
                <td><input type="date" name="grant_date"></td>
            </tr>

            <!-- FINANCIAL YEAR -->
            <tr>
                <th>Financial Year</th>
                <td>
                    <select name="fy_from">
                        <option value="">From Year</option>
                        <?php for ($y = 1950; $y <= 2050; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php selected($fy_from, $y); ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <select name="fy_to">
                        <option value="">To Year</option>
                        <?php for ($y = 1950; $y <= 2050; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php selected($fy_to, $y); ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>

            <!-- CALENDAR YEAR -->
            <tr>
                <th>Calendar Year</th>
                <td>
                    <select name="calendar_year">
                        <option value="">Select Year</option>
                        <?php for ($y = 1950; $y <= 2050; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php selected($calendar_year, $y); ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>

        </table>
        <?php
    }

    /**
     * Save meta box data when CPT is saved.
     * 
     * @param int $post_id ID of the post being saved
     * 
     * @since 1.0.0
     * @return void
     */
    public function sdw_save_inventor_meta($post_id)
    {
        if (
            !isset($_POST['rp_inventor_nonce']) ||
            !wp_verify_nonce($_POST['rp_inventor_nonce'], 'rp_save_inventor_meta')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!current_user_can('edit_post', $post_id))
            return;

        // Text fields
        update_post_meta($post_id, '_ipa_number', sanitize_text_field($_POST['ipa_number'] ?? ''));
        update_post_meta($post_id, '_grant_no', sanitize_text_field($_POST['grant_no'] ?? ''));

        // Calendar year
        update_post_meta($post_id, '_calendar_year', sanitize_text_field($_POST['calendar_year'] ?? ''));

        // Financial year (From-To)
        if (!empty($_POST['fy_from']) && !empty($_POST['fy_to'])) {
            $financial_year = intval($_POST['fy_from']) . '-' . intval($_POST['fy_to']);
            update_post_meta($post_id, '_financial_year', $financial_year);
        } else {
            delete_post_meta($post_id, '_financial_year');
        }

        // Dates
        if (!empty($_POST['filing_date'])) {
            update_post_meta(
                $post_id,
                '_filing_date',
                date('d.m.Y', strtotime($_POST['filing_date']))
            );
        }

        if (!empty($_POST['grant_date'])) {
            update_post_meta(
                $post_id,
                '_grant_date',
                date('d.m.y', strtotime($_POST['grant_date']))
            );
        }
    }


}
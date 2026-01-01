<?php

/**
 * Admin Page Controller Class
 *
 * Handles:
 *  - Registering plugin admin menu page
 *  - Displaying shortcode usage for admins
 *  - Rendering a DataTable preview inside WordPress admin
 *  - Loading DataTables assets ONLY on plugin admin page
 *
 *
 * @since 1.0.0
 * @package RP_Data_Table
 */
class RP_Table_Admin
{
    /**
     * Constructor.
     *
     * Registers admin menu and loads assets conditionally.
     *
     * @since 1.0.0
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'sdw_register_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'sdw_ui_enqueue_admin_assets']);
    }

    /**
     * Register plugin admin menu page.
     *
     * Adds a top-level menu item in WordPress dashboard
     * for "RP Data Table".
     *
     * @since 1.0.0
     * @return void
     */
    public function sdw_register_admin_page()
    {
        add_menu_page(
            __('RP Data Table', 'rp-data-table'),
            'RP Data Table',
            'manage_options',
            'rp-data-table',
            [$this, 'sdw_render_admin_page'],
            'dashicons-excerpt-view',
            25
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * Loads DataTables and plugin assets ONLY on
     * the RP Data Table admin page.
     *
     * @since 1.0.0
     * @param string $hook Current admin page hook.
     * @return void
     */
    public function sdw_ui_enqueue_admin_assets($hook)
    {
        if ($hook !== 'toplevel_page_rp-data-table') {
            return;
        }

        // Same DataTables assets as frontend
        wp_enqueue_style('dt-css', 'https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css');
        wp_enqueue_script('dt-js', 'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js', ['jquery'], null, true);

        wp_enqueue_style('dt-btn-css', 'https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css');
        wp_enqueue_script('dt-btn-js', 'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js', ['dt-js'], null, true);

        wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', [], null, true);
        wp_enqueue_script('pdfmake', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', [], null, true);
        wp_enqueue_script('pdfmake-fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['pdfmake'], null, true);

        wp_enqueue_script('dt-html5', 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js', ['dt-btn-js'], null, true);
        wp_enqueue_script('dt-print', 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js', ['dt-btn-js'], null, true);

        wp_enqueue_script('rp-table-js', RP_DATA_TABLE_URL . 'assets/js/table.js', ['dt-js'], null, true);
        wp_enqueue_style('rp-table-css', RP_DATA_TABLE_URL . 'assets/css/table.css');
    }

    /**
     * Render admin page content.
     *
     * Displays:
     *  - Shortcode copy box for admin users
     *  - Inventor data table preview with filters & export options
     *
     * @since 1.0.0
     * @return void
     */
    public function sdw_render_admin_page()
    {
        ?>
        <div class="wrap">
            <h1>RP Data Table</h1>

            <!-- SHORTCODE BOX -->
            <div style="background:#fff;padding:15px;border:1px solid #ddd;margin-bottom:20px;">
                <h2>Shortcode</h2>
                <p>Copy and paste this shortcode into any Page, Post, or Elementor block:</p>

                <input type="text" value="[responsive_data_table]" readonly onclick="this.select();"
                    style="width:300px;font-size:16px;padding:6px;" />

                <p style="color:#666;margin-top:10px;">
                    This shortcode will display the inventor data table on the frontend.
                </p>
            </div>

            <!-- TABLE PREVIEW -->
            <h2>Inventor Data Preview</h2>

            <?php
            // Reuse SAME table markup logic (no duplicate JS)
            $query = new WP_Query([
                'post_type' => 'our_inventors',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC'
            ]);
            ?>

            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Inventor</th>
                        <th>Title</th>
                        <th>IPA</th>
                        <th>Filing Date</th>
                        <th>Grant No</th>
                        <th>Grant Date</th>
                        <th>Financial Year</th>
                        <th>Calendar Year</th>
                    </tr>
                    <tr class="filters">
                        <th><input type="text" placeholder="Search"></th>
                        <th><input type="text" placeholder="Search"></th>
                        <th><input type="text" placeholder="Search"></th>
                        <th><input type="text" placeholder="Search"></th>
                        <th><input type="text" placeholder="Search"></th>
                        <th><input type="text" placeholder="Search"></th>
                        <th><input type="text" placeholder="Search"></th>
                        <th><input type="text" placeholder="Search"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query->have_posts()):
                        while ($query->have_posts()):
                            $query->the_post(); ?>
                            <tr>
                                <td><?php the_title(); ?></td>
                                <td><?php echo wp_trim_words(get_the_content(), 10); ?></td>
                                <td><?php echo esc_html(get_post_meta(get_the_ID(), '_ipa_number', true)); ?></td>
                                <td><?php echo esc_html(get_post_meta(get_the_ID(), '_filing_date', true)); ?></td>
                                <td><?php echo esc_html(get_post_meta(get_the_ID(), '_grant_no', true)); ?></td>
                                <td><?php echo esc_html(get_post_meta(get_the_ID(), '_grant_date', true)); ?></td>
                                <td><?php echo esc_html(get_post_meta(get_the_ID(), '_financial_year', true)); ?></td>
                                <td><?php echo esc_html(get_post_meta(get_the_ID(), '_calendar_year', true)); ?></td>
                            </tr>
                        <?php endwhile;
                        wp_reset_postdata();
                    endif; ?>
                </tbody>
            </table>

        </div>
        <?php
    }

}
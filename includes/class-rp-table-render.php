<?php

/**
 * Frontend Table Render Class.
 *
 * Handles rendering of inventor data tables on the frontend
 * using the `[responsive_table]` shortcode.
 *
 * @since   1.0.0
 * @package RP_Data_Table
 */
class RP_Table_Render
{

    /**
     * Constructor.
     *
     * Registers shortcode and hooks frontend assets.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_shortcode('responsive_data_table', [$this, 'sdw_render_table']);
        add_action('wp_enqueue_scripts', [$this, 'sdw_enqueue_scripts']);
    }

    /**
     * Enqueue frontend scripts and styles for DataTables.
     *
     * Loads:
     *  - DataTables core
     *  - Buttons extension
     *  - Export dependencies (Excel / PDF / Print)
     *  - Plugin custom JS & CSS
     *
     * @since 1.0.0
     * @return void
     */
    public function sdw_enqueue_scripts()
    {
        // DataTables core
        wp_enqueue_style('dt-css', 'https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css');
        wp_enqueue_script('dt-js', 'https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js', ['jquery'], null, true);

        // Buttons
        wp_enqueue_style('dt-btn-css', 'https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css');
        wp_enqueue_script('dt-btn-js', 'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js', ['dt-js'], null, true);

        wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', [], null, true);
        wp_enqueue_script('pdfmake', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', [], null, true);
        wp_enqueue_script('pdfmake-fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['pdfmake'], null, true);

        wp_enqueue_script('dt-html5', 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js', ['dt-btn-js'], null, true);
        wp_enqueue_script('dt-print', 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js', ['dt-btn-js'], null, true);

        // Custom plugin JS/CSS
        wp_enqueue_script('rp-table-js', RP_DATA_TABLE_URL . 'assets/js/table.js', ['dt-js'], null, true);
        wp_enqueue_style('rp-table-css', RP_DATA_TABLE_URL . 'assets/css/table.css');
    }

    /**
     * Shortcode callback function.
     *
     * Generates HTML markup for inventor data table
     * and returns it for frontend display.
     *
     * Usage:
     *  [responsive_table]
     *
     * @since 1.0.0
     * @return string HTML output
     */
    public function sdw_render_table()
    {
        ob_start(); ?>
        <div class="container">
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
                        <th><input type="text" placeholder="Search Inventor"></th>
                        <th><input type="text" placeholder="Search Title"></th>
                        <th><input type="text" placeholder="Search IPA"></th>
                        <th><input type="text" placeholder="Search Date"></th>
                        <th><input type="text" placeholder="Search Grant No"></th>
                        <th><input type="text" placeholder="Search Grant Date"></th>
                        <th><input type="text" placeholder="Search FY"></th>
                        <th><input type="text" placeholder="Search CY"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = new WP_Query([
                        'post_type' => 'our_inventors',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ]);
                    if ($query->have_posts()):
                        while ($query->have_posts()):
                            $query->the_post(); ?>
                            <tr>
                                <td><?php the_title(); ?></td>
                                <td><?php the_content(); ?></td>
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
        return ob_get_clean();
    }
}
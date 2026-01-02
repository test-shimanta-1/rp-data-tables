<?php
/**
 * Plugin Meta Class File.
 * 
 * Handles: Plugin row meta links and Thickbox modal rendering
 *
 * @since 1.0.1
 * @package RP_Data_Table
 */

if (!defined('ABSPATH')) {
    exit;
}

class RP_Data_Table_Plugin_Meta
{

    /**
     * Constructor.
     *
     * Registers hooks for plugin meta row and admin UI.
     *
     * @since 1.0.1
     */
    public function __construct()
    {
        add_filter('plugin_row_meta', array($this, 'sdw_add_view_details_link'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'sdw_load_thickbox'));
        add_action('admin_footer', array($this, 'sdw_render_details_modal'));
    }

    /**
     * Load Thickbox scripts on Plugins page.
     *
     * @param string $hook Current admin page hook
     *
     * @since 1.0.1
     * @return void
     */
    public function sdw_load_thickbox($hook)
    {
        if ($hook === 'plugins.php') {
            add_thickbox();
        }
    }

    /**
     * Add "View Details" link to plugin row meta.
     *
     * @param array  $links Existing plugin meta links
     * @param string $file  Plugin file path
     *
     * @since 1.0.1
     * @return array
     */
    public function sdw_add_view_details_link($links, $file)
    {
        if ($file === plugin_basename(RP_DATA_TABLE_FILE)) {
            $links[] = sprintf(
                '<a href="%s" class="thickbox">%s</a>',
                esc_url('#TB_inline?width=600&height=550&inlineId=rp_data_table_details'),
                esc_html__('View details', 'rp-data-table')
            );
        }

        return $links;
    }

    /**
     * Render Thickbox modal content in admin footer.
     *
     * @since 1.0.1
     * @return void
     */
    public function sdw_render_details_modal()
    {
        $screen = get_current_screen();

        if (!$screen || $screen->id !== 'plugins') {
            return;
        }
        ?>
        <div id="rp_data_table_details" style="display:none;">
            <h1><?php esc_html_e('RP Data Table', 'rp-data-table'); ?></h1>

            <div style="background:#fff;padding:15px;border:1px solid #ddd;margin-bottom:20px;">
                <h2><?php esc_html_e('Shortcode', 'rp-data-table'); ?></h2>

                <p><?php esc_html_e('Copy and paste this shortcode:', 'rp-data-table'); ?></p>

                <input
                    type="text"
                    value="<?php echo esc_attr('[responsive_data_table]'); ?>"
                    readonly
                    onclick="this.select();"
                    style="width:200px;font-size:16px;padding:6px;"
                />

                <p style="color:#666;margin-top:10px;">
                    <?php esc_html_e('Displays the responsive data table on the frontend.', 'rp-data-table'); ?>
                </p>
            </div>
        </div>
        <?php
    }
}

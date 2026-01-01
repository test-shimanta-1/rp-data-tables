<?php
/*
 * Plugin Name:       RP Data Table
 * Description:       Responsive Data Tables is a WordPress plugin that helps you manage and display inventor-related information in responsive, searchable, and feature-rich data tables across your website.
 * Text Domain:       rp-data-table
 * Version:           1.0.0
 * Author:            sundew team
 * Author URI:        https://sundewsolutions.com/
 * 
 * 
 * @package RP_Data_Table
 * @since 1.0.0
 * 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define Constants.
define('RP_DATA_TABLE_VERSION', '1.0.0');
define('RP_DATA_TABLE_PATH', plugin_dir_path(__FILE__));
define('RP_DATA_TABLE_URL', plugin_dir_url(__FILE__));
define('RP_DATA_TABLE_FILE', __FILE__);

// Core Includes.
require_once RP_DATA_TABLE_PATH . 'includes/class-rp-data-table.php';
require_once RP_DATA_TABLE_PATH . 'includes/class-rp-table-render.php';
require_once RP_DATA_TABLE_PATH . 'includes/class-rp-table-admin.php';


// Initialize
new RP_Data_Table();
new RP_Table_Render();
new RP_Table_Admin();
<?php
/**
 * Plugin Name: CSV Export
 */

if (!defined('CSV_DIR')){
     define('CSV_DIR', plugin_dir_path(__FILE__ ));
}

Global $shop_id;
$shop_id = 2;

function csv_export_activate()
{
    //Do Nothing
}
register_activation_hook(__FILE__, 'csv_export_activate');

function csv_export_deactivate()
{
    //Do Nothing
}
register_deactivation_hook(__FILE__, 'csv_export_deactivate');

function csv_export_uninstall()
{
    //Do Nothing
}
register_uninstall_hook(__FILE__, 'csv_export_uninstall');

require_once(CSV_DIR.'templates/admin_page.php');



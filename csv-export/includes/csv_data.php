<?php

require_once(CSV_DIR.'includes/csv_data_users.php');

require_once(CSV_DIR.'includes/csv_data_orders.php');

require_once(CSV_DIR.'includes/csv_data_withdrawals.php');

require_once(CSV_DIR.'includes/csv_output.php');


// Add action hook only if action=download_csv
if ( isset($_GET['action'] )){
    if ($_GET['action'] == 'download_csv_users') {
        // Handle CSV Export
        add_action('admin_init', 'csv_export_users') ;

    } elseif($_GET['action'] == 'download_csv_orders'){
        // Handle CSV Export
        add_action('admin_init', 'csv_export_orders');

    } elseif($_GET['action'] == 'download_csv_withdrawals'){
        // Handle CSV Export
        add_action('admin_init', 'csv_export_withdrawals');

    } else {
        return;
    }
}


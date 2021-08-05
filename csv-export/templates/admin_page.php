<?php

require_once(CSV_DIR.'includes/csv_data.php');

function csv_export_admin_menu()
{
  add_menu_page( 'CSV Export', 'CSV Export', 'manage_options', 'csv-export-menu', 'csv_export_admin_page', '', '6' );
}
add_action('admin_menu', 'csv_export_admin_menu');

function csv_export_admin_page(){
  ?>
  <div class="wrap">
  <h1>CSV EXPORT</h1>
  <p></p>

  <h2>User Data</h2>
  <p>User Email, Name</p>
  <a href="<?php echo admin_url( 'admin.php?page=csv-export-menu' ) ?>&action=download_csv_users&_wpnonce=<?php echo wp_create_nonce( 'download_csv_users' )?>" class="page-title-action"><?php _e('Export User Data','csv-export');?></a>
  <p></p>

  <h2>Order Data</h2>
  <p>Vendor Id, Vendor Name, Order Id, Product Id, Product Name, Payment Method, Quantity,
  <br>Created, Customer Id, Customer Name,Customer Address</p>
  <a href="<?php echo admin_url( 'admin.php?page=csv-export-menu' ) ?>&action=download_csv_orders&_wpnonce=<?php echo wp_create_nonce( 'download_csv_orders' )?>" class="page-title-action"><?php _e('Export Order Data','csv-export');?></a>
  <p></p>

  <h2>Withdrawal Data</h2>
  <p>User Email, Name</p>
  <a href="<?php echo admin_url( 'admin.php?page=csv-export-menu' ) ?>&action=download_csv_withdrawals&_wpnonce=<?php echo wp_create_nonce( 'download_csv_withdrawals' )?>" class="page-title-action"><?php _e('Export Withdrawal Data','csv-export');?></a>
  <p></p>

  </div>
  <?php
}




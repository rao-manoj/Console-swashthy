<?php

function csv_export_orders()
{

    global $shop_id;
    
    // Check for current user privileges
    if (!current_user_can('manage_options')) {
        return false;
    }

    // Check if we are in WP-Admin
    if (!is_admin()) {
        return false;
    }

    // Nonce Check
    $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
    if (! wp_verify_nonce($nonce, 'download_csv_orders')) {
        die('Security check error');
    }

    $domain = $_SERVER['SERVER_NAME'];
    $filename = 'orders-' . $domain . '-' . time() . '.csv';

    $header_row = array(
        'Vendor Id',
        'Vendor Name',
        'Order Id',
        'Product Id',
        'Product Name',
        'Payment Method',
        'Quantity',
        'Created',
        'Customer Id',
        'Customer Name',
        'Customer Address'
    );
    $data_rows = array();
    global $wpdb;

    $sql=
    "SELECT
    WCFMMPO.vendor_id,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.vendor_id = user_id
                AND meta_key = 'store_name') AS vendor_name,
    WCFMMPO.order_id,
    WCFMMPO.product_id,
    WCFMMPO.variation_id,
    (SELECT
            post_title
        FROM
            {$wpdb->get_blog_prefix($shop_id)}posts
        WHERE
            post_type = 'product'
                AND ID = WCFMMPO.product_id) AS product_name,
    WCFMMPO.payment_method,
    WCFMMPO.quantity,
    WCFMMPO.product_price,
    WCFMMPO.created,
    WCFMMPO.customer_id,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.customer_id = user_id
                AND meta_key = 'shipping_first_name') AS customer_first_name,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.customer_id = user_id
                AND meta_key = 'shipping_last_name') AS customer_last_name,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.customer_id = user_id
                AND meta_key = 'shipping_address_1') AS customer_address_1,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.customer_id = user_id
                AND meta_key = 'shipping_address_2') AS customer_address_2,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.customer_id = user_id
                AND meta_key = 'shipping_city') AS customer_city,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.customer_id = user_id
                AND meta_key = 'shipping_state') AS customer_state,
    (SELECT
            meta_value
        FROM
            {$wpdb->get_blog_prefix($shop_id)}usermeta
        WHERE
            WCFMMPO.customer_id = user_id
                AND meta_key = 'shipping_postcode') AS customer_PIN
    FROM
        {$wpdb->get_blog_prefix($shop_id)}wcfm_marketplace_orders AS WCFMMPO
    WHERE
    WCFMMPO.order_status = 'processing'
    ";

    $orders = $wpdb->get_results($sql, 'ARRAY_A');

    foreach ($orders as $order) {
        $row = array(
            $order['vendor_id'],
            $order['vendor_name'],
            $order['order_id'],
            $order['product_id'],
            $order['product_name'],
            $order['payment_method'],
            $order['quantity'],
            $order['created'],
            $order['customer_id'],
            $order['customer_first_name'].''.$order['customer_last_name'],
            $order['customer_address_1'].''.$order['customer_address_2'].''.$order['customer_city'].''.$order['customer_PIN']
        );
        $data_rows[] = $row;
    }

    generate_csv_output($header_row, $data_rows, $filename);
}

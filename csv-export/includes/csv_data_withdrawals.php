<?php


function csv_export_withdrawals()
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
    if (! wp_verify_nonce($nonce, 'download_csv_withdrawals')) {
        die('Security check error');
    }

    $domain = $_SERVER['SERVER_NAME'];
    $filename = 'withdrawals-' . $domain . '-' . time() . '.csv';

    $header_row = array(
        'ID',
        'vendor_id',
        'order_id',
        'customer_id',
        'payment_method',
        'product_id',
        'product_name',
        'quantity',
        'product_price',
        'item_sub_total',
        'item_total',
        'shipping',
        'discount_amount',
        'admin_coupon_id',
        'admin_coupon_type',
        'admin_coupon_value'
    );
    $data_rows = array();
    global $wpdb;

    $sql=
    "SELECT
        WCFMMPO.ID,
        WCFMMPO.vendor_id,
        WCFMMPO.order_id,
        WCFMMPO.customer_id,
        WCFMMPO.payment_method,
        WCFMMPO.product_id,
        (SELECT
                post_title
            FROM
                {$wpdb->get_blog_prefix($shop_id)}posts
            WHERE
                post_type = 'product'
                    AND ID = WCFMMPO.product_id) AS product_name,
        WCFMMPO.variation_id,
        WCFMMPO.quantity,
        WCFMMPO.product_price,
        WCFMMPO.item_sub_total,
        WCFMMPO.item_total,
        WCFMMPO.shipping,
        WCFMMPO.tax,
        WCFMMPO.discount_amount,
        WCOCP.coupon_id as admin_coupon_id,
        (SELECT
                meta_value
            FROM
                {$wpdb->get_blog_prefix($shop_id)}postmeta
            WHERE
                WCOCP.coupon_id = post_id
                    AND meta_key = 'discount_type') AS admin_coupon_type,
        ((SELECT
                meta_value
            FROM
                {$wpdb->get_blog_prefix($shop_id)}postmeta
            WHERE
                WCOCP.coupon_id = post_id
                    AND meta_key = 'coupon_amount')/100) AS admin_coupon_value
    FROM
        {$wpdb->get_blog_prefix($shop_id)}wcfm_marketplace_orders AS WCFMMPO
            JOIN
        (SELECT
            *
        FROM
            {$wpdb->get_blog_prefix($shop_id)}wc_order_coupon_lookup AS WCOC
        JOIN (SELECT
            *
        FROM
            {$wpdb->get_blog_prefix($shop_id)}posts
        WHERE
            post_type = 'shop_coupon'
                AND (post_author = '13' OR post_author = '25'
                OR post_author = '53')) AS WP ON WCOC.coupon_id = WP.ID) AS WCOCP ON WCFMMPO.order_id = WCOCP.order_id
    WHERE
        WCFMMPO.withdraw_status = 'requested'
    ";

    $withdrawals = $wpdb->get_results($sql, 'ARRAY_A');

    foreach ($withdrawals as $withdrawal) {
        $row = array(
            $withdrawal['ID'],
            $withdrawal['vendor_id'],
            $withdrawal['order_id'],
            $withdrawal['customer_id'],
            $withdrawal['payment_method'],
            $withdrawal['product_id'],
            $withdrawal['product_name'],
            $withdrawal['quantity'],
            $withdrawal['product_price'],
            $withdrawal['item_sub_total'],
            $withdrawal['item_total'],
            $withdrawal['shipping'],
            $withdrawal['discount_amount'],
            $withdrawal['admin_coupon_id'],
            $withdrawal['admin_coupon_type'],
            $withdrawal['admin_coupon_value']
            );
        $data_rows[] = $row;
    }

    generate_csv_output($header_row, $data_rows, $filename);
}

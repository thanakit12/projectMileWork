<?php

use Tygh\Registry;

function fn_export_abandoned_carts_timestamp_to_date($timestamp)
{
    return !empty($timestamp) ? date('d/m/Y ', intval($timestamp)) : '';
}

function fn_export_abandoned_carts_getBoxSize($product_id)
{

    $shipping_params = db_get_field('SELECT shipping_params FROM ?:products WHERE product_id = ?i', $product_id);

    if (!empty($shipping_params)) {
        $shipping_params = unserialize($shipping_params);
        return 'length:' . (empty($shipping_params['box_length']) ? 0 : $shipping_params['box_length']) . ';width:' . (empty($shipping_params['box_width']) ? 0 : $shipping_params['box_width']) . ';height:' . (empty($shipping_params['box_height']) ? 0 : $shipping_params['box_height']);
    }

    return 'length:0;width:0;height:0';

}


function fn_export_abandoned_carts_GetVendor($product_id, $lang_code = 'th')
{
    $sql = db_get_row("SELECT ?:companies.company FROM ?:companies INNER JOIN ?:products ON ?:products.company_id = ?:companies.company_id
                        WHERE  ?:products.product_id = ?i AND  ?:companies.lang_code = ?s", $product_id, $lang_code);

    return !empty($sql) ? $sql['company'] : '';

}



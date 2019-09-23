<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
//
function fn_shipping_pro_cate_addon_shippings_get_shippings_list($group, $shippings, $condition)
{
    fn_print_r("THIS IS GET SHIPPING LIST METHOD");
//    fn_print_r($shippings);
}

function fn_shipping_pro_cate_addon_shippings_group_products_list($products, $groups)
{
    fn_print_r("THIS IS GROUP PRODUCT LIST");
    fn_print_r($groups);
//    fn_print_r($products);
//    fn_print_r($groups);
}

//

function fn_shipping_pro_cate_addon_shippings_get_shippings_list_post($group, $lang, $area, $shippings_info)
{
    fn_print_r("click to cart this funcion work");
    fn_print_r($shippings_info);
}

function fn_shipping_pro_cate_addon_update_product_pre(&$product_data, $product_id, $lang_code, $can_update)
{
    if (!empty($product_data['shippings'])) {
        if (!empty($product_data['shippings']['defaults']) && $product_data['shippings']['defaults'] == "Y") {
            $product_data['shipping_ids'] = 0;
        } else {
            $product_data['shipping_ids'] = implode(',', $product_data['shippings']);
        }
    }
}


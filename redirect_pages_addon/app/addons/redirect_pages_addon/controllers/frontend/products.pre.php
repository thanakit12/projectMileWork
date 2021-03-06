<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if($mode == "view") {

    // Variable name should be relevant and consistent. For example: $pro_id should be $product_id
    $product_id = $_REQUEST["product_id"];
    $is_exists = fn_product_exists($product_id);
    if($is_exists)
    {
        $is_disable = fn_redirect_pages_addon_is_disable($product_id);
        if($is_disable) {
            $main_category_id = fn_redirect_pages_addon_get_main_category_id($product_id);
            fn_redirect("categories.view?category_id=".$main_category_id);
        }
    }
}
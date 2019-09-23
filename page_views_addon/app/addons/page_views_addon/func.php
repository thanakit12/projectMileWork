<?php

function fn_page_views_addon_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    $product_id = $product_data['product_id'];
    $query = db_get_row("SELECT viewed,bought FROM cscart_product_popularity WHERE product_id = ?i", $product_id);
    $viewed = $query['viewed'];
    $bought = $query['bought'];
    $product_data['viewed'] = $viewed;
    $product_data['bought'] = $bought;
}
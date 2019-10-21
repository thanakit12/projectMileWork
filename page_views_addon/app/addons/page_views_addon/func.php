<?php

use Tygh\Registry;

function fn_page_views_addon_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    $product_id = $product_data['product_id'];
    $query = db_get_row("SELECT viewed,bought,added,deleted FROM cscart_product_popularity WHERE product_id = ?i", $product_id);
    if (!empty($query)) {
        $viewed = $query['viewed'];
        $bought = $query['bought'];
        $add_to_cart = $query['added'];
        $deleted = $query['deleted'];

        $product_data['viewed'] = $viewed;
        $product_data['bought'] = $bought;
        $product_data['added_to_cart'] = $add_to_cart;
        $product_data['deleted_from_cart'] = $deleted;
    }
}

function fn_page_views_addon_gather_additional_product_data_post(&$product_ids, $params, $products)
{
    $product_id = $product_ids['product_id'];
    $query = db_get_row("SELECT viewed,bought,added,deleted FROM cscart_product_popularity WHERE product_id = ?i", $product_id);
    if (!empty($query)) {
        $viewed = $query['viewed'];
        $bought = $query['bought'];
        $add_to_cart = $query['added'];
        $deleted_from_cart = $query['deleted'];

        $product_ids['viewed'] = number_format($viewed);
        $product_ids['bought'] = number_format($bought);
        $product_ids['added_to_cart'] = number_format($add_to_cart);
        $product_ids['deleted_from_cart'] = number_format($deleted_from_cart);
    }
}

function fn_page_views_addon_GetSumByCompanies($company_id)
{
//Admin
    if (!Registry::get('runtime.company_id')) {
        $query = db_get_row("SELECT SUM(viewed) as view,SUM(added) as add_to_cart,SUM(deleted) as deleted, SUM(bought) as bought FROM `?:product_popularity`
                         LEFT JOIN ?:products
                         ON
                         ?:products.product_id = ?:product_popularity.product_id");
    } //Vendor
    else {
        $query = db_get_row("SELECT SUM(viewed) as view,SUM(added) as add_to_cart,SUM(deleted) as deleted, SUM(bought) as bought FROM `?:product_popularity`
                             LEFT JOIN ?:products
                             ON
                             ?:products.product_id = ?:product_popularity.product_id
                             WHERE
                             ?:products.company_id = ?i", $company_id);
    }
    $query['view'] = number_format($query['view']);
    $query['add_to_cart'] = number_format($query['add_to_cart']);
    $query['deleted'] = number_format($query['deleted']);
    $query['bought'] = number_format($query['bought']);

    return !empty($query) ? $query : '';
}
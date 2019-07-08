<?php

if ( !defined('AREA') ) { die('Access denied'); }
// Overall
// =======
// 1. Please change method to query data from database. Think about this criteria and refer to functions provide in fn.database.php file.
//    - Use placeholder, not string substitution for variables.
//    - Use appropriate function for expecpted result. For example: result return one field, one row or multiple rows
function fn_redirect_pages_addon_is_disable($product_id)
{
    $query = db_get_array("select status from ?:products where product_id = '$product_id'");
    if($query[0]["status"] == "D") {
        return true;
    }
    else
        return false;
}
function fn_redirect_pages_addon_get_main_category_id($product_id)
{
    $query = db_get_array("SELECT link_type,category_id FROM  ?:products_categories WHERE ?:products_categories.product_id = $product_id");

    // Why should we have 'for loop' for result that guarantee one row?
    for($i = 0 ; $i < count($query) ; $i++)
    {
        if($query[$i]["link_type"] == 'M')
            return $query[$i]["category_id"];
    }
}
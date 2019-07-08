<?php

if ( !defined('AREA') ) { die('Access denied'); }
// Overall
// =======
// 1. Please change method to query data from database. Think about this criteria and refer to functions provide in fn.database.php file.
//    - Use placeholder, not string substitution for variables.
//    - Use appropriate function for expecpted result. For example: result return one field, one row or multiple rows
function fn_redirect_pages_addon_is_disable($product_id)
{
    $query = db_get_row("select status from ?:products where product_id = ?i",$product_id);
    return ($query["status"] == "D") ? true : false;
}
function fn_redirect_pages_addon_get_main_category_id($product_id)
{
    $query = db_get_row("SELECT link_type,category_id FROM  ?:products_categories WHERE ?:products_categories.product_id = ?i 
                            and link_type = 'M'",$product_id);
    return $query["category_id"];
}
<?php
if ( !defined('AREA') ) { die('Access denied'); }


function fn_order_export_get_product_name($item_id)
{
    $query = db_get_row("SELECT product FROM ?:order_details
                        inner JOIN
                        ?:product_descriptions
                        ON
                        ?:order_details.product_id = ?:product_descriptions.product_id
                        WHERE ?:order_details.item_id in (?n)
                        and ?:product_descriptions.lang_code = 'th'",$item_id);
   return $query['product'];
}
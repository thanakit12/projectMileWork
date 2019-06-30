<?php

if ( !defined('AREA') ) { die('Access denied'); }

function fn_phone_field_addon_get_orders_post($params, &$orders)
{

$add_field = db_get_array("SELECT * from ?:profile_fields_data");
foreach ($orders as &$order)
{
    if(isset($order["phone"]))
    {
        $order["s_phone_one"] = $order["phone"];
    }
}
 foreach ($orders as &$order)
 {
    foreach ($add_field as $add)
    {
        if ($order["order_id"] == $add["object_id"])
        {
            if($add["field_id"] == "57")
                $order["s_phone_one"] = $add["value"];
           else if($add["field_id"] == "59")
                $order["s_phone_two"] = $add["value"];
        }
    }
 }
}
?>
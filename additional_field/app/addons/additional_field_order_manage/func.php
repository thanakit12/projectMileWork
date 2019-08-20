<?php

if (!defined('AREA')) {
    die('Access denied');
}

function fn_additional_field_order_manage_getGroup($user_id)
{
    $query = db_get_row("SELECT usergroup FROM ?:usergroup_links
                         INNER JOIN
                         ?:users ON
                         ?:usergroup_links.user_id = ?:users.user_id
                         INNER JOIN
                         ?:usergroup_descriptions
                         ON
                         ?:usergroup_descriptions.usergroup_id = ?:usergroup_links.usergroup_id
                         WHERE
                         ?:users.user_id = ?i
                         AND
                         ?:usergroup_descriptions.lang_code = 'th'
                         AND
                         ?:usergroup_links.status = 'A'", $user_id);
    return $query['usergroup'];
}

//This function assign usergroup  and new phone field data to order page
function fn_additional_field_order_manage_get_orders_post($params, &$orders)
{
    foreach ($orders as &$o) {
        $user_id = $o['user_id'];
        $order_id = $o['order_id'];
        $group_name = fn_additional_field_order_manage_get_groupname($user_id);

        if (!empty($group_name)) {
            $o['usergroup'] = $group_name['usergroup'];
        }
        $new_phone = fn_additional_field_order_manage_get_newPhone($order_id);
        if (!empty($new_phone) > 0) {
            $o['phone'] = $new_phone['value'];
        }
    }
}

function
fn_additional_field_order_manage_get_newPhone($order_id)
{
    $query = db_get_row("SELECT value FROM ?:profile_fields_data
                         INNER JOIN
                         ?:profile_fields
                         ON
                         ?:profile_fields_data.field_id = ?:profile_fields.field_id
                         WHERE
                         object_id = ?i
                         AND
                         ?:profile_fields.section = 'S'", $order_id);
    return !empty($query) ? $query : '';
}

function fn_additional_field_order_manage_get_groupname($user_id)
{
    $query = db_get_row("SELECT usergroup FROM ?:usergroup_links
                         INNER JOIN
                         ?:users ON
                         ?:usergroup_links.user_id = ?:users.user_id
                         INNER JOIN
                         ?:usergroup_descriptions
                         ON
                         ?:usergroup_descriptions.usergroup_id = ?:usergroup_links.usergroup_id
                         WHERE
                         ?:users.user_id = ?i
                         AND
                         ?:usergroup_descriptions.lang_code = 'th'
                         AND
                         ?:usergroup_links.status = 'A'", $user_id);
    return !empty($query) ? $query : '';
}

function fn_additional_field_order_manage_getGroupByOrder($order_id)
{
    $query = db_get_row("SELECT ?:usergroup_descriptions.usergroup FROM ?:orders
                         LEFT JOIN
                         ?:usergroup_links
                         ON
                         ?:orders.user_id = ?:usergroup_links.user_id
                         LEFT JOIN
                         ?:usergroup_descriptions
                         ON
                         ?:usergroup_links.usergroup_id = ?:usergroup_descriptions.usergroup_id
                         WHERE
                         order_id = ?i 
                         AND
                         ?:usergroup_links.status = 'A'
                         AND
                         ?:usergroup_descriptions.lang_code = 'th'", $order_id);
    return $query['usergroup'];
}

function fn_additional_field_order_manage_exportPhone($order_id)
{
    $order = fn_get_order_info($order_id);
    $result = fn_additional_field_order_manage_get_newPhone($order_id);
    if (!empty($result)) {
        $response = $result['value'];
    } else {
        $response = $order['phone'];
    }
    return $response;
}


function fn_additional_field_order_manage_importPhone($data,$order_id)
{
//    $order = fn_get_order_info($order_id);
//    if (!isset($order['phone'])) {
//        db_query("UPDATE ?:profile_fields_data SET value = '$data' WHERE object_id = ?i",$order_id);
//    } else {
//        db_query("UPDATE ?:orders SET phone = '$data' where order_id = ?i",$order_id);
//    }

}

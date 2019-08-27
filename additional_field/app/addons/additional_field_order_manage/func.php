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
        $new_phone = fn_additional_field_order_manage_get_PhoneFormatted($order_id);
        if (!empty($new_phone) > 0) {
            $o['phone'] = $new_phone['value'];
        }
    }
}

// This Function return Shipping phone formatted,phone formatted form is xxx-xxx-xxxx.
function fn_additional_field_order_manage_get_PhoneFormatted($order_id)
{
    $query = db_get_row("SELECT value FROM ?:profile_fields_data
                         INNER JOIN
                         ?:profile_fields
                         ON
                         ?:profile_fields_data.field_id = ?:profile_fields.field_id
                         WHERE
                         object_id = ?i
                         AND
                         ?:profile_fields.section = 'S'
                         AND ?:profile_fields.class = 'shipping-new-phone'", $order_id);
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

function fn_additional_field_order_manage_exportPhoneFormatted($order_id, $type,$class)
{
    $query = db_get_row("select value FROM ?:profile_fields_data
                         INNER JOIN
                         ?:profile_fields
                         ON
                         ?:profile_fields_data.field_id = ?:profile_fields.field_id
                         WHERE
                         object_id = ?i
                         AND
                         ?:profile_fields.section = ?s
                         AND 
                         ?:profile_fields.class = ?s", $order_id, $type,$class);
    return $query['value'];
}


if (!function_exists('fn_additional_field_order_manage_clean_data')) {
    function fn_additional_field_order_manage_clean_data()
    {
        $html = '';
        $html .= '<button type="submit" name="dispatch[additional_field_order_manage.clean]">click to clean data here</button>';
        return $html;
    }
}

if (!function_exists('fn_additional_field_order_manage_clean_phone_format')) {
    function fn_additional_field_order_manage_clean_phone_format()
    {
        $html = '';
        $html .= '<br/><button type="submit" name="dispatch[additional_field_order_manage.clean_phone]">click to clean phone format</button>';
        return $html;
    }
}

function fn_additional_field_order_manage_exportReservePhone($order_id,$type,$class)
{
    $query = db_get_row("select value FROM ?:profile_fields_data
                         INNER JOIN
                         ?:profile_fields
                         ON
                         ?:profile_fields_data.field_id = ?:profile_fields.field_id
                         WHERE
                         object_id = ?i
                         AND
                         ?:profile_fields.section = ?s
                         AND 
                         ?:profile_fields.class = ?s", $order_id, $type,$class);
    return $query['value'];
}

function fn_formatPhoneNumber($phoneNumber)
{
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    if (strlen($phoneNumber) > 10) {
        $phoneNumber = 'xxx-xxx-xxxx';
    } else if (strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '' . $areaCode . '-' . $nextThree . '-' . $lastFour;
    } else if (strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);
        $phoneNumber = $nextThree . '-' . $lastFour;
    } else {
        $phoneNumber = '';
    }
    return $phoneNumber;
}

function fn_additional_field_order_manage_importPhoneFormatted($data,$order_id,$type)
{
    $field_id = '';
    if($type == 'S')
    {
        $field_id = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'shipping-new-phone'");
    }
    else if ($type == 'B')
    {
        $field_id = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'billing-new-phone'");
    }
    $f_id = $field_id['field_id'];
    db_query("UPDATE 
            ?:profile_fields_data 
            SET `value` = ?s 
            WHERE `?:profile_fields_data`.`object_id` = ?i 
            AND `?:profile_fields_data`.`object_type` = 'O' 
            AND `?:profile_fields_data`.`field_id` = ?i",$data,$order_id,$f_id);
}
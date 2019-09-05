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

// This Function return Shipping phone formatted,phone formatted form is 000-000-0000.
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
                         AND ?:profile_fields.field_name = 's_formatted_phone'", $order_id);
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

function fn_additional_field_order_manage_exportPhoneAll($order_id, $type, $field_name)
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
                         ?:profile_fields.field_name = ?s", $order_id, $type, $field_name);
    return  !empty($query['value']) ? $query['value'] : '';
}


if (!function_exists('fn_additional_field_order_manage_clean_data')) {
    function fn_additional_field_order_manage_clean_data()
    {
        $html = '';
        $html .= "<br/><h5>ถ้าคุณต้องการที่จะ ล้างข้อมูล ของ ผู้ใช้งาน หลังจากติดตั้ง ให้กดปุ่มข้างล่างนี้ คือ</h5><br/>";
        $html .= "<h5>ล้างข้อมูลของผู้ใช้งาน คือ copy ข่้อมูลจาก ฟิลด์เก่า ซึ่ง ฟิลด์เก่า อยู่ในรูปแบบ ที่ ไม่เป็น format ที่ถูกต้อง ย้ายไปยัง ฟิลด์ใหม่ นั่นคือ phone-formatted(xxx-xxx-xxxx)</h5><br/>";
        $html .= '<center><button type="submit" name="dispatch[additional_field_order_manage.clean]">click to clean data here</button></center><br/>';
        $html .= "<p style='color: red'>***ควร กด เพียง ครั้ง เดียว หลังจากที่ติดตั้งไปแล้ว ถ้ากดปุ่มนี้ แล้วข้อมูล ใน ฟิลด์ โทรศัพท์ ใหม่ ในหน้ารายละเอียดคำสั่งซื้อจะหายทั้งหมด</p>";
        return $html;
    }
}

if (!function_exists('fn_additional_field_order_manage_clean_phone_format')) {
    function fn_additional_field_order_manage_clean_phone_format()
    {
        $html = '';
        $html .= "<br/><h5>ถ้าคุณต้องการที่จะ ปรับ รูปแบบ ของ โทรศัพท์ ให้อยู่ในรูปที่ถูกต้อง(xxx-xxx-xxxx) กดปุ่มนี้</h5><br/>";
        $html .= "<h5>ปรับรูปแบบ ของ โทรศัพท์ ที่อยู่ ในหน้า คำสั่งซื้อ ให้อยู่ในรูปแบบที่ถูกต้อง</h5><br/>";
        $html .= '<br/><center><button type="submit" name="dispatch[additional_field_order_manage.clean_phone]">click to clean phone format</button></center><br/>';
        $html .= "<p style='color: red'>***กด เพียง ครั้ง เดียว หลังจากที่ติดตั้งไปแล้ว ถ้าติดตั้งไปแล้วไม่ต้องกดปุ่มนี้ เพราะ ถ้ากดปุ่มนี้ แล้วข้อมูล ใน ฟิลด์ โทรศัพท์ ใหม่ ในหน้าคำสั่งซื้อจะหายทั้งหมด****</p>";
        return $html;
    }
}

function fn_formatPhoneNumber($phoneNumber)
{
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    //if user has phone number  more than one.
    if (strlen($phoneNumber) > 11) {
        $phoneNumber = '000-000-0000';
    } //if phone number format +6682-565-5555
    else if (strlen($phoneNumber) == 11) {
        $nexttwo = substr($phoneNumber, 2, 2);
        $nextThree = substr($phoneNumber, 4, 3);
        $lastFour = substr($phoneNumber, 7, 4);

        $phoneNumber = '0' . $nexttwo . '-' . $nextThree . '-' . $lastFour;
    } else if (strlen($phoneNumber) == 10) {
        $check_countrycode_exist = substr($phoneNumber, 0, 2);
        //check if format phone is +662-555-5555 this is example
        if ($check_countrycode_exist == '66') {
            $areaCode = substr($phoneNumber, 2, 1);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '0' . $areaCode . '-' . $nextThree . '-' . $lastFour;
        } else {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '' . $areaCode . '-' . $nextThree . '-' . $lastFour;
        }
    } else if (strlen($phoneNumber) == 9) {
        $nexttwo = substr($phoneNumber, 0, 2);
        $nextThree = substr($phoneNumber, 2, 3);
        $lastFour = substr($phoneNumber, 5, 4);
        $phoneNumber = $nexttwo . '-' . $nextThree . '-' . $lastFour;
    } //if user has phone number format is text or other doesn't correct phone format.
    else {
        $phoneNumber = '';
    }
    return $phoneNumber;
}

//This Function import phone format, reserve_phone
function fn_additional_field_order_manage_importPhoneAll($data, $order_id, $type, $field_name)
{
    $field_id = '';
    if ($type == 'S') {
        $field_id = db_get_row("SELECT field_id FROM ?:profile_fields where ?:profile_fields.field_name = ?s", $field_name);
    } else if ($type == 'B') {
        $field_id = db_get_row("SELECT field_id FROM ?:profile_fields where ?:profile_fields.field_name = ?s", $field_name);
    }
    $f_id = $field_id['field_id'];
    $phone_all = array(
        'object_id' => $order_id,
        'object_type' => 'O',
        'field_id' => $f_id,
        'value' => $data
    );
    db_query("REPLACE INTO ?:profile_fields_data ?e",$phone_all);
}

function fn_additional_field_order_manage_cleanuser_data()
{
    db_query("DELETE FROM ?:profile_fields_data where object_type = 'P' || object_type = 'S'");
    $query = db_get_array("select profile_id from ?:user_profiles");

    $query_field_two = db_get_row("SELECT field_id FROM ?:profile_fields where field_name = 's_formatted_phone'");
    $field_shipping_formatted_phone = $query_field_two['field_id'];
    //billing-phone
    $query_field_three = db_get_row("SELECT field_id FROM ?:profile_fields where field_name = 'b_formatted_phone'");
    $field_billing_formatted_phone = $query_field_three['field_id'];


    for ($i = 0; $i < count($query); $i++) {

        $profile_id = $query[$i]['profile_id'];

        $query_ship_phone_second = db_get_row("select s_phone from ?:user_profiles where profile_id = ?i and profile_type = 'S'", $profile_id);

        $query_bill_phone_second = db_get_row("select b_phone from ?:user_profiles where profile_id = ?i and profile_type = 'S'", $profile_id);

        $query_ship_phone_main = db_get_row("select s_phone from ?:user_profiles where profile_id = ?i and profile_type = 'P'", $profile_id);

        $query_bill_phone_main = db_get_row("select b_phone from ?:user_profiles where profile_id = ?i and profile_type = 'P'", $profile_id);

        //check user have other profile
        if (!empty($query_ship_phone_second) || !empty($query_bill_phone_second)) {

            $ship_phone_second = $query_ship_phone_second['s_phone'];
            $bill_phone_second = $query_bill_phone_second['b_phone'];

            $ship_phone_second = fn_formatPhoneNumber($ship_phone_second);
            $bill_phone_second = fn_formatPhoneNumber($bill_phone_second);

            db_query("INSERT INTO `?:profile_fields_data` (`object_id`, `object_type`, `field_id`, `value`)
               values ('$profile_id','P','$field_shipping_formatted_phone','$ship_phone_second'),
                      ('$profile_id','P','$field_billing_formatted_phone','$bill_phone_second')");
        }
        if (!empty($query_ship_phone_main) || !empty($query_bill_phone_main)) {
            $shipping_phone_main = $query_ship_phone_main['s_phone'];
            $billing_phone_main = $query_bill_phone_main['b_phone'];

            $shipping_phone_main_check = fn_formatPhoneNumber($shipping_phone_main);
            $billing_phone_main_check = fn_formatPhoneNumber($billing_phone_main);

            //if format = '000-000-00000' user have phone number more than one
            if ($shipping_phone_main_check == '000-000-0000' || $billing_phone_main_check == '000-000-0000') {
                db_query("INSERT INTO `?:profile_fields_data` (`object_id`, `object_type`, `field_id`, `value`)
               values ('$profile_id','P','$field_shipping_formatted_phone','$shipping_phone_main'),
                      ('$profile_id','P','$field_billing_formatted_phone','$billing_phone_main')");
            } else {
                db_query("INSERT INTO `?:profile_fields_data` (`object_id`, `object_type`, `field_id`, `value`)
               values ('$profile_id','P','$field_shipping_formatted_phone','$shipping_phone_main_check'),
                      ('$profile_id','P','$field_billing_formatted_phone','$billing_phone_main_check')");
            }
        }
    }
}

function fn_additional_field_order_manage_cleanphone_inorder()
{
    db_query("DELETE from ?:profile_fields_data where object_type = 'O'");
    $order_all = db_get_array("select order_id from ?:orders");

    //phone
    $query_field = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'phone'");
    $field_phone = $query_field['field_id'];

    //shipping-phone
    $query_field_two = db_get_row("SELECT field_id FROM ?:profile_fields where field_name = 's_formatted_phone'");
    $field_shipping_formatted_phone = $query_field_two['field_id'];

    //billing-phone
    $query_field_three = db_get_row("SELECT field_id FROM ?:profile_fields where field_name = 'b_formatted_phone'");
    $field_billing_formatted_phone = $query_field_three['field_id'];


    for ($i = 0; $i < count($order_all); $i++) {
        $order_id = $order_all[$i]['order_id'];
        $query_phone = db_get_row("select phone from ?:orders where order_id = ?i", $order_id);
        $phone = $query_phone['phone'];

        $query_ship_phone = db_get_row("select s_phone from ?:orders where order_id = ?i", $order_id);
        $ship_phone = $query_ship_phone['s_phone'];

        $query_bill = db_get_row("select b_phone from ?:orders where order_id = ?i", $order_id);
        $bill_phone = $query_bill['b_phone'];

        $phone = fn_formatPhoneNumber($phone);
        $ship_phone = fn_formatPhoneNumber($ship_phone);
        $bill_phone = fn_formatPhoneNumber($bill_phone);

        db_query("INSERT INTO `?:profile_fields_data` (`object_id`, `object_type`, `field_id`, `value`)
               values ('$order_id','O','$field_phone','$phone'),
                      ('$order_id','O','$field_shipping_formatted_phone','$ship_phone'),
                      ('$order_id','O','$field_billing_formatted_phone','$bill_phone')");
    }
}

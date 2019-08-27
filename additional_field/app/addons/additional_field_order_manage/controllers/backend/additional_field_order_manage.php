<?php
if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($mode == 'clean') {
    //This Action move user profile to profile_fields_data in database. Then this action clean user_profile phone incorrect format
    //to formatted phone show in order.detail and user.details
    db_query("DELETE FROM ?:profile_fields_data where object_type = 'P' || object_type = 'S'");
    $query = db_get_array("select profile_id from ?:user_profiles");

    $query_field_two = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'shipping-new-phone'");
    $field_shipping_phone = $query_field_two['field_id'];
    //billing-phone
    $query_field_three = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'billing-new-phone'");
    $field_billing = $query_field_three['field_id'];

    for ($i = 0; $i < count($query); $i++) {

        $profile_id = $query[$i]['profile_id'];

        $query_ship_phone_second = db_get_row("select s_phone from ?:user_profiles where profile_id = ?i and profile_type = 'S'", $profile_id);

        $query_bill_phone_second = db_get_row("select b_phone from ?:user_profiles where profile_id = ?i and profile_type = 'S'", $profile_id);

        $query_ship_phone_main = db_get_row("select s_phone from ?:user_profiles where profile_id = ?i and profile_type = 'P'", $profile_id);

        $query_bill_phone_main = db_get_row("select b_phone from ?:user_profiles where profile_id = ?i and profile_type = 'P'", $profile_id);

        if (!empty($query_ship_phone_second) || !empty($query_bill_phone_second)) {

            $ship_phone_second = $query_ship_phone_second['s_phone'];
            $bill_phone_second = $query_bill_phone_second['b_phone'];

            $ship_phone_second = fn_formatPhoneNumber($ship_phone_second);
            $bill_phone_second = fn_formatPhoneNumber($bill_phone_second);

            db_query("INSERT INTO `?:profile_fields_data` (`object_id`, `object_type`, `field_id`, `value`)
               values ('$profile_id','P','$field_shipping_phone','$ship_phone_second'),
                      ('$profile_id','P','$field_billing','$bill_phone_second')");

        }
        if (!empty($query_ship_phone_main) || !empty($query_bill_phone_main)) {
            $shipping_phone_main = $query_ship_phone_main['s_phone'];
            $billing_phone_main = $query_bill_phone_main['b_phone'];

            $shipping_phone_main_check = fn_formatPhoneNumber($shipping_phone_main);
            $billing_phone_main_check = fn_formatPhoneNumber($billing_phone_main);

            if ($shipping_phone_main_check == 'xxx-xxx-xxxx' || $billing_phone_main_check == 'xxx-xxx-xxxx') {
                db_query("INSERT INTO `?:profile_fields_data` (`object_id`, `object_type`, `field_id`, `value`)
               values ('$profile_id','P','$field_shipping_phone','$shipping_phone_main'),
                      ('$profile_id','P','$field_billing','$billing_phone_main')");
            } else {
                db_query("INSERT INTO `?:profile_fields_data` (`object_id`, `object_type`, `field_id`, `value`)
               values ('$profile_id','P','$field_shipping_phone','$shipping_phone_main_check'),
                      ('$profile_id','P','$field_billing','$billing_phone_main_check')");
            }
        }
    }
    fn_set_notification("N", "Clean", "Data is Finished");

} else if ($mode == 'clean_phone') {
    //This Action clean format phone incorrect to formatted phone(xxx-xxx-xxxx) in order.manage page
    db_query("DELETE from ?:profile_fields_data where object_type = 'O'");
    $order_all = db_get_array("select order_id from ?:orders");

    //phone
    $query_field = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'phone'");
    $field_phone = $query_field['field_id'];

    //shipping-phone
    $query_field_two = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'shipping-new-phone'");
    $field_shipping_phone = $query_field_two['field_id'];

    //billing-phone
    $query_field_three = db_get_row("SELECT field_id FROM ?:profile_fields where class = 'billing-new-phone'");
    $field_billing = $query_field_three['field_id'];


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
                      ('$order_id','O','$field_shipping_phone','$ship_phone'),
                      ('$order_id','O','$field_billing','$bill_phone')");
    }
    fn_set_notification("N", "Clean", "Phone is Finished");
}




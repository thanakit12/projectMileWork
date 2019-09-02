<?php
if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($mode == 'clean') {
    //This Action move user profile to profile_fields_data in database. Then this action clean user_profile phone incorrect format
    //to formatted phone show in order.detail and user.details
    try {
        fn_additional_field_order_manage_cleanuser_data();
        fn_set_notification("N", "Clean", "Data is Finished");
    } catch (Exception $e) {
        fn_print_r($e->getMessage());
    }
} else if ($mode == 'clean_phone') {
    //This Action clean format phone incorrect to formatted phone(xxx-xxx-xxxx) in order.manage page
    try {
        fn_additional_field_order_manage_cleanphone_inorder();
        fn_set_notification("N", "Clean", "Phone is Finished");
    } catch (Exception $e) {
        fn_print_r($e->getMessage());
    }
}




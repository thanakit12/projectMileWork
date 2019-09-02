<?php

use Tygh\Registry;

include_once(Registry::get('config.dir.addons') . 'additional_field_order_manage/func.php');


$schema['export_fields']['User Group'] = array(
    'db_field' => 'user_id',
    'process_get' => array('fn_additional_field_order_manage_getGroupByOrder', '#key'),
    'export_only' => true,
    'linked' => false,
);


$schema['export_fields']['Shipping: phone'] = array(
    'db_field' => 's_phone',
    'process_get' => array('fn_additional_field_order_manage_exportPhoneAll', '#key', 'S','s_formatted_phone'),
    'process_put' => array('fn_additional_field_order_manage_importPhoneAll', '#this', '#key', 'S','s_formatted_phone'),
    'required' => true,
);

$schema['export_fields']['Billing: phone'] = array(
    'db_field' => 'b_phone',
    'process_get' => array('fn_additional_field_order_manage_exportPhoneAll', '#key', 'B','b_formatted_phone'),
    'process_put' => array('fn_additional_field_order_manage_importPhoneAll', '#this', '#key', 'B','b_formatted_phone'),
    'required' => true,
);

$schema['export_fields']['Shipping: reserve_phone'] = array(
    'linked' => false,
    'process_get' => array('fn_additional_field_order_manage_exportPhoneAll', '#key', 'S','s_reserve_phone'),
    'process_put' => array('fn_additional_field_order_manage_importPhoneAll','#this','#key','S','s_reserve_phone'),
);

$schema['export_fields']['Billing: reserve_phone'] = array(
    'linked' => false,
    'process_get' => array('fn_additional_field_order_manage_exportPhoneAll', '#key', 'B','b_reserve_phone'),
     'process_put' => array('fn_additional_field_order_manage_importPhoneAll','#this','#key','B','b_reserve_phone'),

);

return $schema;
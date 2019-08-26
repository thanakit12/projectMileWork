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
    'process_get' => array('fn_additional_field_order_manage_exportPhone', '#key', 'S'),
    'process_put' => array('fn_additional_field_order_manage_importPhone', '#this', '#key', 'S'),
    'required' => true,
);

$schema['export_fields']['Billing: phone'] = array(
    'db_field' => 'b_phone',
    'process_get' => array('fn_additional_field_order_manage_exportPhone', '#key', 'B'),
    'process_put' => array('fn_additional_field_order_manage_importPhone', '#this', '#key', 'B'),
    'required' => true,
);

$schema['export_fields']['Shipping: phone_second'] = array(
    'db_field' => 'order_id',
    'process_get' => array('fn_additional_field_order_manage_exportPhoneSecond', '#key', 'S'),
    'export_only' => true,
    'linked' => false,
);

$schema['export_fields']['Billing: phone_second'] = array(
    'db_field' => 'order_id',
    'process_get' => array('fn_additional_field_order_manage_exportPhoneSecond', '#key', 'B'),
    'export_only' => true,
    'linked' => false,
);

return $schema;
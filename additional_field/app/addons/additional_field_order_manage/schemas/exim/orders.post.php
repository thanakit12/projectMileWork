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
    'process_get' => array('fn_additional_field_order_manage_exportPhoneFormatted', '#key', 'S','shipping-new-phone'),
    'process_put' => array('fn_additional_field_order_manage_importPhoneFormatted', '#this', '#key', 'S'),
    'required' => true,
);

$schema['export_fields']['Billing: phone'] = array(
    'db_field' => 'b_phone',
    'process_get' => array('fn_additional_field_order_manage_exportPhoneFormatted', '#key', 'B','billing-new-phone'),
    'process_put' => array('fn_additional_field_order_manage_importPhoneFormatted', '#this', '#key', 'B'),
    'required' => true,
);

$schema['export_fields']['Shipping: reserve_phone'] = array(
    'db_field' => 'order_id',
    'process_get' => array('fn_additional_field_order_manage_exportReservePhone', '#key', 'S','shipping-phone-two'),
    'export_only' => true,
    'linked' => false,
);

$schema['export_fields']['Billing: reserve_phone'] = array(
    'db_field' => 'order_id',
    'process_get' => array('fn_additional_field_order_manage_exportReservePhone', '#key', 'B','billing-phone-two'),
    'export_only' => true,
    'linked' => false,
);

return $schema;
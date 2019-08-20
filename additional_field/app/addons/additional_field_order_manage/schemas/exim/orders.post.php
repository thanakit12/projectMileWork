<?php

use Tygh\Registry;

include_once(Registry::get('config.dir.addons') . 'additional_field_order_manage/func.php');


$schema['export_fields']['User Group'] = array(
    'db_field' => 'user_id',
    'process_get' => array('fn_additional_field_order_manage_getGroupByOrder', '#key'),
    'export_only' => true,
    'linked' => false,
);

$schema['export_fields']['Phone'] = array(
    'process_get' => array('fn_additional_field_order_manage_exportPhone', '#key'),
    'process_put' => array('fn_additional_field_order_manage_importPhone', '#this', '#key'),
);

return $schema;
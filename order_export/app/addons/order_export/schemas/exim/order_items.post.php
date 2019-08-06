<?php

use Tygh\Registry;

include_once(Registry::get('config.dir.addons') . 'order_export/func.php');

$schema['export_fields']['Product Name'] = array(
    'process_get' => array('fn_order_export_get_product_name', '#key'),
    'linked' => false,
    'export_only' => true,
);

$schema['export_fields']['First name'] = array(
    'db_field' => 'firstname',
    'table' => 'orders',
);

$schema['export_fields']['Last name'] = array(
    'db_field' => 'lastname',
    'table' => 'orders',
);

$schema['export_fields']['Date'] = array(
    'db_field' => 'timestamp',
    'table' => 'orders',
    'process_get' => array('fn_order_export_timestamp_to_date', '#this'),
//        'convert_put' => array('fn_date_to_timestamp', '#this'),
);

$schema['export_fields']['Status'] = array(
    'db_field' => 'order_id',
    'table' => 'orders',
    'process_get' => array('fn_order_export_get_status_desc', '#this'),
);

$schema['export_fields']['Finish Date'] = array(
    'db_field' => 'order_id',
    'table' => 'orders',
    'process_get' => array('fn_order_export_order_history', '#this'),
);

$schema['export_fields']['Shipment'] = array(
    'db_field' => 'order_id',
    'table' => 'orders',
    'process_get' => array('fn_order_export_shipment_method', '#this'),
);

$schema['export_fields']['User ID'] = array(
    'db_field' => 'user_id',
    'table' => 'orders'
);

return $schema;


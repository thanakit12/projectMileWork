<?php

use Tygh\Registry;

$schema['export_fields']['Finish Date'] = array(
    'db_field' => 'order_id',
    'process_get' => array('fn_order_export_order_history', '#this'),
);

$schema['export_fields']['Shipment'] = array(
    'db_field' => 'order_id',
    'process_get' => array('fn_order_export_shipment_method', '#this'),
);

$schema['export_fields']['Status'] = array(
    'db_field' => 'order_id',
    'process_get' => array('fn_order_export_get_status_desc', '#this'),
);



return $schema;
<?php

use Tygh\Registry;

include_once(Registry::get('config.dir.addons') . 'order_export/func.php');
include_once(Registry::get('config.dir.schemas') . 'exim/products.functions.php');
include_once(Registry::get('config.dir.schemas') . 'exim/orders.functions.php');

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

$schema['export_fields']['Category'] = array(
    'db_field' => 'product_id',
    'process_get' => array('fn_exim_get_product_categories', '#this', 'M', '///', '#lang_code'),
//    'multilang' => true,
//    'linked' => false,
);


$schema['export_fields']['E-mail'] = array(
    'db_field' => 'email',
    'table' => 'orders',
    'required' => true,
);

$schema['export_fields']['Issuer ID'] = array(
    'db_field' => 'issuer_id',
    'table' => 'orders',
);

$schema['export_fields']['Total'] = array(
    'db_field' => 'total',
    'table' => 'orders',
);

$schema['export_fields']['Subtotal'] = array(
    'db_field' => 'subtotal',
    'table' => 'orders',
);

$schema['export_fields']['Discount'] = array(
    'db_field' => 'discount',
    'table' => 'orders',
);

$schema['export_fields']['Payment surcharge'] = array(
    'db_field' => 'payment_surcharge',
    'table' => 'orders',
);

$schema['export_fields']['Shipping cost'] = array(
    'db_field' => 'shipping_cost',
    'table' => 'orders',
);

$schema['export_fields']['Notes'] = array(
    'db_field' => 'notes',
    'table' => 'orders',
);

$schema['export_fields']['Payment ID'] = array(
    'db_field' => 'payment_id',
    'table' => 'orders',
);

$schema['export_fields']['IP address'] = array(
    'db_field' => 'ip_address',
    'table' => 'orders',
    'process_get' => array('fn_ip_from_db', '#this'),
    'convert_put' => array('fn_ip_to_db', '#this')
);

$schema['export_fields']['Details'] = array(
    'db_field' => 'details',
    'table' => 'orders',
);

$schema['export_fields']['Payment information'] = array(
    'linked' => false,
    'process_get' => array('fn_exim_orders_get_data', '#key', 'P'),
    'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'P')
);

$schema['export_fields']['Taxes'] = array(
    'linked' => false,
    'process_get' => array('fn_exim_orders_get_data', '#key', 'T'),
    'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'T')
);

$schema['export_fields']['Coupons'] = array(
    'linked' => false,
    'process_get' => array('fn_exim_orders_get_data', '#key', 'C'),
    'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'C')
);

$schema['export_fields']['Shipping'] = array(
    'linked' => false,
    'process_get' => array('fn_exim_orders_get_data', '#key', 'L'),
    'process_put' => array('fn_exim_orders_set_data', '#key', '#this', 'L')
);

$schema['export_fields']['Invoice ID'] = array(
    'linked' => false,
    'process_get' => array('fn_exim_orders_get_docs', '#key', 'I'),
    'process_put' => array('fn_exim_orders_set_docs', '#key', '#this', 'I')
);

$schema['export_fields']['Credit memo ID'] = array(
    'linked' => false,
    'process_get' => array('fn_exim_orders_get_docs', '#key', 'C'),
    'process_put' => array('fn_exim_orders_set_docs', '#key', '#this', 'C')
);

$schema['export_fields']['Company'] = array(
    'db_field' => 'company',
    'table' => 'orders',
);

$schema['export_fields']['Fax'] = array(
    'db_field' => 'fax',
    'table' => 'orders',
);

$schema['export_fields']['Phone'] = array(
    'db_field' => 'phone',
    'table' => 'orders',
);

$schema['export_fields']['Web site'] = array(
    'db_field' => 'url',
    'table' => 'orders',
);

$schema['export_fields']['Tax exempt'] = array(
    'db_field' => 'tax_exempt',
    'table' => 'orders',
);

$schema['export_fields']['Language'] = array(
    'db_field' => 'lang_code',
    'table' => 'orders',
);

$schema['export_fields']['Billing: first name'] = array(
    'db_field' => 'b_firstname',
    'table' => 'orders',
);

$schema['export_fields']['Billing: last name'] = array(
    'db_field' => 'b_lastname',
    'table' => 'orders',
);

$schema['export_fields']['Billing: address'] = array(
    'db_field' => 'b_address',
    'table' => 'orders',
);

$schema['export_fields']['Billing: address (line 2)'] = array(
    'db_field' => 'b_address_2',
    'table' => 'orders'
);

$schema['export_fields']['Billing: city'] = array(
    'db_field' => 'b_city',
    'table' => 'orders'
);

$schema['export_fields']['Billing: state'] = array(
    'db_field' => 'b_state',
    'table' => 'orders'
);

$schema['export_fields']['Billing: country'] = array(
    'db_field' => 'b_country',
    'table' => 'orders'
);

$schema['export_fields']['Billing: zipcode'] = array(
    'db_field' => 'b_zipcode',
    'table' => 'orders'
);

$schema['export_fields']['Billing: phone'] = array(
    'db_field' => 'b_phone',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: first name'] = array(
    'db_field' => 's_firstname',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: last name'] = array(
    'db_field' => 's_lastname',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: address'] = array(
    'db_field' => 's_address',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: address (line 2)'] = array(
    'db_field' => 's_address_2',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: city'] = array(
    'db_field' => 's_city',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: state'] = array(
    'db_field' => 's_state',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: country'] = array(
    'db_field' => 's_country',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: zipcode'] = array(
    'db_field' => 's_zipcode',
    'table' => 'orders'
);

$schema['export_fields']['Shipping: phone'] = array(
    'db_field' => 's_phone',
    'table' => 'orders'
);

return $schema;


<?php

use Tygh\Registry;

include_once(Registry::get('config.dir.addons') . 'export_abandoned_carts/schemas/exim/abandoned_carts.functions.php');
include_once(Registry::get('config.dir.schemas') . 'exim/products.functions.php');

$schema = array(
    'section' => 'abandoned_carts',
    'pattern_id' => 'abandoned_carts',
    'name' => __('abandoned_carts'),
    'key' => array('user_id', 'item_id'),
    'order' => 1,
    'table' => 'user_session_products',
    'permissions' => array(
        'export' => 'view_abandoned_carts',
    ),
    'references' => array(
        'users' => array(
            'reference_fields' => array('user_id' => '#key'),
            'join_type' => 'LEFT'
        ),
        'product_descriptions' => array(
            'reference_fields' => array('product_id' => '&product_id', 'lang_code' => '@lang_code'),
            'join_type' => 'LEFT'
        ),
        'user_profiles' => array(
            'reference_fields' => array('user_id' => '#key'),
            'join_type' => 'LEFT'
        ),
        'products' => array(
            'reference_fields' => array('product_id' => '&product_id'),
            'join_type' => 'INNER',
        ),
    ),
    'options' => array(
        'lang_code' => array(
            'title' => 'language',
            'type' => 'languages',
        ),
        'category_delimiter' => array(
            'title' => 'category_delimiter',
            'description' => 'text_category_delimiter',
            'type' => 'input',
            'default_value' => '///',
            'position' => 500,
        ),
    ),
    'condition' => array(
        'conditions' => array(
            '&user_session_products.type' => array('C', 'W'),
        ),
        '&user_profiles.profile_type' => 'P'
    ),
    'range_options' => array(
        'selector_url' => 'cart.cart_list',
        'object_name' => __('abandoned_carts'),
    ),
    'export_fields' => array(
        'User ID' => array(
            'db_field' => 'user_id',
            'alt_key' => true,
            'required' => true,
        ),
        'Item ID' => array(
            'db_field' => 'item_id',
            'alt_key' => true,
            'required' => true,
        ),
        'E-Mail' => array(
            'db_field' => 'email',
            'table' => 'users',
            'required' => true,
        ),
        'timestamp' => array(
            'db_field' => 'timestamp',
            'process_get' => array('fn_export_abandoned_carts_timestamp_to_date', '#this'),
            'convert_put' => array('fn_date_to_timestamp', '#this'),
        ),
        'Product ID' => array(
            'db_field' => 'product_id',
        ),
        'Amount' => array(
            'db_field' => 'amount'
        ),
        'Price' => array(
            'db_field' => 'price'
        ),
        'Product Name' => array(
            'table' => 'product_descriptions',
            'db_field' => 'product',
        ),
        'First Name' => array(
            'db_field' => 'firstname',
            'table' => 'users',
        ),
        'Last Name' => array(
            'db_field' => 'lastname',
            'table' => 'users',
        ),
        'Phone' => array(
            'db_field' => 'phone',
            'table' => 'users',
        ),
        'Billing: first name' => array(
            'db_field' => 'b_firstname',
            'table' => 'user_profiles',
        ),
        'Billing: last name' => array(
            'db_field' => 'b_lastname',
            'table' => 'user_profiles',
        ),
        'Billing: address' => array(
            'db_field' => 'b_address',
            'table' => 'user_profiles',
        ),
        'Billing: address (line 2)' => array(
            'db_field' => 'b_address_2',
            'table' => 'user_profiles',
        ),
        'Billing: city' => array(
            'db_field' => 'b_city',
            'table' => 'user_profiles',
        ),
        'Billing: state' => array(
            'db_field' => 'b_state',
            'table' => 'user_profiles',
        ),
        'Billing: country' => array(
            'db_field' => 'b_country',
            'table' => 'user_profiles',
        ),
        'Billing: zipcode' => array(
            'db_field' => 'b_zipcode',
            'table' => 'user_profiles',
        ),
        'Billing: phone' => array(
            'db_field' => 'b_phone',
            'table' => 'user_profiles',
        ),
        'Shipping: first name' => array(
            'db_field' => 's_firstname',
            'table' => 'user_profiles',
        ),
        'Shipping: last name' => array(
            'db_field' => 's_lastname',
            'table' => 'user_profiles',
        ),
        'Shipping: address' => array(
            'db_field' => 's_address',
            'table' => 'user_profiles',
        ),
        'Shipping: address (line 2)' => array(
            'db_field' => 's_address_2',
            'table' => 'user_profiles',
        ),
        'Shipping: city' => array(
            'db_field' => 's_city',
            'table' => 'user_profiles',
        ),
        'Shipping: state' => array(
            'db_field' => 's_state',
            'table' => 'user_profiles',
        ),
        'Shipping: country' => array(
            'db_field' => 's_country',
            'table' => 'user_profiles',
        ),
        'Shipping: zipcode' => array(
            'db_field' => 's_zipcode',
            'table' => 'user_profiles',
        ),
        'Shipping: phone' => array(
            'db_field' => 's_phone',
            'table' => 'user_profiles',
        ),
        'Box Size' => array(
            'db_field' => 'product_id',
            'table' => 'products',
            'process_get' => array('fn_export_abandoned_carts_getBoxSize', '#this'),
        ),
        'Category' => array(
            'db_field' => 'product_id',
            'table' => 'products',
            'process_get' => array('fn_exim_get_product_categories', '#this', 'M', '@category_delimiter', '#lang_code'),
            'default' => 'Products',
        ),
        'Secondary categories' => array(
            'db_field' => 'product_id',
            'table' => 'products',
            'process_get' => array('fn_exim_get_product_categories', '#this', 'A', '@category_delimiter', '#lang_code'),
            'multilang' => true,
        ),
        'Vendor' => array(
            'db_field' => 'product_id',
            'table' => 'products',
            'process_get' => array('fn_export_abandoned_carts_GetVendor', '#this', '#lang_code'),
        ),
        'Shipping freight Exact' => array(
            'db_field' => 'shipping_freight',
            'table' => 'products'
        ),
    )
);


return $schema;
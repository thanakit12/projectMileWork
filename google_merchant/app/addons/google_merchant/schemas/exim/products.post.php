<?php

defined('BOOTSTRAP') or die('Access denied');

if(isset($schema['export_fields']))
{
    $schema['export_fields']['Description'] = array(
        'table' => 'product_descriptions',
        'db_field' => 'full_description',
        'multilang' => true,
        'process_get' => array('fn_export_product_descr', '#key', '#this', '#lang_code', 'full_description'),
        'process_put' => array('fn_import_product_descr', '#this', '#key', 'full_description'),
        'required' => true
    );
}
return $schema;

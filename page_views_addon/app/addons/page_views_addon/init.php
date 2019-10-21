<?php

if ( !defined('AREA') ) { die('Access denied'); }

fn_register_hooks(
    'get_product_data_post',
    'gather_additional_product_data_post'
);

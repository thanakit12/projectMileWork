<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
fn_register_hooks(
    'shippings_group_products_list',
    'shippings_get_shippings_list',
    'update_product_pre',
    'update_category_post',
    'shippings_get_shippings_list_post'
);
<?php

if (!defined('AREA')) {
    die('Access denied');
}

fn_register_hooks(
    'update_product_post',
    'import_post',
    'tools_change_status'
);
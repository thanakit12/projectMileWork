<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

use Tygh\Registry;

if ($mode == 'update') {
    //Set Navigation Tabs
    $tabs = array(
        'title' => __('shipping_method'),
        'js' => true,
    );
    Registry::set("navigation.tabs.shipping_methods", $tabs);

    $company_id = Registry::ifGet('runtime.company_id', null);
    Registry::get('view')->assign('shippings', fn_get_available_shippings($company_id));
}
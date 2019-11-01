<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($mode == "export_range") {
    if (!empty($_REQUEST['user_ids'])) {
        if (empty(Tygh::$app['session']['export_ranges'])) {
            Tygh::$app['session']['export_ranges'] = array();
        }
        if (empty(Tygh::$app['session']['export_ranges']['abaddon_cart'])) {
            Tygh::$app['session']['export_ranges']['abaddon_cart'] = array('pattern_id' => 'abaddon_cart');
        }
        Tygh::$app['session']['export_ranges']['abaddon_cart']['data'] = array('user_id' => $_REQUEST['user_ids']);
        unset($_REQUEST['redirect_url']);

        return array(CONTROLLER_STATUS_REDIRECT, 'exim.export?section=abaddon_cart&pattern_id=' . Tygh::$app['session']['export_ranges']['abaddon_cart']['pattern_id']);
    }
}
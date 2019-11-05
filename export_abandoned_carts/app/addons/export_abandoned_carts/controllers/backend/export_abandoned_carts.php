<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($mode == "export_range") {
    if (!empty($_REQUEST['user_ids'])) {
        if (empty(Tygh::$app['session']['export_ranges'])) {
            Tygh::$app['session']['export_ranges'] = array();
        }
        if (empty(Tygh::$app['session']['export_ranges']['abandoned_carts'])) {
            Tygh::$app['session']['export_ranges']['abandoned_carts'] = array('pattern_id' => 'abandoned_carts');
        }
        Tygh::$app['session']['export_ranges']['abandoned_carts']['data'] = array('user_id' => $_REQUEST['user_ids']);
        unset($_REQUEST['redirect_url']);

        return array(CONTROLLER_STATUS_REDIRECT, 'exim.export?section=abandoned_carts&pattern_id=' . Tygh::$app['session']['export_ranges']['abandoned_carts']['pattern_id']);
    }
}


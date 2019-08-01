<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'delete') {
        try {
            $product_id = $_REQUEST['product_id'];
            fn_google_merchant_delete($product_id);
        } catch (Exception $e) {
            if ($e->getCode() == '404') {
            } else {
                fn_print_r($e->getMessage());
            }
        }
    }
}

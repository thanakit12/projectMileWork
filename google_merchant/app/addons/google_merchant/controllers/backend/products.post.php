<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'm_delete' || $mode == 'm_disable') {
        if (isset($_REQUEST['product_ids'])) {
            try {
                $product_id = $_REQUEST['product_ids'];
                fn_google_merchant_deleteBatch($product_id);
            } catch (Exception $e) {
                $http_code = $e->getCode();
                if ($http_code == "404") {
                } else {
                    fn_print_r($e->getMessage());
                }
            }
        }
    }
    if ($mode == "m_activate") {
        if (isset($_REQUEST['product_ids'])) {
            $product_id = $_REQUEST['product_ids'];
            $data = [];
            for ($i = 0; $i < count($product_id); $i++) {
                $data[] = fn_get_product_data($product_id[$i], $auth, 'th', '', true, true, true, true, false, false, '');
                $product = fn_google_merchant_create($product_id[$i], $data[$i]);
                $products[] = $product;
            }
            $arr = array_unique($products, SORT_REGULAR);
            fn_google_merchant_insertBatch($arr);
        }
    }
}



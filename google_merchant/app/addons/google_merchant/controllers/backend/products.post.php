<?php


if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'm_delete')
    {
        if (isset($_REQUEST['product_ids']))
        {
            try {
                $product_id = $_REQUEST['product_ids'];

                for ($i = 0; $i < count($product_id); $i++) {
                    fn_google_merchant_delete($product_id[$i]);
                }
            }
            catch (Exception $e)
            {
                $http_code = $e->getCode();
                if($http_code == "404")
                    fn_redirect('products.manage');
            }

        }
    }
}



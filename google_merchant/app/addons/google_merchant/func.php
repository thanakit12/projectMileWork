<?php
require_once "content/ContentSession.php";
require_once "content/BaseSample.php";
require_once "content/Product.php";
require_once "content/php/vendor/autoload.php";

use Tygh\Api;
use Tygh\Registry;
use Tygh\Api\Request;

if (!defined('AREA')) {
    die('Access denied');
}

function fn_google_merchant_update_product_post($product_data, $product_id, $lang_code, $create)
{
    $isset_image = fn_google_merchant_fetch_images_url($product_id);
    $status = db_get_row("select status from ?:products where product_id = ?i", $product_id);
    $product_status = $status["status"];
    //if create and have image send it to google merchant
    if ($isset_image != '') {
        if ($create) {
            $product = fn_google_merchant_create($product_id, $product_data);
            fn_google_merchant_insert($product);
        } else {
            //  if check has one product and edit some thing then save in products.update page
            if (isset($_REQUEST['product_id']) && !isset($_REQUEST['product_ids'])) {
                //A - Active
                //D - Disable
                if ($product_status == 'A') {
                    $product = fn_google_merchant_create($product_id, $product_data);
                    if ($product != null) {
                        fn_google_merchant_insert($product);
                    }
                } elseif ($product_status == 'D') {
                    $result = fn_google_merchant_getProductFromMerchant($product_id);
                    if ($result != false) {
                        fn_google_merchant_delete($product_id);
                    }
                }
            } // if have api request
            elseif (Tygh::$app['api'] instanceof Api) {
                $request = new Request();
                $method = $request->getMethod();
                if ($method == 'PUT') {
                    if ($product_status == 'A') {
                        $data = fn_get_product_data($product_id, $_SESSION['auth']);
                        $product = fn_google_merchant_create($product_id, $data);
                        if ($product != null) {
                            fn_google_merchant_insert($product);
                        }
                    } elseif ($product_status == 'D') {
                        $check_product = fn_google_merchant_getProductFromMerchant($product_id);
                        if ($check_product != false) {
                            fn_google_merchant_delete($product_id);
                        }
                    }
                }
            } // else one product click change status in products.manage then pass parameter to functions
            else {
                if (isset($_REQUEST['id'])) {
                    $params['id'] = $_REQUEST['id'];
                    $params['status'] = $product_status;
                    fn_google_merchant_tools_change_status($params, true);
                }
            }
        }

    }
}

//This Function is cut html elements
function fn_rip_tags($string)
{
    // ----- remove HTML TAGs -----
    $string = preg_replace('/<[^>]*>/', ' ', $string);

    // ----- remove control characters -----
    $string = str_replace("\r", '', $string);    // --- replace with empty space
    $string = str_replace("\n", ' ', $string);   // --- replace with space
    $string = str_replace("\t", ' ', $string);   // --- replace with space

    // ----- remove multiple spaces -----
    $string = trim(preg_replace('/ {2,}/', ' ', $string));

    return $string;
}

function fn_google_merchant_fetch_product_url($product_id)
{
    $product_url = fn_exim_get_product_url($product_id, "th");
    return $product_url;
}

function fn_google_merchant_fetch_images_url($product_id)
{
    $images_url = fn_exim_get_image_url($product_id, "product", "M", true, true, "th");

    if ($images_url != '') {
        return $images_url;
    } else
        return false;
}

function fn_google_merchant_insert(Google_Service_ShoppingContent_Product $product)
{
    $product_data = new Product();
    $product_data->session->service->products->insert($product_data->session->merchantId, $product);
}

function fn_google_merchant_update(Google_Service_ShoppingContent_Product $product)
{
    $product_data = new Product();
    $product_data->session->service->products->insert($product_data->session->merchantId, $product);
}

function fn_google_merchant_delete($product_id)
{
    $product = new Product();
    $product->deleteProduct($product_id);
}

function fn_google_merchant_insertBatch($products)
{
    try {
        $p = [];
        $product_data = new Product();
        foreach ($products as $key => $val) {
            $product = new Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry();
            $product->setMethod('insert');
            $product->setBatchId($key);
            $product->setProduct($val);
            $product->setMerchantId($product_data->session->merchantId);
            $p[] = $product;
        }
        $batchRequest = new Google_Service_ShoppingContent_ProductsCustomBatchRequest();
        $batchRequest->setEntries($p);
        $product_data->session->service->products->custombatch($batchRequest);
    } catch (Exception $e) {
        fn_set_notification('E', __('error'), _("Have something error") . ' ' . $e->getMessage());
    }

}

function fn_google_merchant_import_post($pattern, $import_data, $options)
{
    $collect_data = fn_google_merchant_collect_data($import_data);
    $data_product = fn_google_merchant_prepare_product($collect_data);
    $result = [];
    // check product has imagelink
    for ($i = 0; $i < count($data_product); $i++) {
        if ($data_product[$i] == null) {
            continue;
        } // check field_mapping has selected price
        else {
            if ($data_product[$i]->getPrice()->getvalue() == null) {
                continue;
            } else {
                $result[] = $data_product[$i];
            }
        }
    }
    if (!empty($result)) {
        fn_google_merchant_insertBatch($result);
    }
}

function fn_google_merchant_collect_data($import_data)
{
    $arr = [];
    for ($i = 0; $i < count($import_data); $i++) {
        $arr[] = $import_data[$i]["th"];
    }
    return $arr;
}

function fn_google_merchant_prepare_product($collect_data)
{
    $products = [];
    $result = [];
    for ($i = 0; $i < count($collect_data); $i++) {
        $product_id[] = $collect_data[$i]["product_code"];
        $company_id[] = $collect_data[$i]["company"]; //Vendor
        $query = db_get_row("SELECT product_id,company_id FROM ?:products WHERE ?:products.product_code IN('$product_id[$i]')
                            and (SELECT company_id FROM cscart_companies WHERE company IN('$company_id[$i]'))");
        $product["product_url"] = fn_google_merchant_fetch_product_url($query["product_id"]);
        $product["full_description"] = $collect_data[$i]["full_description"];
        $product["price"] = $collect_data[$i]["price"];
        $product["product"] = $collect_data[$i]["product"];
        $product["status"] = isset($collect_data[$i]["status"]) ? $collect_data[$i]["status"] : fn_google_merchant_getStatus($query['product_id']);
//check product status if status Active push to google merchant if status disable delete from google merchant
        if ($product["status"] == 'A') {
            $create_product = fn_google_merchant_create($query["product_id"], $product);
            $products[] = $create_product;
        } elseif ($product["status"] == 'D') {
            $collect_Id = $query["product_id"];
            $chk_product = fn_google_merchant_getProductFromMerchant($collect_Id);
            if ($chk_product != false) {
                $result[] = $collect_Id;
            }
        }
    }
    if (!empty($result)) {
        fn_google_merchant_DeleteProductBatch($result);
    }

    return $products;
}

//Hook

function fn_google_merchant_tools_change_status($params, $result)
{
    $auth = $_SESSION["auth"];

    //This section is insert or delete one product to google merchant when click tool.updates_status on products.manage page
    if (isset($params['dispatch'])) {
        try {
            $product_id = $_REQUEST['id'];
            $status = $_REQUEST['status'];
            if ($status == 'A') {
                $data = fn_get_product_data($product_id, $auth, 'th', '', true, true, true, true, false, false, '');
                $product = fn_google_merchant_create($product_id, $data);
                if ($product != null) {
                    fn_google_merchant_insert($product);
                }
            } elseif ($status == 'D') {
                fn_google_merchant_delete($product_id);
            }
        } catch (Exception $e) {
            fn_set_notification('E', __('error'), _('This product does not in google merchant center'));
        }
    }
}

function fn_google_merchant_create($product_id, $product_data)
{
    $product = new Google_Service_ShoppingContent_Product();
    $product_name = $product_data["product"];
    $product_url = fn_google_merchant_fetch_product_url($product_id);
    $isset_image = fn_google_merchant_fetch_images_url($product_id);
    $brand = fn_google_merchant_GetBrand($product_id);
    $amount = fn_google_merchant_IsExist($product_id);

    //check product has image and has amount
    if ($isset_image != '' && $amount) {
        $product_price = $product_data["price"];
        //product_description is import but full_description is add each product
        if (isset($product_data["product_description"])) {
            $product_description = $product_data["product_description"];
            $product_description = fn_rip_tags($product_description); //cut html element tags
        } else {
            if (!empty($product_description)) {
                $product_description = $product_data["full_description"];
                $product_description = fn_rip_tags($product_description); //cut html element tags
            } else
                $product_description = '';
        }

        $product->setOfferId($product_id);
        $product->setTitle($product_name);
        $product->setDescription($product_description);
        $product->setLink($product_url);
        $product->setCondition('New');

        $price = new Google_Service_ShoppingContent_Price();
        $price->setValue($product_price);
        $price->setCurrency('THB');
        $product->setPrice($price);
        $product->setAvailability('in stock');
        $product->setImageLink($isset_image);
        $product->setGtin('');
        $product->setMpn('');
        $product->setBrand($brand);
        $product->setContentLanguage('TH');
        $product->setTargetCountry('TH');
        $product->setChannel('online');
        $product->setIncludedDestinations(["Shopping Ads"]);
        return $product;
    }
}

function fn_google_merchant_GetBrand($product_id)
{
    $string = Registry::get('addons.google_merchant.brand_value');
    $query = db_get_row("SELECT feature_id FROM ?:product_features_descriptions where description = ?s AND lang_code = 'th'", $string);
    $feature_id = $query['feature_id'];
    $query_value = db_get_row("SELECT value FROM ?:product_features_values WHERE product_id = ?i and feature_id = ?i", $product_id, $feature_id);

    return !empty($query_value) ? $query_value["value"] : '';
}

function fn_google_merchant_IsExist($product_id)
{
    $query = db_get_row("SELECT amount FROM ?:products where product_id = ?i", $product_id);
    $amount = $query["amount"];
    if ($amount > 0) {
        return true;
    } else {
        return false;
    }
}

function fn_google_merchant_getStatus($product_id)
{
    $query = db_get_row("SELECT status FROM ?:products WHERE product_id = ?i", $product_id);
    $status = $query['status'];
    return !empty($query['status']) ? $status : '';
}

function fn_google_merchant_DeleteProductBatch($products)
{
    $p = [];
    $product = new Product();
    foreach ($products as $key => $offerId) {
        $entry = new Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry();
        $entry->setMethod('delete');
        $entry->setBatchId($key);
        $entry->setProductId($product->buildProductId($offerId));
        $entry->setMerchantId($product->session->merchantId);
        $p[] = $entry;
    }
    $batchRequest = new Google_Service_ShoppingContent_ProductsCustomBatchRequest();
    $batchRequest->setEntries($p);
    $product->session->service->products->custombatch($batchRequest);
}

function fn_google_merchant_checkEmpty($data)
{
    $result = [];
    foreach ($data as $key => $val) {
        if ($val == '')
            continue;
        else {
            $result[] = $val;
        }
    }
    return $result;
}

function fn_google_merchant_getProductFromMerchant($offer_id)
{
    $product = new Product();
    try {
        $productId = $product->buildProductId($offer_id);
        $product = $product->session->service->products->get($product->session->merchantId, $productId);
    } catch (Exception $e) {
        if ($e->getCode() == '404') {
            $product = false;
        }
    }

    return $product;
}

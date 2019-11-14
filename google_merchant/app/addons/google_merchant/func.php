<?php
require_once "content/ContentSession.php";
require_once "content/BaseSample.php";
require_once "content/Product.php";
require_once "content/php/vendor/autoload.php";


use Tygh\Registry;

if (!defined('AREA')) {
    die('Access denied');
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


function fn_google_merchant_create($product_id, $product_data)
{
    $product = new Google_Service_ShoppingContent_Product();
    $product_name = $product_data["product"];
    if (isset($product_data['product_code'])) {
        $product_code = $product_data['product_code'];
    } else {
        $product_code = '';
    }
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
        $product->setMpn($product_code);
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


function fn_google_merchant_cron_job($start_of_day, $end_of_day)
{

    $sql_db = db_get_array("SELECT product_id FROM `?:products`
                            WHERE updated_timestamp BETWEEN UNIX_TIMESTAMP(?s) AND UNIX_TIMESTAMP(?s)", $start_of_day, $end_of_day);

    $check_script = Registry::get('addons.google_merchant.check_script');
    if ($check_script == 'Y') {
        $product_merchant = [];
        $delete_product = [];
        foreach ($sql_db as $key => $value) {
            $product_id = $value['product_id'];
            $product_status = fn_google_merchant_getStatus($product_id);
            if ($product_status == 'A') {
                $data_product = fn_get_product_data($product_id, $_SESSION['auth']);
                $create_product_merchant = fn_google_merchant_create($product_id, $data_product);
                $product_merchant[] = $create_product_merchant;
            } elseif ($product_status == 'D') {
                $delete_product[] = $product_id;
            }
        }
        $filler_product_merchant = [];
        for ($i = 0; $i < count($product_merchant); $i++) {
            if ($product_merchant[$i] == null) {
                continue;
            } else {
                $filler_product_merchant[] = $product_merchant[$i];
            }
        }
        fn_google_merchant_insertBatch($filler_product_merchant);
        if (count($delete_product) > 0) {
            fn_google_merchant_DeleteProductBatch($delete_product);
        }
    }
}

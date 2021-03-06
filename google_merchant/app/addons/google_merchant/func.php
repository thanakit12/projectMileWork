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
        error_log($e->getMessage());
    }
}


function fn_google_merchant_create($product_id, $product_data)
{

    $product = new Google_Service_ShoppingContent_Product();
    $product_name = $product_data["product"];
    $part_number = $product_data["part_number"];
    $product_url = fn_google_merchant_fetch_product_url($product_id);
    $isset_image = fn_google_merchant_fetch_images_url($product_id);
    $brand = fn_google_merchant_GetBrand($product_id);
    $amount = fn_google_merchant_IsExist($product_id);
    $product_description = isset($product_data['full_description']) ? fn_rip_tags($product_data['full_description']) : '';
    //check product has image and has amount
    if ($isset_image != '' && $amount) {
        $product_price = $product_data["price"];

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
        $product->setMpn($part_number);
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
    //If you want to change brand you can change in this function

    $str = '';

    if (Registry::get('addons.google_merchant.checkbox_brand_select_all') == 'Y') {

        if (Registry::get('addons.google_merchant.field_brand_value_main') != '' && Registry::get('addons.google_merchant.field_brand_value_secondary') != '') {

            $string = Registry::get('addons.google_merchant.field_brand_value_main');
            $brand = Registry::get('addons.google_merchant.field_brand_value_secondary');

            $query = db_get_row("SELECT feature_id FROM ?:product_features_descriptions where description = ?s AND lang_code = 'th'", $string);
            $feature_id = $query['feature_id'];

            $field_main_value = db_get_row("SELECT value FROM ?:product_features_values WHERE product_id = ?i and feature_id = ?i", $product_id, $feature_id);

            $query_two = db_get_row("SELECT feature_id FROM ?:product_features_descriptions where description = ?s AND lang_code = 'th'", $brand);
            $brand_id = $query_two['feature_id'];

            $field_secondary_value = db_get_row("SELECT value FROM ?:product_features_values WHERE product_id = ?i and feature_id = ?i", $product_id, $brand_id);

            if (!empty($feature_id) && !empty($brand_id)) {
                if (empty($field_main_value) && empty($field_secondary_value)) {
                    $str = '';
                } elseif (empty($field_main_value) && !empty($field_secondary_value)) {
                    $str = strtolower($field_secondary_value['value']);
                } elseif (!empty($field_brand_value_main) && empty($field_secondary_value)) {
                    $str = strtolower($field_brand_value_main['value']);
                } else {
                    $str = strtolower($field_main_value['value']) . "-" . strtolower($field_secondary_value['value']);
                }
            }
        }
    } else {
        if (Registry::get('addons.google_merchant.field_brand_value_main') != '') {

            $string = Registry::get('addons.google_merchant.field_brand_value_main');

            $query = db_get_row("SELECT feature_id FROM ?:product_features_descriptions where description = ?s AND lang_code = 'th'", $string);
            $feature_id = $query['feature_id'];

            $field_main_value = db_get_row("SELECT value FROM ?:product_features_values WHERE product_id = ?i and feature_id = ?i", $product_id, $feature_id);

            if (!empty($field_main_value['value'])) {
                $str = strtolower($field_main_value['value']);
            }
        }
    }
    return $str;
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
    try {
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
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}


function fn_google_merchant_cron_job($start_of_day, $end_of_day)
{
    if (Registry::get('addons.google_merchant.check_script') == 'Y') {
        $time = date("Y-m-d H:i:s");
        $array = array(
            "start_time" => $time,
        );
        try {
            $sum_product_insert = 0;
            $sum_product_delete = 0;
            $process_skip = 0;
            db_query("INSERT INTO ?:log_google_merchant ?e", $array);
            $sql_db = db_get_array("SELECT product_id FROM `?:products`
                                    WHERE updated_timestamp
                                    BETWEEN UNIX_TIMESTAMP(?s) AND UNIX_TIMESTAMP(?s)", $start_of_day, $end_of_day);

            db_query("UPDATE ?:log_google_merchant SET Total = ?i WHERE start_time = ?s", count($sql_db), $time);
            // Partition data in Array
            $partition_data = array_chunk($sql_db, 1000);
            for ($i = 0; $i < count($partition_data); $i++) {
                $product_merchant = []; // Reset Array
                $delete_product = []; // Reset Array
                for ($j = 0; $j < count($partition_data[$i]); $j++) {
                    $product_id = $partition_data[$i][$j]['product_id'];
                    $product_status = fn_google_merchant_getStatus($product_id);
                    if ($product_status == 'A') {
                        $data_product = fn_get_product_data($product_id, $_SESSION['auth']);
                        $create_product_merchant = fn_google_merchant_create($product_id, $data_product);
                        if ($create_product_merchant == null) {
                            $process_skip++;
                        } else {
                            $product_merchant[] = $create_product_merchant;
                        }
                    } elseif ($product_status == 'D') {
                        $delete_product[] = $product_id;
                    }
                }
                $fillter_array = array_filter($product_merchant);
                fn_google_merchant_insertBatch($fillter_array);
                if (count($delete_product) > 0) {
                    fn_google_merchant_DeleteProductBatch($delete_product);
                }
                $sum_product_insert += count($fillter_array);
                $sum_product_delete += count($delete_product);
                db_query("UPDATE ?:log_google_merchant SET finish_time = ?s,Process_insert = ?i,Process_deleted = ?i,Process_Skip = ?i  WHERE start_time = ?s", date('Y-m-d H:i:s'), $sum_product_insert, $sum_product_delete, $process_skip, $time);
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
        }
    }
}

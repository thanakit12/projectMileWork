<?php
require_once "content/ContentSession.php";
require_once "content/BaseSample.php";
require_once "content/Product.php";
require_once "content/php/vendor/autoload.php";

if ( !defined('AREA') ) { die('Access denied'); }

function fn_google_merchant_update_product_post($product_data, $product_id, $lang_code, $create)
{
    $isset_image = fn_google_merchant_fetch_images_url($product_id);
    $product_status = $product_data["status"];
    //if create and have image send it to google merchant
    if ($isset_image != '') {
        if ($create) {
            $product = fn_google_merchant_create($product_id, $product_data);
            $result = fn_google_merchant_insert($product);
            fn_print_r($result);
        } else {
            if ($product_status == 'D'){
                fn_print_r($product_id);
                $chk = fn_google_merchant_delete($product_id);
                fn_print_r($chk);
            }
            $product_update = fn_google_merchant_create($product_id, $product_data);
            fn_print_r($product_update);
//            $update_product =  fn_google_merchant_update($product_update);
//            fn_print_r($update_product);
            fn_print_r("UPDATE");
        }
    }
}

function fn_rip_tags($string)
{
    // ----- remove HTML TAGs -----
    $string = preg_replace ('/<[^>]*>/', ' ', $string);

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
    $product_url = fn_exim_get_product_url($product_id,"th");
    return $product_url;
}
function fn_google_merchant_fetch_images_url($product_id)
{
    $images_url = fn_exim_get_image_url($product_id,"product","M",true,true,"th");

    if($images_url != '') {
        return $images_url;
    }
    else
      return false;
}
function fn_google_merchant_insert(Google_Service_ShoppingContent_Product $product)
{
    $product_data = new Product();
    $response = $product_data->session->service->products->insert($product_data->session->merchantId, $product);
    return $response;
}
function fn_google_merchant_create($product_id,$product_data)
{
    $product = new Google_Service_ShoppingContent_Product();
    $product_name = $product_data["product"];
    $product_url = fn_google_merchant_fetch_product_url($product_id);
    $isset_image = fn_google_merchant_fetch_images_url($product_id);
    $product_price = $product_data["price"];

    //product_description is import but full_description is add each product
    if(isset($product_data["product_description"]))
    {
        $product_description = $product_data["product_description"];
        $product_description = fn_rip_tags($product_description); //cut html element tags
    }
    else {
        $product_description = $product_data["full_description"];
        $product_description = fn_rip_tags($product_description); //cut html element tags
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
    $product->setBrand('');
    $product->setContentLanguage('TH');
    $product->setTargetCountry('TH');
    $product->setChannel('online');
    $product->setIncludedDestinations(["Shopping Ads"]);

    return $product;
}
function fn_google_merchant_update(Google_Service_ShoppingContent_Product $product)
{
    $product_data = new Product();
    $response = $product_data->session->service->products->insert($product_data->session->merchantId, $product);
    return $response;
}
function fn_google_merchant_delete($product_id)
{
    $product_data = new Product();
    $response = $product_data->session->service->products->delete($product_data->session->merchantId,$product_id);
    return $response;
}

function fn_google_merchant_insertBatch($products)
{
    $product_data = new Product();
    $p = [];
    foreach ($products as $key => $val)
    {
        $product = new Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry();
        $product->setMethod('insert');
        $product->setBatchId($key);
        $product->setProduct($val);
        $product->setMerchantId($product_data->session->merchantId);

        $p[] = $product;
    }
    return $p;

}

function fn_google_merchant_import_post($pattern, $import_data, $options)
{
    $product_data = new Product();
    $arr = [];
    $product_id = [];
    $products = [];
    for ($i = 0 ; $i < count($import_data) ; $i++)
    {
        if((int)$import_data[$i]["th"]["product_code"] > 0)
        {
            $arr[] = $import_data[$i]["th"];
        }
    }
    for ($i = 0 ; $i < count($arr) ; $i++)
    {
        $product_id[] = $arr[$i]["product_code"];
        $query = db_get_row("select product_id from ?:products where product_code in ($product_id[$i])");
        $product["product_url"] = fn_google_merchant_fetch_product_url($query["product_id"]);
        $product["product_description"] = fn_exim_get_product_features($query["product_id"],"///","th");
        $product["price"] = $arr[$i]["price"];
        $product["product_name"] = $arr[$i]["product"];
        $create_product = fn_google_merchant_create($query["product_id"],$product);
        $products[] = $create_product;
    }
    $test = fn_google_merchant_insertBatch($products);  //  $insert = fn_google_merchant_insert($create_product);
    $batchRequest = new Google_Service_ShoppingContent_ProductsCustomBatchRequest();
    $batchRequest->setEntries($test);
    $batchResponse = $product_data->session->service->products->custombatch($batchRequest);
  fn_print_r($batchResponse);



}

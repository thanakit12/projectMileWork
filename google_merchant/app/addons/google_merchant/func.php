<?php
require_once "content/ContentSession.php";
require_once "content/BaseSample.php";
require_once "content/Product.php";
require_once "content/php/vendor/autoload.php";

if ( !defined('AREA') ) { die('Access denied'); }

function fn_google_merchant_update_product_post($product_data, $product_id, $lang_code, $create)
{
    $isset_image = fn_google_merchant_fetch_images_url($product_id);
    $status = db_get_row("select status from ?:products where product_id = ?i", $product_id);
    $product_status = $status["status"];
    //if create and have image send it to google merchant
    if ($isset_image != '') {
        if ($create) {
            $product = fn_google_merchant_create($product_id, $product_data);
            $result = fn_google_merchant_insert($product);
        } else {
            //when click product
            //  if check has one product and edit some thing then save
            // else one product click change status in products.manage then pass parameter to functions
            if (isset($_REQUEST['product_id']) && !isset($_REQUEST['product_ids']))
            {
                //A - Active
                //D - Disable
                if($product_status == 'A')
                {
                    $product = fn_google_merchant_create($product_id,$product_data);
                    $result = fn_google_merchant_insert($product);
                }
                elseif ($product_status == 'D')
                {
                    $response = fn_google_merchant_delete($product_id);
                }
            }
            else
            {
                if(isset($_REQUEST['id']))
                {
                    $params['id'] = $_REQUEST['id'];
                    $params['status'] = $product_status;
                    fn_google_merchant_tools_change_status($params,true);
                }
            }
        }
    }
}

//This Function is cut html elements
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

function fn_google_merchant_update(Google_Service_ShoppingContent_Product $product)
{
    $product_data = new Product();
    $response = $product_data->session->service->products->insert($product_data->session->merchantId, $product);
    return $response;
}
function fn_google_merchant_delete($product_id)
{
    $product = new Product();
   $response =  $product->deleteProduct($product_id);
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
    $batchRequest = new Google_Service_ShoppingContent_ProductsCustomBatchRequest();
    $batchRequest->setEntries($p);
    $batchResponse = $product_data->session->service->products->custombatch($batchRequest);
}

function fn_google_merchant_import_post($pattern, $import_data, $options)
{
    $product_data = new Product();
    $collect_data = fn_google_merchant_collect_data($import_data);

    $data_product = fn_google_merchant_prepare_product($collect_data);
    fn_google_merchant_insertBatch($data_product);
}

function fn_google_merchant_collect_data($import_data)
{
    $arr = [];
    for ($i = 0 ; $i < count($import_data) ; $i++)
    {
        if((int)$import_data[$i]["th"]["product_code"] > 0)
        {
            $arr[] = $import_data[$i]["th"];
        }
    }
    return $arr;
}
function fn_google_merchant_prepare_product($collect_data)
{
    $products = [];
    for ($i = 0 ; $i < count($collect_data) ; $i++)
    {
        $product_id[] = $collect_data[$i]["product_code"];
        $company_id[] = $collect_data[$i]["company"]; //Vendor
        $query = db_get_row("SELECT product_id,company_id FROM ?:products WHERE ?:products.product_code IN('$product_id[$i]')
                            and (SELECT company_id FROM cscart_companies WHERE company IN('$company_id[$i]'))");
        $product["product_url"] = fn_google_merchant_fetch_product_url($query["product_id"]);
        $product["full_description"] = $collect_data[$i]["full_description"];
        $product["price"] = $collect_data[$i]["price"];
        $product["product"] = $collect_data[$i]["product"];
        $product["brand"] = fn_exim_get_product_features($query["product_id"],"///","th");
        $create_product = fn_google_merchant_create($query["product_id"],$product);
        $products[] = $create_product;
    }
    return $products;
}

//Hook

function fn_google_merchant_tools_change_status($params, $result)
{
    $auth = $_SESSION["auth"];
    $status = $params['status'];
    $products = [];

    //This section is insert or delete one product to google merchant when click tool.updates_status on products.manage page
    if(isset($params['dispatch'])) {
        try {
            $product_id = $_REQUEST['id'];
            $status = $_REQUEST['status'];
            if ($status == 'A') {
                $data = fn_get_product_data($product_id, $auth, 'th', '', true, true, true, true, false, false, '');
                $product = fn_google_merchant_create($product_id, $data);
                $response = fn_google_merchant_insert($product);
            } elseif ($status == 'D') {
                fn_google_merchant_delete($product_id);
            }
        }catch (Exception $e)
        {
            fn_set_notification('E', __('error'), _('This product does not in google merchant center'));
        }
    }
    //if have many products select and change status
    elseif (isset($_REQUEST['product_ids']))
    {
        $product_id = $_REQUEST['product_ids'];
        $data = [];
        $mode = $_REQUEST['dispatch'];
        if($mode == "products.m_activate") {
            for ($i = 0; $i < count($product_id); $i++) {
                $data[] = fn_get_product_data($product_id[$i], $auth, 'th', '', true, true, true, true, false, false, '');
                $product = fn_google_merchant_create($product_id[$i], $data[$i]);
                $products[] = $product;
            }
            $arr = array_unique($products, SORT_REGULAR);
             fn_google_merchant_insertBatch($arr);
             fn_redirect('products.manage');
        }
        else if($mode == "products.m_disable")
        {
            for ($i = 0 ; $i < count($product_id) ; $i++)
            {
                fn_google_merchant_delete($product_id[$i]);
            }
        }
    }
}

    function fn_google_merchant_create($product_id, $product_data)
    {
        $product = new Google_Service_ShoppingContent_Product();

        $product_name = $product_data["product"];
        $product_url = fn_google_merchant_fetch_product_url($product_id);
        $isset_image = fn_google_merchant_fetch_images_url($product_id);
        $product_price = $product_data["price"];

        //product_description is import but full_description is add each product
        if (isset($product_data["product_description"])) {
            $product_description = $product_data["product_description"];
            $product_description = fn_rip_tags($product_description); //cut html element tags
        } else {
            $product_description = $product_data["full_description"];
            $product_description = fn_rip_tags($product_description); //cut html element tags
        }
        if(isset($product_data["brand"]))
        {
            $band = $product_data['brand'];
        }
        else
            $band = '';

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
        $product->setBrand($band);
        $product->setContentLanguage('TH');
        $product->setTargetCountry('TH');
        $product->setChannel('online');
        $product->setIncludedDestinations(["Shopping Ads"]);
        return $product;
    }



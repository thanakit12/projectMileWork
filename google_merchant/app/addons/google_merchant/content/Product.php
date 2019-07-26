<?php
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once 'BaseSample.php';

// Class for running through some example interactions with the
// Products service.
class Product extends BaseSample
{
    // These constants define the identifiers for all of our example products

    const CHANNEL = 'online';

    const CONTENT_LANGUAGE = 'TH';

    const TARGET_COUNTRY = 'TH';

    // This constant defines how many example products to create in a batch
    const BATCH_SIZE = 50;

    public function run()
    {
        if (is_null($this->session->websiteUrl)) {
            throw InvalidArgumentException(
                'Cannot run Products workflow on a Merchant Center account without '
                . 'a configured website URL.');
        }
    }

    private function buildProductId($offer_id)
    {
        return sprintf('%s:%s:%s:%s', self::CHANNEL, self::CONTENT_LANGUAGE,
            self::TARGET_COUNTRY, $offer_id);
    }

    public function deleteProduct($offerId)
    {
        $productId = $this->buildProductId($offerId);
        // The response for a successful delete is empty
     $this->session->service->products->delete($this->session->merchantId, $productId);
    }


    public function createProduct($product_id, $product_data)
    {
        $product = new Google_Service_ShoppingContent_Product();
        $product_url = fn_google_merchant_fetch_product_url($product_id);
        $isset_image = fn_google_merchant_fetch_images_url($product_id);

        $product_name = $product_data["product"];
        $product_price = $product_data["price"];

        if(isset($product["brand"]))
            $brand = $product_data["brand"];
        else
            $brand = 'test';

        $product_description = $product_data["full_description"];
        $product_description = fn_rip_tags($product_description); //cut html element tags

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
//  }
    }
}


//  public function updateProduct(
//      Google_Service_ShoppingContent_Product $product) {
//    // Let's fix the warning about product_type and update the product
//    $product->setProductType('English/Classics');
//    // Notice that we use insert. The products service does not have an update
//    // method. Inserting a product with an ID that already exists means the same
//    // as doing an update anyway.
//
//    $response = $this->session->service->products->insert(
//        $this->session->merchantId, $product);
//
////    // We should no longer get the product_type warning.
////    $warnings = $response->getWarnings();
////    printf("Product updated, there are now %d warnings\n", count($warnings));
////    foreach($warnings as $warning) {
////      printf(" [%s] %s\n", $warning->getReason(), $warning->getMessage());
////    }
//  }

//  public function insertProduct(
//      Google_Service_ShoppingContent_Product $product) {
//     $response = $this->session->service->products->insert(
//        $this->session->merchantId, $product);
//  }
//  public function getProduct($offerId) {
//    $productId = $this->buildProductId($offerId);
//    $product = $this->session->service->products->get(
//        $this->session->merchantId, $productId);
//    printf("Retrieved product %s: '%s'\n", $product->getId(),
//        $product->getTitle());
//  }
//public function insertProductBatch($products) {
//    $entries = [];
//
//    foreach ($products as $key => $product) {
//      $entry = new Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry();
//      $entry->setMethod('insert');
//      $entry->setBatchId($key);
//      $entry->setProduct($product);
//      $entry->setMerchantId($this->session->merchantId);
//
//      $entries[] = $entry;
//    }
//
//    $batchRequest =
//        new Google_Service_ShoppingContent_ProductsCustomBatchRequest();
//    $batchRequest->setEntries($entries);
//
//    $batchResponse =
//        $this->session->service->products->custombatch($batchRequest);
//
//    printf("Inserted %d products.\n", count($batchResponse->entries));
//
//    foreach ($batchResponse->entries as $entry) {
//      if (empty($entry->getErrors())) {
//        $product = $entry->getProduct();
//        printf("Inserted product %s with %d warnings\n", $product->getOfferId(),
//            count($product->getWarnings()));
//      } else {
//        print ("There were errors inserting a product:\n");
//        foreach ($entry->getErrors()->getErrors() as $error) {
//          printf("\t%s\n", $error->getMessage());
//        }
//      }
//    }
//  }

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

    public function deleteProduct($offerId)
    {
        $productId = $this->buildProductId($offerId);
        // The response for a successful delete is empty
        $this->session->service->products->delete($this->session->merchantId, $productId);
    }

    private function buildProductId($offer_id)
    {
        return sprintf('%s:%s:%s:%s', self::CHANNEL, self::CONTENT_LANGUAGE,
            self::TARGET_COUNTRY, $offer_id);
    }

    public function createProduct($product_id, $product_data)
    {
        $product = new Google_Service_ShoppingContent_Product();
        $product_url = fn_google_merchant_fetch_product_url($product_id);
        $isset_image = fn_google_merchant_fetch_images_url($product_id);

        $product_name = $product_data["product"];
        $product_price = $product_data["price"];

        if (isset($product["brand"]))
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
    }

    public function DeleteBatch($products)
    {
        $p = [];
        foreach ($products as $key => $offerId) {
            $entry =
                new Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry();
            $entry->setMethod('delete');
            $entry->setBatchId($key);
            $entry->setProductId($this->buildProductId($offerId));
            $entry->setMerchantId($this->session->merchantId);
            $p[] = $entry;
        }
        $batchRequest = new Google_Service_ShoppingContent_ProductsCustomBatchRequest();
        $batchRequest->setEntries($p);
        $batchResponses = $this->session->service->products->custombatch($batchRequest);
    }
}


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

    public function buildProductId($offer_id)
    {
        return sprintf('%s:%s:%s:%s', self::CHANNEL, self::CONTENT_LANGUAGE,
            self::TARGET_COUNTRY, $offer_id);
    }

}


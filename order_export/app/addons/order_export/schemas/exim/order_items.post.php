<?php

use Tygh\Registry;

include_once(Registry::get('config.dir.addons') . 'order_export/func.php');

    $schema['export_fields']['Product Name'] = array(
        'process_get' => array('fn_order_export_get_product_name','#key'),
        'linked' => false,
        'export_only' => true,
    );

return $schema;


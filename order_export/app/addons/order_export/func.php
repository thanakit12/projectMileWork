<?php
if (!defined('AREA')) {
    die('Access denied');
}

function fn_order_export_get_product_name($item_id)
{
    $query = db_get_row("SELECT product FROM ?:order_details
                        inner JOIN
                        ?:product_descriptions
                        ON
                        ?:order_details.product_id = ?:product_descriptions.product_id
                        WHERE ?:order_details.item_id = ?i
                        and ?:product_descriptions.lang_code = 'th'", $item_id);
    return $query['product'];
}

function fn_order_export_timestamp_to_date($timestamp)
{
    return !empty($timestamp) ? date('d/m/Y H:i:s', intval($timestamp)) : '';
}

function fn_order_export_get_status_desc($order_id)
{

    $query = db_get_row("select description from ?:orders INNER JOIN
             ?:statuses
              ON
             ?:statuses.status = ?:orders.status
              INNER JOIN
              ?:status_descriptions
              ON
              ?:status_descriptions.status_id = ?:statuses.status_id
              WHERE
              order_id = ?i
              and ?:status_descriptions.lang_code = 'th'
              and type = 'O'", $order_id);

    return $query['description'];
}

function fn_order_export_order_history($order_id)
{
    $params = array(
        "order_id" => $order_id,
        "page" => "1"
    );
    $result = [];
    $time = '';
    $result = fn_get_order_history($params, 0);
    $j = 0;
    for ($i = 0; $i < count($result); $i++) {
        $response = $result[$i][$j]["content"]["status_to"];
        if ($response == 'C') {
            $time = $result[$i][$j]["timestamp"];
        }
        $j++;
    }

    $finish_date = fn_order_export_timestamp_to_date($time);

    return $finish_date;
}

function fn_generateCsv($data, $delimiter = ',', $enclosure = '"')
{
    $contents = '';
    $handle = fopen('php://temp', 'r+');
    foreach ($data as $line) {
        fputcsv($handle, $line, $delimiter, $enclosure);
    }
    rewind($handle);
    while (!feof($handle)) {
        $contents .= fread($handle, 8192);
    }
    fclose($handle);
    return $contents;
}

function fn_order_export_shipment_method($order_id)
{
    $query = db_get_row("SELECT shipping FROM ?:orders inner JOIN ?:shipping_descriptions ON ?:orders.shipping_ids = ?:shipping_descriptions.shipping_id 
              WHERE ?:orders.order_id = ?i 
              and ?:shipping_descriptions.lang_code = 'th'", $order_id);

    return $query['shipping'];
}

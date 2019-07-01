<?php

use Tygh\Registry;
use Elasticsearch\ClientBuilder;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_mw_elastic_get_products($params, $fields, $sortings, $condition, $join, $sorting, $group_by, $lang_code, $having)
{

}


function fn_elastic_search($word)
{
    $path = dirname(__FILE__);
    require ($path."/vendor/autoload.php");

    $hosts = [
        // This is effectively equal to: "https://username:password!#$?*abc@foo.com:9200/elastic"
        [
            'host' => 'localhost',
            'port' => '9200',
            'scheme' => 'https'
        ]];

    $client = ClientBuilder::create()           // Instantiate a new ClientBuilder
    ->setHosts($hosts)      // Set the hosts
    ->build();

    $params = [
        'index' => 'product',
        'body'  => [
            'query' => [
                'match' => [
                    'Product name' => $word
                ]
            ]
        ]
    ];
    if ($client) {
        echo 'connected';
        $response = $client->search($params);
        echo "<br/>";
        fn_print_r($response) ;
    }
}
?>
<?php

require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

//connect elastic
$hosts = [
    // This is effectively equal to: "https://username:password!#$?*abc@foo.com:9200/elastic"
    [
        'host' => '76b20857ce18454bb9198f44ca7b7899.ap-southeast-1.aws.found.io',
        'port' => '9243',
        'scheme' => 'https',
        'user' => 'elastic',
        'pass' => 'STajmYSiSJ9GYcYrJ1FBYatd'
    ]];

    $client = ClientBuilder::create()           // Instantiate a new ClientBuilder
    ->setHosts($hosts)      // Set the hosts
    ->build();

    if($client)
        echo "Success";
    echo "test";
?>
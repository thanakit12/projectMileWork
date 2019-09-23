<?php

$schema['references']['product_popularity'] = array(
    'reference_fields' => array('product_id' => '#key'),
    'join_type' => 'LEFT'
);

$schema['export_fields']['Viewed'] = array(
    'table' => 'product_popularity',
    'db_field' => 'viewed',
);

$schema['export_fields']['Add To Cart'] = array(
    'table' => 'product_popularity',
    'db_field' => 'added',
);

$schema['export_fields']['del From Cart'] = array(
    'table' => 'product_popularity',
    'db_field' => 'deleted',
);

$schema['export_fields']['bought'] = array(
    'table' => 'product_popularity',
    'db_field' => 'bought',
);

return $schema;
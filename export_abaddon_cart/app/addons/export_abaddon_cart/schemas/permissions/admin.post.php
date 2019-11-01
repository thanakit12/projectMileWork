<?php

$schema['export_abaddon_cart'] = array(
    'permissions' =>  array ('GET' => 'view_abaddon_cart', 'POST' => 'manage_abaddon_cart')
);

$schema['exim']['modes']['export']['param_permissions']['section']['abaddon_cart'] = 'view_abaddon_cart';

return $schema;

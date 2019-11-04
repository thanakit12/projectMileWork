<?php

$schema['export_abaddon_cart'] = array(
    'permissions' =>  array ('GET' => 'view_abaddoned_carts', 'POST' => 'manage_abaddoned_carts')
);

$schema['exim']['modes']['export']['param_permissions']['section']['abaddoned_carts'] = 'view_abaddoned_carts';

return $schema;

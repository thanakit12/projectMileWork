<?php

$schema['export_abandoned_carts'] = array(
    'permissions' =>  array ('GET' => 'view_abandoned_carts_carts', 'POST' => 'manage_abandoned_carts_carts')
);

$schema['exim']['modes']['export']['param_permissions']['section']['abandoned_carts'] = 'view_abandoned_carts';

return $schema;

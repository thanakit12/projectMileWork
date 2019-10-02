<?php

use Tygh\Registry;

if (Registry::get('addons.blog_extend_permission.permission_vendor') == 'Y') {
    $schema['controllers']['pages'] = array(
        'permissions' => false,
    );
}
else
    $schema['controllers']['pages'] = array(
        'permissions' => true,
    );

return $schema;
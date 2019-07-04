<?php

if ( !defined('AREA') ) { die('Access denied'); }

function fn_error_pages_addon_get_route_runtime(&$req, $area, $result, $is_allowed_url, $controller, $mode, $action, $dispatch_extra, $current_url_params, &$current_url)
{
    //check url if url exists return true else return false then redirect to index
    if(!$is_allowed_url)
    {
        $req["dispatch"] = "index.index";
    }
}
?>
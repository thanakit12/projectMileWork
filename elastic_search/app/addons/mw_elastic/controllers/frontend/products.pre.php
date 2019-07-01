<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if($mode == "search")
{
    $params = $_REQUEST;
   // fn_print_r($params);
    $search_word = $_REQUEST['q'];
    //fn_print_r($search_word);

    $elastic = fn_elastic_search($search_word);
    fn_print_r($elastic);


}
?>
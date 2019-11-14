<?php


define('AREA', 'A');
define('ACCOUNT_TYPE', 'admin');

require_once('../../../init.php');

$date = new DateTime();
date_default_timezone_set("Asia/Bangkok");
$dt_begin = $date->format('Y-m-d 00:00:00');
$dt_end   = $date->format('Y-m-d 23:59:59');

fn_google_merchant_cron_job($dt_begin,$dt_end);














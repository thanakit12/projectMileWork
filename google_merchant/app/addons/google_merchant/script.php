<?php


define('AREA', 'A');
define('ACCOUNT_TYPE', 'admin');

require_once('../../../init.php');

date_default_timezone_set("Asia/Bangkok");
$date = new DateTime();

//SET job at 24:00:00 of next day
$date_begin = date("Y-m-d 00:00:00", strtotime("yesterday"));
$date_end   = date("Y-m-d 23:59:59", strtotime("yesterday"));

fn_google_merchant_cron_job($date_begin,$date_end);














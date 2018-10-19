<?php

// Test code

define('PAYTM_ENVIRONMENT', 'Retail'); // PROD
define('PAYTM_MERCHANT_KEY', 'Ezo2fe!K%ky5PNbP'); //Change this constant's value with Merchant key downloaded from portal
define('PAYTM_MERCHANT_MID', 'SANKSH35528775090099'); //Change this constant's value with MID (Merchant ID) received from Paytm
define('PAYTM_MERCHANT_WEBSITE', 'WEB_STAGING'); //Change this constant's value with Website name received from Paytm
define('PAYTM_INDUTSTRY_TYPE_ID', 'Retail'); 
define('PAYTM_CHANNEL_ID', 'WEB'); 
$PAYTM_DOMAIN = 'pguat.paytm.com';
/*
define('PAYTM_ENVIRONMENT', 'PROD'); // PROD
define('PAYTM_MERCHANT_KEY', '4!UuxJWbGAisZ@X3'); //Change this constant's value with Merchant key downloaded from portal
define('PAYTM_MERCHANT_MID', 'SaNksh33768522158185'); //Change this constant's value with MID (Merchant ID) received from Paytm
define('PAYTM_MERCHANT_WEBSITE', 'SaNkshWEB'); //Change this constant's value with Website name received from Paytm
define('PAYTM_INDUTSTRY_TYPE_ID', 'Retail109'); 
define('PAYTM_CHANNEL_ID', 'WEB'); 
$PAYTM_DOMAIN = 'secure.paytm.in';*/

define('PAYTM_REFUND_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/REFUND');
define('PAYTM_STATUS_QUERY_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_STATUS_QUERY_NEW_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/getTxnStatus');
define('PAYTM_TXN_URL', 'https://'.$PAYTM_DOMAIN.'/oltp-web/processTransaction');

// Create connection
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$db = "mockexam_db";
/*$dbhost = "localhost";
$dbuser = "siddh1lt_exam";
$dbpass = "TsafHGgKgU#W";
$db = "siddh1lt_mock_exam_db";*/

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $db);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


?>
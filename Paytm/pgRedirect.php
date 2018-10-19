<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

// following files need to be included
require_once("./lib/config_paytm.php");
require_once("./lib/encdec_paytm.php");

$checkSum = "";
$paramList = array();

$posts = array();
$ORDER_ID = '';
$CUST_ID = '';
$INDUSTRY_TYPE_ID = '';
$CHANNEL_ID = '';
$TXN_AMOUNT = '';
$TXN_PAYMENT_ID = '';

if ($_POST["TXN_PAYMENT_ID"] != '') {
    $selQuy = "SELECT spd.*,s.stud_email,s.stud_contact FROM vid_student_payment_details as spd inner join vid_student as s ON s.stud_id = spd.stud_id where spd.payment_id = ".$_POST["TXN_PAYMENT_ID"]." ORDER BY spd.payment_id DESC limit 0,1";
    $result = mysqli_query($link,$selQuy);

    $paymentdata = mysqli_fetch_array($result);
    /*print_r($paymentdata);
    die();*/
    if ($paymentdata['payment_id'] == $_POST["TXN_PAYMENT_ID"]) {
        $ORDER_ID = $paymentdata['txnid'];
        $CUST_ID = $paymentdata['stud_id'];
        $INDUSTRY_TYPE_ID = PAYTM_INDUTSTRY_TYPE_ID;
        $CHANNEL_ID = PAYTM_CHANNEL_ID;
        $TXN_AMOUNT = $paymentdata['amount'];
        $TXN_PAYMENT_ID = $paymentdata['payment_id'];   
        $TXN_PAYMENT_EMAIL_ID = $paymentdata['stud_email'];   
        $TXN_PAYMENT_CONTACT = $paymentdata['stud_contact'];   
    }
}

// Create an array having all required parameters for creating checksum.
$paramList["MID"] = PAYTM_MERCHANT_MID;
$paramList["ORDER_ID"] = $ORDER_ID;
$paramList["CUST_ID"] = $CUST_ID;
$paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
$paramList["CHANNEL_ID"] = $CHANNEL_ID;
$paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
$paramList["MOBILE_NO"] = $TXN_PAYMENT_CONTACT;
$paramList["EMAIL"] = $TXN_PAYMENT_EMAIL_ID;
//$paramList["EMAIL"] = 'pratk3892@gmail.com';
$paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
//$paramList["CALLBACK_URL"] = 'http://mockexam.mockexam.org//index.php/dashboard/paymentsuccess?paymentid='.$TXN_PAYMENT_ID;
$paramList["MERC_UNQ_REF"] =$ORDER_ID."_".$CUST_ID."_".$TXN_AMOUNT;

//$_POST["paymentid"]="asdf";
$paramList["CALLBACK_URL"] = 'http://localhost/mockexam/index.php/dashboard/paymentsuccess?paymentid='.$TXN_PAYMENT_ID;	


/*
$paramList["MSISDN"] = $MSISDN; //Mobile number of customer
$paramList["EMAIL"] = $EMAIL; //Email ID of customer
$paramList["VERIFIED_BY"] = "EMAIL"; //
$paramList["IS_USER_VERIFIED"] = "YES"; //
*/

//Here checksum string will return by getChecksumFromArray() function.
$checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
/*
//print_r($checkSum);
$responseParamList = array();


//$checkSum = getChecksumFromArray($paramList, PAYTM_MERCHANT_KEY);
$paramList['CHECKSUMHASH'] = urlencode($checkSum);

$data_string = "JsonData=".json_encode($paramList);
echo $data_string;

$ch = curl_init();                    // initiate curl
$url = "https://pguat.paytm.com/oltp/HANDLER_INTERNAL/getTxnStatus?"; // where you want to post data






//$url = "https://secure.paytm.in/oltp/HANDLER_INTERNAL/getTxnStatus?"; // where you want to post data
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); // define what you want to post
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
$headers = array();
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$output = curl_exec($ch); // execute
$info = curl_getinfo($ch);

echo $output;
$data = json_decode($output, true);
echo "<pre>";
print_r($data);
echo "</pre>";
die();
*/
?>
<html>
<head>
<title>Merchant Check Out Page</title>
</head>
<body>
	<center><h1>Please do not refresh this page...</h1></center>
		<form method="post" action="<?php echo PAYTM_TXN_URL ?>" name="f1">
		<table border="1">
			<tbody>
			<?php
			foreach($paramList as $name => $value) {
				echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
			}
			?>
			<input type="hidden" name="CHECKSUMHASH" value="<?php echo $checkSum ?>">
			</tbody>
		</table>
		<script type="text/javascript">
			document.f1.submit();
		</script>
	</form>
</body>
</html>
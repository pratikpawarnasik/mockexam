<?php

date_default_timezone_set('asia/kolkata');
class Go_model extends CI_Model 
{ 
	static $loggedinerror = "You need to be logged in and have the necessary clearance for this action.";
	static $usermismatcherror = "Given userid is not matched with authcode.";
	static $userinstituteerror = "Institute id is missing for give user.";
	
	/*static $apikey="112182AgH7bWaVEkg5a8e84dd";
	static $senderId = 'VMitra';
	static $domain = "http://api.msg91.com/api/sendhttp.php?";
	*/
	

	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	function GetLoggedinUserid($usersessionid)
	{
		$userid = 0;
		$this->db->select('userid');
		$query = $this->db->get_where('usersession',array('usersessionid' => $usersessionid, 'logoutdate is null'=>null));
        	$sessiondata = $query->row_array();
		if($sessiondata)
		{
			$userid = $sessiondata['userid'];
		}
		return (int)$userid;
	}
	
	function GetLoggedinUserData($usersessionid)
	{
		$userdata = array();
		$this->db->select('userid,usertype');
		$query = $this->db->get_where('usersession',array('usersessionid' => $usersessionid, 'logoutdate'=>null));
        	$sessiondata = $query->row_array();
		if($sessiondata)
		{
			$userdata['userid'] = (int)$sessiondata['userid'];
			$userdata['type'] = (int)$sessiondata['usertype'];
		}
		return $userdata;
	}
	
	function sendSmsModel($mobile,$message)
	{
		$apikey="1121s382AgH7bWasdfasdVEkg5a8e84dd";
	 	$senderId = "VMitra";
			$domain = "http://api.msg91.com/api/sendsdfsdfhttp.php?";
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $domain."sender=".$senderId."&route=4&mobiles=".$mobile."&authkey=".$apikey."&country=91&message=".$message,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_SSL_VERIFYHOST => 0,
			  CURLOPT_SSL_VERIFYPEER => 0,
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			/*if ($err) {
			  echo "cURL Error #:" . $err;
			} else {
			  echo $response;
			}
			die();*/
		return $response;
	}	
	static $header = "<!DOCTYPE html><html><head><meta charset='UTF-8'><meta http-equiv='X-UA-Compatible' content='IE=edge'><meta name='viewport' content='width=device-width, initial-scale=1'><title>*|MC:SUBJECT|*</title></head><body style='background-color:#f3f3f3;margin: 0px;'><center><table style='background-color:#fff; width:45%'><tbody><tr style='background:#383B0A;'><td style='padding:20px; float:right;'><a href='*|ARCHIVE|*' target='_blank' style='color: #ffffff;font-size: 14px;'></a></td></tr><tr><td><table style='padding: 15px;'><tr><td><img src='https://gallery.mailchimp.com/c702d72d55f4f0a37f65611e1/images/1ce92dee-bb5a-44bd-98f0-23422bd45083.jpg' align='center' width='564' style='width:100%; padding-bottom: 0; display: inline !important; vertical-align: bottom;' class='mcnImage'></td></tr>";

	static $centerBody = "";

	static $footer = "<tr><td style='padding-top: 40px;color: #ED1C24;font-weight: normal;text-decoration: underline'><a style='color: #ED1C24;font-weight: normal;text-decoration: underline' href='http://mockexam.mockexam.org/' target='_blank'>mockexam.mockexam.org</a></td></tr><tr><td style='color: #ED1C24;font-weight: normal;text-decoration: underline'><a style='color: #ED1C24;font-weight: normal;text-decoration: underline' href='mailto:support@mockexam.org?subject=Off-line%20(Paper%20%26%20Pen)%20Exam%20Schedule&body=Hello%2C%0A' target='_blank'>support@mockexam.org</a></td></tr><tr> <td style='background-color:#404040;' align='center'> <p style='color:#f2f2f2; font-family: Helvetica;font-size: 14px;font-weight:bold;margin: 10px; line-height: 150%;' align='center'>mockexam.org is the largest education portal in Maharashtra. It is an extensive search engine for anyone who wants precise, authentic and up to date information on everything related to education, jobs & careers, at all levels, all streams in India & Abroad. </p></td></tr><tr> <td> <div style='padding:13px; width: 140px; background-color:#ED1C24;border-collapse: separate !important;border-radius: 3px;'><a title='mockexam' href='http://mockexam.org/' target='_blank' style='font-family: Arial;font-size: 16px;font-weight: bold;text-decoration: none;color: #FFFFFF;'>mockexam.org</a></div></td></tr><tr> <td style='padding-top: 20px;'> <center> <table width='50%'> <tr> <td> <a href='mailto:info@mockexam.org' target='_blank'><img src='https://cdn-images.mailchimp.com/icons/social-block-v2/color-forwardtofriend-48.png' style='display:block;' height='24' width='24' class=''></a> </td><td> <a href='https://www.facebook.com/mockexam.ORG' target='_blank'><img src='https://cdn-images.mailchimp.com/icons/social-block-v2/color-facebook-48.png' style='display:block;' height='24' width='24' class=''></a> </td><td> <a href='https://twitter.com/@mockexam' target='_blank'><img src='https://cdn-images.mailchimp.com/icons/social-block-v2/color-twitter-48.png' style='display:block;' height='24' width='24' class=''></a> </td><td> <a href='http://mockexam.org/' target='_blank'><img src='https://cdn-images.mailchimp.com/icons/social-block-v2/color-link-48.png' style='display:block;' height='24' width='24' class=''></a> </td><td> <a href='https://www.youtube.com/watch?v=PimdfgUeqozswI' target='_blank'><img src='https://cdn-images.mailchimp.com/icons/social-block-v2/color-youtube-48.png' style='display:block;' height='24' width='24' class=''></a> </td><td> <a href='https://plus.google.com/118032303455987571980' target='_blank'><img src='https://cdn-images.mailchimp.com/icons/social-block-v2/color-googleplus-48.png' style='display:block;' height='24' width='24' class=''></a> </td></tr></table> </center> </td></tr><tr> <td>&nbsp;</td></tr></table></td></tr><tr style='background-color:#000;'> <td align='center'> <table style='color:#fff; font-size: 13px;'> <tr> <td align='center' style='color:#fff; font-family: Helvetica;'>Contact us -- contact@mockexam.org / info@mockexam.org</td></tr><tr> <td align='center' style='color:#fff; padding-top: 20px;'><a style='color:#fff; font-family: Helvetica;' href='http://www.mockexam.org/universities'>TOP UNIVERSITIES</a> |<a style='color:#fff; font-family: Helvetica;' href='http://www.mockexam.org/colleges'>TOP COLLEGES</a> |<a style='color:#fff; font-family: Helvetica;' href='http://www.mockexam.org/entrance_exams'>TOP ENTRANCE EXAM</a> |<a style='color:#fff; font-family: Helvetica;' href='http://www.mockexam.org/rank_predictor'>RANK PREDICTOR</a></td></tr><tr> <td align='center' style='color:#fff !important;'>© Copyright&nbsp;<a style='color:#fff; font-family: Helvetica;' href='http://www.mockexam.org'>mockexam</a>&nbsp;2018. All Rights Reserved <br></td></tr><tr> <td align='center'>Click here for <a style='color:#fff; font-family: Helvetica;' href='mailto:contact@mockexam.org?subject=Unsubscribe%20me&amp;body=Hello%20Sir%2C%20' target='_blank'>Unsubscribe</a></td></tr></table> </td></tr></tbody></table></center></body></html>";
}
?>
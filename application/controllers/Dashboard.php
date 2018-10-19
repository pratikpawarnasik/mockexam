<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
require_once("./Paytm/lib/config_paytm.php");
require_once("./Paytm/lib/encdec_paytm.php");
class Dashboard extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Dashboard_model');
	}
	
	public function _remap($method, $param)
	{
		$type = strtolower($_SERVER['REQUEST_METHOD']);
		$method = $method."_".$type;
		if (method_exists($this, $method))
		{
			return $this->$method($param);
		}
		else
		{
			$this->load->view('pagenotfound',null);
		}
	}
	
	public function index_get()
	{
		
	}
	
	public function dashboardCounts_get()
	{
		$data = array();

		$data['userid'] = $this->get('userid');
		//$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");

		$countdata = $this->Dashboard_model->studDashboardCounts($data, $errormessage);
		if(isset($countdata) && count($countdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['countdata'] = $countdata;
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function course_get()
	{
		$data = array();
		$data['catid'] = $this->get('catid');
		$coursedata = $this->Dashboard_model->getCourse($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['course'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function coursePrice_get()
	{
		$data = array();
		$errormessage = '';
		$data['id'] = $this->get('id');
		$coursedata = $this->Dashboard_model->coursePrice($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['course'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function orderSummary_get()
	{
		$data = array();
		$errormessage = '';
		$data['userid'] = $this->get('userid');
		$orderdata = $this->Dashboard_model->orderSummary($data, $errormessage);
		if(isset($orderdata) && count($orderdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['order'] = $orderdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function tempStudentCourse_post()
	{ 
		$course_id=$this->post('courseid'); 

		$data = array();
		$errormessage = ''; 
		$data['courseid'] = $course_id;
		$data['userid'] = $this->post('userid');
		$tempid = $this->Dashboard_model->tempStudentCourse($data, $errormessage);
		if(isset($tempid) && count($tempid) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $tempid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function tempStudentExam_post()
	{ 
		$course_id=$this->post('schedule'); 

		$data = array();
		$errormessage = ''; 
		$data['schedule'] = $course_id;
		$data['userid'] = $this->post('userid');
		$tempid = $this->Dashboard_model->tempStudentExam($data, $errormessage);
		if(isset($tempid) && count($tempid) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $tempid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function payAmount_post()
	{
		$data = array();
		$errormessage = '';
		$data['userid'] = $this->post('userid');
		$data['orderData'] = $this->post('orderData');
		$data['totalAmount'] = $this->post('totalAmount');
		$data['promocodeid'] = $this->post('promocodeid');
		$tempid = $this->Dashboard_model->payAmount($data, $errormessage);
		if(isset($tempid) && count($tempid) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $tempid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function paymentsuccessapp_post()
	{
		$status=$_POST["status"];
		$firstname=$_POST["firstname"];
		$amount=$_POST["amount"];
		$txnid=$_POST["txnid"];
		$posted_hash=$_POST["hash"];
		$key=$_POST["key"];
		$productinfo=$_POST["productinfo"];
		$email=$_POST["email"];
		$userid=$_POST["udf1"];
		$promocodeid=$_POST["udf2"];
		$disval=$_POST["udf3"];
		$paymid=$_POST["udf4"];
		$salt="yIEkykqEH3";
		//$salt="XhDCpjMhgI";

		$errormessage= '';
	   	  $order = array();
	   	  $order['userid'] = $userid;
	      $_POST['orderData'] = $this->Dashboard_model->orderSummary($order, $errormessage); 
	      $_POST['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
	   	  $paymentid = $this->Dashboard_model->payAmount($_POST, $errormessage);  
	   	  if($paymentid > 0)
	   	  {
		  	$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $paymentid;
		  }else{
		  	$json = array("status"=>0,"message"=>$errormessage);
		  }	
		
         header('Content-type: application/json');
		 echo json_encode($json);
      
	}
	public function paymentfailureapp_post()
	{

		

		$json = array();
		$json['name'] = "appData_fail";
		$_POST['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
	   	$paymentid = $this->Dashboard_model->UpdatePaymentData($_POST, $errormessage); 
		if($paymentid){
			$json['name'] = "appData_fail";
		}else{
			$json['name'] = "appData_fail";
		}
		header('Content-type: application/json');
		echo json_encode($json);
		}
	public function paymentsuccess_post()
	{
		header("Pragma: no-cache");
		header("Cache-Control: no-cache");
		header("Expires: 0");

		// following files need to be included
		

		$ORDER_ID = "";
		$requestParamList = array();
		$responseParamList = array();

		$requestParamList = array("MID" => $_POST['MID'] , "ORDERID" => $_POST['ORDERID']);

		$checkSum = getChecksumFromArray($requestParamList,PAYTM_MERCHANT_KEY);
		$requestParamList['CHECKSUMHASH'] = urlencode($checkSum);

		$data_string = "JsonData=".json_encode($requestParamList);
		//echo $data_string;


		$ch = curl_init();                    // initiate curl
		$url = "https://pguat.paytm.com/oltp/HANDLER_INTERNAL/getTxnStatus?"; //	 for testing payment module
		
		//$url = "https://secure.paytm.in/oltp/HANDLER_INTERNAL/getTxnStatus?"; //	 where you want to post data
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

		//echo "kkk".$output;
		$data = json_decode($output, true);
	/*		echo "<pre>";
		print_r($_POST);
		echo "<hr>";
		
		print_r($data);
		
		echo "</pre>";
		die();*/
		$status=$_POST["STATUS"];
		$amount=$_POST["TXNAMOUNT"];
		$txnid=$_POST["TXNID"];
		$posted_hash=$_POST["CHECKSUMHASH"];
		$gateway_name=$_POST["GATEWAYNAME"];
		$orderid=$_POST["ORDERID"];
		$responseCode=$_POST["RESPCODE"];

       //if ($responseCode !== '01') {
       if ($data['RESPCODE'] != '01') {

	       header("Location: ".base_url()."paymentfailure");
	   }
	   else 
	   { 

	   	  $getStudData = array();
	   	  $getStudData = $this->Dashboard_model->getStudId($orderid, $errormessage);
	   	   $_POST['userid']=$getStudData['userid'];
	   	   $_POST['payment_id']=$getStudData['payment_id'];
	      $_POST['orderData'] = $this->Dashboard_model->orderSummary($getStudData, $errormessage); 


	      $_POST['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
	   	  $paymentid = $this->Dashboard_model->payAmount($_POST, $errormessage);  
	   	 
	   	  if($paymentid > 0)
	   	  {
		  	header("Location: ".base_url()."paymentsuccess");
		  	//header("Location: http://localhost/mockexam/paymentsuccess");
		  }else{
		  	header("Location: ".base_url()."paymentfailure");
		  	//header("Location: http://localhost/mockexam/paymentfailure");
		  }	   
          /*echo "<h3>Thank You. Your order status is ". $status .".</h3>";
          echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
          echo "<h4>We have received a payment of Rs. " . $amount . ". Your order will soon be shipped.</h4>";*/
       }
    }
    
    public function paymentfailure_get()
	{
		$data = array();
		$data['paymentid'] = $this->get('paymentid');
		
       if ($hash != $posted_hash) {
	       echo "Invalid Transaction. Please try again";
	   }
	   else 
	   { 
	      $_POST['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
	   	  $paymentid = $this->Dashboard_model->UpdatePaymentData($_POST, $errormessage); 
		  header("Location: ".base_url()."index.html#/paymentfailure");
	   	 // header("Location: http://localhost/mockexam/paymentfailure");
	   }	 
     }
    
	public function tempStudentCourseUpdate_put()
	{
		$data = array();
		$errormessage = '';
		$data['id'] = $this->put('id');
		$data['userid'] = $this->put('userid');
		$tempid = $this->Dashboard_model->tempStudentCourseUpdate($data, $errormessage);
		if(isset($tempid) && count($tempid) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $tempid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	

		
	public function removeOrder_delete()
	{
		$data = array();
		$errormessage = '';
		$data['id'] = $this->delete('id');
		$tempid = $this->Dashboard_model->removeOrder($data, $errormessage);
		if(isset($tempid) && count($tempid) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $tempid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function question_get()
	{
		$data = array();
		$data['id'] = $this->get('courseId');
		$coursename = $this->Dashboard_model->getCoursename($data, $errormessage);
		$quesdata = $this->Dashboard_model->getDemoQuestion($data, $errormessage);
		if(isset($quesdata) && count($quesdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['coursename'] = $coursename;
			$json['question'] = $quesdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
			$json['coursename'] = $coursename;
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function option_get()
	{
		$data = array();
		$data['id'] = $this->get('id');
		$optiondata = $this->Dashboard_model->getOption($data, $errormessage);
		if(isset($optiondata) && count($optiondata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['option'] = $optiondata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
}
?>
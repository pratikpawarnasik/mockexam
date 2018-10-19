<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class User extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('User_model');
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
	
	public function MerchantDetail_post()
	{
		$data = array();
		// Merchant key here as provided by Payu
		
		//test
		/*$data['key'] = "hDkYGPQe";
		$data['merchantid'] = 4929687;
		$data['SALT'] = "yIEkykqEH3";
		$PAYU_BASE_URL = "https://test.payu.in";*/
		
		//live
		/*$data['key'] = "XP0XFMsO";
		$data['merchantid'] = 5613422;
		$data['SALT'] = "6T0BDbBZIZ";
		$PAYU_BASE_URL = "https://secure.payu.in";*/
		$data['retail'] = "Retail";
		
		// End point - change to https://secure.payu.in for LIVE mode
		//$PAYU_BASE_URL = "https://test.payu.in";

		$data['action'] = '';
		//$data['hash'] = '';
		$data['amount'] = $this->post('fee');
		$data['firstname'] = $this->post('name');
		//$data['email'] = $this->post('email');
		
		$data['userid'] = $this->post('userid');
		
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		//transaction id
		$data['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
		
		$paymid = $this->User_model->addPaymentTempData($data, $errormessage);
		if($paymid > 0)
		{
			$data['paymentId'] = $paymid;
		}
		else{
			$data['paymentId'] = 0;
		}
		
		// Hash Sequence
		/*$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|paymentId|udf5|udf6|udf7|udf8|udf9|udf10";
		$hashVarsSeq = explode('|', $hashSequence);
	    $hash_string = '';	
		foreach($hashVarsSeq as $hash_var) {
	      $hash_string .= isset($data[$hash_var]) ? $data[$hash_var] : '';
	      $hash_string .= '|';
	    }
	    $hash_string .= $data['SALT'];
	    $data['hash'] = strtolower(hash('sha512', $hash_string));
	    $data['action'] = $PAYU_BASE_URL . '/_payment';
    */
    	header('Content-type: application/json');
		echo json_encode($data);
	}
	
	public function studentMobileLogin_post()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['username'] = $this->post('username');
		$data['ptw'] = $this->post('password');
		$errormessage = "";
		$valid = $this->validateCreateUser($data, $errormessage);
		if($valid)
		{
			$userdata = $this->User_model->studentMobileLogin($data, $errormessage);
			if(isset($userdata) && count($userdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($userdata as $key=>$value)
				{
					$json[$key] = $value;
				}
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function index_post()
	{
		
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['email'] = $this->post('email');
		$data['ptw'] = $this->post('password');
		$data['subdomain'] = $this->post('subdomain');
		$errormessage = "";
		$valid = $this->validateCreateUser($data, $errormessage);
		if($valid)
		{
		
			$userdata = $this->User_model->getLoginUser($data, $errormessage);
			
			if(isset($userdata) && count($userdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($userdata as $key=>$value)
				{
					$json[$key] = $value;
				}
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function masterStudentLogin_post()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['username'] = $this->post('username');
		$data['ptw'] = $this->post('password');
		$data['portal'] = $this->post('portal');
		
		$errormessage = "";
		$valid = $this->validateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['subdomain'] = $this->post('subdomain');
			$userdata = $this->User_model->masterStudentLogin($data, $errormessage);
			if(isset($userdata) && count($userdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($userdata as $key=>$value)
				{
					$json[$key] = $value;
				}
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		/*echo "<pre>";
		print_r($json);
		die();*/
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function checkUserSession_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['userid'] = $this->get('userid');
		$data['usertype'] = $this->get('usertype');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$errormessage = "";
		$valid = $this->validateCreateUser($data, $errormessage);
		if($valid)
		{
			$userid = $this->User_model->checkUserSession($data, $errormessage);
			if(isset($userid) && $userid > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function idelTimeOut_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['userid'] = $this->get('userid');
		$data['usertype'] = $this->get('usertype');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$errormessage = "";
		$valid = $this->validateCreateUser($data, $errormessage);
		if($valid)
		{
			$userid = $this->User_model->idelTimeOut($data, $errormessage);
			if(isset($userid) && $userid > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getConfigValue_get()
	{
		$data = array();
		$errormessage = "";
		$config = $this->User_model->getConfigValue($data, $errormessage);
		
			if(isset($config) && count($config) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				$json['config'] = $config;
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function checkUsername_get()
	{
		$data = array();
		$data['username'] = $this->get('username');
		$errormessage = "";
		$valid = $this->validateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['userid'] = $this->get('userid');
			$result = $this->User_model->checkUsername($data, $errormessage);
		
			if(isset($result) && (int)$result > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function checkSubdomain_get()
	{
		$data = array();
		$data['subdomain'] = $this->get('subdomain');
		$errormessage = "";
		$result = $this->User_model->checkSubdomain($data, $errormessage);
		
			if(isset($result) && (int)$result > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($result as $key=>$value)
				{
					$json[$key] = $value;
				}
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function forgetPasswordRequest_put()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['email'] = $this->put('email');
		$errormessage = "";
			
		$userdata = $this->User_model->forgetPasswordRequest($data, $errormessage);
		if(isset($userdata) && count($userdata) > 0)
		{
			
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = md5((int)$userdata['userid']);
			$json['type'] = (int)$userdata['usertype'];
			$json['contact'] = $userdata['usercontact'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function forgetPasswordMasterStudent_put()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['username'] = $this->put('username');
		
		$data['portal'] = $this->put('portal');
		$data['subdomain'] = $this->put('subdomain');
		$errormessage = "";
			
		$userdata = $this->User_model->forgetPasswordMasterStudent($data, $errormessage);
		if(isset($userdata) && count($userdata) > 0)
		{
			
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = md5((int)$userdata['studid']);
			$json['userid'] = (int)$userdata['studid'];
			$json['contact'] = $userdata['contact'];
			$json['type'] = 3;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	
	public function checktoken_put()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['token'] = $this->put('token');
		$data['type'] = $this->put('type');
		$errormessage = "";
			
		$userdata = $this->User_model->checkToken($data, $errormessage);
		if(count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($userdata as $key=>$value)
			{
				$json[$key] = $value;
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function emailverify_put()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['token'] = $this->put('token');
		$data['type'] = $this->put('type');
		$errormessage = "";
			
		$userid = $this->User_model->emailVerify($data, $errormessage);
		if((int)$userid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id']=(int)$userid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function forgetpassword_put()
	{
		$data = array();
		$data['id'] = $this->put('id');
		$data['type'] = $this->put('type');
		$data['password'] = $this->put('password');
		$data['otp'] = $this->put('otp');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$userid = $this->User_model->forgetpassword($data, $errormessage);
			$valid = ((int)$userid > 0);	
		}
		
		if($valid)
		{
			
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["userid"] = (int)$userid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function otpresend_put()
	{
		$data = array();
		$data['id'] = $this->put('id');
		$data['type'] = $this->put('type');
		$data['contact'] = $this->put('contact');
		$data['message'] = $this->put('message');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$userid = $this->User_model->otpResend($data, $errormessage);
			$valid = ((int)$userid > 0);	
		}
		
		if($valid)
		{
			
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["userid"] = (int)$userid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function changepassword_put()
	{
		$data = array();
		$data['id'] = $this->put('userid');
		$data['password'] = $this->put('password');
		$data['oldpassword'] = $this->put('oldpassword');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$userdata = $this->User_model->ChangePassword($data, $errormessage);
		}
		
		if(isset($userdata) && count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($userdata as $key=>$value)
			{
				$json[$key] = $value;
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function subcribeForm_post()
	{
		$data = array();
		
		$data['email'] = $this->post('email');
		$data['contact'] = $this->post('contact');
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$userdata = $this->User_model->subscribeFormModel($data, $errormessage);
		
		if($userdata)
			{
				$json = array("status"=>200, "message"=>"Thank you for Subscribing.");
				
			}
			else
			{
				$json = array("status"=>0,"message"=>"System error comes to send your contact form");
			}
			
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function contactForm_post()
	{
		$data = array();
		$data['name'] = $this->post('name');
		$data['city'] = $this->post('city');
		$data['email'] = $this->post('email');
		$data['contact'] = $this->post('contact');
		$data['message'] = $this->post('message');
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$userdata = $this->User_model->contactForm($data, $errormessage);
		
		if($userdata)
			{
				$json = array("status"=>200, "message"=>"Thank you for contacting us!");
				
			}
			else
			{
				$json = array("status"=>0,"message"=>"System error comes to send your contact form");
			}
			
		header('Content-type: application/json');
		echo json_encode($json);
	}
		public function feedbackForm_post()
	{
		$data = array();
		$data['name'] = $this->post('name');
		$data['city'] = $this->post('city');
		$data['email'] = $this->post('email');
		$data['contact'] = $this->post('contact');
		$data['message'] = $this->post('message');
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$userdata = $this->User_model->studFeedbackFormModel($data, $errormessage);
		
		if($userdata)
			{
				$json = array("status"=>200, "message"=>"Thank you for your valuable feedback!");
				
			}
			else
			{
				$json = array("status"=>0,"message"=>"System error comes to send your contact form");
			}
			
		header('Content-type: application/json');
		echo json_encode($json);
	}
	private function validateCreateUser($data, &$errormessage)
	{
		$success = true;
		foreach($data as $key=>$val)
		{
			if($val == null || $val == '')
			{
				$errormessage = "$key cannot be empty.";
				$success = false;
				return $success;
			}
		}
		return $success;
	}
	
	private function validateChangePassword($data, &$errormessage)
	{
		$success = true;
		
		if($data["oldPass"] == null)
		{
			$errormessage = "Old password cannot be empty.";
			$success = false;
			return $success;
		}
		
		if($data["newPass"] == null)
		{
			$errormessage = "New password cannot be empty.";
			$success = false;
			return $success;
		}
		return $success;
	}
	
}

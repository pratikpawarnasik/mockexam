<?php

require "Go_model.php";
class User_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	public function sendEmail($to, $subject, $message, $headers)
	{
		if(@mail($to, $subject, $message, $headers))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		return TRUE;
	}
 
	public function getLoginUser($data, &$errormessage)
	{
		$userdata = array();
		//$this->db->select('userid');
		//$query = $this->db->get_where('getallusers',array('useremail' => $data['email'],'password' => $data['ptw']));
		$email = trim($data['email']);
		$ptw = trim($data['ptw']);
		$subdomain = trim($data['subdomain']);
		$query = $this->db->query("call GetLoggedUser('".$email."','".md5($ptw)."')");
        $resultdata = $query->row_array();
        $query->next_result();
        $query->free_result();
        $this->db->reconnect();
        //print_r($resultdata);exit;
		if(count($resultdata) > 0)
			{	
				$userdata["userid"] = (int)$resultdata['userid'];
				
				$userdata["name"] = $resultdata['name'];
				$userdata["email"] = $resultdata['email'];
				$userdata["type"] = (int)$resultdata['type'];
				if($userdata["type"] == 3)
				{
					$userdata["instid"] = $resultdata['instid'];
				}
				else
				{
					$userdata["instid"] = $resultdata['instid'];
				}
				
				$userdata["authcode"] = $resultdata['authcode'];
				$userdata["packageid"] = $resultdata['packageid'];
				$userdata["verify"] = (int)$resultdata['verify_flag'];
				$userdata["regtype"] = (int)$resultdata['register_type'];
				$userdata["contact"] = $resultdata['contact'];
				if($userdata["verify"] == 0 && $userdata["type"] == 3)
				{
					$otp = rand(pow(10, 6-1), pow(10, 6)-1);
					$upddata = array('otp' => $otp);
					$this->db->where(array('stud_id'=>(int)$userdata['userid'],'active' => '1'));
					$result = $this->db->update('student', $upddata);
					if(!$result)
					{
						$userdata = array();
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else{
							//OTP SMS SEND
							$smsmessage = "Hello ".$userdata['name'].",";
							if($userdata["regtype"] == 3)
							{
								$smsmessage .= "Your OTP for change password is -".$otp;
							}
							else{
								$smsmessage .= "Your OTP for registration is -".$otp;
							}
							$smsmessage .= ". Regards,mockexam Team. ";
							$this->sendSmsModel($userdata['contact'],$smsmessage);
							
					}
				}else{
					$userdata["authcode"] = md5(uniqid());
					$updatedata = array('usersessionid'=>$userdata["authcode"],'login_flag'=>'1');
					$this->db->where(array('userid'=>$userdata["userid"],'usertype'=>$userdata["type"]));
					$result = $this->db->update('usersession', $updatedata);
					if(!$result){
						$userdata["authcode"] = $resultdata['authcode'];
					}
				}
			}
			else
			{
				$errormessage = "Wrong Password or Email. Try Again";
			}
						
		return $userdata;
	}
	
	public function masterStudentLogin($data, &$errormessage)
	{
		$userdata = array();
		$username = trim($data['username']);
		$ptw = trim($data['ptw']);
			$this->db->select("s.stud_id as userid,s.stud_name as name,s.stud_email as email,3 as type,s.address as address,u.usersessionid as authcode,s.verify_flag,s.register_type,s.stud_contact as contact,s.prof_pic as imgpath");
			$this->db->from("student as s");
			$this->db->join("usersession as u",'u.userid=s.stud_id and u.usertype = 3','inner');
			$where = " (s.stud_email = '".$username."' OR s.stud_contact = '".$username."' ) ";
			$this->db->where($where);
			$this->db->where(array("s.stud_password"=>md5($ptw)));
			$query = $this->db->get();
	        $resultdata = $query->row_array();
       	if(count($resultdata) > 0)
			{	
				$userdata["userid"] = (int)$resultdata['userid'];
				
				$userdata["name"] = $resultdata['name'];
				$userdata["email"] = $resultdata['email'];
				$userdata["type"] = (int)$resultdata['type'];
				$userdata["address"] = $resultdata['address'];
				$userdata["contact_flag"] = 0;	
				$userdata["instid"] = $resultdata['instid'];
				if($resultdata['imgpath'] == null){
					$userdata["imgpath"] = "images/man.png";
				}else{
					$userdata["imgpath"] = $resultdata['imgpath'];
				}
				$userdata["authcode"] = $resultdata['authcode'];
				$userdata["verify"] = (int)$resultdata['verify_flag'];
				$userdata["regtype"] = (int)$resultdata['register_type'];
				$userdata["contact"] = $resultdata['contact'];
				if($userdata["verify"] == 0 && $userdata["type"] == 3)
				{
					$otp = rand(pow(10, 6-1), pow(10, 6)-1);
					$upddata = array('otp' => $otp);
					$this->db->where(array('stud_id'=>(int)$userdata['userid'],'active' => '1'));
					$result = $this->db->update('student', $upddata);
					if(!$result)
					{
						$userdata = array();
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else{
							//OTP SMS SEND
							$smsmessage = "Hello ".$userdata['name'].",";
							if($userdata["regtype"] == 3)
							{
								$smsmessage .= "Your OTP for change password is -".$otp;
							}
							else{
								$smsmessage .= "Your OTP for registration is -".$otp;
							}
							$smsmessage .= ". Regards,mockexam Team. ";
							$this->sendSmsModel($userdata['contact'],$smsmessage);;
							
					}
				}
				else{
					$userdata["authcode"] = md5(uniqid());
					$updatedata = array('usersessionid'=>$userdata["authcode"],'login_flag'=>'1');
					$this->db->where(array('userid'=>$userdata["userid"],'usertype'=>$userdata["type"]));
					$result = $this->db->update('usersession', $updatedata);
					if(!$result){
						$userdata["authcode"] = $resultdata['authcode'];
					}
				}
			}
			else{
				$errormessage = "Wrong Password or Username. Try Again";
			}
						
		return $userdata;
	}
	
	public function studentMobileLogin($data, &$errormessage)
	{
		$userdata = array();
		//$this->db->select('userid');
		//$query = $this->db->get_where('getallusers',array('useremail' => $data['email'],'password' => $data['ptw']));
		$username = trim($data['username']);
		$ptw = trim($data['ptw']);
		
			$this->db->select("s.stud_id as userid,s.branch_id as instid,s.stud_name as name,s.stud_email as email,3 as type,u.usersessionid as authcode,s.verify_flag,s.register_type,s.stud_contact as contact,i.inst_name,i.inst_logo");
			$this->db->from("student as s");
			$this->db->join("usersession as u",'u.userid=s.stud_id and u.usertype = 3','inner');
			$this->db->join("institute_branch as ib",'ib.branch_id=s.branch_id','inner');
			$this->db->join("institute as i",'i.inst_id=ib.parant_institute','inner');
			$this->db->where(array("s.username"=>$username,"s.branch_id!="=>123456789,"s.stud_password"=>md5($ptw)));
			$query = $this->db->get();
	        $resultdata = $query->row_array();
	        
       	if(count($resultdata) > 0)
			{	
				$userdata["userid"] = (int)$resultdata['userid'];
				
				$userdata["name"] = $resultdata['name'];
				$userdata["email"] = $resultdata['email'];
				$userdata["type"] = (int)$resultdata['type'];
				$userdata["instid"] = $resultdata['instid'];
				
				$userdata["authcode"] = $resultdata['authcode'];
				$userdata["verify"] = (int)$resultdata['verify_flag'];
				$userdata["regtype"] = (int)$resultdata['register_type'];
				$userdata["contact"] = $resultdata['contact'];
				$userdata["inst_name"] = $resultdata['inst_name'];
				$userdata["inst_logo"] = $resultdata['inst_logo'];
				if($userdata["verify"] == 0 && $userdata["type"] == 3)
				{
					$otp = rand(pow(10, 6-1), pow(10, 6)-1);
					$upddata = array('otp' => $otp);
					$this->db->where(array('stud_id'=>(int)$userdata['userid'],'active' => '1'));
					$result = $this->db->update('student', $upddata);
					if(!$result)
					{
						$userdata = array();
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else{
							//OTP SMS SEND
							$smsmessage = "Hello ".$userdata['name'].",";
							if($userdata["regtype"] == 3)
							{
								$smsmessage .= "Your OTP for change password is -".$otp;
							}
							else{
								$smsmessage .= "Your OTP for registration is -".$otp;
							}
							$smsmessage .= ". Regards,mockexam Team. ";
							$this->sendSmsModel($userdata['contact'],$smsmessage);
							
							
					}
				}
				else{
					$userdata["authcode"] = md5(uniqid());
					$updatedata = array('usersessionid'=>$userdata["authcode"],'login_flag'=>'1');
					$this->db->where(array('userid'=>$userdata["userid"],'usertype'=>$userdata["type"]));
					$result = $this->db->update('usersession', $updatedata);
					if(!$result){
						$userdata["authcode"] = $resultdata['authcode'];
					}
				}
			}
			else
			{
				$errormessage = "Wrong Password or Username. Try Again";
			}		
		return $userdata;
	}
	
	
	public function idelTimeOut($data, &$errormessage)
	{
		$user = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0){
			$errormessage = "New user session is created.";
			return $user;
		}
		else if($userid != (int)$data['userid'])		{
			$errormessage = "New user session is created.";
			return $user;
		}
		else{
			$updatedata = array('login_flag'=>'0');
			$this->db->where(array('userid'=>$data['userid'],'usertype'=>$data['usertype']));
			$result = $this->db->update('usersession', $updatedata);
			if($result){
				return $user = $userid;
			}else{
				$errormessage = "Some unknown error has occurred. Please try again.";
				return $user;
			}
		}
	}
	
	public function checkUserSession($data, &$errormessage)
	{
		$user = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0){
			$errormessage = "New user session is created.";
			return $user;
		}
		else if($userid != (int)$data['userid'])		{
			$errormessage = "New user session is created.";
			return $user;
		}
		else{
			return $user = $userid;
		}
	}
	
	public function forgetPasswordRequest($data, &$errormessage)
	{
		$where = '(useremail="'.$data['email'].'" or usercontact = "'.$data['email'].'")';
		$query = $this->db->get_where('getallusers',$where);
        $userdata = $query->row_array();  
			
			if($userdata)
			{
				if((int)$userdata['usertype'] == 3)
				{
					$otp = rand(pow(10, 6-1), pow(10, 6)-1);
					$upddata = array('otp' => $otp,'forget_flag' => '1');
					$this->db->where(array('stud_id'=>(int)$userdata['userid']));
					$this->db->update('student', $upddata);
					if(!$this->db->affected_rows() > 0)
					{
						$userdata = array();
						$errormessage = "This user account is not verified.";
					}
					else{
							//OTP SMS SEND
							$smsmessage = "Hello ".$userdata['username'].",";
							$smsmessage .= "Your OTP for forgot password is -".$otp;
							$smsmessage .= ". Regards,mockexam Team. ";
							$this->sendSmsModel($userdata['usercontact'],$smsmessage);

					}
				}

				else
				{
					$userdata = array();
					$errormessage = "This user not allow to forgot password.";
				}
			}
			else
			{
				$userdata = array();
				$errormessage = "This email or phone is not Registered.";
			}				
		return $userdata;
	}
	
	public function forgetPasswordMasterStudent($data, &$errormessage)
	{
		if($data['portal'] == 'main')
		{
			$this->db->select('stud_id as studid,stud_name as studname,stud_contact as contact');
			//$where = 'stud_email="'.$data['username'];
			$this->db->where(array("stud_contact"=>$data['username']));
			$query = $this->db->get_where('student',$where);
	        $userdata = $query->row_array();  
		}
		
			if($userdata)
			{
					$otp = rand(pow(10, 6-1), pow(10, 6)-1);
					$upddata = array('otp' => $otp,'forget_flag' => '1');
					$this->db->where(array('stud_id'=>(int)$userdata['studid']));
					$this->db->update('student', $upddata);
					if(!$this->db->affected_rows() > 0)
					{
						$userdata = array();
						$errormessage = "This user account is not verified.";
					}
					else{
							//OTP SMS SEND
							$smsmessage = "Hello ".$userdata['studname'].",";
							$smsmessage .= "Your OTP for forgot password is -".$otp;
							$smsmessage .= ". Regards,mockexam Team. ";
							$this->sendSmsModel($data['username'],$smsmessage);

					}
			}
			else
			{
				$userdata = array();
				$errormessage = "This username is not Registered.";
			}				
		return $userdata;
	}
	
	public function checkToken($data, &$errormessage)
	{
		$userdata = array();
		
		if($data['type'] == "fi2")
		{
			$this->db->select('inst_id as id,inst_contact_number as contact,2 as type');
			$query = $this->db->get_where('institute',array('md5(inst_id)' => $data['token']));
	        $resultdata = $query->row_array();
			
			if(!$resultdata)
			{
				$errormessage = "This token id is either invalid or used.";
			}
			else{
				$userdata = $resultdata;
			}
		}
		else
		if($data['type'] == "fb4")
		{
			$this->db->select('branch_id as id,contact_number as contact,4 as type');
			$this->db->from('institute_branch');
			$this->db->where('(forget_flag = "1" or verify_flag = "0")');
			$this->db->where(array('md5(branch_id)' => $data['token']));
			$query = $this->db->get();
	        $resultdata = $query->row_array();
			
			if(!$resultdata)
			{
				$errormessage = "This token id is either invalid or used.";
			}
			else{
				$userdata = $resultdata;
			}
		}
		else
		if((int)$data['type'] == "fs3")
		{
			$this->db->select('stud_id as id,stud_contact as contact,3 as type');
			$this->db->from('student');
			$this->db->where('(forget_flag = "1" or (register_type = 3 and verify_flag = "0"))');
			$this->db->where(array('md5(stud_id)' => $data['token']));
			$query = $this->db->get();
	        $resultdata = $query->row_array();
			
			if(!$resultdata)
			{
				$errormessage = "This token id is either invalid or used.";
			}
			else{
				$userdata = $resultdata;
			}
		}	
		return $userdata;
	}	
	
	public function emailVerify($data, &$errormessage)
	{
		$userid = 0;
		
		
		if((int)$data['type'] == "fs3")
		{
			$this->db->select('stud_id');
			$query = $this->db->get_where('student',array('md5(stud_id)' => $data['token'],'email_flag'=>'0'));
	        $resultdata = $query->row_array();
			
			if(!$resultdata)
			{
				$errormessage = "This token id is either invalid or used.";
			}
			else{
				$upddata = array('email_flag' => '1');
					$this->db->where(array('stud_id'=>(int)$resultdata['stud_id'],'active' => '1'));
					$result = $this->db->update('student', $upddata);
					if(!$result)
					{
						$userdata = array();
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else
					{
						$userid = (int)$resultdata['stud_id'];
					}
				
			}
		}
		else{
			$errormessage = "Some unknown error has occurred. Please try again.";
		}	
		return $userid;
	}	
	
	public function forgetpassword($data, &$errormessage)
	{
		$userid = 0;
		
		if($data['type'] == "fs3")
		{
			$this->db->select('stud_id');
			$query = $this->db->get_where('student',array('otp' => (int)$data['otp'],'md5(stud_id)' => $data['id']));
	        $userdata = $query->row_array();
			if($userdata)
			{
				$updata = array('stud_password' => md5($data['password']),'forget_flag' => '0','verify_flag'=>'1');
				$this->db->where(array('stud_id'=> (int)$userdata['stud_id'],'otp' => (int)$data['otp'], 'active' => '1'));
				$result = $this->db->update('student', $updata);
				if(!$result)
				{
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
				else
				{
					$userid = (int)$userdata['stud_id']; 
				}
			}
			else
			{
				$errormessage = "This OTP is Invalid. Please try another.";
			}
		}
		
		return $userid;
	}

	public function otpResend($data, &$errormessage)
	{
		$userid = 0;
		
			
		if((int)$data['type'] == 3)
		{
			$this->db->select('stud_id,stud_name,stud_contact');
			$query = $this->db->get_where('student',array('stud_contact' => $data['contact'],'stud_id' => $data['id']));
	        $userdata = $query->row_array();
			if($userdata)
			{
				$otp = rand(pow(10, 6-1), pow(10, 6)-1);
				$updata = array('otp' => $otp);
				$this->db->where(array('stud_id'=> (int)$userdata['stud_id'], 'active' => '1'));
				$result = $this->db->update('student', $updata);
				if(!$result)
				{
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
				else
				{
					$smsmessage = "Hello ".$userdata['stud_name'].",";
					$smsmessage .= $data['message'].$otp;
					$smsmessage .= ". Regards,mockexam Team. ";
					$this->sendSmsModel($userdata['stud_contact'],$smsmessage);

				}
			}
			else
			{
				$errormessage = "This userid is Invalid. Please try another.";
			}
		}
		
		return $userid;
	}
	
	public function addPaymentTempData($data, &$errormessage)
	{
		$paymid = 0;
			$paymdata = array('txnid' => $data['txnid'], 'stud_id' => $data['userid'], 'amount' => $data['amount'], 'payment_date' => $data['createddate']);
			
			$result = $this->db->insert('student_payment_details', $paymdata);
			if($result)
			{
				$paymid = $this->db->insert_id();
			}
			return $paymid;
	}
	
	public function createToken($data, &$errormessage)
	{
		
			$userdata = array('userid' => $data['userid'], 'email' => $data['email'], 'type' => $data['type'], 'token' => $data['token'], 'submitdate' => $data['createddate']);
			$result = $this->db->insert('forget_password', $userdata);
			
			if(!$result)
			{
				$userid = 0;
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$token = $data['token'];
				$to = $data['email'];
				$from = 'contact@mockexam.org';
				$subject = 'Forgot Password Link';

				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				
			
				$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>Forgot Password</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><h3>Dear ".$data['name'].",</h3></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>We have received your request to reset password</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."index.php/forgot?token=".$token."'>Click here to change your password.</a></td></tr>".Go_model::$footer;
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);
				if($sendmail)
				{
					$userid = $this->db->insert_id();
				}
				else
				{
					$userid = 0;
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
			}
			return $userid;
	}
	
	public function getConfigValue($data, &$errormessage)
	{
		$configdata = array();
		
		$query = $this->db->get_where('config_value',array('active'=>'1'));
	    $resultdata = $query->result_array();
			
			if(!$resultdata)
			{
				$errormessage = "This token id is either invalid or used.";
			}
			else{
				$configdata = $resultdata;
			}
		
		return $configdata;
	}
	
	public function checkUsername($data, &$errormessage)
	{
		$configid = 0;
		$this->db->select('username');
		$this->db->from('student');
		if($data['userid'] != null && $data['userid'] != ''){
			$this->db->where(array('stud_id !='=>$data['userid']));
		}
		$this->db->where(array('username'=>$data['username'],'active'=>'1'));
		$query = $this->db->get();
	    $resultdata = $query->row_array();
			
			if($resultdata)
			{
				$errormessage = "That username is taken. Try another.";
			}
			else{
				$configid = 1;
			}
		
		return $configid;
	}
	
	public function checkSubdomain($data, &$errormessage)
	{
		$instdata = 0;
		$query = $this->db->get_where('institute',array('inst_subdomain'=>$data['subdomain'],'active'=>'1'));
	    $resultdata = $query->row_array();
			
			if(!$resultdata)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$instdata = $resultdata;
			}
		
		return $instdata;
	}
	
	public function ChangePassword($data, &$errormessage)
	{
		$userdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $userdata;
		}
		else if($userid != (int)$data['id'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $userdata;
		}
		else
		{
					$this->db->select('master_id');
					$query = $this->db->get_where('master',array('master_id' => $userid, 'password' => md5($data['oldpassword']), 'active' => '1'));
					$tempdata = $query->row_array();
					if(!$tempdata)
					{
						$errormessage = "Given old password is wrong";
						return $userdata;
					}
					
					$upddata = array('password' => md5($data['password']));
					$this->db->where('master_id', $userid);
					$result = $this->db->update('master', $upddata);
				
					if(!$result)
					{
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else
					{
							$userdata["userid"] = (int)$userid;
					}
		}
		
		return $userdata;
	}
	public function subscribeFormModel($data)
	{
			$userdata = array('subscriber_email' => $data['email'], 'subscriber_contact' => $data['contact'],'subscribe_date' => $data['createddate']);
			$result = $this->db->insert('subscriber', $userdata);
			
			if(!$result)
			{
				$sendmail = false;
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{			
				

				$to = 'pratikpawarnasik@gmail.com';
				$from = $data['email'];
				
				
				$subject = 'mockexam Contact Form';

				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				/*$message = '<h2>Thank you, </h3><br>';
				$message .= '<h3>for being with us.</h3><br>';
				$message .= '<h3>Your subscribing mockexam</h3><br>';

				$message .= '<br>Regards,<br>mockexam Team.';
*/
				$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>Subscription</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><h3>Thank you for being with us.</h3></td></tr>".Go_model::$footer;
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);	
			}	
		return $result ;
  }
	public function contactForm($data)
	{
			$userdata = array('name' => $data['name'], 'email' => $data['email'], 'contact' => $data['contact'], 'city' => $data['city'],'contact_date' => $data['createddate'], 'message' => $data['message']);
			$result = $this->db->insert('contact_us', $userdata);
			
			if(!$result)
			{
				$sendmail = false;
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{			
				

				$to = 'pratikpawarnasik@gmail.com';
				$from = $data['email'];
				
				
				$subject = 'mockexam Contact Form';

				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$message = '<h3>Hello Admin, </h3><br>';
				$message .= 'Contact Form Details :<br>';
				$message .= 'Name : '.$data['name'].'<br>';
				$message .= 'Email : '.$data['email'].'<br>';
				$message .= 'Contact : '.$data['contact'].'<br>';
				$message .= 'City : '.$data['city'].'<br>';
				$message .= 'Message : '.$data['message'].'<br>';
				$message .= '<br>Regards,<br>mockexam Team.';
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);	
			}	
		return $result ;
  	}
  	public function studFeedbackFormModel($data)
	{
			$userdata = array('name' => $data['name'], 'email' => $data['email'], 'contact' => $data['contact'], 'city' => $data['city'],'feedback_date' => $data['createddate'], 'message' => $data['message']);
			$result = $this->db->insert('studfeedback', $userdata);
			if(!$result)
			{
				$sendmail = false;
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{			
				$from = 'pratikpawarnasik@gmail.com';
				$to = $data['email'];
				$subject = 'mockexam Contact Form';
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				/*$message = '<h3>Hello '.$data['name'].', </h3><br>';
				$message .= 'Thank you For Your valuable feedback. :<br>';
				$message .= '<br>Regards,<br>mockexam Team.';*/
				$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>Feedback</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><h3>Hello ".$data['name'].",</h3></td></tr>
				<tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><h3>Thank you For Your valuable feedback.</h3></td></tr>".Go_model::$footer;
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);	
			}	
		return $result ;
  	}
	
	/*
		Notificatoion
	*/
	//get user register date
	public function getUserRegDate($data, &$errormessage){
		$regDate = null;
		
		if($data['usertype'] == 3){
			$this->db->select("submitdate");
			$this->db->from("student");
			$this->db->where(array('stud_id'=> $data['userid']));
			$query = $this->db->get();
		    $result = $query->row_array();
		    if($result){
				$regDate = $result['submitdate'];
			}
		}
		
	    return $regDate;
	}
	
		


	
}

/* End of file user_model.php */

/* Location: ./application/models/user_model.php */
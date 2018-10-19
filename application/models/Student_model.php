<?php

require "Go_model.php";
class Student_model extends Go_model 
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
	
	public function bulksms()
	{
		//OTP SMS SEND
				$smsmessage = "Hello ".$data['name'].",";
				$smsmessage .= "Your OTP for registration is -".$otp;
				$smsmessage .= ". Regards,mockexam Team. ";
				$this->sendSmsModel($data['contact'],$smsmessage);
	}
	public function getStateModal($data, &$errormessage)
	{
		$diststatedata = array();
			$this->db->select('state_name,state_id',false);
	        $this->db->from('states');
			$query = $this->db->get();
	        $resultdata = $query->result_array();
		return $resultdata;
	}
	public function getDistrictModal($data, &$errormessage)
	{
		$diststatedata = array();
			$this->db->select('district_name,district_id',false);

	        $this->db->from('district');
	        $this->db->where('state_id', $data['state_id']);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
		return $resultdata;
	}	
	public function createUser($data, &$errormessage)
	{
		$userid = 0;
		$this->db->select('stud_id');
		$where = '( stud_email="'.$data['email'].'" or stud_contact="'.$data['contact'].'" )';
		//$query = $this->db->get_where('getallusers',array('useremail' => $data['email'],'usercontact' => $data['contact']));
		$query = $this->db->get_where('student',$where);
        $userdata = $query->row_array();
		if(!$userdata)
		{
			/*$branch[] = (string)$data['instid'];
			$branch = serialize($branch);*/
			$otp = rand(pow(10, 6-1), pow(10, 6)-1);
			//$otp='123456';
			$userdata = array('stud_name' => $data['name'],
				//'gender' => $data['gender'], 
				'stud_email' => $data['email'],
				'stud_contact' => $data['contact'],
				//'dob' => $data['dob'], 
				'otp'=>$otp ,
				'submitdate' => $data['createddate'],
				'mailStatus'=>0,
				/*'address' => $data['address'],
				'taluka' => $data['taluka'],
				'district' => $data['district'],
				'state' => $data['state'],
				'pin_code' => $data['pin'],
				'standard' => $data['standard'],
				'college_name' => $data['college_name'],
				'college_taluka' => $data['college_taluka'],*/
			 	'active' => '1');
			$result = $this->db->insert('student', $userdata);
			if(!$result){
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{

				$userid = $this->db->insert_id();

				$data['usersessionid'] = md5(uniqid());
				$usersessiondata = array('usersessionid' => $data['usersessionid'], 'userid' => $userid, 'createddate' => $data['createddate'], 'usertype' => 3);
				$result = $this->db->insert('usersession', $usersessiondata);

				//OTP SMS SEND
				$smsmessage = "Hello ".$data['name'].",";
				$smsmessage .= "Your OTP for registration is -".$otp;
				$smsmessage .= ". Regards,mockexam Team. ";				
				$sendsms= $this->sendSmsModel($data['contact'],$smsmessage);


				if ($sendmail) {

					$upddata = array('mailStatus'=>1);
					$this->db->where(array('stud_id'=>(int)$userid));
					$this->db->update('student', $upddata);
					return (int)$userid;
				}
				else{
					
					return (int)$userid;
				}
					
					
				
				if(!$result)
				{
					$userid = 0;
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
			}
		}
		else
		{
			$errormessage = "This email or phone is already exists. Please try another.";
		}
		return $userid;
	}

		public function createDemoStudModel($data, &$errormessage)
	{
		$userid = 0;
		$this->db->select('id');
		$where = '( email="'.$data['email'].'" or contact="'.$data['contact'].'" )';
		//$query = $this->db->get_where('getallusers',array('useremail' => $data['email'],'usercontact' => $data['contact']));
		$query = $this->db->get_where('demo_test_student',$where);
        $userdata = $query->row_array();
		if(!$userdata)
		{
			
			$userdata = array('name' => $data['name'],
				'email' => $data['email'],
				'contact' => $data['contact'],
				'submitdate' => $data['createddate'],
			 	'active' => '1');
			$result = $this->db->insert('demo_test_student', $userdata);
			if(!$result){
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{

				$userid = $this->db->insert_id();

			}
		}
		else
		{
			$errormessage = "You have already taken Demo test. Please register for more exam.";
			//$errormessage = "This email or phone is already exists. Please try another.";
		}
		return $userid;
	}
	public function checkOTP($data, &$errormessage)
	{
		$userdata = array();
		$this->db->select('s.stud_id,s.stud_name,s.address,s.stud_email,u.usersessionid,s.prof_pic as imgpath');
		$this->db->from('student s');
		$this->db->join('usersession AS u', 'u.userid = s.stud_id and u.usertype = 3', 'inner');
		$this->db->where(array('s.stud_id' => (int)$data['userid'],'s.otp'=> (int)$data['otp'], 's.active' => '1'));
		$query = $this->db->get();

		$resultdata = $query->row_array();	
		if($resultdata)
		{								
			$userdata["userid"] = (int)$resultdata['stud_id'];
		}
		else{
			$errormessage = "OTP is invalid. please try again";
		}
			
		return $userdata;
	}
	public function setPassword($data, &$errormessage)
	{
		$userdata = array();
		$this->db->select('s.stud_id,s.stud_name,s.address,s.stud_email,u.usersessionid,s.prof_pic as imgpath,s.stud_contact');
		$this->db->from('student s');
		$this->db->join('usersession AS u', 'u.userid = s.stud_id and u.usertype = 3', 'inner');
		$this->db->where(array('stud_id' => (int)$data['userid']));
		$query = $this->db->get();
		$resultdata = $query->row_array();	
			if($resultdata)
			{
				$upddata = array('verify_flag' => '1','stud_password'=>md5((int)$data['passwordForm']));
				$this->db->where(array('stud_id'=>(int)$data['userid']));
				$result = $this->db->update('student', $upddata);
								
					$userdata["userid"] = (int)$resultdata['stud_id'];
					$userdata["name"] = $resultdata['stud_name'];
					$userdata["email"] = $resultdata['stud_email'];
					$userdata["address"] = $resultdata['address'];
					$userdata["contact_flag"] = 0;
					if($resultdata['imgpath'] == null){
						$userdata["imgpath"] = "images/man.png";
					}else{
						$userdata["imgpath"] = $resultdata['imgpath'];
					}
					$userdata["type"] = 3;
					$userdata["authcode"] = $resultdata['usersessionid'];
					$userdata["authcode"] = md5(uniqid());
					$updatedata = array('usersessionid'=>$userdata["authcode"],'login_flag'=>'1');
					$this->db->where(array('userid'=>$userdata["userid"],'usertype'=>$userdata["type"]));
					$result = $this->db->update('usersession', $updatedata);

				$smsmessage = "Hello ".$resultdata['stud_name'].",";
				$smsmessage .= "Your registration has successfully completed. Your Username is :".$resultdata['stud_contact']." and Password is :".$data['passwordForm'].". " ;
				$smsmessage .= " https://goo.gl/y62kr9. ";				
				$sendsms= $this->sendSmsModel($resultdata['stud_contact'],$smsmessage);
				//email send
				$to = $resultdata['stud_email'];
				$from = 'contact@vidhyarthimitra.org';
				
				$subject = 'Registration on mockexam.org';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>mockexam Registration</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$resultdata['stud_name']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Get ready to experience a whole new way of learning. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Email/Username :".$resultdata['stud_email']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Password :".$data['password']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);
				
					if(!$result){
						$userdata["authcode"] = $resultdata['usersessionid'];
					}
		}
		else{
			$errormessage = "Set password Information not valid. please try again";
		}
			
		return $userdata;
	}
	
	public function changepassword($data, &$errormessage)
	{
		$user = array();
		$query = $this->db->get_where('student',array('otp' => (int)$data['otp'],'stud_id' => $data['id']));
        $userdata = $query->row_array();
		if($userdata)
		{
			$updata = array('stud_password' => md5($data['password']),'verify_flag' => '1');
			$this->db->where(array('stud_id'=> (int)$userdata['stud_id'],'otp' => (int)$data['otp'], 'active' => '1'));
			$result = $this->db->update('student', $updata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
        			$user["authcode"] = md5(uniqid());
					$updatedata = array('usersessionid'=>$user["authcode"],'login_flag'=>'1');
					$this->db->where(array('userid'=>$userdata["stud_id"],'usertype'=>3));
					$result = $this->db->update('usersession', $updatedata);
					if(!$result){
						$this->db->select('usersessionid');
						$query = $this->db->get_where('usersession',array('userid' => (int)$userdata['stud_id'],'usertype' =>3));
			        	$authdata = $query->row_array();
		        		$user['authcode']=$authdata['usersessionid'];
					}
					
				$user["userid"] = (int)$userdata['stud_id'];
				
				$user["name"] = $userdata['stud_name'];
				$user["email"] = $userdata['stud_email'];
				if($userdata['prof_pic'] == null){
						$user["prof_pic"] = "images/man.png";
					}else{
						$user["prof_pic"] = $userdata['prof_pic'];
					}
				$user["type"] = 3;
				$user["verify"] = $userdata['verify_flag'];
				$user["regtype"] = (int)$userdata['register_type'];
				$user["contact"] = (int)$userdata['stud_contact'];
				
				//send mail to institute		
							$to = $userdata['stud_email'];
							$from = 'contact@vidhyarthimitra.org';
							
							$subject = 'Change Password';
							
							$headers = "From: " . strip_tags($from) . "\r\n";
							$headers .= "MIME-Version: 1.0\r\n";
							$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
							
							

							$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>Password Change</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$user['name']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Get ready to experience a whole new way of learning. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Your Password has been change successful.The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Email/Username :".$resultdata['stud_email']."/".$user['contact']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>New Password :".$data['password']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
							$sendmail = $this->sendEmail($to, $subject, $message, $headers);	
			}
		}
		else
		{
			$errormessage = "This OTP is Invalid. Please try another.";
		}
		
		return $user;
	}
	
	public function socialCreateUser($data, &$errormessage)
	{
		$userarray = array();
		$this->db->select('stud_id');
		$where = '( stud_email="'.$data['email'].'" or stud_contact="'.$data['contact'].'" )';
		$query = $this->db->get_where('student',$where);
        $userdata = $query->row_array();
		if(!$userdata)
		{
			$otp = rand(pow(10, 6-1), pow(10, 6)-1);
			$userdata = array('stud_name' => $data['name'], 'stud_email' => $data['email'], 'register_type' => $data['regtype'],'stud_contact' => $data['contact'], 'socialid' =>$data['faceid'],'otp'=>$otp, 'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('student', $userdata);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$userid = $this->db->insert_id();
				$usersessiondata = array('usersessionid' => $data['usersessionid'], 'userid' => $userid, 'createddate' => $data['createddate'], 'usertype' => 3);
				$result = $this->db->insert('usersession', $usersessiondata);
				if($result)
				{
					$social = '';
					if($data['regtype'] == 1) $social="Facebook";
					else $social="Google";
					
					//OTP SMS SEND
					$smsmessage = "Hello ".$data['name'].",";
					$smsmessage .= "Your OTP for registration is -".$otp;
					$smsmessage .= ". Regards,mockexam Team. https://goo.gl/y62kr9.";
					$this->sendSmsModel($data['contact'],$smsmessage);
				
					//send email
					$to = $data['email'];
					$from = 'pratikpawarnasik@gmail.com';
					
					$subject = 'Your account on mockexam';

					$headers = "From: " . strip_tags($from) . "\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
					
					/*$message = '<h3>Hello '.$data['name'].', </h3><br>';
					$message .= '<p>Your account has been created successfully for mockexam as Student</p>';
					$message .= '<p>Using '.$social.' Login.</p><br>';
					$message .= '<p>User Name: '.$data['email'].'</p>';
					$message .= '<p>Contact : '.$data['contact'].'</p>';
					$message .= '<br /><p>OTP :- '.$otp.'</p><br />';
					$message .= '<p><a href="'.base_url().'/emailverify/fs3/'.md5($userid).'" target="_blank">Please Click Here to Email Verification</a></p>';
					$message .= '<br>Regards,<br>mockexam Team.';*/

					$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>Registration With ".$social."</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$data['name']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Your account has been created successfully for mockexam as Student. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>User Name :".$data['email']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Mobile number :".$data['contact']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>OTP :".$otp." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
					$sendmail = $this->sendEmail($to, $subject, $message, $headers);
		
					$userarray['userid']=$userid;
					$userarray['name']=$data['name'];
					$userarray['instid']=$data['instid'];
					$userarray["imgpath"] = "images/man.png";
					$userarray['email']=$data['email'];
					$userarray['contact']=$data['contact'];
					$userarray['type']=3;
					$userarray['authcode']=$data['usersessionid'];
				}
				else {
				$errormessage = "Some unknown error has occurred. Please try again.";
				}
			}
		}
		else{
			$errormessage = "This email is already exists. Please try another.";
		}
		return $userarray;
	}
	
	public function createSocialUser($data, &$errormessage)
	{
			$userarray = array();
			$this->db->select('stud_id');
			$where = '( stud_email="'.$data['email'].'" or stud_contact="'.$data['contact'].'" ) and branch_id = 123456789';
			$query = $this->db->get_where('student',$where);
	        $userdata = $query->row_array();
			if(!$userdata)
			{
				$query = $this->db->get_where('student',array('socialid' => $data['socialid']));
		        $userdata = $query->row_array();
				if($userdata){
					$errormessage = "This user is allready register";
					return $userarray;
				}
				$otp = rand(pow(10, 6-1), pow(10, 6)-1);
				$userdata = array('stud_name' => $data['name'],'branch_id' => $data['instid'], 'stud_email' => $data['email'], 'register_type' => $data['regtype'],'stud_contact' => $data['contact'], 'socialid' =>$data['socialid'],'otp'=>$otp, 'submitdate' => $data['createddate'], 'active' => '1');
				$result = $this->db->insert('student', $userdata);
				
				if(!$result)
				{
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
				else
				{
					$userid = $this->db->insert_id();
					$usersessiondata = array('usersessionid' => $data['usersessionid'], 'userid' => $userid, 'createddate' => $data['createddate'], 'usertype' => 3);
					$result = $this->db->insert('usersession', $usersessiondata);
					if($result)
					{
						$social = '';
						if($data['regtype'] == 1) $social="Facebook";
						else $social="Google";
						
						//OTP SMS SEND
						$smsmessage = "Hello ".$data['name'].",";
						$smsmessage .= "Your OTP for registration is -".$otp;
						$smsmessage .= ". Regards,mockexam Team. ";
						$this->sendSmsModel($data['contact'],$smsmessage);
					
						//send email
						$to = $data['email'];
						$from = 'contact@vidhyarthimitra.org';
						
						$subject = 'Your account on mockexam';

						$headers = "From: " . strip_tags($from) . "\r\n";
						$headers .= "MIME-Version: 1.0\r\n";
						$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
						
						/*$message = '<h3>Hello '.$data['name'].', </h3><br>';
						
						$message .= '<p>Your account has been created successfully for mockexam as Student</p>';
						$message .= '<p>Using '.$social.' Login.</p><br>';
						$message .= '<p>User Name: '.$data['email'].'</p>';
						$message .= '<p>Contact : '.$data['contact'].'</p>';
						$message .= '<br /><p>OTP :- '.$otp.'</p><br />';
						$message .= '<p><a href="'.base_url().'index.html#/emailverify/fs3/'.md5($userid).'" target="_blank">Please Click Here to Email Verification</a></p>';
						$message .= '<br>Regards,<br>mockexam Team.';*/

						$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>Registration With ".$social."</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$data['name']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Your account has been created successfully for mockexam as Student. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>User Name :".$data['email']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Mobile number :".$data['contact']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>OTP :".$otp." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
						$sendmail = $this->sendEmail($to, $subject, $message, $headers);
			
						$userarray['userid']=$userid;
						$userarray['name']=$data['name'];
						$userarray['instid']=$data['instid'];
						$userarray['email']=$data['email'];
						$userarray["imgpath"] = "images/man.png";
						$userarray['contact']=$data['contact'];
						$userarray['type']=3;
						$userarray['verify']=0;
						$userarray['authcode']=$data['usersessionid'];
					}
					else {
					$errormessage = "Some unknown error has occurred. Please try again.";
					}
				}
			}
			else{
				$errormessage = "This email is already exists. Please try another.";
			}				
		return $userarray;
	}
	
	public function getsocialuser($data, &$errormessage)
	{
		$userarray = array();
		$query = $this->db->get_where('student',array('socialid' => $data['faceid']));
        $userdata = $query->row_array();
		if(!$userdata)
		{
			$errormessage = "First register this user";
		}
		else
		{
			$this->db->select('usersessionid');
			$query = $this->db->get_where('usersession',array('userid' => $userdata['stud_id'],'usertype' =>3));
	        $authdata = $query->row_array();
        	
        	$userarray['authcode']=$authdata['usersessionid'];
			$userarray['userid']=$userdata['stud_id'];
			$userarray['instid']=$userdata['branch_id'];
			$userarray['name']=$userdata['stud_name'];
			if($userdata['prof_pic'] == null){
				$userarray["imgpath"] = "images/man.png";
			}else{
				$userarray["imgpath"] = $userdata['prof_pic'];
			}
			$userarray['email']=$userdata['stud_email'];
			$userarray['contact']=$userdata['stud_contact'];
			$userarray['verify']=(int)$userdata['verify_flag'];
			if($userarray['verify'] == 0)
			{
				//OTP SMS SEND
				
				$otp = rand(pow(10, 6-1), pow(10, 6)-1);
				$udata = array('otp'=>$otp);
				$query = $this->db->where(array('stud_id' => $userdata['stud_id']));
				$result = $this->db->update('student', $udata);
			
			
				$smsmessage = "Hello ".$data['name'].",";
				$smsmessage .= "Your OTP for registration is -".$otp;
				$smsmessage .= ". Regards,mockexam Team. ";
				$this->sendSmsModel($data['contact'],$smsmessage);
			}
			$userarray['type']=3;
		}
		
		return $userarray;
	}
	
	public function getsocial($data, &$errormessage)
	{
		$userarray = array();
		$query = $this->db->get_where('student',array('socialid' => $data['socialid']));
        $userdata = $query->row_array();
		if(!$userdata)
		{
			$errormessage = "First register this user";
		}
		else
		{
			$this->db->select('usersessionid');
			$query = $this->db->get_where('usersession',array('userid' => $userdata['stud_id'],'usertype' =>3));
	        $authdata = $query->row_array();
        	
        	$userarray['authcode']=$authdata['usersessionid'];
			$userarray['userid']=$userdata['stud_id'];
			$userarray['instid']=$userdata['branch_id'];
			$userarray['name']=$userdata['stud_name'];
			$userarray['email']=$userdata['stud_email'];
			if($userdata['prof_pic'] == null){
				$userarray["imgpath"] = "images/man.png";
			}else{
				$userarray["imgpath"] = $userdata['prof_pic'];
			}
			$userarray['contact']=$userdata['stud_contact'];
			$userarray['address']=$userdata['address'];
			$userarray['contact_flag']=0;
			$userarray['verify']=(int)$userdata['verify_flag'];
			if($userarray['verify'] == 0)
			{
				//OTP SMS SEND
				
				$otp = rand(pow(10, 6-1), pow(10, 6)-1);
				$udata = array('otp'=>$otp);
				$query = $this->db->where(array('stud_id' => $userdata['stud_id']));
				$result = $this->db->update('student', $udata);
			
			
				$smsmessage = "Hello ".$userdata['stud_name'].",";
				$smsmessage .= "Your OTP for registration is -".$otp;
				$smsmessage .= ". Regards,mockexam Team. ";
				$this->sendSmsModel($userdata['stud_contact'],$smsmessage);
			}
			$userarray['type']=3;
		}
		return $userarray;
	}
		
	public function getUser($data, &$errormessage)
	{
		$userdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $userdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $userdata;
		}
		else
		{
			$this->db->select('stud_id,stud_name,stud_email,stud_contact');
			$query = $this->db->get_where('student',array('stud_id' => $userid, 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
				$userdata["id"] = $userid;
				$userdata["name"] = $resultdata['stud_name'];
				$userdata["email"] = $resultdata['stud_email'];
				$userdata["contact"] = $resultdata['stud_contact'];
			}
			else
			{
				$errormessage = "This student is not found.";
			}
		}
				
		return $userdata;
	}
	

	public function getexamsmodel($data, &$errormessage)
	{
		$coursedata = array();
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $coursedata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $coursedata;
		}
		else
		{
			$this->db->select('es.schedule_id AS id, e.exam_name AS name, es.exam_duration AS duration, es.fee AS fee, sg.subject_group_name,
								e.exam_id, es.start_time, es.end_time, es.exam_date,c.course_name,cc.category_name
								',false);
			$this->db->from('exam_schedule AS es');
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
			$this->db->join('course AS c', 'c.course_id = sg.course_id', 'inner');
			$this->db->join('course_category AS cc', 'cc.category_id = c.category_id', 'inner');

			$this->db->join('student_buy_exam AS sbe', 'sbe.exam_schedule_id = es.schedule_id', 'inner');
			$this->db->where(array('sbe.stud_id' => (int)$data['userid'],'c.active'=>'1','e.active'=>'1'));
			$this->db->group_by('e.exam_name');
			$this->db->order_by('es.exam_date',asc);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	       //print_r($resultdata);
	       //die();
	        if(!$resultdata){
				$coursedata = array();
				$errormessage = "Courses are not available.";
			}
			else{
				for($i=0;$i < count($resultdata);$i++){
					$tempdata = array();
					$tempdata['category_name'] = $resultdata[$i]['category_name'];
					$tempdata['id'] = $resultdata[$i]['id'];
					$tempdata['name'] = $resultdata[$i]['name'];
					$tempdata['duration'] = $resultdata[$i]['duration'];
					$tempdata['fee'] = $resultdata[$i]['fee'];
					$tempdata['sub_group_name'] = $resultdata[$i]['subject_group_name'];
					$tempdata['exam_date'] = $resultdata[$i]['exam_date'];
					$tempdata['examid'] = $resultdata[$i]['exam_id'];
					$tempdata['start_time'] = $resultdata[$i]['start_time'];
					$tempdata['end_time'] = $resultdata[$i]['end_time'];
					$tempdata['days'] = $resultdata[$i]['days'];
					$tempdata['course'] = $resultdata[$i]['course_name'];
					
					array_push($coursedata,$tempdata);
				}
				
			}
		}
		//print_r($coursedata);
		return $coursedata;
	}
	public function getexamsdatamodel($data, &$errormessage)
	{
		$coursedata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $coursedata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $coursedata;
		}
		else
		{
			$curDate = date("Y-m-d",time());
			//$sortingby=$data['sortBy'];
			//echo $sortingby;
			$this->db->select('es.schedule_id AS id, e.exam_name AS exam_name, es.exam_duration AS duration, es.fee AS fee,
								e.exam_id, es.start_time, es.end_time, es.exam_date,sbe.roll_no
								',false);
			$this->db->from('exam_schedule AS es');
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
			$this->db->join('student_buy_exam AS sbe', 'sbe.exam_schedule_id = es.schedule_id', 'inner');
			if($data['sortBy'] == '<='){
				$this->db->where('es.exam_date <', $curDate);
			}	
			else{
				$this->db->where('es.exam_date >=', $curDate);
			}	
			 
			$this->db->where(array('sbe.stud_id' => (int)$data['userid']));
			//$this->db->where('es.exam_date '$sortingby, $curDate);
			$this->db->group_by('sbe.roll_no');
			$this->db->order_by('sbe.stud_course_batch_id',desc);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	       // print_r($resultdata);
	        if(!$resultdata){
				$coursedata = array();
				$errormessage = "Courses are not available.";
			}
			else{
				for($i=0;$i < count($resultdata);$i++){
					$tempdata = array();
					$tempdata['id'] = $resultdata[$i]['id'];
					$tempdata['exam_name'] = $resultdata[$i]['exam_name'];
					$tempdata['duration'] = $resultdata[$i]['duration'];
					$tempdata['exam_date'] = $resultdata[$i]['exam_date'];
					$tempdata['examid'] = $resultdata[$i]['exam_id'];
					$tempdata['start_time'] = $resultdata[$i]['start_time'];
					$tempdata['end_time'] = $resultdata[$i]['end_time'];
					$tempdata['days'] = $resultdata[$i]['days'];
					$tempdata['roll_no'] = $resultdata[$i]['roll_no'];
					
					array_push($coursedata,$tempdata);
				}
				
			}
		}
		return $coursedata;
	}
	public function hallTicketModel($data, &$errormessage)
	{
		$coursedata = array();
		
					$this->db->select('es.exam_mode,
								es.exam_date,
								es.start_time,
								es.end_time,
								e.exam_name,
								es.exam_duration,
								
								e.no_of_question,
								
								c.course_name,
								sg.subject_group_id,
								sbe.stud_course_batch_id,
								sbe.roll_no
								',false);
			$this->db->from('exam_schedule AS es');
			$this->db->join('student_buy_exam AS sbe', 'sbe.exam_schedule_id = es.schedule_id', 'inner');

			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->join('course AS c', 'c.course_id = e.course_id', 'inner');

			$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
			$this->db->where(array('es.schedule_id' => (int)$data['exam_schedule_id'],'sbe.stud_id'=>(int)$data['userid']));
			
			$query = $this->db->get();
	        $resultdata = $query->row_array();
			
			$this->db->select('stud_exam_id');
			$this->db->from('student_exam');
			$this->db->where(array('roll_no'=>$resultdata['roll_no'],'iscomplete_flag'=>'1'));
			$query2 = $this->db->get();
			$getRepeatData= $query2->row_array();
			if(count($getRepeatData) > 0){
				$resultdata['startBtn'] = 'hide';
			}else{
				$resultdata['startBtn'] = 'show';
			}
			
			//$resultdata['exam_date']= date('Y-m-d');
			/*$examTime='16:21:33';
			echo $examTime;*/
			$curTime=date('H:i:s');
			$resultdata['examStatus'] = 2;

			
			if ($resultdata['exam_date'] == date('Y-m-d')) {
				$resultdata['examStatus'] = 0;
				//echo "Your date is today";
				//$exam_end_time = date("H:i:s", strtotime($resultdata['start_time'])+(60*$resultdata['exam_duration']));
				//$exam_start_time = date("H:i:s", strtotime($resultdata['start_time']));
			

				/*if ($curTime > $exam_start_time){
					$resultdata['examStatus'] = 0;
					if ($curTime < $exam_end_time){
						$resultdata['examStatus'] = 3;				
					}		
				}*/
				
			}
			/*echo $resultdata['examStatus'];
			die();*/

	   // get subject name
	        $this->db->select('s.subject_name',false);
			$this->db->from('subject_group_sub AS sgs');
			$this->db->join('subject AS s', 's.subject_id = sgs.subject_id', 'inner');	
			
			$this->db->where(array('sgs.sub_group_id' => (int)$resultdata['subject_group_id']));
			$query1 = $this->db->get();
	        $subjectdata = $query1->result_array();
	     // get student information
	        $this->db->select('stud_id,stud_name,gender,dob,mother_name,stud_email,stud_contact,address,standard,college_name,prof_pic',false);
			$this->db->from('student');
			$this->db->where(array('stud_id' => (int)$data['userid']));
			$query2 = $this->db->get();
			$resultdata['studentdata'] = $query2->row();
	        for($s=0;$s < count($subjectdata);$s++){
	        	$temp_sub = array();
				$temp_sub['subject_name'] = $subjectdata[$s]['subject_name'];
				$sub_temp[] = $temp_sub;
				
	        }
	      	$resultdata['subject']= $sub_temp;
	      	//array_push($resultdata,$studentdata);
			
		
		   
		return $resultdata;
	}
	public function getexamsDataByIdamodel($data, &$errormessage)
	{

		$coursedata = array();
			$this->db->select('es.schedule_id AS id, e.exam_name AS exam_name, es.exam_duration AS duration, es.fee AS fee,es.exam_mode,
								e.exam_id, es.start_time, es.end_time, es.exam_date,sg.subject_group_name,c.course_name
								',false);
			$this->db->from('exam_schedule AS es');
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
			//$this->db->join('subject_group_sub AS sgs', 'sgs.sub_group_id = sg.subject_group_id', 'inner');
			//$this->db->join('subject AS s', 's.subject_id = sgs.subject_id', 'inner');
			$this->db->join('course AS c', 'c.course_id = e.course_id', 'inner');

			//$this->db->join('student_buy_exam AS sbe', 'sbe.exam_schedule_id = es.schedule_id', 'inner');
			//$this->db->join('exam AS e', 'e.course_id = c.course_id and e.active = "1"', 'left');			
			//$this->db->where('sbe.exam_date >=', date('Y-m-d')); 
			$this->db->where(array('c.course_id' => (int)$data['examCourseId'],'e.active'=> '1','c.active' => '1','sg.active' => '1'));
			$this->db->where('es.exam_date >=', date('Y-m-d'));
			$this->db->group_by('es.schedule_id');
			$this->db->order_by('es.exam_date',asc);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	        //print_r($resultdata);
	        if(!$resultdata){
				$coursedata = array();
				$errormessage = "Courses are not available.";
			}
			else{
				for($i=0;$i < count($resultdata);$i++){
					$tempdata = array();
					$tempdata['id'] = $resultdata[$i]['id'];
					$tempdata['exam_name'] = $resultdata[$i]['exam_name'];
					$tempdata['duration'] = $resultdata[$i]['duration'];
					$tempdata['exam_mode'] = $resultdata[$i]['exam_mode'];
					$tempdata['exam_date'] = $resultdata[$i]['exam_date'];
					$tempdata['examid'] = $resultdata[$i]['exam_id'];
					$tempdata['start_time'] = $resultdata[$i]['start_time'];
					$tempdata['end_time'] = $resultdata[$i]['end_time'];
					$tempdata['days'] = $resultdata[$i]['days'];
					$tempdata['fee'] = $resultdata[$i]['fee'];
					$tempdata['course_name'] = $resultdata[$i]['course_name'];
					$tempdata['group_name'] = $resultdata[$i]['subject_group_name'];
					
					array_push($coursedata,$tempdata);
				}
			}
		
		return $coursedata;
	}
	public function getallexamsdatamodel($data, &$errormessage)
	{
		$coursedata = array();
			
			$this->db->select('es.schedule_id AS id, e.exam_name AS exam_name, es.exam_duration AS duration,es.fee AS fee,c.course_name,es.exam_mode,
								e.exam_id, es.start_time, es.end_time, es.exam_date,sg.subject_group_name
								',false);
			$this->db->from('exam_schedule AS es');
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
			//$this->db->join('subject_group_sub AS sgs', 'sgs.sub_group_id = sg.subject_group_id', 'inner');
			//$this->db->join('subject AS s', 's.subject_id = sgs.subject_id', 'inner');
			$this->db->join('course AS c', 'c.course_id = e.course_id', 'inner');

			//$this->db->join('student_buy_exam AS sbe', 'sbe.exam_schedule_id = es.schedule_id', 'inner');
			//$this->db->join('exam AS e', 'e.course_id = c.course_id and e.active = "1"', 'left');			
			//$this->db->where('sbe.exam_date >=', date('Y-m-d')); 
			$this->db->where(array('c.active' => '1','e.active' => '1','sg.active' => '1'));
			$this->db->where('es.exam_date >=', date('Y-m-d'));
			$this->db->group_by('es.schedule_id');
			$this->db->order_by('es.exam_date',asc);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	        //print_r($resultdata);
	        if(!$resultdata){
				$coursedata = array();
				$errormessage = "Courses are not available.";
			}
			else{
				for($i=0;$i < count($resultdata);$i++){
					$tempdata = array();
					$tempdata['id'] = $resultdata[$i]['id'];
					$tempdata['exam_name'] = $resultdata[$i]['exam_name'];
					$tempdata['duration'] = $resultdata[$i]['duration'];
					$tempdata['course_name'] = $resultdata[$i]['course_name'];
					$tempdata['exam_date'] = $resultdata[$i]['exam_date'];
					$tempdata['examid'] = $resultdata[$i]['exam_id'];
					$tempdata['start_time'] = $resultdata[$i]['start_time'];
					$tempdata['exam_mode'] = $resultdata[$i]['exam_mode'];
					$tempdata['days'] = $resultdata[$i]['days'];
					$tempdata['fee'] = $resultdata[$i]['fee'];
					$tempdata['group_name'] = $resultdata[$i]['subject_group_name'];
					
					array_push($coursedata,$tempdata);
				}
			}
		
		return $coursedata;
	}
	public function downloadReportExcel($data, &$errormessage)
	{		
		$studdata = array();
					
				$this->db->select('s.stud_id as id,s.stud_name as name,s.stud_contact as contact,s.stud_email as email');
				$this->db->from('student as s');
				if($data['startdate'] != null && $data['startdate'] != '')
					{
						$where = "DATE(s.submitdate) BETWEEN '".$data['startdate']."' AND '".$data['enddate']."' ";
						$this->db->where($where);
					}
				if($data['courseid'] != null && $data['courseid'] != '')
				{
					$this->db->join('student_buy_exam AS sc', 'sc.stud_id = s.stud_id', 'inner');
					$where = "CURDATE() BETWEEN sc.start_date AND sc.end_date AND sc.course_id = ".$data['courseid']." ";
					$this->db->where($where);
				}
				//$like = 's:'.strlen($data['branchid']).':"'.(string)$data['branchid'].'";';
				if($data['searchtext'] != null)
				{
					//$text = "'%".$data['searchtext']."%'";
					$this->db->like('s.stud_name',$data['searchtext']);
				}
				$this->db->where('s.branch_id = '.$data['branchid']);
				
				$this->db->where('s.active="1"');
				$this->db->group_by('s.stud_id');
				$this->db->order_by("s.stud_id", "desc");
				
				$query = $this->db->get();
	        	$resultdata = $query->result_array();
		        if($resultdata)
				{
					$studdata = $resultdata;					
				}
				else{
					$errormessage = "Students are not available.";
				}
			
		return $studdata;
	}

	public function createFinalTest($data, &$errormessage)
	{
		$finaldata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $finaldata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $finaldata;
		}
		else
		{	
			$userdata = array();
			$this->db->select('*');
			$this->db->from('student_exam');
			$this->db->where(array('roll_no' => (int)$data['rollNo']));
			$query = $this->db->get();
			$resultdata = $query->row_array();
			/*echo count($resultdata);
			print_r($resultdata);*/
			$finaldata['student_exam_id'] = $resultdata['stud_exam_id'];
			if (count($resultdata) == 0) {
				
				$time = date('H:i:s');
				//$valdate  = $data['createddate'].('H:s:i');

				$pdata = array('stud_id' => (int)$data['userid'],'exam_schedule_id' => (int)$data['exam_schedule_id'], 'roll_no' => (int)$data['rollNo'],'start_time' => $time,'submitdate' => $data['createddate']);
				$result = $this->db->insert('student_exam', $pdata);
				$finaldata['student_exam_id'] = $this->db->insert_id();
			}
				if($finaldata['student_exam_id'])
				{
					 
					$finaldata['userid'] = (int)$data['userid'];
					$finaldata['exam_schedule_id'] = (int)$data['exam_schedule_id'];
					$finaldata['courseid'] = (int)$data['courseid'];
					$finaldata['rollno'] = (int)$data['rollNo'];
				}
				else{
					$finaldata = array();
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
			
		}
		return $finaldata;
	}
	public function getFinalExamDetails($data, &$errormessage)
	{
		$exam = array();
		
			$this->db->select('e.exam_name,se.start_time,es.exam_duration,se.stud_exam_id,se.submitdate');
			$this->db->from('student_exam AS se');
			$this->db->join('exam_schedule AS es', 'es.schedule_id = se.exam_schedule_id', 'inner');
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->where(array('se.roll_no'=>(int)$data['roll_no'],'se.stud_id'=>(int)$data['userid']));
			$query = $this->db->get();
	        $result = $query->row_array();
	       //print_r($result);
			if(!$result)
			{
				$exam = array();
				$errormessage = "Exams are not available.";
			}
			else{
				//echo "string";
				$this->db->from('student_exam_result');
				$where = "time_taken IS NOT NULL";
				$this->db->where($where);
				$this->db->where(array('stud_exam_id'=>$result['stud_exam_id']));
				$query = $this->db->get();
	        	$studexam = $query->result_array();
	        	
	        	$starttime = $result['start_time'];
	        	date_default_timezone_set("Asia/Kolkata"); 
				$curtime = date("H:i:s"); //current datetime
		
				//echo $result['submitdate'];

				//$date = '2011-04-8 08:29:49';
				$formatDate = date("Y-m-d H:i:s", strtotime($result['submitdate'])+(60*(int)$result['exam_duration']));
				$date = date("Y-m-d H:i:s"); 
				//echo $formatDate;
				$result['timeOver'] = 999;
				if ($formatDate <= $date) {
					$result['timeOver'] = 0;
					$updata1 = array('iscomplete_flag' => '1');
					$this->db->where(array('stud_exam_id'=> $result['stud_exam_id']));
					$this->db->update('student_exam', $updata1);					
				}else{
		        	if($starttime <= $curtime)
		        	{
		        		$diff = abs(strtotime($curtime) - strtotime($starttime));
		        		$min = floor($diff/60);
		        		
						if($min > (int)$result['exam_duration'])
						{
							$result['timecount'] = 0;
							$result['continue'] = "false";

						}
						else{
							$result['timecount'] = (int)$result['exam_duration'] - $min;
						}
					}
					else{
						$result['timecount'] = 0;	
						$result['continue'] = "false";
					}
				}	
	        	//echo $result['timecount'];
				$result['attempt'] = $studexam;
				$exam = $result;
			}
			//print_r($data);
		return $exam;
	}
	
	
	public function UpdatePreparation($data, &$errormessage)
	{
		$prepairdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $prepairdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $prepairdata;
		}
		else
		{
				$updata = array('last_attempt' => (int)$data['quesid'],'attempt_question' => (int)$data['count']);
				//if($data['topicid'] != null)
				//$this->db->where(array('topic_id'=>$data['topicid']));
				$this->db->where(array('stud_id'=> (int)$data['userid'],'chapter_id'=>(int)$data['chapterid']));
				$result = $this->db->update('prepair_test', $updata);

				if($result)
				{
					$this->db->select('pid as id,stud_id as studid,chapter_id as chapterid,last_attempt as lastid,attempt_question as totalattempt');
					$this->db->from('prepair_test');
					
					
					$this->db->where(array('stud_id'=>(int)$data['userid'],'chapter_id'=>(int)$data['chapterid'],'active' => '1'));
					$query = $this->db->get();
			        $resultdata = $query->row_array();
			        //print_r($resultdata);
			        if($resultdata)
			        {
						$prepairdata['id'] = $resultdata['id'];
						$prepairdata['userid'] = $resultdata['studid'];
						$prepairdata['chapterid'] = $resultdata['chapterid'];
						//$prepairdata['topicid'] = $resultdata['topicid'];
						$prepairdata['courseid'] = $resultdata['courseid'];
						$prepairdata['lastid'] = $resultdata['lastid'];
						$prepairdata['totalattempt'] = $resultdata['totalattempt'];
					}
					else{
						$prepairdata = array();
						$errormessage = "Some unknown error has occurred. Please try again.11";
					}
				}
				else{
					$prepairdata = array();
					$errormessage = "Some unknown error has occurred. Please try again.22";
				}
			
			
		}
		return $prepairdata;
	}
	
	public function updateFinalTest($data, &$errormessage)
	{
	
		$finalid = 0;
		$updata = array();
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $finalid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $finalid;
		}
		else
		{	
				$updata = array('time_taken' => (int)$data['time']);
				if($data['optionid'] != null)
				{
					$updata['ques_option_id'] = (int)$data['optionid'];
					//print_r($updata);
				}
				$this->db->where(array('stud_exam_id'=> (int)$data['student_exam_id'],'question_id'=>(int)$data['quesid']));
				$result = $this->db->update('student_exam_result', $updata);
				if($result)
				{
					$finalid = (int)$data['student_exam_id'];
				}
				else{
					$finalid = 0;
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
			
			
		}
		return $finalid;
	}
	
	public function PreparationTest($data, &$errormessage)
	{
		$prepairdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $prepairdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $prepairdata;
		}
		else
		{
			$this->db->select('pt.pid as id,pt.stud_id as studid,pt.chapter_id as chapterid,pt.course_id as courseid,pt.last_attempt as lastid,pt.attempt_question as totalattempt,c.chapter_name');
			$this->db->from('prepair_test as pt');
			$this->db->join('chapter as c', 'c.chapter_id = pt.chapter_id', 'inner');
			
			$this->db->where(array('pt.stud_id'=>(int)$data['userid'],'pt.chapter_id'=>(int)$data['chapterid'],'pt.active' => '1'));
			$query = $this->db->get();
	        $resultdata = $query->row_array();
			if(!$resultdata)
			{
				$pdata = array('stud_id' => (int)$data['userid'],'chapter_id' => (int)$data['chapterid'], 'course_id' => (int)$data['courseid'],'submitdate' => $data['createddate'], 'active' => '1');
				
				$result = $this->db->insert('prepair_test', $pdata);
				if($result)
				{
					$prepairdata['id'] = $this->db->insert_id();;
					$prepairdata['userid'] = (int)$data['userid'];
					$prepairdata['chapterid'] = (int)$data['chapterid'];
					$prepairdata['courseid'] = (int)$data['courseid'];
					
					$prepairdata['lastid'] = 0;
					$prepairdata['totalattempt'] = 0;
				}
				else{
					$prepairdata = array();
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
			}
			else{
				$prepairdata['id'] = $resultdata['id'];
				$prepairdata['userid'] = $resultdata['studid'];
				$prepairdata['chapterid'] = $resultdata['chapterid'];
				$prepairdata['courseid'] = $resultdata['courseid'];
				$prepairdata['lastid'] = $resultdata['lastid'];
				$prepairdata['totalattempt'] = $resultdata['totalattempt'];
				$prepairdata['chaptername'] = $resultdata['chapter_name'];
			}
			
		}
		return $prepairdata;
	}

	public function submitFinalTest($data, &$errormessage)
	{
		$finalid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $finalid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $finalid;
		}
		else
		{
				$updata = array('iscomplete_flag' => '1');
				$this->db->where(array('stud_exam_id'=> (int)$data['student_exam_id']));
				$result = $this->db->update('student_exam', $updata);
				if($result)
				{
					$finalid = (int)$data['student_exam_id'];
				}
				else{
					$finalid = 0;
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
		}
		return $finalid;
	}
	public function submitDoubtForm($data, &$errormessage)
	{
		$finalid = 0;


		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $finalid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $finalid;
		}
		else
		{
				$pdata = array('qun_id' => (int)$data['exam_qun_Pid'],'stud_id' => $data['userid'], 'schuduled_id' => (int)$data['exam_schedule_id'],'add_date'=>date('Y-m-d'),'exam_id' => (int)$data['examid']);
					$result = $this->db->insert('doubt_table', $pdata);
				if($result)
				{
					$finalid = 1;
				}
				else{
					$finalid = 0;
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
		}
		return $finalid;
	}
	
	
	//preparation test retest
	public function Retest($data, &$errormessage)
	{
		$prepairdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $prepairdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $prepairdata;
		}
		else
		{
				$updata = array('last_attempt' => 0,'attempt_question' => 0);
				$this->db->set('total_attempt','total_attempt + 1',FALSE);
				
				
				$this->db->where(array('stud_id'=> (int)$data['userid'],'chapter_id'=>(int)$data['chapterid']));
				$result = $this->db->update('prepair_test', $updata);
				if($result)
				{
					$this->db->select('pid as id,stud_id as studid,chapter_id as chapterid,course_id as courseid,last_attempt as lastid,attempt_question as totalattempt');
					$this->db->from('prepair_test');
					
					$this->db->where(array('stud_id'=>(int)$data['userid'],'chapter_id'=>(int)$data['chapterid'],'active' => '1'));
					$query = $this->db->get();
			        $resultdata = $query->row_array();
			        if($resultdata)
			        {
						$prepairdata['id'] = $resultdata['id'];
						$prepairdata['userid'] = $resultdata['studid'];
						$prepairdata['chapterid'] = $resultdata['chapterid'];
						$prepairdata['courseid'] = $resultdata['courseid'];
						$prepairdata['lastid'] = $resultdata['lastid'];
						$prepairdata['totalattempt'] = $resultdata['totalattempt'];
					}
					else{
						$prepairdata = array();
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
				}
				else{
					$prepairdata = array();
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
			
			
		}
		return $prepairdata;
	}
	//for preparation test
	public function getQuestion($data, &$errormessage)
	{
		//print_r($data);
		$questiondata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questiondata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $questiondata;
		}
		else
		{
				//print_r($data);
			$this->db->select('q.ques_id as id,q.chapter_id as chapter,q.topic_id as topicid,q.course_id as course,q.ques_text as question,q.is_sequence,ca.option_id as optionid,ca.ans_explanation as expl,qo.option_text as optiontext,q.paraghaph_id as paraid,q.ques_type as questype');
			$this->db->from('question as q');
			//$this->db->join('question_paragraph as pq', 'pq.para_id = q.paraghaph_id', 'left');
			$this->db->join('question_correct_answer as ca', 'q.ques_id = ca.ques_id', 'inner');
			$this->db->join('question_options as qo', 'ca.option_id = qo.option_id', 'inner');
			
			$this->db->where(array('q.chapter_id'=>(int)$data['chapter_id'],'q.active' => '1','q.is_final' => '0'));
			$this->db->order_by("q.ques_id", "asc");
			//$this->db->order_by('rand()');
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	        //print_r($resultdata);
			if($resultdata)
			{
				for($i=0;$i < count($resultdata) ;$i++)
				{
					$this->db->select('option_id as optid,option_text as option');
					$this->db->from('question_options');
					$this->db->where(array('ques_id'=>(int)$resultdata[$i]['id']));
					if((int)$resultdata[$i]['is_sequence'] != 1)
					{
						$this->db->order_by('rand()');
					}
					$query = $this->db->get();
			        $optiondata = $query->result_array();
			        
			        $question = array();
			        $question['id'] = $resultdata[$i]['id'];
			        $question['chapter'] = $resultdata[$i]['chapter'];
			       // $question['topicid'] = $resultdata[$i]['topicid'];
			        //$question['imgpath'] = $resultdata[$i]['prof_pic'];
			       // $question['course'] = $resultdata[$i]['course'];
			        $question['question'] = $resultdata[$i]['question'];
			        $question['optionid'] = $resultdata[$i]['optionid'];
			        $question['expl'] = $resultdata[$i]['expl'];
			        $question['optiontext'] = $resultdata[$i]['optiontext'];
			       // $question['optimgpath'] = $resultdata[$i]['optimgpath'];
			       // $question['explimg'] = $resultdata[$i]['explimg'];
			        $question['paratext'] = $resultdata[$i]['paratext'];
					//$question['paraimg'] = $resultdata[$i]['paraimg'];
					$question['paraid'] = $resultdata[$i]['paraid'];
					$question['questype'] = $resultdata[$i]['questype'];
			        $question['options'] = $optiondata;
			        $questiondata[] = $question;
				}
				
			}
			else{
				$questiondata = array();
				$errormessage = "Questions are not available for this chapter.";
			}
		}	
		return $questiondata;
	}
	
	public function getFinalExamQues($data, &$errormessage)
	{	
			$questiondata = array();
			//print_r($data);
			$this->db->from('student_exam_result');
			$this->db->where(array('stud_exam_id'=>$data['student_exam_id']));
			$query = $this->db->get();
	        $studexam = $query->result_array();
	        //print_r($studexam);	
	        if($studexam)
	        {
					for($j=0;$j < count($studexam);$j++)
					{
						$this->db->select('q.ques_id as id,q.chapter_id as chapter,q.course_id as course,q.ques_text as question,q.is_sequence,ca.option_id as optionid,ca.ans_explanation as expl,qo.option_text as optiontext,q.ques_type as questype,s.subject_name');
						$this->db->from('question as q');
						$this->db->join('question_correct_answer as ca', 'q.ques_id = ca.ques_id', 'inner');
						//$this->db->join('question_paragraph as pq', 'pq.para_id = q.paraghaph_id', 'left');
						$this->db->join('question_options as qo', 'ca.option_id = qo.option_id', 'inner');
						$this->db->join('chapter as c', 'c.chapter_id = q.chapter_id', 'inner');
						$this->db->join('subject as s', 's.subject_id = c.subject_id', 'inner');
						$this->db->where(array('q.ques_id'=>(int)$studexam[$j]['question_id'],'q.active' => '1','q.is_final' => '1'));
						//$this->db->order_by('rand()');
						//$this->db->limit($studexam[$j]['no_of_ques']);
						$query = $this->db->get();
				        $resultdata = $query->result_array();
						if($resultdata)
						{
							for($i=0;$i < count($resultdata) ;$i++)
							{
								$this->db->select('option_id as optid,option_text as option');
								$this->db->from('question_options');
								$this->db->where(array('ques_id'=>(int)$resultdata[$i]['id']));
								if((int)$resultdata[$i]['is_sequence'] != 1)
								{
									$this->db->order_by('rand()');
								}
								$query = $this->db->get();
						        $optiondata = $query->result_array();
						        
						        $question = array();
						        $question['id'] = $resultdata[$i]['id'];
						        $question['chapter'] = $resultdata[$i]['chapter'];
						        $question['course'] = $resultdata[$i]['course'];
						        $question['question'] = $resultdata[$i]['question'];
						        $question['optionid'] = $resultdata[$i]['optionid'];
						        $question['expl'] = $resultdata[$i]['expl'];
						        $question['optiontext'] = $resultdata[$i]['optiontext'];
								$question['questype'] = $resultdata[$i]['questype'];
								$question['subject_name'] = $resultdata[$i]['subject_name'];
								$question['options'] = $optiondata;
						        
						        $questiondata[] = $question;
						        
						        if(!$studexam)
						        {
									$pdata = array('stud_exam_id' => (int)$data['student_exam_id'],'question_id' => $question['id'], 'correct_option_id' => (int)$question['optionid']);
									//echo "1st case";
									//print_r($pdata);
					$result = $this->db->insert('student_exam_result', $pdata);
								}
							}
						}
					}
			}
			else{
				
			//get chapter and topic questions
				$this->db->select('ecq.chapter_id,ecq.no_of_ques');
	       		$this->db->from('exam_schedule as es');
				$this->db->join('subject_group as sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
				$this->db->join('subject_group_sub as sgs', 'sgs.sub_group_id = es.sub_group_id', 'inner');
				$this->db->join('chapter as c', 'c.subject_id = sgs.subject_id', 'inner');
				$this->db->join('exam_chapter_questions AS ecq', 'ecq.chapter_id = c.chapter_id', 'inner');

				$this->db->where(array('es.schedule_id'=>$data['exam_schedule_id'],'ecq.exam_id'=>$data['examid']));
				$this->db->group_by('c.chapter_id');
				//$this->db->order_by("ecq.ecq_id", "desc");
				$query = $this->db->get();
		        $resultques = $query->result_array();
		        //echo "<pre>";
		       // print_r($resultques); die();
		       	if($resultques)
				{
					for($j=0;$j < count($resultques);$j++)
					{
						$this->db->select('q.ques_id as id,q.chapter_id as chapter,q.course_id as course,q.ques_text as question,q.is_sequence,ca.option_id as optionid,ca.ans_explanation as expl,qo.option_text as optiontext,q.ques_type as questype,s.subject_name');
						$this->db->from('question as q');
						$this->db->join('question_correct_answer as ca', 'q.ques_id = ca.ques_id', 'inner');
						$this->db->join('question_options as qo', 'ca.option_id = qo.option_id', 'inner');
						$this->db->join('chapter as c', 'c.chapter_id = q.chapter_id', 'inner');
						$this->db->join('subject as s', 's.subject_id = c.subject_id', 'inner');
						
						
						$this->db->where(array('q.ques_type'=>'0','q.chapter_id'=>(int)$resultques[$j]['chapter_id'],'q.active' => '1','q.is_final' => '1'));
						$this->db->order_by('rand()');
						$this->db->limit($resultques[$j]['no_of_ques']);
						$query = $this->db->get();
				        $resultdata = $query->result_array();
						if($resultdata)
						{
							for($i=0;$i < count($resultdata) ;$i++)
							{
								$this->db->select('option_id as optid,option_text as option');
								$this->db->from('question_options');
								$this->db->where(array('ques_id'=>(int)$resultdata[$i]['id']));
								if((int)$resultdata[$i]['is_sequence'] != 1)
								{
									$this->db->order_by('rand()');
								}
								$query = $this->db->get();
						        $optiondata = $query->result_array();
						        
						        $question = array();
						        $question['id'] = $resultdata[$i]['id'];
						        $question['chapter'] = $resultdata[$i]['chapter'];
						        $question['course'] = $resultdata[$i]['course'];
						        $question['question'] = $resultdata[$i]['question'];
						        $question['optionid'] = $resultdata[$i]['optionid'];
						        $question['expl'] = $resultdata[$i]['expl'];
						        $question['optiontext'] = $resultdata[$i]['optiontext'];
						        $question['options'] = $optiondata;
								$question['paraid'] = 0;
								$question['questype'] = $resultdata[$i]['questype'];
								$question['subject_name'] = $resultdata[$i]['subject_name'];

						        $questiondata[] = $question;
						        
						        if(!$studexam)
						        {	
									$pdata = array('stud_exam_id' => (int)$data['student_exam_id'],'question_id' => $question['id'], 'correct_option_id' => (int)$question['optionid']);
									//echo "2nd case";
									//print_r($pdata);
					$result = $this->db->insert('student_exam_result', $pdata);
									
								}						        
							}
						
						}
							
					}
				}
				else{
					$questiondata = array();
					$errormessage = "Exam chapter questions are not available.";
				}
			}
	       
		//print_r($questiondata);	
		return $questiondata;
	}
	
	

	public function getChaptername($data, &$errormessage)
	{
		$chaptername = '';
		
			$this->db->select('chapter_name');
			$this->db->from('chapter');
			$this->db->where(array('chapter_id'=>(int)$data['chapter_id'],'active' => '1'));
			$query = $this->db->get();
	        $result = $query->result_array();
	        
			if(!$result)
			{
				$chaptername = '';
				$errormessage = "Chapter are either delete or not inserted.";
			}
			else{
				$chaptername = $result['chapter_name'];
			}
			
		return $chaptername;
	}

	public function getUserType($data, &$errormessage)
	{
		$userid = '';
		
			$this->db->select('stud_id');
			$this->db->from('student');
			$this->db->where(array('stud_id'=>(int)$data['userid'],'active' => '1'));
			/*$where = '(register_type = 1 or register_type = 2) and ISNULL(stud_password)';
			$this->db->where($where);*/
			$query = $this->db->get();
	        $result = $query->row_array();
			if(!$result)
			{
				$userid = '';
				$errormessage = "This user have old password.";
			}
			else{
				$userid = $result['stud_id'];
			}
			
		return $userid;
	}
	public function getCartCount($data, &$errormessage)
	{
		$cartCount = '';
			$today= date("Y-m-d");
			$this->db->select('sct.temp_id');
			$this->db->from('student_course_temp as sct');
			$this->db->join('exam_schedule as es', 'es.schedule_id = sct.schedule_id', 'inner');
			$this->db->where(array('sct.studentid'=>(int)$data['userid']));
			
			$this->db->where('es.exam_date >=',$today);
			$query = $this->db->get();
	        $result = $query->result_array();
	       // print_r( count($result));
			if(!$result)
			{
				$cartCount = '';
				$errormessage = "You have not selected any exam yet.";
			}
			else{
				$cartCount = count($result);
			}
			
		return $cartCount;
	}
	
	public function getExamResult($data, &$errormessage)
	{
		$resultdata = array();
		
			//$this->db->select('*');
			$this->db->select('se.stud_exam_id,date(se.submitdate) as date,c.course_name,e.exam_name,se.roll_no');
			$this->db->from('student_exam se');

			$this->db->join('exam_schedule as es', 'es.schedule_id = se.exam_schedule_id', 'inner');
			$this->db->join('exam AS e','e.exam_id = es.exam_id','inner');
			$this->db->join('course AS c','c.course_id = e.course_id','inner');

			/*if($data['startdate'] != null && $data['startdate'] != '')
			{
				$where = "DATE(se.submitdate) BETWEEN '".$data['startdate']."' AND '".$data['enddate']."' ";
				$this->db->where($where);
			}
			if($data['searchtext'] != null)
			{
				$like = " (c.course_name  LIKE '%".$data['searchtext']."%' or es.exam_name  LIKE '%".$data['searchtext']."%') ";
				$this->db->where($like);
			}*/
			$this->db->where(array('se.stud_id'=>(int)$data['userid'],'se.iscomplete_flag' => '1'));
			$this->db->order_by("se.stud_exam_id", "desc");
			$query = $this->db->get();
	        $result = $query->result_array();
			if(!$result)
			{
				$resultdata = array();
				$errormessage = "Exam result are not available.";
			}
			else{
				$resultdata = $result;
			}
			//print_r($result);
		/*}*/	
		return $resultdata;
	}
	
	
	
	public function getQuestionDetail($data, &$errormessage)
	{
		$question = array();
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $question;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $question;
		}
		else
		{
			$this->db->select('q.ques_id as id,q.chapter_id as chapter,q.course_id as course,q.ques_text as question,q.is_sequence,ca.option_id as optionid,ca.ans_explanation as expl,qo.option_text as optiontext,q.paraghaph_id as paraid,q.ques_type as questype');
			$this->db->from('question as q');
			//$this->db->join('question_paragraph as pq', 'pq.para_id = q.paraghaph_id', 'left');
			$this->db->join('question_correct_answer as ca', 'q.ques_id = ca.ques_id', 'inner');
			$this->db->join('question_options as qo', 'ca.option_id = qo.option_id', 'inner');
			$this->db->where(array('q.ques_id'=>(int)$data['questionid'],'q.active' => '1'));
			//$this->db->order_by("q.ques_id", "asc");
			//$this->db->order_by('rand()');
			$query = $this->db->get();
	        $resultdata = $query->row_array();
			if($resultdata)
			{
					$this->db->select('option_id as optid,option_text as option');
					$this->db->from('question_options');
					$this->db->where(array('ques_id'=>(int)$resultdata['id']));
						
					if((int)$resultdata['is_sequence'] == 0)
					{
						$this->db->order_by('rand()');						
					}
					else
					{
						$this->db->order_by("option_id", "asc");
					}

					$query = $this->db->get();
			        $optiondata = $query->result_array();
			        
			        $question['id'] = $resultdata['id'];
			        $question['chapter'] = $resultdata['chapter'];
			        $question['course'] = $resultdata['course'];
			        $question['question'] = $resultdata['question'];
			        $question['optionid'] = $resultdata['optionid'];
			        $question['expl'] = $resultdata['expl'];
			        $question['optiontext'] = $resultdata['optiontext'];
					$question['optimgpath'] = $resultdata['optimgpath'];
					$question['paratext'] = $resultdata['paratext'];
					$question['paraid'] = $resultdata['paraid'];
					$question['questype'] = $resultdata['questype'];
			        $question['options'] = $optiondata;
			}
			else{
				$question = array();
				$errormessage = "Chapter question are either delete or not inserted.";
			}
		}	
		
		return $question;
	}
	
	public function getExamResultById($data, &$errormessage)
	{//print_r($data);
		$examresult = array();
		$userid = $this->GetLoggedinUserid($data['usersessionid']);

		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $examresult;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $examresult;
		}
		else
		{
			
			$this->db->select('se.stud_exam_id,date(se.submitdate) as date,c.course_name,e.exam_name,es.exam_duration,es.schedule_id,count(ser.stud_exam_result_id) as qunCount,se.roll_no,e.exam_id,es.schedule_id,c.course_id,se.stud_id');
			$this->db->from('student_exam se');
			$this->db->join('exam_schedule as es', 'es.schedule_id = se.exam_schedule_id', 'inner');
			$this->db->join('student_exam_result as ser', 'ser.stud_exam_id = se.stud_exam_id', 'inner');
			$this->db->join('exam as e', 'e.exam_id = es.exam_id', 'inner');
			
			$this->db->join('course as c', 'c.course_id = e.course_id', 'inner');

			$this->db->where(array('se.stud_exam_id'=>(int)$data['studentExamId']/*,'se.iscomplete_flag' => '1'*/));
			//'se.stud_id'=>(int)$data['userid'],
			$query = $this->db->get();
	        $result = $query->row_array();

	    
	        	//print_r($result);
			if($result)
			{
				$examresult['stud_exam_id'] = $result['stud_exam_id'];
				$examresult['date'] = $result['date'];
				$examresult['course_name'] = $result['course_name'];
				$examresult['exam_name'] = $result['exam_name'];
				$examresult['exam_duration'] = $result['exam_duration'];
				$examresult['no_of_question'] = $result['qunCount'];
				$examresult['roll_no'] = $result['roll_no'];
				$examresult['exam_id'] = $result['exam_id'];
				$examresult['schedule_id'] = $result['schedule_id'];
				$examresult['course_id'] = $result['course_id'];
				$examresult['stud_id'] = $result['stud_id'];
			
				
				$this->db->select('count(*) as correct');
				$this->db->where('correct_option_id = ques_option_id and stud_exam_id = '.$data['studentExamId'].' ');
				$this->db->from('student_exam_result');
				$query = $this->db->get();
	        	$correct = $query->row_array();
				
				$examresult['correct'] = $correct['correct'];
				
				$this->db->select('count(*) as attempted');
				$this->db->where('ques_option_id is not null and stud_exam_id = '.$data['studentExamId'].' ');
				$this->db->from('student_exam_result');
				$query = $this->db->get();
	        	$attempted = $query->row_array();
	        	
				$examresult['attempted'] = $attempted['attempted'];
				
				$this->db->select('count(*) as wrong');
				$this->db->where('correct_option_id != ques_option_id and stud_exam_id = '.$data['studentExamId'].' ');
				$this->db->from('student_exam_result');
				$query = $this->db->get();
	        	$wrong = $query->row_array();
	        	
				$examresult['wrong'] = $wrong['wrong'];
				
				$this->db->select('avg(time_taken) as avgtime');
				$this->db->where('time_taken is not null and stud_exam_id = '.$data['studentExamId'].' ');
				$this->db->from('student_exam_result');
				$query = $this->db->get();
	        	$avgtime = $query->row_array();
				$examresult['average_time_ques'] = $avgtime['avgtime'];

				$this->db->select('sum(time_taken) as totalTimeTaken');
				$this->db->where('time_taken is not null and stud_exam_id = '.$data['studentExamId'].' ');
				$this->db->from('student_exam_result');
				$query = $this->db->get();
	        	$Totaltime = $query->row_array();
				$examresult['total_time_taken'] = $Totaltime['totalTimeTaken'];

				$this->db->select('ser.stud_exam_result_id,ser.stud_exam_id,ser.question_id,ser.correct_option_id,ser.ques_option_id,ser.time_taken,q.ques_text as question,qo.option_text as myopttext,qo1.option_text as corropttext,
					(SELECT time_taken FROM `vid_student_exam_result` where question_id = ser.question_id and time_taken is not null and time_taken != 0 order by time_taken asc limit 1) as lessattempttime,
					(SELECT AVG(time_taken) FROM `vid_student_exam_result` where question_id = ser.question_id ) as avgTime,
					q.paraghaph_id as paraid,q.ques_type as questype,q.qun_mark,q.qun_neg_mark',false);
				$this->db->from('student_exam_result as ser');
				$this->db->join('question as q', 'q.ques_id = ser.question_id', 'inner');
				$this->db->join('question_options as qo', 'qo.option_id = ser.ques_option_id', 'left');
				$this->db->join('question_options as qo1', 'qo1.option_id = ser.correct_option_id', 'inner');
				$this->db->where('ser.stud_exam_id = '.$data['studentExamId'].' ');
				$query = $this->db->get();
	        	$resultdata = $query->result_array();
	        	
	        	$total_exam_mark=0;
	        	$total_negative_mark=0;
	        	$exam_mark=0;
		        	for ($qw=0; $qw < count($resultdata); $qw++) { 
		        		if($resultdata[$qw]['correct_option_id'] == $resultdata[$qw]['ques_option_id']){
		        			$total_exam_mark =$total_exam_mark+ $resultdata[$qw]['qun_mark'];
		        		}
		        		else if($resultdata[$qw]['ques_option_id'] == NULL){
		        		}else{
		        			$total_negative_mark =$total_negative_mark+ $resultdata[$qw]['qun_neg_mark'];
		        		}
			  			$exam_mark =$exam_mark+ $resultdata[$qw]['qun_mark'];
			  		}

		  		$examresult['exam_mark_total'] = $total_exam_mark;
		  		$examresult['exam_total_neg'] = $total_negative_mark;
		  		//print_r($examresult);

	        	$examresult['ques'] = $resultdata;

	        	$examresult['exam_mark'] = $exam_mark;
	        	$examresult['totalscore'] = $total_exam_mark - $total_negative_mark;
				
			}
			else{
				$examresult = array();
				$errormessage = "Student exam are not available.";
			}
				/*SELECT `sr_id`, `sr_stud_id`, `sr_stud_roll_no`, `sr_course_id`, `sr_schedule_id`, `sr_exam_id`, `sr_stud_exam_id`, `sr_total_que`, `sr_attempt_que`, `sr_correct_que`, `sr_wrong_que`, `sr_total_marks`, `sr_neg_marks`, `sr_total_score`, `sr_total_time`, `sr_result_date` FROM `vid_student_final_result` WHERE 1*/
				$this->db->select('sr_stud_roll_no');
				$this->db->where('sr_stud_roll_no = '.$examresult['roll_no'].' ');
				$this->db->from('student_final_result');
				$getRollNoQuery = $this->db->get();
	        	$getRollNo = $getRollNoQuery->row_array();

	        	$finalResultArray = [
	        							'sr_stud_id' => $examresult['stud_id'],
	        							'sr_stud_roll_no' => $examresult['roll_no'],
	        							'sr_course_id' => $examresult['course_id'],
	        							'sr_schedule_id' => $examresult['schedule_id'],
	        							'sr_exam_id' => $examresult['exam_id'],
	        							'sr_stud_exam_id' => $examresult['stud_exam_id'],
	        							'sr_total_que' => $examresult['no_of_question'],
	        							'sr_attempt_que' => $examresult['attempted'],
	        							'sr_correct_que' => $examresult['correct'],
	        							'sr_wrong_que' => $examresult['wrong'],
	        							'sr_total_marks' => $examresult['exam_mark_total'],
	        							'sr_exam_max_marks' => $examresult['exam_mark'],
	        							'sr_neg_marks' => $examresult['exam_total_neg'],
	        							'sr_total_time' => $examresult['total_time_taken'],
	        							'sr_total_score' => $examresult['exam_mark_total']-$examresult['exam_total_neg']
	        						];
	        	if ($getRollNo) {
	        						$this->db->where(array('sr_stud_roll_no'=> (int)$examresult['roll_no']));
	        		$getFinalResult = $this->db->update('student_final_result', $finalResultArray);
	        	}
	        	else{
	        		$getFinalResult = $this->db->insert('student_final_result', $finalResultArray);
	        	}
		        	$result = $this->db->query("call Getexamrank(".$examresult['schedule_id'].")");
		        	$getSchRank = $result->result_array();
		        	$query->next_result();
        			$query->free_result();
        			$this->db->reconnect();
        			$examresult['getSchRank'] = $getSchRank;
		        	$result1 = $this->db->query("call Getstudentrank(".$examresult['schedule_id'].",".$examresult['stud_id'].")");
		        	$getSingleRank = $result1->row_array();
		        	$query->next_result();
        			$query->free_result();
        			$this->db->reconnect();
					$examresult['getSingleRank'] = $getSingleRank;

		}

		//echo "<pre>";
		//print_r($examresult); 
		//print_r($getSingleRank);		
		return $examresult;
	}
	

	public function CheckCourseAss($data, &$errormessage)
	{
		$purches = 0;
		
			$this->db->select('e.exam_id',false);
			$this->db->from('student_buy_exam AS sbe');
			$this->db->join('exam_schedule AS es', 'es.schedule_id = sbe.exam_schedule_id', 'inner');				
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');				
			$this->db->where(array('e.exam_id' => $data['exam_id'],'sbe.stud_id' =>$data['userid']));			
			$query = $this->db->get();
		    $subData = $query->result_array();
		  
			if(!$subData)
			{
				$purches = 0;
				$errormessage = "Exam are either delete or not assign.";
			}
			else{
				$this->db->select('c.chapter_id',false);
				$this->db->from('chapter AS c');
				$this->db->where(array('c.chapter_id' => $data['chapterid']));			
				$query = $this->db->get();
		    	$chaData = $query->result_array();

		    	$purches = 1;
					if(!$chaData)
					{
						$purches = 0;
						$errormessage = "Chapter are either delete or not assign.";
						
					}
					
				return $purches;
			}
		return $purches;
	}
	
	public function changepasswordprofile($data, &$errormessage){
		$userdata = array();		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0){
			$errormessage = Go_model::$loggedinerror;
			return $userdata;
		}
		else if($userid != (int)$data['id']){
			$errormessage = Go_model::$usermismatcherror;
			return $userdata;
		}
		else{
				if($data['show'] == 'false'){
					$upddata = array('stud_password' => md5($data['password']));
					$this->db->where('stud_id', $userid);
					$result = $this->db->update('student', $upddata);
				
					if(!$result){
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else{
							$userdata["userid"] = (int)$userid;
					}
				}
				else
				if($data['show'] == 'true'){
					//$this->db->select('stud_id');
					$query = $this->db->get_where('student',array('stud_id' => $userid, 'stud_password' => md5($data['oldpassword']), 'active' => '1'));
					$tempdata = $query->row_array();
					if(!$tempdata){
						$errormessage = "Given old password is wrong";
						return $userdata;
					}
					$upddata = array('stud_password' => md5($data['password']));
					$this->db->where('stud_id', $userid);
					$result = $this->db->update('student', $upddata);
				
					if(!$result){
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else{
							$userdata["userid"] = (int)$userid;
							
							//send mail to institute		
							$to = $tempdata['stud_email'];
							$from = 'contact@vidhyarthimitra.org';
							
							$subject = 'Change Password';
							
							$headers = "From: " . strip_tags($from) . "\r\n";
							$headers .= "MIME-Version: 1.0\r\n";
							$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
							
						
							$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>Password Change</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$result['stud_name']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Get ready to experience a whole new way of learning. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Your Password has been change successful.The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Email/Username :".$to." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>New Password :".$data['password']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
							$sendmail = $this->sendEmail($to, $subject, $message, $headers);
					}
				}
		}
		return $userdata;
	}
	
	public function getStudentByID($data, &$errormessage)
	{		
		$studdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $studdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $studdata;
		}
		else
		{
			$query = $this->db->get_where('student',array('stud_id' => (int)$data['userid'], 'active' => '1'));
	        	$resultdata = $query->row_array();
			if($resultdata)
			{
					$studdata["id"] = (int)$resultdata['stud_id'];
					$studdata["userid"] = (int)$resultdata['stud_id'];
					$studdata["studname"] = $resultdata['stud_name'];
					$studdata["name"] = $resultdata['stud_name'];
					$studdata["username"] = $resultdata['username'];
					$studdata["contact"] = $resultdata['stud_contact'];
					$studdata["contact_flag"] = 0;
					$studdata["email"] = $resultdata['stud_email'];
					$studdata["address"] = $resultdata['address'];
					$studdata["country"] = $resultdata['country'];
					$studdata["other_country"] = $resultdata['other_country'];
					$studdata["district"] = $resultdata['district'];
					$studdata["state"] = $resultdata['state'];
					$studdata["pin"] = $resultdata['pin_code'];
					$studdata["standard"] = $resultdata['standard'];
					$studdata["college_name"] = $resultdata['college_name'];
					$studdata["mother_name"] = $resultdata['mother_name'];
					$studdata["dob"] = $resultdata['dob'];
					$studdata["gender"] = $resultdata['gender'];
					$studdata["coordinator"] = $resultdata['coordinator_name'];
					$studdata["how_to_know"] = $resultdata['how_to_know'];

					if($resultdata['prof_pic'] == null){
						$studdata["imgpath"] = "images/man.png";
					}else{
						$studdata["imgpath"] = $resultdata['prof_pic'];
					}		
			}
			else
			{
				$errormessage = "This Student is not found.";
			}
		}
				
		return $studdata;
	}

	public function uploadPhoto($data, &$errormessage)
	{
		$instid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $instid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $instid;
		}
		else
		{
			$upddata = array('prof_pic' => $data['imgpath']);
			$this->db->where(array('stud_id'=> (int)$data['userid'] , 'active' => '1'));
			$result = $this->db->update('student', $upddata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$instid = (int)$data['userid'];
			}
		}
		
		return $instid;
	}

	public function UpdateStudent($data, &$errormessage)
	{
		$userdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $userdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $userdata;
		}
		else
		{
			
			$this->db->select('stud_id');
			$where = ' stud_id != '.$data["userid"].' and (stud_email="'.$data['email'].'" or stud_contact="'.$data['contact'].'" )';
			$query = $this->db->get_where('student',$where);
	        $udata = $query->row_array();
			if($udata)
			{
				$errormessage = "This email or phone address is already exists. Please try another.";
				return $userdata;
			}
			
			$this->db->select('stud_contact');
			$where = 'stud_id = '.$data["userid"];
			$query = $this->db->get_where('student',$where);
	        $mobdata = $query->row_array();
			$upddata = array('stud_name' => $data['studname'], 'stud_email' => $data['email'], 'stud_contact' => $data['contact'], 'address'=> $data['address'], 'country'=> $data['country'], 'district'=> $data['district'], 'state'=> $data['state'], 'pin_code'=> $data['pin'], 'standard'=> $data['standard'], 'college_name'=> $data['college_name'],'mother_name'=> $data['mother_name'],'dob'=> $data['dob'],'gender'=> $data['gender'],'how_to_know'=> $data['how_to_know'],'coordinator_name'=> $data['coordinator'],'other_country'=> $data['other_country'] );
			$this->db->where('stud_id', (int)$data['userid']);
			$result = $this->db->update('student', $upddata);


		/*	$query1 = $this->db->get_where('student',array('stud_id' => (int)$data['userid'], 'active' => '1'));
	        $resultdata = $query1->row_array();

	        die();*/

			if(!$result){
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$userdata["contact_flag"] = 0;
				if($data['contact'] != $mobdata['stud_contact']){
					$userdata["contact_flag"] = 1;
					
					$upddata = array('verify_flag' => '0');
					$this->db->where('stud_id', (int)$data['userid']);
					$result = $this->db->update('student', $upddata);
			
					//send message to old number to chnange contact number 
					$smsmessage = "Hello ".$data['studname'].",";
					$smsmessage .= " Your profile updated new contact number is -".$data['contact'];
					$smsmessage .= ". Regards,mockexam Team. ";
					$this->sendSmsModel($mobdata['stud_contact'],$smsmessage);

					
					
					//send message to new number to chnange contact number 
					$smsmessage = "Hello ".$data['studname'].",";
					$smsmessage .= " Your profile updated new contact number is -".$data['contact'];
					$smsmessage .= ". Regards,mockexam Team.  ";
					$this->sendSmsModel($data['contact'],$smsmessage);

					$updatedata = array('login_flag'=>'0');
					$this->db->where(array('userid'=>$data['userid'],'usertype'=>3));
					$result = $this->db->update('usersession', $updatedata);
				}
				$query = $this->db->get_where('student',array('stud_id' => (int)$data['userid'], 'active' => '1'));
		        	$resultdata = $query->row_array();
				if($resultdata)
				{
					$userdata["userid"] = (int)$resultdata['stud_id'];
					$userdata["studname"] = $resultdata['stud_name'];
					$userdata["name"] = $resultdata['stud_name'];
					$userdata["username"] = $resultdata['username'];
					$userdata["contact"] = $resultdata['stud_contact'];
					$userdata["email"] = $resultdata['stud_email'];
					$userdata["address"] = $resultdata['address'];
					if($resultdata['prof_pic'] == null){
						$userdata["imgpath"] = "images/man.png";
					}else{
						$userdata["imgpath"] = $resultdata['prof_pic'];
					}
				}
				else
				{
					$errormessage = "This student is not found.";
				}
			}
		}
		
		return $userdata;
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
		public function getConcernDetails($data, &$errormessage)
	{
		$this->db->select('*');
		$this->db->from('fbconcern');
		$this->db->where(array('active' => '1'));
		$query = $this->db->get();
        $coursedata = $query->result_array();
		if(!$coursedata)
		{
			$coursedata = array();
			$errormessage = "Feedback are not available.";
		}		
		return $coursedata;
	}
	public function getFeedbackDetails($data, &$errormessage)
	{
		$feeddata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $feeddata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $feeddata;
		}
		else
		{
			
			$this->db->select('f.feedback_id,date(f.fbsubmit_date) as fbsubmit_date,f.fb_msg as concern');
		   	$this->db->from('fbdetails f');
		   //	$this->db->join('fbconcern AS con', 'con.concern_id = f.concern_id', 'inner');
		   	$this->db->where(array('f.student_id' => (int)$data['userid'], 'f.fbactive' => '1'));
		   	$this->db->order_by("f.feedback_id", "desc");
			$query = $this->db->get();
	        $feeddata = $query->result_array();			
			//print_r($feeddata);
			if(!$feeddata)
			{
				$feeddata = array();
				$errormessage = "Feedback are not available.";
			}
		}
				
		return $feeddata;
	}	



	public function createfeedback($data, &$errormessage)
	{
		$userid = 0;
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $userid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $userid;
		}
		else 
		{
			
			$userdata = array('student_id' => $data['userid'], 'fb_msg' => $data['feedback'],'fbsubmit_date' => $data['createddate'], 'fbactive' => '1');
			$result = $this->db->insert('fbdetails', $userdata);
			

			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$userid = $this->db->insert_id();

				$to = $data['email'];
				$from = 'contact@vidhyarthimitra.org';
				
				$subject = 'Student feedback on mockexam';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$message = '<h3>Hello sir, </h3><br>';				
				$message .= '<p>You get new feedback from student :</p><br>';
				$message .= '<p>Feedback Details</p>';
				$message .= '<p>Name: '.$data['name'].'</p>';
				$message .= '<p>Email : '.$data['email'].'</p>';
				$message .= '<p>Feedback : '.$data['feedback'].'</p>';
				$message .= '<br />';
				$message .= '<br>Regards,<br>mockexam Team.';
				
				/*$msg = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>".$exam_mode." Exam Schedule</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><h3>Dear ".$resultdata[$i]['stud_name'].",</h3></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Roll No :".$resultdata[$i]['roll_no']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Exam Name :".$resultdata[$i]['exam_name']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Exam Date :".$resultdata[$i]['exam_date']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Exam Description :#Value </td></tr>".Go_model::$footer;*/
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);
					
				
			}
			return $userid;
		}
	}

	public function getFeedbackByID($data, &$errormessage)
	{		
		$feedbackdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $feedbackdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $feedbackdata;
		}
		else
		{
			
			$this->db->select('f.fb_msg,f.reply_msg,f.fbreply_date,con.concern,date(f.fbsubmit_date) as fbsubmit_date');
		   	$this->db->from('fbdetails f');
		   	$this->db->join('fbconcern AS con', 'con.concern_id = f.concern_id', 'inner');
		   	$this->db->where(array('f.student_id' => (int)$data['userid'], 'f.fbactive' => '1', 'f.feedback_id' => (int)$data['id']));
			$query = $this->db->get();
	        $resultdata = $query->row_array();			

			if($resultdata)
			{
				$feedbackdata["coursename"] = $resultdata['course_name'];
				$feedbackdata["concerntext"] = $resultdata['concern'];
				$feedbackdata["feedback"] = $resultdata['fb_msg'];
				$feedbackdata["reply"] = $resultdata['reply_msg'];
				$feedbackdata["feedbackdate"] = $resultdata['fbsubmit_date'];
				$feedbackdata["replydate"] = $resultdata['fbreply_date'];									
			}
			else
			{
				$errormessage = "This feedback is not found.";
			}
		}
				
		return $feedbackdata;
	}
	
	
	/**
	* 
	* @param undefined $data
	* @param undefined $errormessage
	* 
	* @ Student dashboard count report
	*/	
	 
	public function dashCountStud($data, &$errormessage)
	{		
		$countdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $feedbackdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $feedbackdata;
		}
		else
		{
			$this->db->select('count(*) as coursecount',false);
			$this->db->from('course AS c');
			$this->db->join('student_buy_exam AS sc', 'sc.course_id = c.course_id', 'inner');
			$where = "CURDATE() BETWEEN sc.start_date AND sc.end_date";
			$this->db->where($where);
			$this->db->where(array('sc.stud_id'=>(int)$data['userid'],'c.active' => '1'));
			$query = $this->db->get();
	        $coursedata = $query->row_array();
	        if($coursedata){
				$countdata['coursecount'] = $coursedata['coursecount'];
			}else{
				$countdata['coursecount'] = 0;
			}
			
	        $this->db->select('count(*) as examcount',false);
			$this->db->from('student_exam');
			$this->db->where(array('stud_id'=>(int)$data['userid'],'iscomplete_flag' => '1'));
			$query = $this->db->get();
	        $examdata = $query->row_array();
	        if($examdata){
				$countdata['examcount'] = $examdata['examcount'];
			}else{
				$countdata['examcount'] = 0;
			}
		}				
		return $countdata;
	}

	public function scoreDashStud($data, &$errormessage)
	{		
		$scoredata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $scoredata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $scoredata;
		}
		else
		{
			$this->db->select('se.stud_exam_id,date(se.submitdate) as date,c.course_name,e.exam_name');
			$this->db->from('student_exam se');
			$this->db->join('course as c', 'c.course_id = se.course_id', 'inner');
			$this->db->join('exam as e', 'e.exam_id = se.exam_id', 'inner');
			$this->db->where(array('se.stud_id'=>(int)$data['userid'],'se.iscomplete_flag' => '1'));
			$this->db->order_by("se.stud_exam_id", "desc");
			$this->db->limit($data['limit'], 0);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	        if($resultdata){
				$scoredata = $resultdata;
			}else{
				$errormessage = "Exam are not available.";
				$scoredata = array();
			}
		}				
		return $scoredata;
	}
	
	public function studCourseExamResult($data, &$errormessage)
	{		
		$studdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $studdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $studdata;
		}
		else
		{
				$sql = "SELECT se.stud_exam_id,se.stud_id,se.exam_schedule_id,date(se.submitdate) as date1,
				SUM(CASE WHEN ser.correct_option_id = ser.ques_option_id THEN 1 ELSE 0 END) AS correct,
				SUM(CASE WHEN ser.correct_option_id != ser.ques_option_id THEN 1 ELSE 0 END) AS wrong,
				SUM(CASE WHEN ser.ques_option_id is not null THEN 1 ELSE 0 END) AS attempt,
				SUM(CASE WHEN ser.ques_option_id is null THEN 1 ELSE 0 END) AS not_attempt,
				SUM(CASE WHEN ser.question_id is not null THEN 1 ELSE 0 END) AS noofquestion
				FROM `vid_student_exam` as se 
				inner join vid_student_exam_result as ser on ser.stud_exam_id=se.stud_exam_id
				where se.iscomplete_flag = '1' and se.stud_id = ".$data['userid']."
				group by se.stud_exam_id order by se.stud_exam_id desc limit ".$data['limit']." ";

			$query = $this->db->query($sql);
			$result = $query->result_array();
	        if($result)
	        {
				$correct = array();
				$wrong = array();
				$attempt = array();
				$notattempt = array();
				$noofquestion = array();
				$examattemp = array();
				for($i=0;$i<count($result);$i++){
					
					$examattemp[] = $result[$i]['date1'];
					$correct[] = (int)$result[$i]['correct'];
					$wrong[] = (int)$result[$i]['wrong'];
					$attempt[] = (int)$result[$i]['attempt'];
					$notattempt[] = (int)$result[$i]['not_attempt'];
					$noofquestion[] = (int)$result[$i]['noofquestion'];
				}
				
				$studdata['examattemp'] = $examattemp;
				$studdata['correct'] = $correct;
				$studdata['wrong'] = $wrong;
				$studdata['attempt'] = $attempt;
				$studdata['notattempt'] = $notattempt;
				$studdata['noofquestion'] = $noofquestion;
			}
			else{
				$studdata['examattemp'] = $examattemp;
				$studdata['correct'] = $correct;
				$studdata['wrong'] = $wrong;
				$studdata['attempt'] = $attempt;
				$studdata['notattempt'] = $notattempt;
				$studdata['noofquestion'] = $noofquestion;
				
			}	
		}	
		$studdata['examResultData'] = $result;
		return $studdata;
	}
		public function getCourse($data, &$errormessage)
	{
		$coursedata = array();
			
			  	$this->db->select('n.note_id as noteid,np.note_path_id as id,n.title,c.course_name as course,np.type,np.file_name as display_name,np.note_path as path');
				$this->db->from('notes_path as np');
				$this->db->join('notes AS n', 'n.note_id = np.note_id and n.active = "1"', 'inner');
				$this->db->join('course AS c', 'c.course_id = n.course_id', 'inner');
				$this->db->join('student_buy_exam AS sbe', 'sbe.course_id = c.course_id', 'inner');

				//$this->db->where_in('n.course_id', $subData['course_id']);
				$this->db->where(array('sbe.stud_id' =>$data['userid']));
				
				if($data['searchtext'] != null && $data['searchtext'] != ''){
					$where = "( n.title like '%".$data['searchtext']."%' || np.file_name like '%".$data['searchtext']."%' || np.type like '%".$data['searchtext']."%' )";
					$this->db->where($where);
				}
				
				$this->db->where(array('np.active' => '1'));

				$this->db->order_by('np.note_path_id desc');
				$this->db->group_by('n.note_id');
				$query = $this->db->get();
		        $notesdata = $query->result_array();
		       // echo "<pre>";
		       // print_r($notesdata);
			if(!$notesdata)
			{
				$notesdata = array();
				$errormessage = "Study Material are not available.";
			}
			
		/*}*/
		return $notesdata;
		//print_r($notesdata);
	}
	public function getStudentRanks($data, &$errormessage)
	{
				$this->db->select('sr_stud_roll_no,sr_schedule_id');
				$this->db->from('student_final_result');
				$this->db->where(array('sr_stud_id' =>$data['userid']));
				$this->db->order_by('sr_id desc');
				$query = $this->db->get();
		        $notesdata = $query->result_array();
		        $masterRankArray =[];
		        for ($i=0; $i < count($notesdata); $i++) { 

		        	$result1 = $this->db->query("call Getstudentrank(".$notesdata[$i]['sr_schedule_id'].",".$data['userid'].")");
		        	//$result1 = $this->db->query("call Getstudentrank(270,318)");
		        	$getSingleRank = $result1->row_array();
		        	$query->next_result();
        			$query->free_result();
        			$this->db->reconnect();
        			$masterRankArray[$i] = $getSingleRank;
		        }
		
		if(!$masterRankArray){
			return 0;
		}
		return $masterRankArray;
	}
	
}

/* End of file stud_model.php */

/* Location: ./application/models/stud_model.php */
<?php

require "Go_model.php";
class Master_stud_model extends Go_model 
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
			//echo "return true";
			return TRUE;
		}
		else
		{ //echo "return false";
			return FALSE;
		}
		return TRUE;
	}
	public function getNewStudModel($data, &$errormessage)
	{
		
		$newAddedStud = array();
			$this->db->select('stud_id,stud_name,stud_contact,stud_email,submitdate,mailStatus,dob,address,standard,college_name,mother_name,pin_code');
			$this->db->from('student');
			if($data['startdate'] != null && $data['startdate'] != '')
			{
				$where = "DATE(submitdate) BETWEEN '".$data['startdate']."' AND '".$data['enddate']."' ";
				$this->db->where($where);
			}
			if($data['searchtext'] != null)
			{
				$this->db->like('stud_name',$data['searchtext']);
			}
			if($data['searchmail'] != null)
			{
				$this->db->like('stud_email',$data['searchmail']);
			}
			if($data['searchmobile'] != null)
			{
				$this->db->like('stud_contact',$data['searchmobile']);
			}
			if($data['mailStatus'] != null)
			{
				$this->db->where('mailStatus='.$data['mailStatus']);
			}
			$this->db->where('active="1"');
			$this->db->order_by('stud_id',desc);
			$query = $this->db->get();
	        $newAddedStud = $query->result_array();
			if(!$newAddedStud)
			{
				$newAddedStud = array();
				$errormessage = "Students are not available.";
			}
				
		return $newAddedStud;
	}
	public function getNewPaymentStudModel($data, &$errormessage)
	{
		//print_r($data['adminCollect']);
		//print_r($data['paytmCollect']);
		$newAddedStud = array();
			$this->db->select('sbe.stud_course_batch_id as batchId,s.stud_id,s.stud_name,es.fee as fees,spd.payment_type,e.exam_name,es.exam_date,sbe.exam_schedule_id as examId,sbe.submitdate,es.exam_mode,s.stud_email,s.stud_contact');
			$this->db->from('student as s');
			$this->db->join("student_buy_exam as sbe",'sbe.stud_id = s.stud_id','inner');
			$this->db->join("student_payment_details as spd",'spd.payment_id = sbe.payment_id','inner');
			$this->db->join("exam_schedule as es",'es.schedule_id = sbe.exam_schedule_id','inner');
			$this->db->join("exam as e",'e.exam_id = es.exam_id','inner');

				if($data['startdate'] != null && $data['startdate'] != '')
				{
					$where = "DATE(sbe.submitdate) BETWEEN '".$data['startdate']."' AND '".$data['enddate']."' ";
					$this->db->where($where);
				}
				if($data['searchtext'] != null)
				{
					$this->db->like('s.stud_name',$data['searchtext']);
				}

				if($data['adminCollect'] === $data['paytmCollect'])
				{ 
					//echo "condition 1";
				}else 
				if($data['adminCollect'] == 'true')
				{ 
					$this->db->like('spd.payment_type','Admin');
					//echo "condition 2";
				}else
				if($data['paytmCollect'] == 'true')
				{
					$this->db->like('spd.payment_type','Paytm');
					//echo "condition 3";
				}
				else{}

				if($data['searchtext'] != null)
				{
					$this->db->like('s.stud_name',$data['searchtext']);
				}
				
				if($data['onlineStud'] == 'true')
				{ 
					$this->db->like('es.exam_mode','0');
				}else
				if($data['OfflineStud'] == 'true')
				{ 
					$this->db->like('es.exam_mode','1');
				}
				else{}
				/*if($data['mailStatus'] != null)
				{
					$this->db->where('s.mailStatus='.$data['mailStatus']);
				}*/

			$this->db->where('s.active="1"');
			$this->db->order_by('sbe.stud_course_batch_id','desc');
			$query = $this->db->get();
	        $newAddedStud = $query->result_array();
	       // print_r($newAddedStud);

			if(!$newAddedStud)
			{
				$newAddedStud = array();
				$errormessage = "Students are not available.";
			}
		/*echo "<pre>";
		print_r($sub1_id);
		die();	*/	
		return $newAddedStud;
	}
	public function getNewSubscribeList($data, &$errormessage)
	{
		
			$this->db->select('subscriber_contact,subscriber_email,subscribe_date');
			$this->db->from('subscriber');
			$this->db->order_by('subscriber_id',desc);
			$query = $this->db->get();
	        $newAddedStud = $query->result_array();
	      

			if(!$newAddedStud)
			{
				$newAddedStud = array();
				$errormessage = "Subscribe list not available.";
			}
				
		return $newAddedStud;
	}
	public function gettestimonialList($data, &$errormessage)
	{
		
			$this->db->select('*');
			$this->db->from('studfeedback');
			$this->db->order_by('sf_id',desc);
			$query = $this->db->get();
	        $newAddedStud = $query->result_array();
	      

			if(!$newAddedStud)
			{
				$newAddedStud = array();
				$errormessage = "Feedback list not available.";
			}
				
		return $newAddedStud;
	}
	public function gettestimonialListForAdmin($data, &$errormessage)
	{
		
			$this->db->select('*');
			$this->db->from('studfeedback');
			$this->db->where(array('status'=>'1'));
			 $this->db->order_by('rand()');
    		$this->db->limit(3);
			$query = $this->db->get();
	        $newAddedStud = $query->result_array();
	      
//print_r($newAddedStud);
			if(!$newAddedStud)
			{
				$newAddedStud = array();
				$errormessage = "
				Feedback list not available.";
			}
				
		return $newAddedStud;
	}
	public function changeStatusTestModel($data, &$errormessage){
			//print_r($data);
				$upddata = array('status' => $data['finalstatus']);
			
			$this->db->where(array('sf_id'=> (int)$data['testId']));
			$paradata = $this->db->update('studfeedback', $upddata);
			//print_r($upddata);
		return $paradata;
	}
	public function deleteMultipleStudentModel($data, &$errormessage)
	{
		
		$questionid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questionid;
		}
		else
		{
			$result = false;
			foreach($data['ids'] as $id){
				$upddata = array('active'=>'0');
				$this->db->where(array('stud_id'=>$id));
				$result = $this->db->update('student', $upddata);
				//$query = $this->db->where(array('stud_id' => $id));
				//$result = $this->db->delete('student');
			}
			if($result)
			{
				$questionid = $data['ids'];	
			}
			else
			{
				$errormessage = "This question is either deleted or not found.";
			}
		}
				
		return $questionid;
	}
	public function addStudModel($data, &$errormessage)
	{
		/*print_r($data);
		die();*/
		$password = rand(pow(10, 6-1), pow(10, 6)-1);
		$studId = 0;
		$where = '(stud_email="'.$data['email'].'" or stud_contact = '.$data['contact'].')';
		//$query = $this->db->get_where('getallusers',array('useremail' => $data['email'],'usercontact' => $data['contact']));
		$query = $this->db->get_where('student',$where);
        $userdata = $query->row_array();

		//$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if($userdata)
		{
			$errormessage = "This email or phone is already exists. Please try another.";
			return $studId;
		}
		else
		{
			$paraArr = array('stud_name' => $data['studname'], 
								'gender' => $data['gender'], 
								'dob' => $data['dob'], 
								'stud_email' => $data['email'],
								'stud_contact' => $data['contact'], 
								'mother_name' => $data['mother_name'],
								'address' => $data['address'], 
								'pin_code' => $data['pin'], 
								'standard' => $data['standard'], 
								'country' => $data['country'],
								'other_country' => $data['other_country'],
								'state' => $data['state'],
								'stud_password'=>md5($password),
								'district' => $data['district'],
								'college_name' => $data['college_name'],
								'author_id' => 2,
								'mailStatus'=>0,
								'submitdate' => $data['createddate'], 
								'coordinator_name' => $data['coordinator'], 
								'how_to_know' => $data['how_to_know']);
			$result = $this->db->insert('student', $paraArr);
				

		
			if($result)
			{ 
				$studId = $this->db->insert_id();
				$data['usersessionid'] = md5(uniqid());
				$usersessiondata = array('usersessionid' => $data['usersessionid'], 'userid' => $studId, 'createddate' => $data['createddate'], 'usertype' => 3);
				$this->db->insert('usersession', $usersessiondata);
				//echo "send mail loading";
				
			//OTP SMS SEND
				$smsmessage = "Hello ".$data['studname'].",";
				$smsmessage .= "Your registration is successfully.Username is : ".$data['email'].", Password is : ".$password;
				$smsmessage .= ". Regards,mockexam ";
				$this->sendSmsModel($data['contact'],$smsmessage);

			//email send
				$to = $data['email'];
				//$to= 'pratikpawarnasik@gmail.com';
				$from = 'pratikpawarnasik@gmail.com';
				$subject = 'Registration on mockexam.org';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>mockexam Registration</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$data['studname']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Get ready to experience a whole new way of learning. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Email/Username :".$data['email']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Password :".$password." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);
				
				if ($sendmail) {

					$upddata = array('mailStatus'=>1);
					$this->db->where(array('stud_id'=>(int)$studId));
					$result = $this->db->update('student', $upddata);
					return $result;
				}
				else{
					
					return $studId;
				}
				
					
			

			}
			else{
					$errormessage = "Some unknown error has occurred. Please try again.";
			}
		}
		
		return $studId;
	
		
	}
	public function updateStudModel($data, &$errormessage)
	{
		/*print_r($data);
		die();*/
		$studId = 0;
		$where = '(stud_email="'.$data['email'].'" or stud_contact = '.$data['contact'].')';
		//$query = $this->db->get_where('getallusers',array('useremail' => $data['email'],'usercontact' => $data['contact']));
		$query = $this->db->get_where('student',$where);
        $userdata = $query->row_array();
		//$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(!$userdata)
		{
			$errormessage = "Student not available,please check student id.";
			return $studId;
		}
		else
		{
			$upddata = array('stud_name' => $data['studname'], 
								'gender' => $data['gender'], 
								'dob' => $data['dob'], 
								'stud_email' => $data['email'],
								'stud_contact' => $data['contact'], 
								'mother_name' => $data['mother_name'],
								'address' => $data['address'], 
								'pin_code' => $data['pin'], 
								'standard' => $data['standard'], 
								'country' => $data['country'],
								'other_country' => $data['other_country'],
								'state' => $data['state'],
								'district' => $data['district'],
								'college_name' => $data['college_name'],
								'coordinator_name' => $data['coordinator'], 
								'how_to_know' => $data['how_to_know']);
			$this->db->where(array('stud_id'=>$userdata['stud_id']));
			$updatestud = $this->db->update('student',$upddata );
		
			if($updatestud)
			{ 		
				$password = rand(pow(10, 6-1), pow(10, 6)-1);
			//OTP SMS SEND
				$smsmessage = "Hello ".$data['studname'].",";
				$smsmessage .= "Your registration is successfully. Username is : ".$studId.", Password is : ".$password;
				$smsmessage .= ". Regards,mockexam Team. ";
				//$this->sendSmsModel($data['contact'],$smsmessage);

			//email send
				$to = $data['email'];
				//$to= 'pratikpawarnasik@gmail.com';
				$from = 'pratikpawarnasik@gmail.com';
				$subject = 'Registration on mockexam.in';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				/*$message = '<h3>Dear '.$data['studname'].', </h3><br>';				
				$message .= '<p>Welcome to mockexam. Get ready to experience a whole new way of learning.</p>';
				$message .= '<p>The credentials to your account are as follows:</p>';
				$message .= '<p>User Details</p>';
				$message .= '<p>Email/Username : '.$data['email'].'</p>';
				$message .= '<p>Password : '.$password.'</p>';
				$message .= '<p>Please click on the below link to login using the above credentials.</p>';*/
				$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>mockexam Registration</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$data['studname']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Get ready to experience a whole new way of learning. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Email/Username :".$data['email']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Password :".$password." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;	

				$sendmail = $this->sendEmail($to, $subject, $message, $headers);
				
				//echo $sendmail;
				//print_r($sendmail);
				if ($sendmail) {

					$upddata = array('mailStatus'=>1);
					$this->db->where(array('stud_id'=>(int)$studId));
					$result = $this->db->update('student', $upddata);
					return $result;
				}
				else{
					
					return $updatestud;
				}
			}
			else{
					$errormessage = "Some unknown error has occurred. Please try again.";
			}
		}
		return $studId;
	}
	public function examAlert($data, &$errormessage)
	{
		$upddata = 0;
				$userdata = array();
				$this->db->select('s.stud_name,s.stud_email,s.stud_contact,sbe.roll_no,es.exam_date,e.exam_name,es.exam_mode');
				$this->db->from('student_buy_exam AS sbe');
				$this->db->join('exam_schedule AS es', 'es.schedule_id = sbe.exam_schedule_id','inner');
				$this->db->join('exam AS e', 'es.exam_id = e.exam_id','inner');
				$this->db->join('student AS s', 's.stud_id = sbe.stud_id','inner');
				$today = date("Y-m-d", strtotime(' +1 day'));
				//$this->db->where("es.exam_date =",$today);
				$this->db->group_by('sbe.roll_no');
				$query = $this->db->get();
	        	$resultdata = $query->result_array();
	        	
	     if (count($resultdata) > 0) {
			//email send
				for($i=0;$i<count($resultdata);$i++){
					if ($resultdata[$i]['exam_mode'] == 0) {
						$exam_mode = 'Online';
					}else
					if ($resultdata[$i]['exam_mode'] == 1) {
						$exam_mode = 'Offline (Paper & Pen)';
					} 

					$smsmessage = "Hello, ".$resultdata[$i]['stud_name'].",";
					$smsmessage .= "Your " .$exam_mode.",".$resultdata[$i]['exam_name']." Exam is Scheduled at ".$resultdata[$i]['exam_date'];
					$smsmessage .= ". Regards,mockexam Team. ";
					$this->sendSmsModel(9637960396,$smsmessage);
					//$to = $resultdata[$i]['stud_email'];
					$to = 'pratikpawarnasik@gmail.com';
					$from = 'pratikpawarnasik@gmail.com';
					$subject = 'mockexam : Your Exam Schedule';
					$headers = "From: " . strip_tags($from) . "\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
					
					$msg = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>".$exam_mode." Exam Schedule</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><h3>Dear ".$resultdata[$i]['stud_name'].",</h3></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Roll No :".$resultdata[$i]['roll_no']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Exam Name :".$resultdata[$i]['exam_name']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Exam Date :".$resultdata[$i]['exam_date']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Exam Description :#Value </td></tr>".Go_model::$footer;
					//echo $msg;
					$sendmail = $this->sendEmail($to, $subject, $msg, $headers);






		die();		//	echo $message;
				}	     		
	     }
	     if ($sendmail) {

			$upddata = array('mailStatus'=> 1);
			
			return $upddata;
		}
		else{
			
			return $upddata;
		}
	}
	public function resendMailMessage($data, &$errormessage)
	{
		$result = 0;
				$query = $this->db->get_where('student',array('stud_id' => (int)$data['stuId']));
	        	$resultdata = $query->row_array();
	    

	     if ($resultdata) {
	     //change mailsend status
	     		/*$upddata = array('mailStatus'=> 0);
				$this->db->where(array('stud_id'=>(int)$data['stuId']));
				$result = $this->db->update('student', $upddata);*/

	     		$password = rand(pow(10, 6-1), pow(10, 6)-1);
			//OTP SMS SEND
				$smsmessage = "Hello ".$resultdata['stud_name'].",";
				$smsmessage .= "Your registration is successfully.User name is : ".$resultdata['stud_contact']." and Password is : ".$password;
				$smsmessage .= ". Regards,mockexam Team.  ";
				$this->sendSmsModel($resultdata['stud_contact'],$smsmessage);

				$upddata = array('stud_password'=>md5($password),);
					$this->db->where(array('stud_id'=>(int)$data['stuId']));
					$result = $this->db->update('student', $upddata);
				/*$sendsms=new sendsms();
				$output = $sendsms->send_sms($resultdata['stud_contact'], $smsmessage, '', 'xml');
				die();*/
				
			//email send
				$to = $resultdata['stud_email'];
				//$to = 'pratikpawarnasik@gmail.com';
				$from = 'contact@pratikpawar.com';
				
				$subject = 'Registration on mockexam.org';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
			

				$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>mockexam Registration</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$resultdata['stud_name']."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Get ready to experience a whole new way of learning. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Email/Username :".$resultdata['stud_email']." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Password :".$password." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);

	     		if ($result) {

					$upddata = array('mailStatus'=> 1);
					$this->db->where(array('stud_id'=>(int)$data['stuId']));
					$result = $this->db->update('student', $upddata);
					return (int)$data['stuId'];
				}
				else{
					
					return (int)$data['stuId'];
				}
	     		
	     }
	     return $result;
	}
	public function createMasterStudentList($data, &$errormessage)
	{

		$studId = 0;
		$today = date('Y-m-d');
		$examSchuduleId = $data['list'][0][13];
		$password = rand(pow(10, 6-1), pow(10, 6)-1);
		//$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		$where = '(stud_email="'.$data['list'][0][3].'" or stud_contact = '.$data['list'][0][4].')';
		$query = $this->db->get_where('student',$where);
        $userdata = $query->row_array();
        // Check exam code is valid
        $this->db->select('*');
        $this->db->from('exam_schedule as es');
        $this->db->where('es.exam_date >=', date('Y-m-d')); 
        $this->db->where('es.schedule_id =', $examSchuduleId); 
        $query = $this->db->get();
	    $examData = $query->result_array();


		if($userdata)
		{
			$errormessage = "This email or phone is already exists. Please try another.";
			return $userid;
		}
		elseif (count($examData) == 0) {

			$errormessage = "Please check, Exam not available now.";
			return $userid;
		}
		else
		{
			
			$paraArr = array('stud_name' => $data['list'][0][0], 
								'gender' => $data['list'][0][1], 
								'dob' => $data['list'][0][2], 
								'stud_email' => $data['list'][0][3], 
								'stud_contact' => $data['list'][0][4], 
								'mother_name' => $data['list'][0][5],
								'address' => $data['list'][0][6], 
								'pin_code' => $data['list'][0][7], 
								'standard' => $data['list'][0][8], 
								'college_name' => $data['list'][0][9],
								'author_id' => 2,
								'mailStatus'=>0,
								'stud_password'=> md5($password),
								'submitdate' => $data['createddate'], 
								'mailStatus' =>0, 
								'coordinator_name' => $data['list'][0][10]);
			$result = $this->db->insert('student', $paraArr);
			//$result = 1;
			if($result)
			{ 	

				$studId = $this->db->insert_id();
				if (count($examData)) {
					$data["studentid"] = $studId;
					$data["schedule_id"] = $examSchuduleId;
					$data["amount"] = $data['list'][0][11];
					$data["course_id"] = $data['list'][0][12];
					$data["createddate"] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
					$this->payAmount($data);
				}
				

				$data['usersessionid'] = md5(uniqid());
				$usersessiondata = array('usersessionid' => $data['usersessionid'], 'userid' => $studId, 'createddate' => $data['createddate'], 'usertype' => 3);
				$this->db->insert('usersession', $usersessiondata);
				// Send conformation Message
				

				$smsmessage = "Hello ".$data['list'][0][0].",";
				//$smsmessage .= "Your registration is successful Please check your email.";
				$smsmessage .= "Your registration is successfully. User id is your email id / Mobile number, Password is : ".$password;
				$smsmessage .= ". Regards,mockexam Team.";
				$this->sendSmsModel($data['list'][0][4],$smsmessage);

					//email send
					$to = $data['list'][0][3];
					$from = 'contact@pratik.org';
					$subject = 'Registration on mockexam';
					$headers = "From: " . strip_tags($from) . "\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
					$message = Go_model::$header."<tr><td style='background-color:#FF552A;margin: 10px'><h2 align='center' style='color: #fff; font-size: 27px;font-family: Helvetica;'>mockexam Registration</h2></td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Dear ".$data['list'][0][0]."</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>Welcome to mockexam. Get ready to experience a whole new way of learning. </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'>The credentials to your account are as follows:</td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Email/Username :".$data['list'][0][0]." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;;'>Password :".$password." </td></tr><tr><td style='padding-top: 20px;color: #000; font-size: 15px;font-family: Helvetica;'><a href='".base_url()."'index.html' target='_blank'>Click Here</a> </td></tr><tr>".Go_model::$footer;
					$sendmail = $this->sendEmail($to, $subject, $message, $headers);
				if($sendmail) {

					$upddata = array('mailStatus'=>1);
					$this->db->where(array('stud_id'=>(int)$studId));
					$result = $this->db->update('student', $upddata);
					return $studId;
				}
				else{
					
					return $studId;
				}
			return $studId;

			}
			else{
					$errormessage = "Some unknown error has occurred. Please try again.";
			}
		}
		
		//return $studId;
	}

	public function payAmount($data)
	{
		$tempid = array();
		$paymid = 0;
					$data['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
					$paymdata = array('txnid' => $data['txnid'], 'stud_id' => $data["studentid"], 'amount' => $data['amount'], 'payment_date' => date('Y-m-d H:i:s'), 'payment_type' => 'Admin','status'=>'1','course_id'=>$data['course_id']);
					$this->db->insert('student_payment_details', $paymdata);
					$payment_id = $this->db->insert_id();

					$this->db->select('roll_no');
					$this->db->from('student_buy_exam');			
					$this->db->where(array('exam_schedule_id'=>$data['schedule_id']));
					$this->db->order_by("stud_course_batch_id", "desc");
					$query = $this->db->get();
			        $resultdata = $query->row_array();
			        if ($resultdata['roll_no']== 0) {
			        	//$previousRoll = $resultdata['roll_no']+1;
			        	$previousRoll = str_pad($resultdata['roll_no'] + 1, 4, 0, STR_PAD_LEFT);
			        	$courseId = str_pad($data['course_id'], 2, 0, STR_PAD_LEFT);
			        	$schuduleId = str_pad($data['schedule_id'], 4, 0, STR_PAD_LEFT);
			        	$rollNo= $courseId.$schuduleId.$previousRoll;
			        }else{
			        	$tempRoll = str_pad($resultdata['roll_no']+1, 10, 0, STR_PAD_LEFT);
			        	$rollNo = $tempRoll ;

			        }
					    
					$tempdata = array('stud_id' => $data['studentid'],'exam_schedule_id' => $data['schedule_id'],'payment_id' => $payment_id,'is_payment'=>'1','submitdate' => date('Y-m-d H:i:s'),'roll_no'=>$rollNo,'course_id'=>$data['course_id']);
					$result = $this->db->insert('student_buy_exam', $tempdata);
		
		return $tempid;
	}
	public function getStudentByID($data, &$errormessage)
	{		
		$studdata = array();
		
		
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
				$errormessage = "This Student is either deleted or not found.";
			}
		
				
		return $studdata;
	}
	public function createDemoStudModelotpsend($data, &$errormessage)
	{
		$otp = 0;
		
		$this->db->select('id');
		$where = '( email="'.$data['email'].'" or contact="'.$data['contact'].'" )';
		$query = $this->db->get_where('demo_test_student',$where);
        $userdata = $query->row_array();
        //print_r($userdata);
		if(count($userdata) == 0)
		{
			$otp = rand(pow(10, 6-1), pow(10, 6)-1);
			$smsmessage = "Hello ".$data['name'].",";
			$smsmessage .= "Demo Test OTP is ".$otp;	
			$smsmessage .= ". Regards,mockexam Team.  ";			
			$sendsms= $this->sendSmsModel($data['contact'],$smsmessage);
			if($sendsms){
				return $otp;
			}
			return 0;
		}
		else
		{
			$smsmessage = "Hello ".$data['name'].",";
			$smsmessage .= "You have already taken demo test,Please Registration and buy Exam.";	
			$smsmessage .= " Regards,mockexam Team. https://goo.gl/y62kr9. ";			
			$sendsms= $this->sendSmsModel($data['contact'],$smsmessage);

			$errormessage = "You have already taken Demo Test. Please register for more Exam.";
		}
		return $otp;
	}
	public function getAllStudentRanks($data, &$errormessage)
	{
				$this->db->select('sfr.sr_stud_roll_no,sfr.sr_schedule_id,sfr.sr_stud_id,e.exam_name,es.exam_date,sg.subject_group_name');
				$this->db->from('student_final_result as sfr');
				$this->db->join('exam AS e', 'e.exam_id = sfr.sr_exam_id','inner');
				$this->db->join('exam_schedule AS es', 'es.schedule_id = sfr.sr_schedule_id','inner');
				$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id','inner');
				$this->db->order_by('sfr.sr_id desc');
				$this->db->group_by('sfr.sr_stud_roll_no');
				if($data['selectExam'] != null && $data['selectExam'] != ''){
					$this->db->where(array('sfr.sr_exam_id' =>$data['selectExam']));
				}
				if($data['selectSchedule'] != null && $data['selectSchedule'] != ''){
					$this->db->where(array('sfr.sr_schedule_id' =>$data['selectSchedule']));
				}
				$query = $this->db->get();
		        $notesdata = $query->result_array();
		       /* echo "<pre>";
		        print_r($notesdata);*/
		        $masterRankArray =[];
		        for ($i=0; $i < count($notesdata); $i++) { 

		        	$result1 = $this->db->query("call Getstudentrank(".$notesdata[$i]['sr_schedule_id'].",".$notesdata[$i]['sr_stud_id'].")");
		        	//$result1 = $this->db->query("call Getstudentrank(270,318)");
		        	$getSingleRank = $result1->row_array();
		        	$getSingleRank['exam_name'] = $notesdata[$i]['exam_name'];
		        	$getSingleRank['exam_date'] = $notesdata[$i]['exam_date'];
		        	$getSingleRank['subject_group_name'] = $notesdata[$i]['subject_group_name'];
		        	$query->next_result();
        			$query->free_result();
        			$this->db->reconnect();
        			$masterRankArray[$i] = $getSingleRank;
		        }
		/*echo "<pre>";
		print_r($masterRankArray);
		die();*/
		if(!$masterRankArray){
			return 0;
		}
		return $masterRankArray;
	}
	public function getAllExamRanks($data, &$errormessage)
	{
				$this->db->select('e.exam_name,e.exam_id');
				$this->db->from('student_final_result as sfr');
				$this->db->join('exam AS e', 'e.exam_id = sfr.sr_exam_id','inner');
				$this->db->group_by('sfr.sr_exam_id');
				$query = $this->db->get();
		        $examList = $query->result_array();
		       
		if(!$examList){
			return 0;
		}
		return $examList;
	}
	public function rankScheduleList($data, &$errormessage)
	{
				$this->db->select('sfr.sr_schedule_id,es.exam_date,sg.subject_group_name');
				$this->db->from('student_final_result as sfr');
				//$this->db->join('exam AS e', 'e.exam_id = sfr.sr_exam_id','inner');
				$this->db->join('exam_schedule AS es', 'es.schedule_id = sfr.sr_schedule_id','inner');
				$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id','inner');
				$this->db->where(array('es.exam_id' =>$data['selectExam']));
				$this->db->group_by('es.schedule_id');
				$query = $this->db->get();
		        $scheduleList = $query->result_array();
		       
		if(!$scheduleList){
			return 0;
		}
		return $scheduleList;
	}
}

/* End of file Question_model.php */

/* Location: ./application/models/Question_model.php */
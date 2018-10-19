<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Student extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Student_model');
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
	
	public function bulksms_get()
	{
		//OTP SMS SEND
		$data = $this->Student_model->bulksms();
		return $data;
	}

	public function create_post(){
		$data = array();
		$data['name'] = $this->post('name');
		/*$data['gender'] = $this->post('gender');
		$data['dob'] = $this->post('dob');*/
		$data['email'] = $this->post('email');
		$data['contact'] = $this->post('contact');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);

		
		if($valid)
		{
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = md5(uniqid());
			
			$userid = $this->Student_model->createUser($data, $errormessage);
			$valid = ((int)$userid > 0);	
		}
		
		if($valid)
		{
			//$message = "We have sent instructions on ".$data['email']." which will help you to generate your password and access your account. Please check your email now.";
			$message = PageBase::$successmessage;
			$json = array("status"=>200, "message"=>$message);
			$json["id"] = $userid;
			$json["userid"] = $userid;
			$json["name"] = $data['name'];
			$json["username"] = $data['uname'];
			$json["imgpath"] = "images/man.png";
			$json["type"] = 3;
			$json["address"] = null;
			$json["contact_flag"] = 0;
			$json["contact"] = $data['contact'];
			$json["authcode"] = $data['usersessionid'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
		
	public function createDemoStud_post()

	{
		$data = array();
		$data['name'] = $this->post('name');
		
		$data['email'] = $this->post('email');
		$data['contact'] = $this->post('contact');
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			
			$userid = $this->Student_model->createDemoStudModel($data, $errormessage);
			$valid = ((int)$userid > 0);	
		}
		if($valid)
		{
			//$message = "We have sent instructions on ".$data['email']." which will help you to generate your password and access your account. Please check your email now.";
			$message = PageBase::$successmessage;
			$json = array("status"=>200, "message"=>$message);
			$json["userdemoid"] = $userid;
			$json["name"] = $data['name'];
			$json["contact"] = $data['contact'];
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function checkotp_put()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['otp'] = $this->put('otp');
		$data['userid'] = $this->put('userid');
		$errormessage = "";
			
		$userdata = $this->Student_model->checkOTP($data, $errormessage);
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
	public function setPassword_put()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['passwordForm'] = $this->put('password');
		$data['userid'] = $this->put('userid');
		$errormessage = "";
			
		$userdata = $this->Student_model->setPassword($data, $errormessage);
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
	
	public function changepassword_put()
	{
		$data = array();
		$data['id'] = $this->put('id');
		$data['password'] = $this->put('password');
		$data['otp'] = $this->put('otp');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$userdata = $this->Student_model->changepassword($data, $errormessage);
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
	
	public function changepasswordprofile_put()
	{
		$data = array();
		$data['id'] = $this->put('userid');
		$data['password'] = $this->put('password');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['oldpassword'] = $this->put('oldpassword');
			$data['show'] = $this->put('show');
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$userdata = $this->Student_model->changepasswordprofile($data, $errormessage);
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
	
	public function getsocialuser_post()
	{
		$data = array();
		$data['faceid'] = $this->post('faceid');
		
		$errormessage = "";
		
			$userdata = $this->Student_model->getsocialuser($data, $errormessage);
		
		if(count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["userid"] = $userdata['userid'];
			$json["name"] = $userdata['name'];
			$json["imgpath"] = $userdata['imgpath'];
			$json["instid"] = $userdata['instid'];
			$json["authcode"] = $userdata['authcode'];
			$json["email"] = $userdata['email'];
			$json["contact"] = $userdata['contact'];
			$json["type"] = $userdata['type'];
			$json["verify"] = $userdata['verify'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getsocial_get()
	{
		$data = array();
		$data['socialid'] = $this->get('socialid');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$userdata = $this->Student_model->getsocial($data, $errormessage);
		}
		if(count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["userid"] = $userdata['userid'];
			$json["name"] = $userdata['name'];
			$json["imgpath"] = $userdata['imgpath'];
			$json["address"] = $userdata['address'];
			$json["contact_flag"] = $userdata['contact_flag'];
			$json["instid"] = $userdata['instid'];
			$json["authcode"] = $userdata['authcode'];
			$json["email"] = $userdata['email'];
			$json["contact"] = $userdata['contact'];
			$json["type"] = $userdata['type'];
			$json["verify"] = $userdata['verify'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function socialCreate_post()
	{
		$data = array();
		$data['faceid'] = $this->post('faceid');
		$data['contact'] = $this->post('contact');
		$data['email'] = $this->post('email');
		$data['name'] = $this->post('name');
		$data['regtype'] = $this->post('regtype');
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = md5(uniqid());
			$userdata = $this->Student_model->socialCreateUser($data, $errormessage);
		}	
		if(count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["userid"] = $userdata['userid'];
			$json["name"] = $userdata['name'];
			$json["contact"] = $userdata['contact'];
			$json["imgpath"] = $userdata['imgpath'];
			$json["authcode"] = $userdata['authcode'];
			$json["email"] = $userdata['email'];
			$json["type"] = $userdata['type'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function createSocialUser_post()
	{
		$data = array();
		$data['socialid'] = $this->post('socialid');
		$data['contact'] = $this->post('contact');
		$data['email'] = $this->post('email');
		$data['name'] = $this->post('name');
		$data['regtype'] = $this->post('regtype');
		$data['instid'] = 123456789;
		
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = md5(uniqid());
			$userdata = $this->Student_model->createSocialUser($data, $errormessage);
		}	
		if(count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["userid"] = $userdata['userid'];
			$json["name"] = $userdata['name'];
			$json["instid"] = $userdata['instid'];
			$json["contact"] = $userdata['contact'];
			$json["authcode"] = $userdata['authcode'];
			$json["imgpath"] = $userdata['imgpath'];
			$json["email"] = $userdata['email'];
			$json["type"] = $userdata['type'];
			$json["verify"] = $userdata['verify'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function index_get($userid)
	{
		$data = array();
		
		$data['userid'] = $userid[0];
		$data['userid'] = $this->get('id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$userdata = $this->Student_model->getUser($data, $errormessage);
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
	

	public function course_get()
	{
		
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['searchtext'] = $this->get('searchtext');
	
		$filename = 'log.txt';

 
		$coursedata = $this->Student_model->getCourse($data, $errormessage);
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
	public function exams_get()
	{
		$data = array();
		$data['userid'] = $this->get('id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
	
		$filename = 'log.txt';

    /*$current = file_get_contents($filename);
    $current .= "   ".$data['usersessionid']."   id :- ".$data['userid']."    \n";
    echo $current;
    file_put_contents($filename, $current);*/
     
    
		$coursedata = $this->Student_model->getexamsmodel($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['exams'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function examsData_get()
	{
		$data = array();
		$data['userid'] = $this->get('id');
		$data['sortBy'] = $this->get('sortBy');
		$data['usersessionid'] = PageBase::GetHeader("authcode");

		$coursedata = $this->Student_model->getexamsdatamodel($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examdata'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function allExamsDat_get(){
		$data = array();
		
		//$data['usersessionid'] = PageBase::GetHeader("authcode");

		$coursedata = $this->Student_model->getallexamsdatamodel($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examdata'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function examsDataById_get(){
		$data = array();

		$data['examCourseId']= $this->get('examCourseId');
		//$data['usersessionid'] = PageBase::GetHeader("authcode");

		$coursedata = $this->Student_model->getexamsDataByIdamodel($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examdata'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function allState_get(){
		$stateData = array();
		
		//$data['usersessionid'] = PageBase::GetHeader("authcode");

		$stateData = $this->Student_model->getStateModal($data, $errormessage);
		if(isset($stateData) && count($stateData) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['stateData'] = $stateData;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function getDistrict_get(){
		$districtData = array();
		$data['state_id']= $this->get('state_id');
		//$data['usersessionid'] = PageBase::GetHeader("authcode");

		$districtData = $this->Student_model->getDistrictModal($data, $errormessage);
		if(isset($districtData) && count($districtData) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['districtData'] = $districtData;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getcartDetail_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$count = $this->Student_model->getCartCount($data, $errormessage);
		if((int)$count > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['cartcount'] = $count;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
			$json['cartcount'] = 0;
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function getusertype_get()
	{
		$data = array();
		$data['userid'] = $this->get('id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$userid = $this->Student_model->getUserType($data, $errormessage);
		if((int)$userid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['userid'] = $userid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getExamResult_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['searchtext'] = $this->get('searchtext');
		$data['startdate'] = $this->get('startdate');
		$data['enddate'] = $this->get('enddate');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examresult = $this->Student_model->getExamResult($data, $errormessage);
		if(isset($examresult) && count($examresult) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examresult'] = $examresult;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	

	public function getExamResultById_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['studentExamId'] = $this->get('studentExamId');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examresult = $this->Student_model->getExamResultById($data, $errormessage);
		if(isset($examresult) && count($examresult) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examresult'] = $examresult;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getUnittestResultById_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['student_exam_id'] = $this->get('student_exam_id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examresult = $this->Student_model->getUnittestResultById($data, $errormessage);
		if(isset($examresult) && count($examresult) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examresult'] = $examresult;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getQuestionDetail_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['questionid'] = $this->get('questionid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examresult = $this->Student_model->getQuestionDetail($data, $errormessage);
		if(isset($examresult) && count($examresult) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['questionresult'] = $examresult;
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
		
		$data['chapter_id'] = $this->get('chapter_id');
		$data['userid'] = $this->get('userid');
		$data['exam_id'] = $this->get('exam_id');
		
		
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$chaptername = $this->Student_model->getChaptername($data, $errormessage);
		
		//$topicname = $this->Student_model->getTopicname($data, $errormessage);
		
		$quesdata = $this->Student_model->getQuestion($data, $errormessage);
		if(isset($quesdata) && count($quesdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['chaptername'] = $chaptername;
			//$json['topicname'] = $topicname;
			$json['question'] = $quesdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
			$json['chaptername'] = $chaptername;
			$json['topicname'] = $topicname;
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function preparationtest_get()
	{
		$data = array();
		$data['chapterid'] = $this->get('chapterid');
		$data['exam_id'] = $this->get('exam_id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		
		$purches = $this->Student_model->CheckCourseAss($data, $errormessage);
		
		if((int)$purches == 1)
		{
			$prepairdata = $this->Student_model->PreparationTest($data, $errormessage);
			if(isset($prepairdata) && count($prepairdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($prepairdata as $key=>$value)
				{
					$json[$key] = $value;
				}
				$json['notallow'] = "";
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else
		{
				$json = array("status"=>0,"message"=>$errormessage);
				$json['notallow'] = "notallow";
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function downloadHallTicketPDF_get() 
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['exam_schedule_id'] = $this->get('exam_schedule_id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		//$names = $this->Master_model->getNameByID($data, $errormessage);
		//$studentdata = $this->Student_model->downloadReportExcel($data, $errormessage);
		$studentdata = $this->Student_model->hallTicketModel($data, $errormessage);
		/*echo "<pre>";
		print_r($studentdata);*/
		if(isset($studentdata) && count($studentdata) > 0)
		{
			//echo "string";
				$header = "";
				/*if($names['coursename'] != ''){
					$header .= "Course Name :- ".$names['coursename']." <br>";
				}*/
                //$htmltoPDF = $header.'';
                $htmltoPDF = '<table style="width:80%" >';
                $htmltoPDF.='<b>'.$studentdata['course_name'].','.$studentdata['exam_date'].', ADMIT CARD</b>';
                $htmltoPDF.='
		
		<th colspan="4" height="130px" class="text-center">
				<img src="" alt="photo" width="350" height="70">
				</br>
				<b>'.$studentdata['course_name'].','.$studentdata['exam_date'].', ADMIT CARD</b>
		</th>
		
		
		<tr>
		<td align="left" width="33.33%"><b> Roll No: </b></td>
		<td align="left" width="33.33%">'.$studentdata['roll_no'].'	</td>
		<td align="left" width="33.33%"><b>Photo & Signature<b>	</td>
		</tr>
		
		<tr>
				<td align="left" > <b>Application No.:</b></td>
				<td>'.$studentdata['studentdata']->stud_id.'</td>
				<td rowspan="8">
				<center><img 
			     			src="'.$studentdata['studentdata']->prof_pic.'" 
			     			alt="profile_picture"  
			     			id="blah" 
			     			alt="your image" 
			     			width="125" height="140"
			     			/><br><br><br>
			     			<input type="text" class="sign_box" name="" disabled><center></center>
			    </td>
		</tr>
		
		<tr>
				<td align="left"><b>	Candidate`s Full Name: <b/></td>
				<td>'.$studentdata['studentdata']->stud_name.'</td>
		
		</tr>
		<tr>
				<td align="left"><b>	Medium of Question Paper:</b> </td>
				<td>English	</td>
		</tr>
		
		
		<tr>
				<td align="left">	<b>Subjects opted in CET :</b></td>
				<td data-ng-repeat="sub in hallticketdata.subject"  style="display: block; " >{{sub.subject_name}}</td>
		</tr>
		
		
		<tr>
				<td align="left"><b>	Father`s/Husband`s First Name:</b></td>
				<td>N/A	</td>
		</tr>
		
		
		<tr>
				<td align="left">	<b>Mother`s First Name : </b></td>
				<td>'.$studentdata['studentdata']->mother_name.'</td>
		</tr>
		
		
		<tr>
				<td align="left">	<b>Date of Birth :</b></td>
				<td>'.$studentdata['studentdata']->dob.'</td>
		</tr>
		
		
		<tr>
				<td align="left">	<b>Type of Disability (if any):</b> </td>
				<td>NA	</td>
		</tr>
		
		
		<tr>
				<td align="left">	<b>Exam Centre Details:</b> </td>
				<td colspan="2">N/A</td>
					
		</tr>
		
		
		<tr>
				<td colspan="3" align="left">	<b>NOTE: This admit card is issued for appearing in Mock Exam of '.$studentdata['course_name'].' only and does not imply that the Candidate is eligible for admission</b></td>
		</tr>
		
		
		<tr>
				<td colspan="3" ><h3> This is a system generated admit card and does not require any signature.</h3></td>
		</tr>
		
		
		<tr>
				<td colspan="3" ><h3> </h3></td>
		</tr>
		
		
		<tr>
				<td colspan="3" > <center><b>Examination & Bell Schedule </b></center></td>
		</tr>
		
		<tr>
				<td  width="33.33%"><center><b>Description <b></center></td>
				<td  width="33.33%"><center><b>Paper I :- Mathematics<b></center></td>
				<td  width="33.33%"><center><b>Paper II : Physics and Chemistry <b>	</center></td>
		</tr>
		
		<tr>
				<td align="left" >Entry in Examination Hall</td>
				<td>09.15 a.m. Long Bell	</td>
				<td>11.40 noon Long Bell	</td>
		</tr>
		
		
		<tr>
				<td align="left" >Distribution of OMR Answer Sheets</td>
				<td>09.40 a.m.& Bell Schedule	</td>
				<td>11.40 a.m.	</td>
		</tr>
		
		
		<tr>
				<td align="left" >Distribution of Question Booklets</td>
				<td>09.50 a.m.	</td>
				<td>11.40 a.m.	</td>
		</tr>
			
		<tr>
				<td align="left" >Last entry permitted in Examination Hall</td>
				<td>10.00 a.m.	</td>
				<td>11.40 a.m.	</td>
		</tr>
		
		
		<tr>
				<td align="left" >Examination Commences</td>
				<td>10.00 a.m. Long Bell	</td>
				<td>11.40 a.m Long Bell	</td>
		</tr>
		
		
		<tr>
				<td align="left" >Paper concludes at</td>
				<td>11.30 a.m. Long Bell	</td>
				<td>01.10. p.m. Long Bell	</td>
		</tr>
		
		<tr>
				<td colspan="3" align="left">	<b>CANDIDATES SHALL BRING THE FOLLOWING ALONG WITH ADMIT CARD</b></td>
		</tr>
		
		<tr>
				<td colspan="3" align="left"> 1  Black ball point pen and clip board.<br>
											 2  The person with disability shall bring original Disability Certificate, Scribe (if permitted) and Scribe Form as applicable.<br>
											 3  Identity proof as per DTE guidelines</td>
		</tr>
		
		<tr>
				<td colspan="3" ><center><b>IMPORTANT INSTRUCTIONS</b></center>	</td>
		</tr>
		
		<tr>
				<td colspan="3" align="left"> <b>1 Do not bring any mathematical tables with you as they are provided in Question Booklet<br>
												2 Electronics item/Gadget, Calculator and communication devices like mobile phones, gear watches etc. are not permitted in the
												Examination centre/Hall </b></br>
												3 Darken only one circle completely for answering each question as shown below.<br>
												Example <img src="images/Capture.JPG" alt="photo" width="60" height="40">where (C) is the correct response. Any other partial or <b>"✘" or "✔" </b>as below, may not be captured by the scanner.
												INCORRECT METHODS<br>
												4 Ensure that you have correctly filled up your Roll Number, Question Booklet Number and Question Booklet Version Number in your OMR answer
												sheet and sign at space provided</br>
												5 The candidate will not be allowed to leave the examination hall during the Examination<br>
												6 On completion of exam, candidate must handover the OMR answer sheet to the invigilator.<br>
												7 Adoption of any unfair means in the examination shall render a candidate liable for punishment under "Maharashtra Prevention of malpractices
												Act, university, Board and other specified examination Act,1982" and disqualify him/her for MHTCET 2017 Examination.<br>
												8 All data and information required to be filled in by candidates must be filled by himself/herself.<br>
												9 Disparity between signature uploaded at the time of registration of the application form, attendance sheet and OMR answer sheet during exam
												may lead to disqualification.
				</td>
		</tr>
		
	</table>
	<b>'.$studentdata['course_name'].','.$studentdata['exam_date'].', ADMIT CARD</b>
'; 
   
				$htmltoPDF .= "</table>"; 
				//print_r($htmltoPDF);
				include APPPATH.'third_party/html2fpdf.php';
				$filename='Student_HallTicket.pdf';
				$pdf=new HTML2FPDF();
				$pdf->AddPage("P", "A4");
				$pdf->WriteHTML($htmltoPDF);
				$pdf->Output($filename,'D');
			
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
			header('Content-type: application/json');
			echo json_encode($json);
		}
		/*echo "<pre>";
		print_r($studentdata);*/
	}
	/*public function downloadHallTicketPDF_get() 
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['exam_schedule_id'] = $this->get('exam_schedule_id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		//$names = $this->Master_model->getNameByID($data, $errormessage);
		//$studentdata = $this->Student_model->downloadReportExcel($data, $errormessage);
		$finaldata = $this->Student_model->hallTicketModel($data, $errormessage);
		print_r($finaldata);
		die();
		if(isset($studentdata) && count($studentdata) > 0)
		{
				$header = "";
				if($names['coursename'] != ''){
					$header .= "Course Name :- ".$names['coursename']." <br>";
				}
				
                $htmltoPDF = $header.'<table width="99%" cellpadding="5" border="1" class="displaytable">
							<thead>
								<tr>
									<td width="10%">Sr.No.</td>
									<td width="30%">Student Name</td>
									<td width="30%">Email Address</td>
									<td width="20%">Contact Number</td>
								</tr>
							</thead>'; 
                
                for($i = 0 ; $i < count($studentdata);$i++){
                	$row = $i + 1 ;
                	$htmltoPDF.="<tr>";
                	$htmltoPDF.="<td>".$row."</td>";
                	$htmltoPDF.="<td>".$studentdata[$i]['name']."</td>";
                	$htmltoPDF.="<td>".$studentdata[$i]['email']."</td>";
                	$htmltoPDF.="<td>".$studentdata[$i]['contact']."</td>";
                	$htmltoPDF.="</tr>";
				}
				
				$htmltoPDF .= "</table>"; 
				include APPPATH.'third_party/html2fpdf.php';
				$filename='Student_List.pdf';
				$pdf=new HTML2FPDF();
				$pdf->AddPage("P", "A4");
				$pdf->WriteHTML($htmltoPDF);
				$pdf->Output($filename,'D');
			
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
			header('Content-type: application/json');
			echo json_encode($json);
		}
	}*/
	public function getFinalTest_get()
	{
		$data = array();
		$data['examid'] = $this->get('examid');
		$data['userid'] = $this->get('userid');
		$data['exam_schedule_id'] = $this->get('exam_schedule_id');
		$data['student_exam_id'] = $this->get('student_exam_id');
		$data['roll_no'] = $this->get('roll_no');
		//$data['student_exam_id'] = '43';
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$examDetails = $this->Student_model->getFinalExamDetails($data, $errormessage);

			//print_r($examDetails);
			if(isset($examDetails) && count($examDetails) > 0)
			{
				$quesDetail = $this->Student_model->getFinalExamQues($data, $errormessage);
				if(isset($quesDetail) && count($quesDetail) > 0)
				{
					$json = array("status"=>200, "message"=>PageBase::$successmessage);
					$json['examDetail'] = $examDetails;
					$json['quesDetail'] = $quesDetail;
					$json['notallow'] = "";
				}
				else
				{
					$json = array("status"=>0,"message"=>$errormessage);
				}
			}
			else
			{
					$json = array("status"=>0, "message"=>'Exam all ready taken.');
					$json['examDetail'] = $examDetails;
					$json['notallow'] = "notallow";
			}

		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		if ($examDetails['timeOver'] == 0) {
			$json = array("status"=>0, "message"=>'Exam is Expire.');
					$json['examDetail'] = $examDetails;
					$json['notallow'] = "notallow";
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	

	public function createFinalTest_post()
	{
		
		$data = array();
		$data['exam_schedule_id'] = $this->post('exam_schedule_id');
		$data['userid'] = $this->post('userid');
		$data['rollNo'] = $this->post('roll_no');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$valid = $this->ValidateCreateUser($data, $errormessage);

		if($valid)
		{
			$finaldata = $this->Student_model->createFinalTest($data, $errormessage);
			if(isset($finaldata) && count($finaldata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($finaldata as $key=>$value)
				{
					$json[$key] = $value;
				}
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function hallTicket_get()
	{
		$data = array();
		$data['exam_schedule_id'] = $this->get('exam_schedule_id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$finaldata = $this->Student_model->hallTicketModel($data, $errormessage);
			if(isset($finaldata) && count($finaldata) > 0)
			{	
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($finaldata as $key=>$value)
				{
					$json[$key] = $value;
				}
				

			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}

	public function updateFinalTest_put()
	{
		$data = array();
		$data['student_exam_id'] = $this->put('student_exam_id');
		$data['userid'] = $this->put('userid');
		$data['optionid'] = $this->put('optionid');
		$data['quesid'] = $this->put('quesid');
		$data['time'] = $this->put('time');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$finalid = $this->Student_model->updateFinalTest($data, $errormessage);
		
		if((int)$finalid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $finalid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}

	public function submitFinalTest_put()
	{
		$data = array();
		$data['student_exam_id'] = $this->put('student_exam_id');
		$data['userid'] = $this->put('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$finalid = $this->Student_model->submitFinalTest($data, $errormessage);
		if((int)$finalid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['id'] = $finalid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function submitdoubtForm_put()
	{
		$data = array();
		$data['userid'] = $this->put('userid');
		$data['exam_qun_Pid'] = $this->put('exam_qun_Pid');
		$data['examid'] = $this->put('examid');
		$data['exam_schedule_id'] =$this->put('exam_schedule_id');
		$data['usersessionid']= PageBase::GetHeader("authcode");
		$finalid = $this->Student_model->submitDoubtForm($data, $errormessage);
		
		if((int)$finalid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['status_id'] = $finalid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	

	public function retest()
	{
		
		$data = array();
		$data['chapterid'] = $this->put('chapterid');
		//$data['topicid'] = $this->put('topicid');
		$data['userid'] = $this->put('userid');
		//$data['courseid'] = $this->put('courseid');
		$data['quesid'] = $this->put('quesid');
		$data['count'] = $this->put('count');
		$data['usersessionid'] = PageBase::GetHeader("authcode");

		$prepairdata = $this->Student_model->UpdatePreparation($data, $errormessage);
		if(isset($prepairdata) && count($prepairdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($prepairdata as $key=>$value)
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
	
	public function retest_put()
	{
		$data = array();
		$data['chapterid'] = $this->put('chapterid');
		$data['topicid'] = $this->put('topicid');
		$data['userid'] = $this->put('userid');
		
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$prepairdata = $this->Student_model->Retest($data, $errormessage);
		if(isset($prepairdata) && count($prepairdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($prepairdata as $key=>$value)
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
	
	private function ValidateCreateUser($data, &$errormessage)
	{
		$success = true;
		foreach($data as $key=>$val)
		{
			if($val == null)
			{
				$errormessage = "$key cannot be empty.";
				$success = false;
				return $success;
			}
		}
		return $success;
	}
	
	public function studentById_get($userid)
	{
		$data = array();

		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$userdata = $this->Student_model->getStudentByID($data, $errormessage);
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

	public function updatestud_put($userid)
	{
		$data = array();
		
		$data["userid"] = $this->put('userid');
		$data["studname"] = $this->put('studname');
		$data["gender"] = $this->put('gender');
		$data["email"] = $this->put('email');
		$data["contact"] = $this->put('contact');
		$data["address"] = $this->put('address');
		$data["standard"] = $this->put('standard');
		$data["college_name"] = $this->put('college_name');

		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		$data["dob"] = $this->put('dob');
		$data["mother_name"] = $this->put('mother_name');		
		$data["coordinator"] = $this->put('coordinator');
		$data["how_to_know"] = $this->put('how_to_know');
		$data["country"] = $this->put('country');
		$data["other_country"] = $this->put('other_country');
		$data["district"] = $this->put('district');
		$data["pin"] = $this->put('pin');		
		$data["state"] = $this->put('state');

		if($valid)
		{
			/*$result = $this->Student_model->checkUsername($data, $errormessage);
			if(isset($result) && $result > 0){*/
				$data['usersessionid'] = PageBase::GetHeader("authcode");
				$instdata = $this->Student_model->UpdateStudent($data, $errormessage);
				if(isset($instdata) && count($instdata) > 0){
					$json = array("status"=>200, "message"=>PageBase::$successmessage);
					foreach($instdata as $key=>$value){
						$json[$key] = $value;
					}
				}
				else{
					$json = array("status"=>0,"message"=>$errormessage);
				}
			/*}
			else{
				$json = array("status"=>0,"message"=>$errormessage);
			}*/
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}

	public function uploadPhoto_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		$data['instid'] = $this->post('instid');
		$data['filename'] = $_FILES['userphoto']['name'];
		//print_r($data);
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$uploaddir = 'images/student/';
			$fparray = explode(".",$_FILES["userphoto"]["name"]); 
			$fileName = $data['docid']."_".uniqid().".".$fparray[1];
			$uploadfile = $uploaddir . $fileName;
			move_uploaded_file($_FILES['userphoto']['tmp_name'], $uploadfile);
			$data['imgpath'] = $uploadfile;
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$instid = $this->Student_model->uploadPhoto($data, $errormessage);
			$valid = ((int)$instid > 0);	
		}
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $instid;
			$json["imgpath"] = $data['imgpath'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function concern_get()
	{
		$data = array();
		//$data['userid'] = $userid[0];
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Student_model->getConcernDetails($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['concern'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function feedbackcreate_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		$data['name'] = $this->post('name');
		$data['email'] = $this->post('email');
		//$data['concern_id'] = $this->post('concern');
		$data['feedback'] = $this->post('feedback');	
		
		$errormessage = "";

		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$feedbackid = $this->Student_model->createfeedback($data, $errormessage);
		$valid = ((int)$feedbackid > 0);	
	
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $feedbackid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function feedback_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['instid'] = $this->get('instid');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$feeddata = $this->Student_model->getFeedbackDetails($data, $errormessage);
		if(isset($feeddata) && count($feeddata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['feedback'] = $feeddata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	


	public function feedbackById_get($userid)
	{
		$data = array();

		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$feedbackdata = $this->Student_model->getFeedbackByID($data, $errormessage);
		if(isset($feedbackdata) && count($feedbackdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($feedbackdata as $key=>$value)
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
	
	
	public function studCourseExamResult_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['limit'] = $this->get('limit');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$studData = $this->Student_model->studCourseExamResult($data, $errormessage);
		if(isset($studData) && count($studData) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['studresult'] = $studData;
			$json['examResultData'] = $studData['examResultData'];
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function studentRanksCtr_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$studData = $this->Student_model->getStudentRanks($data, $errormessage);
		if(isset($studData) && count($studData) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['studRank'] = $studData;
		}
		else{
			$json = array("status"=>0,"message"=>'Exam report not available.');
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}

}

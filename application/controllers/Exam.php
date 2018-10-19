<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Exam extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Exam_model');
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
//create new exam	
public function create_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		$data['name'] = $this->post('name');
		$data['course'] = $this->post('course');
		//$data['duration'] = $this->post('duration');
		//$data['mark'] = $this->post('mark');
		//$data['markperques'] = $this->post('markperques');
		$data['noofques'] = $this->post('noofques');
		$data['subjectGroups'] = $this->post('subjectGroup');
		$errormessage = "";
		$valid = $this->validateCreateExam($data, $errormessage);
		
		if($valid)
		{
			$data['quesCount'] = $this->post('quesCount');
			//$data['quesCountTopic'] = $this->post('quesCountTopic');
            //data['paragrapQuestionArr'] = $this->post('paragrapQuestionArr');
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$examid = $this->Exam_model->createExam($data, $errormessage);
			//print_r($data);
			//die();
			$valid = ((int)$examid > 0);	
		}
		
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $examid;
			$json["name"] = $data['name'];
		}
		else
		{
			$json = array("status"=>0,"message"=>'Please check all field and manage exam carefully (Add subject group and schedule.).');
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get all exams		
public function index_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examdata = $this->Exam_model->getExamDetails($data, $errormessage);
		if(isset($examdata) && count($examdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['exam'] = $examdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function allExams_get()
	{
		$data = array();
		
		$examdata = $this->Exam_model->getAllExamDetails($data, $errormessage);
		if(isset($examdata) && count($examdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['exam'] = $examdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function allexam_get()
	{
		$data = array();
		$data['group_id'] = $this->get('group_id');
		$data['mode'] = $this->get('mode');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		
		$examdata = $this->Exam_model->getExamDetailsAll($data, $errormessage);
		if(isset($examdata) && count($examdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['exam'] = $examdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function subGroup_get()
	{
		$data = array();
		$data['course_id'] = $this->get('course_id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subGroup = $this->Exam_model->getSubGroup($data, $errormessage);
		if(isset($subGroup) && count($subGroup) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['subGroup'] = $subGroup;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get exam by id	
public function examCourse_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['action_type'] = $this->get('action');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Exam_model->examCourse($data, $errormessage);
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

//get exam by id	
public function examById_get($userid)
	{
		$data = array();
		
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examdata = $this->Exam_model->getExamByID($data, $errormessage);
		if(isset($examdata) && count($examdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($examdata as $key=>$value)
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
//update exam
public function update_put($userid)
	{
		$data = array();
		
		$data['userid'] = $userid[0];
		$data['id'] = $this->put('id');
		//echo $data['id'];exit;
		$data['userid'] = $this->put('userid');
		$data['name'] = $this->put('name');
		$data['course'] = $this->put('course');
		//$data['duration'] = $this->put('duration');
		//$data['mark'] = $this->put('mark');
		//$data['markperques'] = $this->put('markperques');
		$data['noofques'] = $this->put('noofques');
		$data['subjectGroups'] = $this->put('subjectGroup');
		//print_r($data['subjectGroups']);exit;
		$errormessage = "";
		$valid = $this->validateCreateExam($data, $errormessage);
		if($valid)
		{
			if($this->put('isnegative') == 'true')
			{
				$data['isnegative'] = '1';
				$data['negativewt'] = $this->put('negativewt');
			}
			else
			{
				$data['isnegative'] = '0';
				$data['negativewt'] = null;
			}
			
			$data['quesCount'] = $this->put('quesCount');
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$examdata = $this->Exam_model->updateExam($data, $errormessage);
			if(isset($examdata) && count($examdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($examdata as $key=>$value)
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
// delete exam
public function delete_delete($userid)
	{
		$data = array();
		
		$data['id'] = $this->delete('id');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examid = $this->Exam_model->deleteExamByID($data, $errormessage);
		if((int)$examid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $examid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
// delete multiple exam	
public function deleteMultiple_delete($userid)
	{
		$data = array();
		
		$data['ids'] = $this->delete('ids');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$examid = $this->Exam_model->deleteMultipleExam($data, $errormessage);
		if($examid != 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $courseid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
// validate exam				
private function validateCreateExam($data, &$errormessage)
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
	
}

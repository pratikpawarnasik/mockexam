<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Course extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Course_model');
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
	
public function upload_post()
	{
		$name =  $this->files['file']['name'];
		$name1 =  $_FILES['file']['name'];
		echo $name1;
		echo $name;
		print_r($_FILES);
	}	
//create new course	
public function create_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		$data['name'] = $this->post('name');
		$data['description'] = $this->post('desc');
		//$data['duration'] = $this->post('duration');
		//$data['courseMonthFee'] = $this->post('courseMonthFee');
		$data['category'] = $this->post('category');
		$data['level'] = $this->post('level');
		
		$errormessage = "";
		$valid = $this->validateCreateCourse($data, $errormessage);
		if($valid)
		{
			$data['authorid'] = $this->post('userid');
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$courseid = $this->Course_model->createCourse($data, $errormessage);
			$valid = ((int)$courseid > 0);	
		}
		
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $courseid;
			$json["name"] = $data['name'];
			//$json["authcode"] = $data['usersessionid'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//all get courses	
public function index_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Course_model->getCourseDetails($data, $errormessage);
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
	//all get courses	
public function getAllCourse_get()
	{
		$data = array();
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Course_model->getAllCourseDetails($data, $errormessage);
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
//get course by id
public function category_get($userid)
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$categorydata = $this->Course_model->getCategory($data, $errormessage);
		if(isset($categorydata) && count($categorydata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['category'] = $categorydata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get course Hirarchy	
public function getCourseHirarchy_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['courseid'] = $this->get('courseid');
		$coursedata = $this->Course_model->getCourseHirarchy($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['courseHirarchi'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get course subject Hirarchy	
public function courseSubjectHirachy_get($userid)
	{
		$data = array();
		//$data['userid'] = $userid[0];
		$data['schedule_id'] = $this->get('schedule_id');
		$data['exam_id'] = $this->get('exam_id');
		$data['userId'] = $this->get('id');
		$coursedata = $this->Course_model->getCourseSubjectHirarchy($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['courseHirarchi'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get courses list
public function getCourseAllList_get($userid)
	{
		$data = array();

		$data['courseid'] = $this->get('courseid');
		$coursedata = $this->Course_model->getCourseChapterTopic($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['courseHirarchi'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get courses by id	
public function courseById_get($userid)
	{
		$data = array();

		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$userdata = $this->Course_model->getCourseByID($data, $errormessage);
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
	//get courses by id	
public function courseByIdDashboard_get($userid)
	{
		$data = array();
		$data['id'] = $this->get('id');

		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$userdata = $this->Course_model->getCourseByIDDashboard($data, $errormessage);
		if(isset($userdata) && count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage,"courseList"=>$userdata);
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		//print_r($json);
		header('Content-type: application/json');
		echo json_encode($json);
	}
//update course
public function update_put($userid)
	{
		$data = array();
		
		$data['userid'] = $userid[0];
		$data['id'] = $this->put('id');
		$data['userid'] = $this->put('userid');
		$data['name'] = $this->put('name');
		$data['description'] = $this->put('desc');
		$data['category'] = $this->put('category');
		
		$errormessage = "";
		$valid = $this->validateCreateCourse($data, $errormessage);
		if($valid)
		{
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$coursedata = $this->Course_model->updateCourse($data, $errormessage);
			if(isset($coursedata) && count($coursedata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($coursedata as $key=>$value)
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
//delete course	
public function delete_delete($userid)
	{
		$data = array();
		
		$data['id'] = $this->delete('id');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$courseid = $this->Course_model->deleteCourseByID($data, $errormessage);
		if((int)$courseid > 0)
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
//delete multiple courses	
public function deleteMultiple_delete($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['ids'] = $this->delete('ids');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$courseid = $this->Course_model->deleteMultipleCourse($data, $errormessage);
		if($courseid != 0)
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
//validate course
private function validateCreateCourse($data, &$errormessage)
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

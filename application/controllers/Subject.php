<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Subject extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Subject_model');
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
	
	public function create_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		$data['name'] = $this->post('name');
		//$data['weightage'] = $this->post('weightage');
		
		$errormessage = "";
		$valid = $this->validateCreateSubject($data, $errormessage);
		if($valid)
		{
			$data['courseid'] = $this->post('courseid');
			$data['levelid'] = $this->post('levelid');
			$data['desc'] = $this->post('desc');
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$subjectid = $this->Subject_model->createSubject($data, $errormessage);
			$valid = ((int)$subjectid > 0);	
		}
		
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $subjectid;
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
	
	public function index_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['userid'] = $this->get('userid');
		//$data['levelid'] = $this->get('levelid');
		$data['courseid'] = $this->get('courseid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subjectdata = $this->Subject_model->getSubjectDetails($data, $errormessage);
		if(isset($subjectdata) && count($subjectdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['subject'] = $subjectdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function subjectById_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subjectdata = $this->Subject_model->getSubjectByID($data, $errormessage);
		if(isset($subjectdata) && count($subjectdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($subjectdata as $key=>$value)
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
	
	public function subjectcourse_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Subject_model->getSubjectCourse($data, $errormessage);
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
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function subjectlevel_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$leveldata = $this->Subject_model->getSubjectlevel($data, $errormessage);
		if(isset($leveldata) && count($leveldata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($leveldata as $key=>$value)
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

	public function update_put($userid)
	{
		$data = array();
		
		$data['userid'] = $userid[0];
		$data['id'] = $this->put('id');
		$data['userid'] = $this->put('userid');
		$data['name'] = $this->put('name');
		//$data['weightage'] = $this->put('weightage');
		$errormessage = "";
		$valid = $this->validateCreateSubject($data, $errormessage);
		if($valid)
		{
			$data['desc'] = $this->put('desc');
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$subjectdata = $this->Subject_model->updateSubject($data, $errormessage);
			if(isset($subjectdata) && count($subjectdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($subjectdata as $key=>$value)
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
	
	public function delete_delete($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->delete('id');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subjectid = $this->Subject_model->deleteSubjectByID($data, $errormessage);
		if((int)$subjectid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $subjectid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function deleteMultiple_delete($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['ids'] = $this->delete('ids');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subjectid = $this->Subject_model->deleteMultipleSubject($data, $errormessage);
		if($subjectid != 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $subjectid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	private function validateCreateSubject($data, &$errormessage)
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

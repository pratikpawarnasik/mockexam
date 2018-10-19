<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class SubjectGroup extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Subjectgroup_model','SubjectGroup_model');
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
//create new subject group
public function create_post()
	{
		$data = array();
		$data['author_id'] = $this->post('userid');
		$data['userid'] = $this->post('userid');
		$data['subject_group_name'] = $this->post('sub_group_name');
		$data['course_id'] = $this->post('course_id');
		$data['subjectlist'] = $this->post('subject');
		$errormessage = "";
		$valid = $this->validateCreateSubjectGroup($data, $errormessage);
		if($valid)
		{
			$data['authorid'] = $this->post('userid');
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$subjectgroupid = $this->SubjectGroup_model->createSubjectGroup($data, $errormessage);
			$valid = ((int)$subjectgroupid > 0);	
		}
		
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $subjectgroupid;
			$json["subject_group_name"] = $data['subject_group_name'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get all subject group	
public function index_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subjectgroupdata = $this->SubjectGroup_model->getSubjectGroupDetails($data, $errormessage);
		if(isset($subjectgroupdata) && count($subjectgroupdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['subjectgroup'] = $subjectgroupdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get subject group by id
public function subjectGroupById_get($userid)
	{
		$data = array();
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$userdata = $this->SubjectGroup_model->getsubjectGroupByID($data, $errormessage);
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
//update subject group
public function update_put($userid)
	{
		$data = array();
		$data['id'] = $this->put('id');
		$data['userid'] = $this->put('userid');
		$data['subject_group_name'] = $this->put('sub_group_name');
		$data['course_id'] = $this->put('course_id');
		$data['subjectlist'] = $this->put('subject');
		$errormessage = "";
		$valid = $this->validateCreateSubjectGroup($data, $errormessage);
		if($valid)
		{
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$data['updateddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");			
			$subjectGroupData = $this->SubjectGroup_model->updateSubjectGroup($data, $errormessage);
			if(isset($subjectGroupData) && count($subjectGroupData) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($subjectGroupData as $key=>$value)
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
//delete subject group	
public function delete_delete($userid)
	{
		$data = array();
		
		$data['id'] = $this->delete('id');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subjectgroupid = $this->SubjectGroup_model->deleteSubjectGroupByID($data, $errormessage);
		if((int)$subjectgroupid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $subjectgroupid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//validate subject group	
private function validateCreateSubjectGroup($data, &$errormessage)
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

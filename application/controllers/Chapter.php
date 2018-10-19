<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Chapter extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Chapter_model');
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
		$valid = $this->validateCreateChapter($data, $errormessage);
		if($valid)
		{
			$data['courseid'] = $this->post('courseid');
			$data['levelid'] = $this->post('levelid');
			$data['subjectid'] = $this->post('subjectid');
			$data['desc'] = $this->post('desc');
			$data['topic'] = $this->post('topic');
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$chapterid = $this->Chapter_model->createChapter($data, $errormessage);
			$valid = ((int)$chapterid > 0);	
		}
		
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $chapterid;
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
		$data['courseid'] = $this->get('courseid');
		$data['levelid'] = $this->get('levelid');
		$data['subjectid'] = $this->get('subjectid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$chapterdata = $this->Chapter_model->getChapterDetails($data, $errormessage);
		if(isset($chapterdata) && count($chapterdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['chapter'] = $chapterdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function chaptercourse_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Chapter_model->getChapterCourse($data, $errormessage);
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
	
	public function chapterlevel_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$leveldata = $this->Chapter_model->getChapterLevel($data, $errormessage);
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
	
	public function chapterSubject_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$subjectdata = $this->Chapter_model->getChapterSubject($data, $errormessage);
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

	public function chapterById_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$chapterata = $this->Chapter_model->getChapterByID($data, $errormessage);
		if(isset($chapterata) && count($chapterata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($chapterata as $key=>$value)
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
		$data['topic'] = $this->put('topic');
		//$data['weightage'] = $this->put('weightage');
		$errormessage = "";
		$valid = $this->validateCreateChapter($data, $errormessage);
		if($valid)
		{
			$data['desc'] = $this->put('desc');
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$chapterdata = $this->Chapter_model->updateChapter($data, $errormessage);
			if(isset($chapterdata) && count($chapterdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($chapterdata as $key=>$value)
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
		$chapterid = $this->Chapter_model->deleteChapterByID($data, $errormessage);
		if((int)$chapterid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $chapterid;
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
		$batchid = $this->Chapter_model->deleteMultipleChapter($data, $errormessage);
		if($batchid != 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $batchid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	private function validateCreateChapter($data, &$errormessage)
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

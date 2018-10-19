<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Notes extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Notes_model');
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
		$data['title'] = $this->post('title');
		
		$data['courseid'] = $this->post('courseid');

		$errormessage = "";
		$valid = $this->validateCreateNotes($data, $errormessage);
		//print_r($valid);
		if($valid)
		{
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$notesid = $this->Notes_model->createNotes($data, $errormessage);
		//	echo "string";
		//print_r($notesid);

			$uploadSuc = false;
			if((int)$notesid > 0)
			{

				$data['notesid'] = $notesid;
				//print_r($data);
				if($_FILES['docfile'] != null && $_FILES['docfile'] != '')
				{
					$uploaddir = 'images/notes/';
					$fparray = explode(".",$_FILES["docfile"]["name"]); 
					$fileName = $notesid."_".uniqid().".".$fparray[1];
					$uploadfile = $uploaddir . $fileName;
					move_uploaded_file($_FILES['docfile']['tmp_name'], $uploadfile);
					$data['filename'] = $_FILES['docfile']["name"];
					$data['filepath'] = $uploadfile;
					$data['filetype'] = "doc";
					$videoid = $this->Notes_model->updateNotes($data, $errormessage);
					$uploadSuc = true;
				}				
			}
			$valid = ((int)$notesid > 0);	
		}
		
		if($valid && $uploadSuc)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $notesid;
			$json["title"] = $data['title'];
		}
		else
		{
			if(!$uploadSuc && $errormessage != ''){
				$errormessage ="File not uploaded successfully.";
			}
			$json = array("status"=>0,"message"=>$errormessage);
		}
		//print_r($json);
		//header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function index_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['userid'] = $this->get('userid');
		$data['courseid'] = $this->get('courseid');
		$data['searchtype'] = $this->get('searchtype');
		$data['searchtext'] = $this->get('searchtext');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Notes_model->getNotesDetails($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['notes'] = $coursedata;
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
		$errormessage = "";
		$valid = $this->validateCreateNotes($data, $errormessage);
		if($valid)
		{
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$notesdata = $this->Notes_model->updateNotes($data, $errormessage);
			if(isset($notesdata) && count($notesdata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($notesdata as $key=>$value)
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
		$notesid = $this->Notes_model->deleteNotesByID($data, $errormessage);
		if((int)$notesid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $notesid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
		
	private function validateCreateNotes($data, &$errormessage)
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Category extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->model('Category_model');
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
//create new category
public function create_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		$data['name'] = $this->post('name');
		
		$errormessage = "";
		$valid = $this->validateCreateCategory($data, $errormessage);
		if($valid)
		{
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$categoryid = $this->Category_model->createCategory($data, $errormessage);
			$valid = ((int)$categoryid > 0);	
		}
		
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $categoryid;
			$json["name"] = $data['name'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//all get categories	
public function index_get()
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursedata = $this->Category_model->getCategoryDetails($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['category'] = $coursedata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//get category by id
public function categoryById_get($userid)
	{
		$data = array();
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$categorydata = $this->Category_model->getCategoryByID($data, $errormessage);
		if(isset($categorydata) && count($categorydata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($categorydata as $key=>$value)
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
//update category
public function update_put($userid)
	{
		$data = array();
		
		$data['userid'] = $userid[0];
		$data['id'] = $this->put('id');
		$data['userid'] = $this->put('userid');
		$data['name'] = $this->put('name');
		$errormessage = "";
		$valid = $this->validateCreateCategory($data, $errormessage);
		if($valid)
		{
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$categorydata = $this->Category_model->updateCategory($data, $errormessage);
			if(isset($categorydata) && count($categorydata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($categorydata as $key=>$value)
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
//delete category
public function delete_delete($userid)
	{
		$data = array();
		$data['id'] = $this->delete('id');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$categoryid = $this->Category_model->deleteCategoryByID($data, $errormessage);
		if((int)$categoryid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $categoryid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
//delete multiple category	
public function deleteMultiple_delete($userid)
	{
		$data = array();
		
		$data['ids'] = $this->delete('ids');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$categoryid = $this->Category_model->deleteMultipleCategory($data, $errormessage);
		if($categoryid != 0)
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
//validate category				
private function validateCreateCategory($data, &$errormessage)
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

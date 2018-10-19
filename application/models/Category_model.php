<?php

require "Go_model.php";
class Category_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
// create category model	
public function createCategory($data, &$errormessage)
	{
		$categoryid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $categoryid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $categoryid;
		}
		else
		{
			$userdataArr = array('category_name' => $data['name'],'author_id' => (int)$data['userid'],'author_type' => (int)$userdata['type'],'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('course_category', $userdataArr);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$categoryid = $this->db->insert_id();
			}
		}
		
		return $categoryid;
	}
// get category details model		
public function getCategoryDetails($data, &$errormessage)
	{
		$coursedata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $coursedata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $coursedata;
		}
		else
		{
			$this->db->select('category_id as id,category_name as name');
			$this->db->from('course_category');
			$this->db->where(array('author_type' => (int)$userdata['type'], 'active' => '1'));
			$query = $this->db->get();
	        $coursedata = $query->result_array();
			if(!$coursedata)
			{
				$coursedata = array();
				$errormessage = "Category are either deleted or not inserted.";
			}
		}
				
		return $coursedata;
	}
// get category details by id model	
public function getCategoryByID($data, &$errormessage)
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
			$this->db->select('category_id,category_name');
			$query = $this->db->get_where('course_category',array('category_id' => $data['id'], 'active' => '1'));
	        	$resultdata = $query->row_array();
			if($resultdata)
			{
					$coursedata["id"] = (int)$resultdata['category_id'];
					$coursedata["name"] = $resultdata['category_name'];
			}
			else
			{
				$errormessage = "This category is either deleted or not found.";
			}
		}
				
		return $coursedata;
	}
// update category model	
public function updateCategory($data, &$errormessage)
	{
		$categorydata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $categorydata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $categorydata;
		}
		else
		{
			$upddata = array('category_name' => $data['name'],'author_id' => (int)$data['userid'],'author_type' => (int)$userdata['type']);
			$this->db->where(array('category_id'=> (int)$data['id'],'author_type' => (int)$userdata['type'], 'active' => '1'));
			$result = $this->db->update('course_category', $upddata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$query = $this->db->get_where('course_category',array('category_id' => $data['id'], 'active' => '1'));
		        $resultdata = $query->row_array();
				if($resultdata)
				{
					$categorydata["id"] = (int)$resultdata['category_id'];
					$categorydata["name"] = $resultdata['category_name'];
				}
				else
				{
					$errormessage = "This category is either deleted or not found.";
				}
			}
		}
		
		return $categorydata;
	}
// delete category model
public function deleteCategoryByID($data, &$errormessage)
	{
		$categoryid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $categoryid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $categoryid;
		}
		else
		{
			$upddata = array('active'=>'0');
			$query = $this->db->where(array('category_id' => $data['id'],'active' => '1'));
			$result = $this->db->update('course_category',$upddata);
			if($result)
			{
				$categoryid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "This category is either deleted or not found.";
			}
		}
				
		return $categoryid;
	}
// delete multiple category model
public function deleteMultipleCategory($data, &$errormessage)
	{
		$categoryid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $categoryid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $categoryid;
		}
		else
		{
			foreach($data['ids'] as $id){
				$upddata = array('active'=>'0');
				$query = $this->db->where(array('category_id' => $id, 'active' => '1'));
				$result = $this->db->update('course_category',$upddata);
			}
			
			if($result)
			{
				$categoryid = $data['ids'];	
			}
			else
			{
				$errormessage = "This course is either deleted or not found.";
			}
		}
				
		return $categoryid;
	}
		
}

/* End of file Category_model.php */

/* Location: ./application/models/Category_model.php */
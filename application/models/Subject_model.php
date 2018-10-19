<?php

require "Go_model.php";
class Subject_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	public function createSubject($data, &$errormessage)
	{
		$subjectid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectid;
		}
		else
		{
			//'weightage' => $data['weightage'],
			$subjectdataArr = array('level_id' => (int)$data['levelid'],'course_id' => (int)$data['courseid'],'subject_name' => $data['name'],'author_id' => (int)$data['userid'],'subject_description' => $data['desc'],'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('subject', $subjectdataArr);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$subjectid = $this->db->insert_id();
			}
		}
		
		return $subjectid;
	}
	
	public function getSubjectDetails($data, &$errormessage)
	{
		$subjectdata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectdata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectdata;
		}
		else
		{
			if($data['courseid'] != null)
			{
				$this->db->select('s.subject_id as id,s.subject_name as name,s.subject_description as desc,c.course_name as dispname,(select count(*) from vid_question as q inner join vid_chapter as ch on ch.chapter_id = q.chapter_id where ch.subject_id = s.subject_id and ch.active= "1") as totalquestion,(select count(*) from vid_question as q inner join vid_chapter as ch on ch.chapter_id = q.chapter_id where ch.subject_id = s.subject_id and q.is_final= "1") as totalfinalquestion');
				$this->db->from('subject AS s');
				$this->db->join('course AS c', 'c.course_id = s.course_id', 'inner');
				//$this->db->where(array('s.author_id' => (int)$data['userid'],'s.course_id' => (int)$data['courseid'],'s.active' => '1'));
				$this->db->where(array('s.course_id' => (int)$data['courseid'],'s.active' => '1'));
				$query = $this->db->get();
				$subjectdata = $query->result_array();
			}
			
	        
			if(!$subjectdata)
			{
				$subjectdata = array();
				$errormessage = "Subject are either deleted or not inserted.";
			}
		}
				
		return $subjectdata;
	}
	
	public function getSubjectByID($data, &$errormessage)
	{
		$subjectdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectdata;
		}
		else
		{
			$this->db->select('subject_id,subject_name,subject_description,level_id,weightage');
			$query = $this->db->get_where('subject',array('subject_id' => $data['id'], 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
					$subjectdata["id"] = (int)$resultdata['subject_id'];
					$subjectdata["name"] = $resultdata['subject_name'];
					$subjectdata["desc"] = $resultdata['subject_description'];
					$subjectdata["levelid"] = (int)$resultdata['level_id'];
					$subjectdata["weightage"] = $resultdata['weightage'];
			}
			else
			{
				$errormessage = "This subject is either deleted or not found.";
			}
		}
				
		return $subjectdata;
	}
	
	public function getSubjectCourse($data, &$errormessage)
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
			$this->db->select('course_id,course_name');
			$query = $this->db->get_where('course',array('course_id' => $data['id'],'course_level' => '2', 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
					$coursedata["id"] = (int)$resultdata['course_id'];
					$coursedata["name"] = $resultdata['course_name'];
			}
			else
			{
				$errormessage = "This course subject not assign.";
			}
		}
				
		return $coursedata;
	}
	
	public function getSubjectLevel($data, &$errormessage)
	{
		$leveldata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $leveldata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $leveldata;
		}
		else
		{
			$this->db->select('level_id,level_name');
			$query = $this->db->get_where('course_level',array('level_id' => $data['id'],'level' => '2', 'active' => '1'));
	        	$resultdata = $query->row_array();
			if($resultdata)
			{
					$leveldata["id"] = (int)$resultdata['level_id'];
					$leveldata["name"] = $resultdata['level_name'];
			}
			else
			{
				$errormessage = "This course subject not assign.";
			}
		}
				
		return $leveldata;
	}

	public function updateSubject($data, &$errormessage)
	{
		$subjectdata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectdata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectdata;
		}
		else
		{
			//,'weightage' => $data['weightage']
			$upddata = array('subject_name' => $data['name'],'subject_description' => $data['desc']);
			$this->db->where(array('subject_id'=> (int)$data['id'],'active' => '1'));
			$result = $this->db->update('subject', $upddata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$query = $this->db->get_where('subject',array('subject_id' => (int)$data['id'], 'active' => '1'));
		        $resultdata = $query->row_array();
				if($resultdata)
				{
					$subjectdata["id"] = (int)$resultdata['subject_id'];
					$subjectdata["name"] = $resultdata['subject_name'];
					$subjectdata["desc"] = $resultdata['subject_description'];
					$subjectdata["weightage"] = $resultdata['weightage'];
					$subjectdata["levelid"] = (int)$resultdata['level_id'];
				}
				else
				{
					$errormessage = "This subject is either deleted or not found.";
				}
			}
		}
		
		return $subjectdata;
	}

	public function deleteSubjectByID($data, &$errormessage)
	{
		$subjectid = 0;
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectid;
		}
		else
		{
			$upddata = array('active'=>'0');
			$query = $this->db->where(array('subject_id' => $data['id'], 'active' => '1'));
			$result = $this->db->update('subject',$upddata);
			if($result)
			{
				$subjectid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "This subject is either deleted or not found.";
			}
		}	
		return $subjectid;
	}

	public function deleteMultipleSubject($data, &$errormessage)
	{
		$subjectid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectid;
		}
		else
		{
			$result = false;
			foreach($data['ids'] as $id){
				$upddata = array('active'=>'0');
				$query = $this->db->where(array('subject_id' => $id,'active' => '1'));
				$result = $this->db->update('subject',$upddata);
			}
			
			if($result)
			{
				$subjectid = $data['ids'];	
			}
			else
			{
				$errormessage = "This subject is either deleted or not found.";
			}
		}
				
		return $subjectid;
	}
		
}

/* End of file Subject_model.php */

/* Location: ./application/models/Subject_model.php */
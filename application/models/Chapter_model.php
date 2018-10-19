<?php

require "Go_model.php";
class Chapter_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	public function createChapter($data, &$errormessage)
	{
		$chapterid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $chapterid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $chapterid;
		}
		else
		{
			//'weightage' => $data['weightage'],
			$chapterdataArr = array('subject_id' => (int)$data['subjectid'],'chapter_name' => $data['name'],'author_id' => (int)$data['userid'],'chapter_description' => $data['desc'],'is_topic' => $data['topic'],'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('chapter', $chapterdataArr);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$chapterid = $this->db->insert_id();
			}
		}
		
		return $chapterid;
	}
	
	public function getChapterDetails($data, &$errormessage)
	{
		$chapterdata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $chapterdata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $chapterdata;
		}
		else
		{
			/*if($data['levelid'] != null && $data['levelid'] != 0){
				$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.chapter_description as desc,ch.weightage as weightage,ch.is_topic as topic,l.level_name as dispname,(select count(*) from question where chapter_id = ch.chapter_id) as totalquestion');
				$this->db->from('chapter AS ch');
				$this->db->join('course_level AS l', 'l.level_id = ch.level_id', 'inner');
				//$this->db->where(array('ch.author_id' => (int)$data['userid'],'ch.level_id' => (int)$data['levelid'],'ch.active' => '1'));
				$this->db->where(array('ch.level_id' => (int)$data['levelid'],'ch.active' => '1'));
				$query = $this->db->get();
	        	$chapterdata = $query->result_array();
			}*/
			if($data['courseid'] != null && $data['courseid'] != 0){
				$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.chapter_description as desc,ch.weightage as weightage,ch.is_topic as topic,c.course_name as dispname,(select count(*) from vid_question where chapter_id = ch.chapter_id) as totalquestion');
				$this->db->from('chapter AS ch');
				$this->db->join('course AS c', 'c.course_id = ch.course_id', 'inner');
				$this->db->where(array('ch.course_id' => (int)$data['courseid'],'ch.active' => '1'));
				$query = $this->db->get();
	        	$chapterdata = $query->result_array();
			}
			else if($data['subjectid'] != null && $data['subjectid'] != 0){
				$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.chapter_description as desc,ch.weightage as weightage,ch.is_topic as topic,s.subject_name as dispname,(select count(*) from vid_question where chapter_id = ch.chapter_id and active= "1") as totalquestion,(select count(*) from vid_question where chapter_id = ch.chapter_id and is_final= "1") as totalfinalquestion');
				$this->db->from('chapter AS ch');
				$this->db->join('subject AS s', 's.subject_id = ch.subject_id', 'inner');
				$this->db->where(array('ch.subject_id' => (int)$data['subjectid'],'ch.active' => '1'));
				$query = $this->db->get();
	        	$chapterdata = $query->result_array();
			}
			if(!$chapterdata)
			{
				$chapterdata = array();
				$errormessage = "Chapter are either deleted or not inserted.";
			}
		}
				
		return $chapterdata;
	}
	
	public function getChapterByID($data, &$errormessage)
	{
		$chapterdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $chapterdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $chapterdata;
		}
		else
		{
			$this->db->select('chapter_id,chapter_name,chapter_description,weightage,is_topic');
			$query = $this->db->get_where('chapter',array('chapter_id' => $data['id'], 'active' => '1'));
	        	$resultdata = $query->row_array();
			if($resultdata)
			{
					$chapterdata["id"] = (int)$resultdata['chapter_id'];
					$chapterdata["name"] = $resultdata['chapter_name'];
					$chapterdata["desc"] = $resultdata['chapter_description'];
					$chapterdata["weightage"] = $resultdata['weightage'];
					$chapterdata["topic"] = $resultdata['is_topic'];
					$chapterdata["courseid"] = (int)$resultdata['course_id'];
			}
			else
			{
				$errormessage = "This chapter is either deleted or not found.";
			}
		}
				
		return $chapterdata;
	}

	public function getChapterCourse($data, &$errormessage)
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
			$query = $this->db->get_where('course',array('course_id' => $data['id'],'course_level' => '3', 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
					$coursedata["id"] = (int)$resultdata['course_id'];
					$coursedata["name"] = $resultdata['course_name'];
			}
			else
			{
				$errormessage = "This course chapter not assign.";
			}
		}
				
		return $coursedata;
	}
	
	public function getChapterLevel($data, &$errormessage)
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
			$query = $this->db->get_where('course_level',array('level_id' => $data['id'],'level' => '3', 'active' => '1'));
	        	$resultdata = $query->row_array();
			if($resultdata)
			{
					$leveldata["id"] = (int)$resultdata['level_id'];
					$leveldata["name"] = $resultdata['level_name'];
			}
			else
			{
				$errormessage = "This course chapter not assign.";
			}
		}
				
		return $leveldata;
	}
	
	public function getChapterSubject($data, &$errormessage)
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
			$this->db->select('subject_id,subject_name');
			$query = $this->db->get_where('subject',array('subject_id' => $data['id'], 'active' => '1'));
	        	$resultdata = $query->row_array();
			if($resultdata)
			{
					$subjectdata["id"] = (int)$resultdata['subject_id'];
					$subjectdata["name"] = $resultdata['subject_name'];
			}
			else
			{
				$errormessage = "This course chapter not assign.";
			}
		}
				
		return $subjectdata;
	}


	public function updateChapter($data, &$errormessage)
	{
		$chapterdata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $chapterdata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $chapterdata;
		}
		else
		{
			//,'weightage' => $data['weightage']
			$upddata = array('chapter_name' => $data['name'],'chapter_description' => $data['desc'],'is_topic' => $data['topic']);
			$this->db->where(array('chapter_id'=> (int)$data['id'],'active' => '1'));
			$result = $this->db->update('chapter', $upddata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$query = $this->db->get_where('chapter',array('chapter_id' => (int)$data['id'], 'active' => '1'));
		        $resultdata = $query->row_array();
				if($resultdata)
				{
					$chapterdata["id"] = (int)$resultdata['chapter_id'];
					$chapterdata["name"] = $resultdata['chapter_name'];
					$chapterdata["desc"] = $resultdata['chapter_description'];
					$chapterdata["weightage"] = $resultdata['weightage'];
					$chapterdata["topic"] = $resultdata['is_topic'];
					$chapterdata["courseid"] = (int)$resultdata['course_id'];
				}
				else
				{
					$errormessage = "This chapter is either deleted or not found.";
				}
			}
		}
		
		return $chapterdata;
	}

	public function deleteChapterByID($data, &$errormessage)
	{
		$chapterid = 0;
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $chapterid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $chapterid;
		}
		else
		{
			$upddata = array('active'=>'0');
			$query = $this->db->where(array('chapter_id' => $data['id'],'active' => '1'));
			$result = $this->db->update('chapter',$upddata);
			if($result)
			{
				$chapterid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "This chapter is either deleted or not found.";
			}
		}	
		return $chapterid;
	}

	public function deleteMultipleChapter($data, &$errormessage)
	{
		$chapterid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $chapterid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $chapterid;
		}
		else
		{
			$result = false;
			foreach($data['ids'] as $id){
				$upddata = array('active'=>'0');
				$query = $this->db->where(array('chapter_id' => $id, 'active' => '1'));
				$result = $this->db->update('chapter',$upddata);
			}
			
			if($result)
			{
				$chapterid = $data['ids'];	
			}
			else
			{
				$errormessage = "This chapter is either deleted or not found.";
			}
		}
				
		return $chapterid;
	}
		
}

/* End of file Chapter_model.php */

/* Location: ./application/models/Chapter_model.php */
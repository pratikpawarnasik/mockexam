<?php

require "Go_model.php";
class Notes_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	public function createNotes($data, &$errormessage)
	{
		$notesid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $notesid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $notesid;
		}
		else
		{
			
			$notedataArr = array('course_id' => (int)$data['courseid'], 'title' => $data['title'],'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('notes', $notedataArr);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$notesid = $this->db->insert_id();
				
				
			}
		}
		
		return $notesid;
	}
	
	
	
	public function getNotesDetails($data, &$errormessage)
	{
		$notesdata = array();

		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $notesdata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $notesdata;
		}
		else
		{
			$this->db->select('n.note_id as noteid,np.note_path_id as id,n.title,c.course_name as course,np.type,np.file_name as display_name,np.note_path as path');
			$this->db->from('notes_path as np');
			$this->db->join('notes AS n', 'n.note_id = np.note_id and n.active = "1"', 'inner');
			$this->db->join('course AS c', 'c.course_id = n.course_id', 'left');
			
			if(($data['courseid'] != null && $data['courseid'] != '')){
				$this->db->where(array('n.course_id' => $data['courseid']));
			}
			
			
			if($data['searchtext'] != null && $data['searchtext'] != ''){
				$where = "( n.title like '%".$data['searchtext']."%' || np.file_name like '%".$data['searchtext']."%' || np.type like '%".$data['searchtext']."%' )";
				$this->db->where($where);
				/*$this->db->like('np.file_name',$data['searchtext']);
				$this->db->like('np.type',$data['searchtext']);*/
			}
			
			
			
			$this->db->where(array('np.active' => '1'));
			$this->db->order_by('np.note_path_id desc');
			$query = $this->db->get();
	        $notesdata = $query->result_array();
			if(!$notesdata)
			{
				$notesdata = array();
				$errormessage = "Study Material are not available.";
			}
		}
				
		return $notesdata;
	}
	
	public function updateNotes($data, &$errormessage)
	{
		$notesid = 0;
		if($data['filepath'] != 'undefined' && isset($data['filepath'])){
			$upddata = array('note_id' => $data['notesid'],'file_name' => $data['filename'],'note_path' => $data['filepath'],'type' => $data['filetype']);
			$result = $this->db->insert('notes_path', $upddata);
			if(!$result){
				$errormessage = "Notes are not available.";
			}
			else{
				$notesid = $this->db->insert_id();
			}
		}
		return $notesid;
	}

	public function deleteNotesByID($data, &$errormessage)
	{
		$notesid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $notesid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $notesid;
		}
		else
		{
			$query = $this->db->get_where('notes_path',array('note_path_id'=>$data['id']));
			$notedata = $query->row_array();
		
			unlink($notedata['note_path']);
			
			$this->db->where(array('note_path_id' => $data['id']));
			$result = $this->db->delete('notes_path');
			if($result)
			{
				$notesid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "Study Material in not found.";
			}
		}
				
		return $notesid;
	}
		
}

/* End of file Notes_model.php */

/* Location: ./application/models/Notes_model.php */
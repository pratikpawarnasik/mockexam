<?php

require "Go_model.php";
class SubjectGroup_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
// Add Subject Group Model
public function createSubjectGroup($data, &$errormessage)
	{
		$sub_group_id = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $sub_group_id;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $sub_group_id;
		}
		else
		{
			$userdataArr = array('subject_group_name' => $data['subject_group_name'],'course_id' => (int)$data['course_id'], 'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('subject_group', $userdataArr);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$sub_group_id = $this->db->insert_id();
				for($i = 0;$i < count($data['subjectlist']);$i++)
				{
					$subjectGroupArr = array('sub_group_id' => $sub_group_id, 'subject_id' => $data['subjectlist'][$i]);
					$result = $this->db->insert('subject_group_sub', $subjectGroupArr);
				}
				
			}
		}
		
		return $sub_group_id;
	}
// Get All  Subject Group Model	
public function getSubjectGroupDetails($data, &$errormessage)
	{
		$subjectgroupdata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectgroupdata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectgroupdata;
		}
		else
		{
			$this->db->select('sg.subject_group_id as id,sg.subject_group_name as name,sg.course_id,cc.course_name as course,GROUP_CONCAT(s.subject_name) as subject_name');
			$this->db->from('subject_group AS sg');
			$this->db->join('course AS cc', 'cc.course_id = sg.course_id', 'inner');
			$this->db->join('subject_group_sub AS sgsb', 'sgsb.sub_group_id = sg.subject_group_id', 'left');
			$this->db->join('subject AS s', 's.subject_id = sgsb.subject_id', 'left');
			$this->db->where(array('sg.active' => '1'));
			$this->db->group_by('sg.subject_group_id');
			$query = $this->db->get();
	        $subjectgroupdata = $query->result_array();
	        for($i=0;$i < count($subjectgroupdata);$i++){
                $subjectgroupdata[$i]["subject_name"] = str_replace(",",", ",$subjectgroupdata[$i]['subject_name']);
	        }
			if(!$subjectgroupdata)
			{
				$subjectgroupdata = array();
				$errormessage = "Subject Group are either deleted or not inserted.";
			}
		}
				//print_r($subjectgroupdata);
		return $subjectgroupdata;
	}
// get Subject Group by id Model	
public function getsubjectGroupByID($data, &$errormessage)
	{
		$subjectgroupdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectgroupdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectgroupdata;
		}
		else
		{
           $this->db->select('sg.subject_group_id,sg.subject_group_name,sg.course_id,GROUP_CONCAT(sgsb.subject_id) as subjectids');
			$this->db->from('subject_group AS sg');
			$this->db->join('subject_group_sub AS sgsb', 'sgsb.sub_group_id = sg.subject_group_id', 'left');
			$this->db->where(array('sg.subject_group_id' => $data['id'], 'sg.active' => '1'));
			$this->db->group_by('sgsb.sub_group_id');
			$query = $this->db->get();
	        $resultdata = $query->row_array();
			if($resultdata)
			{
					$subjectgroupdata["subject_group_id"] = (int)$resultdata['subject_group_id'];
					$subjectgroupdata["subject_group_name"] = $resultdata['subject_group_name'];
					$subjectgroupdata["course_id"] = $resultdata['course_id'];
					$subjectgroupdata["subjectids"] = explode(",",$resultdata['subjectids']);
			}
			else
			{
				$errormessage = "This subject group is either deleted or not found.";
			}
		}
				
		return $subjectgroupdata;
	}
// Update Subject Group Model
public function updateSubjectGroup($data, &$errormessage)
	{
		$subjectGroupData = array();
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectGroupData;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectGroupData;
		}
		else
		{
			$upddata = array('subject_group_name' => $data['subject_group_name'],'course_id' => (int)$data['course_id'],'updatedate' => $data['updateddate']);

			$result = $this->db->where(array('subject_group_id'=> (int)$data['id'],
				                             'active' => '1'))
			                   ->update('subject_group', $upddata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
                 $delete=$this->db->where('sub_group_id', (int)$data['id'])
                               ->delete('subject_group_sub');
                 if($delete){
                 	for($i = 0;$i < count($data['subjectlist']);$i++)
						{
							$subjectGroupArr = array('sub_group_id' => (int)$data['id'], 
								                     'subject_id' => $data['subjectlist'][$i]
								                    );
							$result = $this->db->insert('subject_group_sub', $subjectGroupArr);
						}

						$this->db->select('sg.subject_group_id,sg.subject_group_name,sg.course_id,GROUP_CONCAT(sgsb.subject_id) as subjectids');
						$this->db->from('subject_group AS sg');
						$this->db->join('subject_group_sub AS sgsb', 'sgsb.sub_group_id = sg.subject_group_id', 'left');
						$this->db->where(array('sg.subject_group_id' => $data['id'], 'sg.active' => '1'));
						$this->db->group_by('sgsb.sub_group_id');
						$query = $this->db->get();
				        $resultdata = $query->row_array();
						if($resultdata)
						{
								$subjectGroupData["subject_group_id"] = (int)$resultdata['subject_group_id'];
								$subjectGroupData["subject_group_name"] = $resultdata['subject_group_name'];
								$subjectGroupData["course_id"] = $resultdata['course_id'];
								$subjectGroupData["subjectids"] = explode(",",$resultdata['subjectids']);
						}
						else
						{
							$errormessage = "This subject group is either deleted or not found.";
						}
			  }else{
			  	           $errormessage = "This subject group data is not deleted successfully. Please try again.";
			  }
			}
		}
		
		return $subjectGroupData;
	}
// Delete Subject Group Model
public function deleteSubjectGroupByID($data, &$errormessage)
	{
		$subjectgroupid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $subjectgroupid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $subjectgroupid;
		}
		else
		{
			$upddata = array('active'=>'0');
			$query = $this->db->where(array('subject_group_id' => $data['id'],'active' => '1'));
			$result = $this->db->update('subject_group',$upddata);
			if($result)
			{
				$subjectgroupid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "This subject group is either deleted or not found.";
			}
		}
				
		return $subjectgroupid;
	}		
}

/* End of file SubjectGroup_model.php */
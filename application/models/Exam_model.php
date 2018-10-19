<?php

require "Go_model.php";
class Exam_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	public function createExam($data, &$errormessage)
	{
		$examid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $examid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $examid;
		}
		else
		{	
			//print_r($data);
			$examScheduleArr=array();
			for($i=0;$i<count($data['subjectGroups']);$i++){
             		if($data['subjectGroups'][$i] != ''){
             			for($j=0;$j<count($data['subjectGroups'][$i]['examSchedule']);$j++){
             			$examScheduleArrtemp[] = array(
                  	               'sub_group_id' => $data['subjectGroups'][$i]['subgroup_id'],
                  	               'start_time' => date("H:i:s",strtotime($data['subjectGroups'][$i]['examSchedule'][$j]['start_time'])),
                  	               'exam_mode' => $data['subjectGroups'][$i]['examSchedule'][$j]['mode'],
                  	                );
				         }
				    }
			  }
			
			  if(!$examScheduleArrtemp){
			  	return 0;
			  }
			$userdataArr = array('author_id' => $data['userid'],'exam_name' => $data['name'],'course_id' => $data['course'],'no_of_question' => $data['noofques'],'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('exam', $userdataArr);
			$examid = $this->db->insert_id();
			if($examid > 0){
				$examScheduleArr=array();
				for($i=0;$i<count($data['subjectGroups']);$i++){
					if($data['subjectGroups'][$i] != ''){
						for($j=0;$j<count($data['subjectGroups'][$i]['examSchedule']);$j++){
             					$examScheduleArr[] = array('exam_id' => $examid, 
                  	               'sub_group_id' => $data['subjectGroups'][$i]['subgroup_id'],
                  	               'exam_date' => date("Y-m-d",strtotime($data['subjectGroups'][$i]['examSchedule'][$j]['exam_date'])),
                  	               'fee' => $data['subjectGroups'][$i]['examSchedule'][$j]['fee'],
                  	               'exam_mode' => $data['subjectGroups'][$i]['examSchedule'][$j]['mode'],
                  	               'exam_duration' => $data['subjectGroups'][$i]['examSchedule'][$j]['exam_duration']
                  	                );
             			}
				    }
				  }
				  
				$this->db->insert_batch('exam_schedule', $examScheduleArr);
				if($data['quesCount'] != null){
				foreach ($data['quesCount'] as $key => $value){
					if($value != null && $value != 0){
						$courseFeeArr = array('exam_id' => $examid, 'chapter_id' => $key,'no_of_ques' => $value);
						$result = $this->db->insert('exam_chapter_questions', $courseFeeArr);
					}
				}
			  }
			}

		}
		
		return $examid;
	}
	
	public function getExamDetails($data, &$errormessage)
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
			$this->db->select('e.exam_id as id,e.exam_name as name,e.no_of_question as totalques,c.course_name as cname');
			$this->db->from('exam As e');
			$this->db->join('course AS c', 'c.course_id = e.course_id', 'inner');
			$this->db->where(array('e.active' => '1','c.active' => '1'));
			$query = $this->db->get();
	        $coursedata = $query->result_array();
			if(!$coursedata)
			{
				$coursedata = array();
				$errormessage = "Exam are either deleted or not inserted.";
			}
		}
				
		return $coursedata;
	}
	public function getAllExamDetails($data, &$errormessage)
	{
		$coursedata = array();
		
			$this->db->select('e.exam_id as id,e.exam_name as name,e.no_of_question as totalques,c.course_name as cname');
			$this->db->from('exam As e');
			$this->db->join('course AS c', 'c.course_id = e.course_id', 'inner');
			$this->db->join('exam_schedule AS es', 'es.exam_id = e.exam_id', 'inner');
			$today = date("Y-m-d");
			$this->db->group_by('e.exam_id');
			$this->db->where("es.exam_date >=",$today);
			$this->db->where(array('e.active' => '1','c.active' => '1'));
			
			$query = $this->db->get();
	        $coursedata = $query->result_array();
			if(!$coursedata)
			{
				$coursedata = array();
				$errormessage = "Exam are either deleted or not inserted.";
			}
	
				
		return $coursedata;
	}
	public function getExamDetailsAll($data, &$errormessage)
	{
			$userdata = $this->GetLoggedinUserData($data['usersessionid']);
			
//print_r($data);
			// GET previus buy schedule id
			$this->db->select('exam_schedule_id');
			$this->db->from('student_buy_exam');
			$this->db->where(array('stud_id' => $userdata['userid']));
			$this->db->group_by('exam_schedule_id');	
			$query = $this->db->get();
	        $examScheduleData = $query->result_array();
			
			$scheduleIdArray = [];
			foreach ($examScheduleData as $schedule) {
				array_push($scheduleIdArray, $schedule['exam_schedule_id']);
			}
			$examScheduleData = null;
			//GET for not buy
			$this->db->select('e.exam_id as e_id,e.exam_name as name,es.exam_duration as duration,es.fee,es.exam_date,es.exam_mode,es.schedule_id as id');
			$this->db->from('exam As e');
			$this->db->join('exam_schedule AS es', 'es.exam_id = e.exam_id', 'inner');
			
			if (count($scheduleIdArray) > 0) {
				 $this->db->where_not_in('es.schedule_id', $scheduleIdArray);
			}
			$this->db->where('es.exam_date >=', date('Y-m-d'));
			if($data['mode'] == '' || $data['mode'] == null){
				$this->db->where(array('e.active' => '1','es.sub_group_id'=>$data['group_id']));
			}else{
				$this->db->where(array('e.active' => '1','es.sub_group_id'=>$data['group_id'],'es.exam_mode' => $data['mode']));
			}

			$query = $this->db->get();
	        $examData = $query->result_array();
			if(!$examData)
			{
				$examData = array();
				$errormessage = "All Exams are either deleted or not inserted.";
			}

		return $examData;
	}
	public function getSubGroup($data, &$errormessage)
	{
			$this->db->distinct();
			$this->db->select('sg.subject_group_id as id, sg.subject_group_name as name');
			$this->db->from('subject_group As sg');
			$this->db->join('exam_schedule AS es', 'es.sub_group_id = sg.subject_group_id', 'inner');
			$this->db->where(array('sg.active' => '1','sg.course_id'=>$data['course_id']));
			$today= date("Y-m-d");
			$this->db->where('es.exam_date >=',$today);
			$query = $this->db->get();
	        $subGroup = $query->result_array();

			if(!$subGroup)
			{
				$subGroup = array();
				$errormessage = "Sub Group are either deleted or not inserted.";
			}
		
				
		return $subGroup;
	}
	
	public function examCourse($data, &$errormessage)
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
			if($data['action_type'] == 'add'){
		            $this->db->select('c.course_id as id,c.course_name as name');
					$this->db->from('course As c');
					$this->db->join('exam AS e', 'c.course_id = e.course_id and e.active="1"', 'left');
					$this->db->group_by('c.course_id');
					$this->db->where(array('c.active' => '1','e.active'=>'1'));
					//$this->db->where(array('c.active' => '1','e.exam_name'=>null));
					$query = $this->db->get();
			        $coursedata = $query->result_array();
			}else{
				    $this->db->select('c.course_id as id,c.course_name as name');
					$this->db->from('course As c');
					$this->db->join('exam AS e', 'c.course_id = e.course_id and e.active="1"', 'left');
					$this->db->where(array('c.active' => '1','e.active'=>'1'));
					$query = $this->db->get();
			        $coursedata = $query->result_array();
			}
			if(!$coursedata)
			{
				$coursedata = array();
				$errormessage = "Course exam are either created or deleted or not inserted.";
			}
		}
				
		return $coursedata;
	}
	

	
	public function getExamByID($data, &$errormessage)
	{
		$examdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $examdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $examdata;
		}
		else
		{
			$curDate = date("Y-m-d",time());
			$query = $this->db->get_where('exam',array('exam_id' => $data['id'], 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
				    $query = $this->db->select('sub_group_id')
				                      ->from('exam_schedule')
				                      ->where(array('exam_id' => $data['id']))
									  //->where('exam_date >=', $curDate)
				                      ->group_by('sub_group_id')
				                      ->get();
	        		$result = $query->result_array();
	        		$query1 = $this->db->select('*')
				                      ->from('exam_schedule')
				                      ->where(array('exam_id' => $data['id']))
				                      ->where('exam_date >=', $curDate)
				                      ->get();
	        		$result1= $query1->result_array();
	        		//print_r($result1);exit;
					$schedule_arr=array();
					$group_sub_check_arr=array();
					for($i=0;$i < count($result);$i++){
						   $schedule_arr[$i]=[];
						   $schedule_arr[$i]['examSchedule']=array();
						   $group_sub_check_arr[$i]=true;
						   $schedule_arr[$i]['subgroup_id']=$result[$i]['sub_group_id'];
						for($j=0;$j < count($result1);$j++){
						   if($schedule_arr[$i]['subgroup_id'] == $result1[$j]['sub_group_id']){
						      $schedule_arr[$i]['examSchedule'][$j]['exam_date']=date("d-m-Y",strtotime($result1[$j]['exam_date']));
						      $schedule_arr[$i]['examSchedule'][$j]['fee']=$result1[$j]['fee'];
						      $schedule_arr[$i]['examSchedule'][$j]['schedule_id']=$result1[$j]['schedule_id'];
						      $schedule_arr[$i]['examSchedule'][$j]['exam_duration']=$result1[$j]['exam_duration'];
						      $schedule_arr[$i]['examSchedule'][$j]['start_time']=date("h:i A",strtotime($result1[$j]['start_time']));
						      $schedule_arr[$i]['examSchedule'][$j]['end_time']=date("h:i A",strtotime($result1[$j]['end_time']));
						      $schedule_arr[$i]['examSchedule'][$j]['mode']=$result1[$j]['exam_mode'];
						   }
						}
					        $schedule_arr[$i]['examSchedule'] = array_values($schedule_arr[$i]['examSchedule']);
					}
                
	        		$query5 = $this->db->select('ecq.*,(SELECT COUNT(q.ques_id) FROM  vid_question AS q WHERE q.chapter_id = ecq.chapter_id AND q.is_final = "1") as final_qun')
				                      ->from('exam_chapter_questions AS ecq')
				                      //->join('question AS q','q.chapter_id = ecq.chapter_id')
				                      ->where(array('ecq.exam_id' =>$data['id'],))
				                      ->group_by('ecq.chapter_id')
				                      ->get();
	        		$result6= $query5->result_array();

					$examdata["id"] = (int)$resultdata['exam_id'];
					$examdata["name"] = $resultdata['exam_name'];
					//$examdata["exam_duration"] = $resultdata['exam_duration'];
					$examdata["cid"] = $resultdata['course_id'];
					//$examdata["mark"] = $resultdata['exam_mark'];
					$examdata["noofques"] = $resultdata['no_of_question'];
					//$examdata["markperques"] = $resultdata['mark_per_question'];
					//$examdata["isnegative"] = $resultdata['is_negative'];
					//$examdata["negativewt"] = $resultdata['negative_wt'];
					$examdata['subjectGroup']=$schedule_arr;
					$examdata['checkSubjectGroup']=$group_sub_check_arr;
					$examdata["quesCount"] = $result6;
			}
			else
			{
				$errormessage = "This exam is either deleted or not found.";
			}
		}
		//print_r($examdata);exit;	
		return $examdata;
	}

	public function updateExam($data, &$errormessage) 
	{
		
		$examdata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $examdata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $examdata;
		}
		else
		{
			$upddata = array('exam_name' => $data['name'],'course_id' => $data['course'],'no_of_question' => $data['noofques'],);
			$this->db->where(array('exam_id'=> (int)$data['id'], 'active' => '1'));
			$result = $this->db->update('exam', $upddata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				if(count($data['subjectGroups']) > 0){
				
				for($i=0;$i<count($data['subjectGroups']);$i++){
					for($j=0;$j<count($data['subjectGroups'][$i]['examSchedule']);$j++){
						$examScheduleArr=array();
						if (!$data['subjectGroups'][$i]['examSchedule'][$j]['schedule_id']) {
							$examScheduleArr = array('exam_id' => (int)$data['id'], 
                  	               'sub_group_id' => $data['subjectGroups'][$i]['subgroup_id'],
                  	               'exam_date' => date("Y-m-d",strtotime($data['subjectGroups'][$i]['examSchedule'][$j]['exam_date'])),
                  	               'fee' => $data['subjectGroups'][$i]['examSchedule'][$j]['fee'],
                  	               'exam_mode' => $data['subjectGroups'][$i]['examSchedule'][$j]['mode'],
                  	               'exam_duration' => $data['subjectGroups'][$i]['examSchedule'][$j]['exam_duration']
                  	                );
				  			$this->db->insert('exam_schedule', $examScheduleArr);
						}else{
                 			$examScheduleArr = array('exam_id' => (int)$data['id'], 
                  	               'sub_group_id' => $data['subjectGroups'][$i]['subgroup_id'],
                  	               'exam_date' => date("Y-m-d",strtotime($data['subjectGroups'][$i]['examSchedule'][$j]['exam_date'])),
                  	               'fee' => $data['subjectGroups'][$i]['examSchedule'][$j]['fee'],
                  	               'exam_mode' => $data['subjectGroups'][$i]['examSchedule'][$j]['mode'],
                  	               'exam_duration' => $data['subjectGroups'][$i]['examSchedule'][$j]['exam_duration']
                  	                );
                 			$this->db->where(array('schedule_id'=> $data['subjectGroups'][$i]['examSchedule'][$j]['schedule_id']));
				  			$result = $this->db->update('exam_schedule', $examScheduleArr);
				  		}
				    }
				  }
			     }
			    $chapter_result_delete = $this->db->delete('exam_chapter_questions','exam_id = '.(int)$data['id']);
				if($chapter_result_delete && $data['quesCount'] != null)
				{
				foreach ($data['quesCount'] as $key => $value){
					if($value != null && $value != 0){
						$courseFeeArr = array('exam_id' => (int)$data['id'], 'chapter_id' => $key,'no_of_ques' => $value);
						$result = $this->db->insert('exam_chapter_questions', $courseFeeArr);
					}
				 }
				}

				$query = $this->db->get_where('exam',array('exam_id' => $data['id'], 'active' => '1'));
		        $resultdata = $query->row_array();
				if($resultdata)
				{
					$examdata["id"] = (int)$resultdata['exam_id'];
					$examdata["name"] = $resultdata['exam_name'];
					//$examdata["duration"] = $resultdata['exam_duration'];
					$examdata["cid"] = $resultdata['course_id'];
					//$examdata["mark"] = $resultdata['exam_mark'];
					$examdata["noofques"] = $resultdata['no_of_question'];
					
				}
				else
				{
					$errormessage = "This exam is either deleted or not found.";
				}
			}
		}
		
		return $examdata;
	}

	public function deleteExamByID($data, &$errormessage)
	{
		$examid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $examid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $examid;
		}
		else
		{
			$upddata = array('active'=>'0');
			$query = $this->db->where(array('exam_id' => $data['id'],'active' => '1'));
			$result = $this->db->update('exam',$upddata);
			if($result)
			{
				$examid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "This exam is either deleted or not found.";
			}
		}
				
		return $examid;
	}

	public function deleteMultipleExam($data, &$errormessage)
	{
		$examid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $examid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $examid;
		}
		else
		{
			foreach($data['ids'] as $id){
				$upddata = array('active'=>'0');
				$query = $this->db->where(array('exam_id' => $id,'active' => '1'));
				$result = $this->db->update('exam',$upddata);
			}
			
			if($result)
			{
				$examid = $data['ids'];	
			}
			else
			{
				$errormessage = "This course is either deleted or not found.";
			}
		}
				
		return $examid;
	}
		
}

<?php

require "Go_model.php";
class Course_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
// send mail function	
public function sendEmail($to, $subject, $message, $headers)
	{
		if(@mail($to, $subject, $message, $headers))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		return TRUE;
	}
// create course model 	
public function createCourse($data, &$errormessage)
	{
		$courseid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $courseid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $courseid;
		}
		else
		{
			$userdataArr = array('course_name' => $data['name'],'description' => $data['description'],'author_id' => (int)$data['authorid'],'author_type' => (int)$userdata['type'],'category_id' => (int)$data['category'],'course_level' => (int)$data['level'], 'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('course', $userdataArr);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$courseid = $this->db->insert_id();
				
			}
		}
		
		return $courseid;
	}
// get course details model 	
public function getCourseDetails($data, &$errormessage)
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
			$this->db->select('c.course_id as id,c.course_name as name,c.course_level as level,cc.category_name as category');
			$this->db->from('course AS c');
			$this->db->join('course_category AS cc', 'cc.category_id = c.category_id', 'inner');
			$this->db->where(array('c.active' => '1'));
			$this->db->order_by('c.course_id',asc);
			$query = $this->db->get();
	        $coursedata = $query->result_array();
			if(!$coursedata)
			{
				$coursedata = array();
				$errormessage = "Courses are not available.";
			}
		}
				
		return $coursedata;
	}
	// get course details model 	
public function getAllCourseDetails($data, &$errormessage)
	{
		$coursedata = array();
	
			$this->db->select('c.course_id as id,c.course_name as name,c.course_level as level,cc.category_name as category');
			$this->db->from('course AS c');
			$this->db->join('course_category AS cc', 'cc.category_id = c.category_id', 'inner');
			$this->db->where(array('c.active' => '1'));
			$query = $this->db->get();
	        $coursedata = $query->result_array();
			if(!$coursedata)
			{
				$coursedata = array();
				$errormessage = "Courses are not available.";
			}
		
				
		return $coursedata;
	}
// get all category model 
public function getCategory($data, &$errormessage)
	{
		$categorydata = array();
			$this->db->select('category_id as id,category_name as name');
			$query = $this->db->get_where('course_category',array('active' => '1'));
	        $categorydata = $query->result_array();
			if(!$categorydata)
			{
				$categorydata = array();
				$errormessage = "Categories are not available.";
			}
				
		return $categorydata;
	}
public function getCourseSubjectHirarchy($data, &$errormessage){
	
	$masterData = array();
	$this->db->select('e.exam_id,e.exam_name,es.schedule_id,es.sub_group_id',false);

	$this->db->from('student_buy_exam AS sbe');
	$this->db->join('exam_schedule AS es', 'sbe.exam_schedule_id = es.schedule_id', 'inner');
	$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');			
	//$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
	$this->db->group_by('es.sub_group_id');				

	$this->db->where(array('e.exam_id' => (int)$data['exam_id'],'sbe.stud_id' => (int)$data['userId']));	

	$query = $this->db->get();
    $resultdata = $query->result_array();
  	  	/*print_r($resultdata);

die();*/
//SELECT sgs.subject_id,sbe.stud_id,sbe.course_id FROM vid_student_buy_exam as sbe inner join vid_exam_schedule as sch on sbe.exam_schedule_id = sch.schedule_id inner join vid_subject_group_sub as sgs on sgs.sub_group_id = sch.sub_group_id where sbe.stud_id = 319 and sbe.course_id = 11 order by sgs.subject_id
		$temp_schudule = [];
   

	  	for ($i=0; $i < count($resultdata); $i++) { 

	  		$group_id['sg_id'] = $resultdata[$i]['sub_group_id'];
	  		$temp_schudule[] = $group_id;
	  	}
	  	$subData = [];

	  	$sub1_id=array();
	  	for ($q=0; $q < count($temp_schudule); $q++) { 

	  		$this->db->select('s.subject_name,sgs.sub_group_id,s.subject_id',false);
			$this->db->from('subject_group_sub AS sgs');
			$this->db->join('subject AS s', 's.subject_id = sgs.subject_id', 'inner');
			$this->db->group_by('s.subject_id');				
			$this->db->where(array('sgs.sub_group_id' => $temp_schudule[$q]['sg_id']));
			$query = $this->db->get();
		    $subData = $query->result_array();

		  	for ($qw=0; $qw < count($subData); $qw++) { 
		  			$sub1_id[] = $subData[$qw];
		  	}
	  	  }
	  

 	 	$chapter_count=$sub1_id;
 	 	
	  	   	$temp_array = array(); 
		    $i = 0; 
		    $c = 0;
		    $key_array = array(); 
		    $key = 'subject_id';
		    foreach($sub1_id as $val) { 
		        if (!in_array($val[$key], $key_array)) { 
		            $key_array[$c] = $val[$key]; 
		            $temp_array[$i] = $val; 
		         
		        $i=$i+1; 
		        $c++;
		        }
		    } 



 		if(!$temp_array){
				$coursedata = array();
				$errormessage = "subject or chapter are not available.";
		}else{    	
    	$subData= [];
    	$temp_count=count($temp_array);
    	for($j=0;$j < $temp_count; $j++){
    		
		   
		    	$subData[$j]['subject_id']=$temp_array[$j]['subject_id'];
		    	$subData[$j]['subject_name']=$temp_array[$j]['subject_name'];
		    	$subData[$j]['sub_group_id']=$temp_array[$j]['sub_group_id'];

		    	$this->db->select('c.*',false);
				$this->db->from('subject AS s');
				$this->db->join('chapter AS c', 's.subject_id = c.subject_id', 'inner');				
				$this->db->where(array('s.subject_id' => $temp_array[$j]['subject_id']));			
				$query = $this->db->get();
			    $tempChaData = $query->result_array();
			    $subData[$j]['chapterDetail'] = $tempChaData;
			    
		   		
		    //array_push($masterData['subjectDetail'], $subData);
		    
		   
		    //array_push($masterData1['subjectGroupData'],$tempSubjectDetail);
    	} 
		    $masterData = $subData;
    }
   //print_r($masterData);
     return $masterData;

}

/*public function getCourseSubjectHirarchy__Extra($data, &$errormessage){
	
	$masterData = array();
	$this->db->select('e.exam_id,e.exam_name,es.sub_group_id,sg.subject_group_name',false);

	$this->db->from('exam AS e');
	$this->db->join('exam_schedule AS es', 'es.exam_id = e.exam_id', 'inner');			
	$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');			
	$this->db->where(array('e.exam_id' => (int)$data['exam_id']));	

	$query = $this->db->get();
    $resultdata = $query->result_array();

  
    if($resultdata){    	
    	$subData= [];
    	for($j=0;$j < count($resultdata);$j++){
    		$masterData['exam_id'] = $resultdata[$j]['exam_id'];
			$masterData['exam_name'] = $resultdata[$j]['exam_name'];
			$masterData['sub_group_id'] = $resultdata[$j]['sub_group_id'];
			$masterData['subject_group_name'] = $resultdata[$j]['subject_group_name'];
    		$this->db->select('s.subject_name,sgs.sub_group_sub_id,sgs.sub_group_id,s.subject_id',false);
			$this->db->from('subject_group_sub AS sgs');
			$this->db->join('subject AS s', 's.subject_id = sgs.subject_id', 'inner');
			$this->db->group_by('s.subject_id');				
			$this->db->where(array('sgs.sub_group_id' => $masterData['sub_group_id']));

			$query = $this->db->get();
		    $subData = $query->result_array();
		    //print_r($subData);
		    $sub_length=count($subData);

		    for($i=0;$i < $sub_length;$i++){
		    	
		    	$this->db->select('*',false);
				$this->db->from('subject AS s');
				$this->db->join('chapter AS c', 's.subject_id = c.subject_id', 'inner');				
				$this->db->where(array('s.subject_id' => $subData[$i]['subject_id']));			
				$query = $this->db->get();
			    $tempChaData = $query->result_array();
			    $subData[$i]['chapterDetail'] = $tempChaData;
			    
		    }

		    $masterData['subjectDetail'] = $subData;
		    //array_push($masterData['subjectDetail'], $subData);
		    
		    $masterData1[] = $masterData;
		    //array_push($masterData1['subjectGroupData'],$tempSubjectDetail);
    	}
    		//print_r($masterData1);
    	 return $masterData1;
    	
    }
   
     return $masterData1;

}*/
public function demo(){//delete it after work done

	$query = $this->db->select('sub_group_id')
	                          ->from('exam_schedule')
	                          ->where(array('exam_id' => $data['id']))
	                          ->group_by('sub_group_id')
	                          ->get();
	           $result = $query->result_array();
	           $query1 = $this->db->select('*')
	                          ->from('exam_schedule')
	                          ->where(array('exam_id' => $data['id']))
	                          ->get();
	           $result1= $query1->result_array();
	           //print_r($result1);exit;
	$schedule_arr=array();
	$group_sub_check_arr=array();
	for($i=0;$i < count($result);$i++)
	 {
	    $schedule_arr[$i]=[];
	    $schedule_arr[$i]['examSchedule']=array();
	    $group_sub_check_arr[$i]=true;
	    $schedule_arr[$i]['subgroup_id']=$result[$i]['sub_group_id'];
	for($j=0;$j < count($result1);$j++)
	   {
	     if($schedule_arr[$i]['subgroup_id'] == $result1[$j]['sub_group_id']){
	        $schedule_arr[$i]['examSchedule'][$j]['exam_date']=date("d-m-Y",strtotime($result1[$j]['exam_date']));
	        $schedule_arr[$i]['examSchedule'][$j]['fee']=$result1[$j]['fee'];
	        $schedule_arr[$i]['examSchedule'][$j]['start_time']=date("h:i A",strtotime($result1[$j]['start_time']));
	        $schedule_arr[$i]['examSchedule'][$j]['end_time']=date("h:i A",strtotime($result1[$j]['end_time']));
	        $schedule_arr[$i]['examSchedule'][$j]['mode']=$result1[$j]['exam_mode'];
	             }
	                }
	            $schedule_arr[$i]['examSchedule'] = array_values($schedule_arr[$i]['examSchedule']);
	         }
}
// get Course Hirarchy model 

public function getCourseHirarchy($data, &$errormessage)
	{
		$coursedata = array();
		
		//$this->db->select('course_id,course_name,course_duration,course_mark,course_fee,course_level,category_id');
			$this->db->select('course_id,course_name,category_id,course_level');
			$query = $this->db->get_where('course',array('course_id' => $data['courseid'], 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
/*				$this->db->select('fee_id,fee,month');
				$query = $this->db->get_where('course_fee',array('courseid' => (int)$resultdata['course_id']));
	        	$coursefee = $query->result_array();*/
				
					$coursedata["id"] = (int)$resultdata['course_id'];
					$coursedata["name"] = $resultdata['course_name'];
					//$coursedata["duration"] = $resultdata['course_duration'];
					//$coursedata["mark"] = $resultdata['course_mark'];
					//$coursedata["fee"] = (float)$resultdata['course_fee'];
					$coursedata["level"] = $resultdata['course_level'];
					$coursedata["category"] = $resultdata['category_id'];
					//$coursedata["coursefee"] = $coursefee;
					///$coursedata["levelDetail"] = array();
					$coursedata["subjectDetail"] = array();
					$coursedata["chapterDetail"] = array();
				if((int)$coursedata["level"] == 2)
					{
						$this->db->select('s.subject_id as id,s.subject_name as name,s.subject_description as desc,s.weightage as weightage,(select count(*) from vid_question as q inner join vid_chapter as ch on ch.chapter_id = q.chapter_id where ch.subject_id = s.subject_id and ch.active= "1") as totalquestion');
						$this->db->from('subject AS s');
						$this->db->where(array('s.course_id' => (int)$coursedata['id'],'s.active' => '1'));
						$query = $this->db->get();
						$resultdatas = $query->result_array();
						if(!$resultdatas)
						{
							$subjectdata = array();
							$errormessage = "Subjects are not available.";
						}
						else
						{
							for($j=0;$j < count($resultdatas);$j++)
							{
								$tempsubject = array();
								$tempsubject['id'] = $resultdatas[$j]['id'];
								$tempsubject['name'] = $resultdatas[$j]['name'];
								$tempsubject['desc'] = $resultdatas[$j]['desc'];
								$tempsubject['weightage'] = $resultdatas[$j]['weightage'];
								$tempsubject['totalquestion'] = $resultdatas[$j]['totalquestion'];
								$tempsubject['chapterDetail'] = array();
								$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.chapter_description as desc,ch.is_topic,ch.weightage,(select count(*) from vid_question where chapter_id = ch.chapter_id) as totalquestion');
								$this->db->from('chapter AS ch');
								$this->db->where(array('ch.subject_id' => (int)$tempsubject['id'],'ch.active' => '1'));			
								$query = $this->db->get();
						    	$chapterdata = $query->result_array();
						    	for($c = 0;$c < count($chapterdata);$c++)
					        	{
									$tempchapter = array();
									$tempchapter = $chapterdata[$c];
									if($chapterdata[$c]['is_topic'] == '1')
									{
										/*$this->db->select('topic_id as id,topic_name as name,topic_desc as desc,(select count(*) from question where topic_id = topic_id) as totalquestion');
										$this->db->from('topic');
										$this->db->where(array('chapter_id' => (int)$chapterdata[$c]['id'],'active' => '1'));*/
										$this->db->select('t.topic_id as id,t.topic_name as name,t.topic_desc as desc,(select count(*) from question where topic_id = t.topic_id) as totalquestion');
										$this->db->from('topic as t');
										$this->db->where(array('t.chapter_id' => (int)$chapterdata[$c]['id'],'t.active' => '1'));		
										$query = $this->db->get();
							        	$topicdata = $query->result_array();
							        	$tempchapter['topicDetail'] = $topicdata;
									}
									else{
										$tempchapter['topicDetail'] = array();
									}
									array_push($tempsubject['chapterDetail'],$tempchapter);
								}
						    	//$tempsubject['chapterDetail'] = $chapterdata;
						    	array_push($coursedata['subjectDetail'],$tempsubject);
							}
						}
					}
					
			}
			else{
				$leveldata = array();
				$errormessage = "Course are not available.";
			}			
			return $coursedata;
	}
// get Course Chapter Topic model	
public function getCourseChapterTopic($data, &$errormessage)
	{
		$coursedata = array();
		
			$this->db->select('course_id,course_name,course_duration,course_mark,course_fee,course_level,category_id');
			$query = $this->db->get_where('course',array('course_id' => $data['courseid'], 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
				
					$courselist = array();
					$coursedata["id"] = (int)$resultdata['course_id'];
					$coursedata["name"] = $resultdata['course_name'];
					$coursedata["level"] = $resultdata['course_level'];
					$coursedata["levelDetail"] = array();
					$coursedata["subjectDetail"] = array();
					$coursedata["chapterDetail"] = array();
					$coursedata['courselist'] = array();
					
					if((int)$coursedata["level"] == 1)
					{
						$leveldata = array();
						$this->db->select('cl.level_id as id,cl.level_name as name,cl.level_description as desc,cl.level as level,c.course_name as coursename');
						$this->db->from('course_level AS cl');
						$this->db->join('course AS c', 'c.course_id = cl.course_id', 'inner');
						$this->db->where(array('cl.course_id' => $coursedata["id"],'cl.active' => '1'));
						$query = $this->db->get();
				        $resultdatal = $query->result_array();
						if(!$resultdatal)
						{
							$leveldata = array();
							$errormessage = "Level are not available.";
						}
						else
						{
							for($i=0;$i < count($resultdatal);$i++)
							{
								$templevel = array();
								$templevel['id'] = $resultdatal[$i]['id'];
								$templevel['name'] = $resultdatal[$i]['name'];
								$templevel['level'] = $resultdatal[$i]['level'];
								$templevel['subjectDetail'] = array();
								$templevel['chapterDetail'] = array();
								if((int)$templevel['level'] == 2)
								{
									$subjectdata = array();
									$this->db->select('s.subject_id as id,s.subject_name as name');
									$this->db->from('subject AS s');
									$this->db->where(array('s.level_id' => (int)$templevel['id'],'s.active' => '1'));
									$query = $this->db->get();
									$resultdatas = $query->result_array();
									if(!$resultdatas)
									{
										$subjectdata = array();
										$errormessage = "Level are not available.";
									}
									else
									{
										for($j=0;$j < count($resultdatas);$j++)
										{
											$tempsubject = array();
											$tempsubject['id'] = $resultdatas[$j]['id'];
											$tempsubject['name'] = $resultdatas[$j]['name'];
											$tempsubject['chapterDetail'] = array();
											
											$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.is_topic');
											$this->db->from('chapter AS ch');
											$this->db->where(array('ch.subject_id' => (int)$tempsubject['id'],'ch.active' => '1'));
											$query = $this->db->get();
								        	$chapterdata = $query->result_array();
								        	for($c = 0;$c < count($chapterdata);$c++)
								        	{
												$tempchapter = array();
												$temp = array();
												$temp['id'] = $chapterdata[$c]['id'];
												$temp['name'] = $chapterdata[$c]['name'];
												$temp['type'] = "chapter";
												array_push($courselist,$temp);
												$tempchapter = $chapterdata[$c];
												if($chapterdata[$c]['is_topic'] == '1')
												{
													$this->db->select('topic_id as id,topic_name as name');
													$this->db->from('topic');
													$this->db->where(array('chapter_id' => (int)$chapterdata[$c]['id'],'active' => '1'));
													$query = $this->db->get();
										        	$topicdata = $query->result_array();
										        	foreach($topicdata as $topictempdata)
										        	{
														$temp = array();
														$temp['id'] = $topictempdata['id'];
														$temp['name'] = $topictempdata['name'];
														$temp['type'] = "topic";
														array_push($courselist,$temp);
													}
										        	$tempchapter['topicDetail'] = $topicdata;
												}
												else{
													$tempchapter['topicDetail'] = array();
												}
												array_push($tempsubject['chapterDetail'],$tempchapter);
											}
								        	//$tempsubject['chapterDetail'] = $chapterdata;
								        	array_push($templevel['subjectDetail'],$tempsubject);
							        	}
									}
								}
								else
								if((int)$templevel['level'] == 3)
								{
									$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.is_topic');
									$this->db->from('chapter AS ch');
									
									$this->db->where(array('ch.level_id' => (int)$templevel['id'],'ch.active' => '1'));
									$query = $this->db->get();
						        	$chapterdata = $query->result_array();
						        	for($c = 0;$c < count($chapterdata);$c++)
						        	{
										$tempchapter = array();
										$temp = array();
										$temp['id'] = $chapterdata[$c]['id'];
										$temp['name'] = $chapterdata[$c]['name'];
										$temp['type'] = "chapter";
										array_push($courselist,$temp);
										$tempchapter = $chapterdata[$c];
										if($chapterdata[$c]['is_topic'] == '1')
										{
											$this->db->select('topic_id as id,topic_name as name');
											$this->db->from('topic');
											$this->db->where(array('chapter_id' => (int)$chapterdata[$c]['id'],'active' => '1'));
											$query = $this->db->get();
								        	$topicdata = $query->result_array();
								        	foreach($topicdata as $topictempdata)
										    {
												$temp = array();
												$temp['id'] = $topictempdata['id'];
												$temp['name'] = $topictempdata['name'];
												$temp['type'] = "topic";
												array_push($courselist,$temp);
											}
								        	$tempchapter['topicDetail'] = $topicdata;
										}
										else{
											$tempchapter['topicDetail'] = array();
										}
										array_push($templevel['chapterDetail'],$tempchapter);
									}
						        	//$templevel['chapterDetail'] = $chapterdata;
								}
								array_push($leveldata,$templevel);
							}
							
						}
						
						$coursedata["levelDetail"] = $leveldata;
					}
					else
					if((int)$coursedata["level"] == 2)
					{
						$this->db->select('s.subject_id as id,s.subject_name as name');
						$this->db->from('subject AS s');
						$this->db->where(array('s.course_id' => (int)$coursedata['id'],'s.active' => '1'));
						$query = $this->db->get();
						$resultdatas = $query->result_array();
						if(!$resultdatas)
						{
							$subjectdata = array();
							$errormessage = "Level are not available.";
						}
						else
						{
							for($j=0;$j < count($resultdatas);$j++)
							{
								$tempsubject = array();
								$tempsubject['id'] = $resultdatas[$j]['id'];
								$tempsubject['name'] = $resultdatas[$j]['name'];
								$tempsubject['chapterDetail'] = array();
								$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.is_topic');
								$this->db->from('chapter AS ch');
								$this->db->where(array('ch.subject_id' => (int)$tempsubject['id'],'ch.active' => '1'));			
								$query = $this->db->get();
						    	$chapterdata = $query->result_array();
						    	for($c = 0;$c < count($chapterdata);$c++)
					        	{
									$tempchapter = array();
									$temp = array();
									$temp['id'] = $chapterdata[$c]['id'];
									$temp['name'] = $chapterdata[$c]['name'];
									$temp['type'] = "chapter";
									array_push($courselist,$temp);
									$tempchapter = $chapterdata[$c];
									if($chapterdata[$c]['is_topic'] == '1')
									{
										$this->db->select('topic_id as id,topic_name as name');
										$this->db->from('topic');
										$this->db->where(array('chapter_id' => (int)$chapterdata[$c]['id'],'active' => '1'));
										$query = $this->db->get();
							        	$topicdata = $query->result_array();
							        	foreach($topicdata as $topictempdata)
										{
											$temp = array();
											$temp['id'] = $topictempdata['id'];
											$temp['name'] = $topictempdata['name'];
											$temp['type'] = "topic";
											array_push($courselist,$temp);
										}
							        	$tempchapter['topicDetail'] = $topicdata;
									}
									else{
										$tempchapter['topicDetail'] = array();
									}
									array_push($tempsubject['chapterDetail'],$tempchapter);
								}
						    	//$tempsubject['chapterDetail'] = $chapterdata;
						    	array_push($coursedata['subjectDetail'],$tempsubject);
							}
						}
					}
					else
					{
						
						$this->db->select('ch.chapter_id as id,ch.chapter_name as name,ch.is_topic');
						$this->db->from('chapter AS ch');
						$this->db->where(array('ch.course_id' => (int)$coursedata['id'],'ch.active' => '1'));
						$query = $this->db->get();
			        	$chapterdata = $query->result_array();
			        	for($c = 0;$c < count($chapterdata);$c++)
			        	{
							$tempchapter = array();
							$temp = array();
							$temp['id'] = $chapterdata[$c]['id'];
							$temp['name'] = $chapterdata[$c]['name'];
							$temp['type'] = "chapter";
							array_push($courselist,$temp);
							$tempchapter = $chapterdata[$c];
							if($chapterdata[$c]['is_topic'] == '1')
							{
								$this->db->select('topic_id as id,topic_name as name,topic_desc as desc');
								$this->db->from('topic');
								$this->db->where(array('chapter_id' => (int)$chapterdata[$c]['id'],'active' => '1'));
								$query = $this->db->get();
					        	$topicdata = $query->result_array();
					        	foreach($topicdata as $topictempdata)
								{
									$temp = array();
									$temp['id'] = $topictempdata['id'];
									$temp['name'] = $topictempdata['name'];
									$temp['type'] = "topic";
									array_push($courselist,$temp);
								}
					        	$tempchapter['topicDetail'] = $topicdata;
							}
							else{
								$tempchapter['topicDetail'] = array();
							}
							array_push($coursedata['chapterDetail'],$tempchapter);
						}
					}
					//array_push($coursedata['courselist'],$courselist);
			}
			else{
				$leveldata = array();
				$errormessage = "Course are not available.";
			}			
			return $courselist;
	}
// get course by id model	
public function getCourseByID($data, &$errormessage)
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
			$this->db->select('course_id,course_name,course_level,category_id,description');
			$query = $this->db->get_where('course',array('course_id' => $data['id'], 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
		/*		$this->db->select('fee_id,fee,month');
				$query = $this->db->get_where('course_fee',array('courseid' => (int)$resultdata['course_id']));
	        	$coursefee = $query->result_array();*/
				
					$coursedata["id"] = (int)$resultdata['course_id'];
					$coursedata["name"] = $resultdata['course_name'];
					$coursedata["desc"] = $resultdata['description'];
					
					$coursedata["level"] = $resultdata['course_level'];
					$coursedata["category"] = $resultdata['category_id'];
					//$coursedata["coursefee"] = $coursefee;
			}
			else
			{
				$errormessage = "This course is not available.";
			}
		}
				
		return $coursedata;
	}
	// get course by id model	
public function getCourseByIDDashboard($data, &$errormessage)
	{
		$coursedata = array();
	
			$this->db->select('course_id,course_name,course_level,category_id,description');
			$query = $this->db->get_where('course',array('course_id' => $data['id'], 'active' => '1'));
	        $resultdata = $query->row_array();
			if($resultdata)
			{
			
				
					$coursedata["id"] = (int)$resultdata['course_id'];
					$coursedata["name"] = $resultdata['course_name'];
					$coursedata["desc"] = $resultdata['description'];
					
					$coursedata["level"] = $resultdata['course_level'];
					$coursedata["category"] = $resultdata['category_id'];
			}
			else
			{
				$errormessage = "This course is not available.";
			}
		
				
		return $coursedata;
	}
// update course model	
public function updateCourse($data, &$errormessage)
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
			//'course_mark' => $data['mark'],
			$upddata = array('course_name' => $data['name'],'description' => $data['description'],'category_id' => (int)$data['category'],'author_id' => (int)$data['userid'],);
						$this->db->where(array('course_id'=> (int)$data['id'],'author_type' => (int)$userdata['type'], 'active' => '1'));

			$result = $this->db->update('course', $upddata);
		/*		print_r($data);
		die();*/
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$query = $this->db->get_where('course',array('course_id' => $data['id'], 'active' => '1'));
		        $resultdata = $query->row_array();
				if($resultdata)
				{
					$coursedata["id"] = (int)$resultdata['course_id'];
					$coursedata["name"] = $resultdata['course_name'];
					$coursedata["category"] = (int)$resultdata['category_id'];
				}
				else
				{
					$errormessage = "This course is not available.";
				}
			}
		}
		
		return $coursedata;
	}
// delete course model	
public function deleteCourseByID($data, &$errormessage)
	{
		$courseid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $courseid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $courseid;
		}
		else
		{
			$upddata = array('active'=>'0');
			$query = $this->db->where(array('course_id' => $data['id'],'active' => '1'));
			$result = $this->db->update('course',$upddata);
			if($result)
			{
				$courseid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "This course is not available.";
			}
		}
				
		return $courseid;
	}
// delete multiple course model	
public function deleteMultipleCourse($data, &$errormessage)
	{
		$courseid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $courseid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $courseid;
		}
		else
		{
			foreach($data['ids'] as $id){
				$upddata = array('active'=>'0');
				$query = $this->db->where(array('course_id' => $id, 'active' => '1'));
				$result = $this->db->update('course',$upddata);
			}
			
			if($result)
			{
				$courseid = $data['ids'];	
			}
			else
			{
				$errormessage = "This course is not available.";
			}
		}
				
		return $courseid;
	}
		
}

/* End of file Course_model.php */

/* Location: ./application/models/Course_model.php */
<?php

require "Go_model.php";
class Dashboard_model extends Go_model 
{
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	public function getCourse($data, &$errormessage)
	{
		$coursedata = array();
		
			$this->db->select("c.course_id as id,c.course_name as name,cc.category_name as category");
			$this->db->from('course AS c');
			$this->db->join('course_category AS cc', 'cc.category_id = c.category_id', 'inner');
			$this->db->join('question AS q', 'q.course_id = c.course_id', 'inner');
			$this->db->join('exam AS e', 'e.course_id = c.course_id', 'inner');
			$this->db->join('exam_schedule AS es', 'es.exam_id = e.exam_id', 'inner');
			$this->db->join('subject_group AS sg', 'c.course_id = sg.course_id', 'inner');

			if($data['catid'] != null && $data['catid'] != '')
			{
				$this->db->where(array('c.category_id' => $data['catid'],'c.active'=>'1',''));
			}
			$today= date("Y-m-d");
 			$this->db->where('es.exam_date >=',$today);
			$this->db->where(array('c.active' => '1','q.active' => '1','cc.active' => '1','e.active' => '1','sg.active' => '1',));
			$this->db->group_by('c.course_name');
			$query = $this->db->get();
	        $coursedata = $query->result_array();
			if(!$coursedata)
			{
				$coursedata = array();
				$errormessage = "Courses are not available.";
			}
		return $coursedata;
	}
	
	public function orderSummary($data, &$errormessage)
	{
		$orderdata = array();
		//DATE_ADD(NOW(), INTERVAL ct.duration MONTH) as validtill,
			$this->db->select('c.exam_name as name, es.exam_duration,es.fee, sc.temp_id, sc.schedule_id, c.course_id,es.exam_id,sc.studentid,c.course_id');
			$this->db->from('student_course_temp AS sc');
			$this->db->join('exam_schedule AS es', 'sc.schedule_id = es.schedule_id','inner');
			$this->db->join('exam AS c', 'es.exam_id = c.exam_id','inner');
			
			//$this->db->where(array('sc.studentid'=>'317'));
			$this->db->where(array('sc.studentid'=>$data['userid']));
			/*$today = date("Y-m-d");
			$this->db->where("c.exam_date >=",$today);*/
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	       /* print_r($resultdata);*/
			if(!$resultdata)
			{
				$orderdata = array();
				$errormessage = "Order details are not available.";
			}
			else{
				
				for($i=0;$i<count($resultdata);$i++)
				{
			
					$tempdata = array();
					$tempdata['tempid'] = $resultdata[$i]['temp_id'];
					//$tempdata['courseid'] = $resultdata[$i]['courseid'];
					$tempdata['duration'] = $resultdata[$i]['exam_duration'];
					$tempdata['fee'] = $resultdata[$i]['fee'];
					$tempdata['studentid'] = $resultdata[$i]['studentid'];
					$tempdata['id'] = $resultdata[$i]['exam_id'];
					$tempdata['name'] = $resultdata[$i]['name'];
					$tempdata['schedule_id'] = $resultdata[$i]['schedule_id'];
					$tempdata['course_id'] = $resultdata[$i]['course_id'];
					
					$tempdata['payamount'] = (int)$tempdata['fee'];
					array_push($orderdata,$tempdata);
				}
				
			}
		return $orderdata;
	}
	
	public function tempStudentCourse($data, &$errormessage)
	{
		$tempid = array();
			foreach ($data['courseid'] as $key => $value) {
				
			
				$this->db->where(array('courseid' => $data['courseid'],'studentid' => $data['userid']));
		   		$this->db->delete('student_course_temp');
		   		
				$tempdata = array('courseid' => $value,'studentid' => $data['userid']);
				$result = $this->db->insert('student_course_temp', $tempdata);
			}
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$tempid = $this->db->insert_id();
			}
			
		return $tempid;
	}
	public function tempStudentExam($data, &$errormessage)
	{
		$tempid = array();
			
			foreach ($data['schedule'] as $key => $value) {
				$this->db->where(array('schedule_id' => $value,'studentid' => $data['userid']));
		   		$this->db->delete('student_course_temp');

				$tempdata = array('schedule_id' => $value,'studentid' => $data['userid'],'submitdate'=>date('Y-m-d'));
				$result = $this->db->insert('student_course_temp', $tempdata);
			}
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$tempid = $this->db->insert_id();
			}
			
		return $tempid;
	}
	public function getStudId($orderid, &$errormessage)
	{
		$tempid = array();
			
			$this->db->select('payment_id,stud_id');
			$this->db->from('student_payment_details');			
			$this->db->where(array('txnid'=>$orderid));
			$query = $this->db->get();
	        $resultdata = $query->row_array();
	        
			if(!$resultdata)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$tempid['userid'] = $resultdata['stud_id'];
				$tempid['payment_id'] = $resultdata['payment_id'];
			}
			
		return $tempid;
	}
	
	public function payAmount($data, &$errormessage)
	{
		//print_r($data);
		$tempid = array();
		$paymid = 0;
			$paymdata = array('status' => '1','update_date' => $data['createddate']);
			$this->db->where(array('payment_id'=> $data["payment_id"],'stud_id'=> $data["userid"]));
			$result = $this->db->update('student_payment_details', $paymdata);

			if($result)
			{
				
				for($i = 0;$i < count($data['orderData']);$i++)
				{
	
					$this->db->select('roll_no');
					$this->db->from('student_buy_exam');			
					$this->db->where(array('exam_schedule_id'=>$data['orderData'][$i]['schedule_id']));
					$this->db->order_by("stud_course_batch_id", "desc");
					$query = $this->db->get();
			        $resultdata = $query->row_array();
			        if ($resultdata['roll_no']== 0) {
			        	//$previousRoll = $resultdata['roll_no']+1;
			        	$previousRoll = str_pad($resultdata['roll_no'] + 1, 4, 0, STR_PAD_LEFT);
			        	$courseId = str_pad($data['orderData'][$i]['course_id'], 2, 0, STR_PAD_LEFT);
			        	$schuduleId = str_pad($data['orderData'][$i]['schedule_id'], 4, 0, STR_PAD_LEFT);
			        	$rollNo= $courseId.$schuduleId.$previousRoll;
			        }else{
			        	$tempRoll = str_pad($resultdata['roll_no']+1, 10, 0, STR_PAD_LEFT);
			        	$rollNo = $tempRoll ;

			        }
					      /*  echo $rollNo;
					        $rollNo = str_pad($resultdata['roll_no'] + 1, 5, 0, STR_PAD_LEFT);*/
					        //echo $rollNo;
					       // print_r($resultdata);
					$tempdata = array('stud_id' => $data['orderData'][$i]['studentid'],'exam_schedule_id' => $data['orderData'][$i]['schedule_id'],'payment_id' => $data["payment_id"],'is_payment'=>'1','submitdate' => date('Y-m-d H:i:s'),'roll_no'=>$rollNo,'course_id'=>$data['orderData'][$i]['course_id']);
					$result = $this->db->insert('student_buy_exam', $tempdata);
					
					if(!$result)
					{
						$errormessage = "Some unknown error has occurred. Please try again.";
					}
					else
					{
						$tempid =$rollNo; 
						$this->db->insert_id();
						$this->db->where('temp_id',$data['orderData'][$i]['tempid']);
	   					$this->db->delete('student_course_temp');
					}
				}
				
			}
		return $tempid;
	}
	
	public function UpdatePaymentData($data, &$errormessage)
	{
		$paymid = 0;
			$paymdata = array('status' => '2','update_date' => $data['createddate']);
			$this->db->where(array('payment_id'=> $data["udf4"],'stud_id'=> $data["udf1"]));
			$result = $this->db->update('student_payment_details', $paymdata);
			if($result)
			{
				$paymid = $data["udf4"];
			}
			return $paymid;
	}
	
	public function tempStudentCourseUpdate($data, &$errormessage)
	{
		$tempid = array();
   		
			$tempdata = array('studentid' => $data['userid']);
			$this->db->where(array('tempid'=> $data['id']));
			$result = $this->db->update('student_course_temp', $tempdata);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$tempid = (int)$data['id'];
			}
			
		return $tempid;
	}
	
	public function removeOrder($data, &$errormessage)
	{
		$tempid = array();
			
			$this->db->where(array('temp_id'=> $data['id']));
			$result = $this->db->delete('student_course_temp');
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$tempid = (int)$data['id'];
			}
			
		return $tempid;
	}
	

	public function getQuestion($data, &$errormessage)
	{
		$questiondata = array();
		
			$this->db->select('q.ques_id as id,q.chapter_id as chapter,q.course_id as course,q.ques_text as question,q.is_sequence,ca.option_id as optionid,ca.ans_explanation as expl,qo.option_text as optiontext');
			$this->db->from('question as q');
			$this->db->join('question_correct_answer as ca', 'q.ques_id = ca.ques_id', 'inner');
			$this->db->join('question_options as qo', 'ca.option_id = qo.option_id', 'inner');
			$this->db->where(array('q.course_id'=>(int)$data['id'],'q.active' => '1','q.ques_type' => '0'));
			$this->db->order_by('rand()');
			$this->db->limit('10');
			$query = $this->db->get();
	        $resultdata = $query->result_array();
			if($resultdata)
			{
				for($i=0;$i < count($resultdata) ;$i++)
				{
					$this->db->select('option_id as optid,option_text as option,img_path as imgpath');
					$this->db->from('question_options');
					$this->db->where(array('ques_id'=>(int)$resultdata[$i]['id']));
					if((int)$resultdata[$i]['is_sequence'] != 1)
					{
						$this->db->order_by('rand()');
					}
					
					$query = $this->db->get();
			        $optiondata = $query->result_array();
			        
			        $question = array();
			        $question['id'] = $resultdata[$i]['id'];
			        $question['chapter'] = $resultdata[$i]['chapter'];
			        $question['course'] = $resultdata[$i]['course'];
			        $question['question'] = $resultdata[$i]['question'];
			        $question['optionid'] = $resultdata[$i]['optionid'];
			        $question['expl'] = $resultdata[$i]['expl'];
			        $question['optiontext'] = $resultdata[$i]['optiontext'];
			        $question['options'] = $optiondata;
			        $questiondata[] = $question;
				}
				
			}
			else{
				$questiondata = array();
				$errormessage = "Course question are either delete or not inserted.";
			}
			
		return $questiondata;
	}
	public function getDemoQuestion($data, &$errormessage)
	{
		//print_r($data['id']);
		$questiondata = array();
		
			$this->db->select('q.ques_id as id,q.chapter_id as chapter,q.course_id as course,q.ques_text as question,q.is_sequence,ca.option_id as optionid,ca.ans_explanation as expl,qo.option_text as optiontext');
			$this->db->from('question as q');
			$this->db->join('question_correct_answer as ca', 'q.ques_id = ca.ques_id', 'inner');
			$this->db->join('course as c', 'c.course_id = q.course_id', 'inner');
			//$this->db->join('exam as e', 'e.course_id = c.course_id', 'inner');
			$this->db->join('question_options as qo', 'ca.option_id = qo.option_id', 'inner');
			$this->db->where(array('q.course_id'=>$data['id'],'q.active' => '1','q.is_final' => '0'));
			$this->db->order_by('q.ques_id',asc);
			$this->db->limit('10');
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	       
			if($resultdata)
			{
				for($i=0;$i < count($resultdata) ;$i++)
				{
					$this->db->select('option_id as optid,option_text as option,img_path as imgpath');
					$this->db->from('question_options');
					$this->db->where(array('ques_id'=>(int)$resultdata[$i]['id']));
					if((int)$resultdata[$i]['is_sequence'] != 1)
					{
						$this->db->order_by('rand()');
					}
					
					$query = $this->db->get();
			        $optiondata = $query->result_array();
			        
			        $question = array();
			        $question['id'] = $resultdata[$i]['id'];
			        $question['chapter'] = $resultdata[$i]['chapter'];
			        $question['course'] = $resultdata[$i]['course'];
			        $question['question'] = $resultdata[$i]['question'];
			        $question['optionid'] = $resultdata[$i]['optionid'];
			        $question['expl'] = $resultdata[$i]['expl'];
			        $question['optiontext'] = $resultdata[$i]['optiontext'];
			        $question['options'] = $optiondata;
			        $questiondata[] = $question;
				}
				
			}
			else{
				$questiondata = array();
				$errormessage = "Course question are either delete or not inserted.";
			}
		return $questiondata;
	}
	
	public function getOption($data, &$errormessage)
	{
		$optiondata = array();
		
			$this->db->select('option_id as id,option_text as option');
			$this->db->from('question_options');
			$this->db->where(array('ques_id'=>(int)$data['id']));
			$this->db->order_by('rand()');
			$query = $this->db->get();
	        $optiondata = $query->result_array();
			if(!$optiondata)
			{
				$optiondata = array();
				$errormessage = "Course question are either delete or not inserted.";
			}
			
		return $optiondata;
	}
	public function studDashboardCounts($data, &$errormessage)
	{		
		$countdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $feedbackdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $feedbackdata;
		}
		else
		{
			$curDate = date("Y-m-d",time());

	        $this->db->select('es.schedule_id as upcomingExam',false);
	        //$this->db->select('COUNT(sbe.*) as upcomingExam',false);
			$this->db->from('student_buy_exam as sbe');
			$this->db->join('exam_schedule AS es', 'es.schedule_id = sbe.exam_schedule_id', 'inner');
			$this->db->where(array('sbe.stud_id' => $data['userid']));//AND 'es.exam_mode' => '2'
			$this->db->where('es.exam_date >=', date('Y-m-d'));
			$query = $this->db->get();
	        $upcomingExam = $query->result_array();
	        $countdata['upcomingExam'] = count($upcomingExam);
	        // My apeared exam
	        $this->db->select('es.schedule_id as upcomingExam',false);
			$this->db->from('student_buy_exam as sbe');
			$this->db->join('exam_schedule AS es', 'es.schedule_id = sbe.exam_schedule_id', 'inner');
			$this->db->where(array('sbe.stud_id' => $data['userid']));//AND 'es.exam_mode' => '2'
			$this->db->where('es.exam_date <=', date('Y-m-d'));
			$query = $this->db->get();
	        $apeared = $query->result_array();
	        $countdata['appearedExam'] = count($apeared);
	       
		}				
		return $countdata;
	}
	public function getCoursename($data, &$errormessage)
	{
		$coursename = '';
		
			$this->db->select('course_name');
			$this->db->from('course');
			$this->db->where(array('course_id'=>(int)$data['id'],'active' => '1'));
			$query = $this->db->get();
	        $result = $query->row_array();
			if(!$result)
			{
				$coursename = '';
				$errormessage = "Course are either delete or not inserted.";
			}
			else{
				$coursename = $result['course_name'];
			}
			
		return $coursename;
	}
	
}

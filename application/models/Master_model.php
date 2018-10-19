<?php

require "Go_model.php";
class Master_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
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
	
	public function getDoubtsDetails($data, &$errormessage)
	{
		$querydata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);

		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $querydata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $querydata;
		}
		else
		{

				$this->db->select('dt.d_id,s.stud_name as student,
									s.stud_contact as mobile,
									q.ques_text as question,
									e.exam_name as exam,
									dt.add_date add_date,
									es.exam_date as exam_date,
									dt.status as solved_status
									');
			   	$this->db->from('doubt_table AS dt');
			    $this->db->join('student AS s', 's.stud_id = dt.stud_id', 'inner');
			   	$this->db->join('question AS q', 'q.ques_id = dt.qun_id', 'inner');
			   	$this->db->join('exam AS e', 'e.exam_id = dt.exam_id', 'inner');
			   	$this->db->join('exam_schedule AS es', 'es.schedule_id = dt.schuduled_id', 'inner');
			    //$this->db->where(array('dt.status' => '0'));
			   	$this->db->order_by("dt.d_id", "desc");
				$query = $this->db->get();
		        $querydata = $query->result_array();
		       // echo "doubt history";	
		   //	print_r($querydata);
			if(!$querydata)
			{
				$querydata = array();
				$errormessage = "Feedback are either deleted or not inserted.";
			}
		}
				
		return $querydata;
	}
	public function solvedDoubtById($data, &$errormessage)
	{
		$examid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $examid;
		}
		else
		{
			$upddata = array('status'=>'1');
			$query = $this->db->where(array('d_id' => $data['id']));
			$result = $this->db->update('doubt_table',$upddata);
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
		public function getFeedbackDetails($data, &$errormessage)
	{
		$feeddata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $feeddata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $feeddata;
		}
		else
		{
			
			
				$receiver_id = 	(int)$data['instid'];
				$receiver_id = 	(int)$data['userid'];
				$this->db->select('f.feedback_id,date(f.fbsubmit_date) as fbsubmitdate,f.reply_msg,f.fb_msg as concern,s.stud_name as studname,s.stud_id as studid,s.stud_contact as contact');
			   	$this->db->from('fbdetails f');
			   	//$this->db->join('fbconcern AS con', 'con.concern_id = f.concern_id', 'inner');
			   	$this->db->join('student AS s', 's.stud_id = f.student_id', 'inner');
			   	$this->db->where(array('f.fbactive' => '1'));
			   	$this->db->order_by("f.feedback_id", "desc");
				$query = $this->db->get();
		        $feeddata = $query->result_array();			
			
			
			if(!$feeddata)
			{
				$feeddata = array();
				$errormessage = "Feedback are either deleted or not inserted.";
			}
		}
				
		return $feeddata;
	}
	
	public function getFeedbackByID($data, &$errormessage)
	{		
		$feedbackdata = array();
		
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
			
			$this->db->select('f.fb_msg,f.reply_msg,f.fbreply_date,con.concern,c.course_name,date(f.fbsubmit_date) as fbsubmit_date');
		   	$this->db->from('fbdetails f');
		   	$this->db->join('fbconcern AS con', 'con.concern_id = f.concern_id', 'inner');
		   	$this->db->join('course AS c', 'c.course_id = f.course_id', 'inner');
		   	$this->db->where(array('f.fbactive' => '1', 'f.feedback_id' => (int)$data['id']));
			$query = $this->db->get();
	        $resultdata = $query->row_array();			

			if($resultdata)
			{
				$feedbackdata["coursename"] = $resultdata['course_name'];
				$feedbackdata["concerntext"] = $resultdata['concern'];
				$feedbackdata["feedback"] = $resultdata['fb_msg'];
				$feedbackdata["reply"] = $resultdata['reply_msg'];
				$feedbackdata["feedbackdate"] = $resultdata['fbsubmit_date'];
				$feedbackdata["replydate"] = $resultdata['fbreply_date'];									
			}
			else
			{
				$errormessage = "This Feedback is either deleted or not found.";
			}
		}
				
		return $feedbackdata;
	}
	public function getallexamsdatamodelcalender($data, &$errormessage)
	{
		$coursedata = array();
			
			$this->db->select('es.schedule_id AS id, e.exam_name AS exam_name, es.exam_duration AS duration,es.fee AS fee,c.course_name,es.exam_mode,
								e.exam_id, es.start_time, es.end_time, es.exam_date,sg.subject_group_name
								',false);
			$this->db->from('exam_schedule AS es');
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
			//$this->db->join('subject_group_sub AS sgs', 'sgs.sub_group_id = sg.subject_group_id', 'inner');
			//$this->db->join('subject AS s', 's.subject_id = sgs.subject_id', 'inner');
			$this->db->join('course AS c', 'c.course_id = e.course_id', 'inner');

			//$this->db->where('es.exam_date >=', date('Y-m-d'));
			$this->db->group_by('es.schedule_id');
			$this->db->order_by('es.exam_date',asc);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	        //print_r($resultdata);
	        $color = [];
	        $color[0] = '#f56954';
	        $color[1] = '#0073b7';
	        $color[2] = '#00a65a';
	        $color[3] = '#d6471b';
	        $color[4] = '#94d11b';
	        $color[5] = '#31e2ad';
	        $color[6] = '##8e098a';
	        $color[7] = '#ff0090';
	        $color[8] = '#f20014';
	        $color[9] = '#00a65a';
	        if(!$resultdata){
				$coursedata = array();
				$errormessage = "Courses are either deleted or not inserted.";
			}
			else{
				for($i=0;$i < count($resultdata);$i++){
					$tempdata = array();
					$tempdata['title'] = $resultdata[$i]['exam_name'];
					$tempdata['start'] = $resultdata[$i]['exam_date'];
					$tempdata['backgroundColor'] = $color[$i];
					$tempdata['borderColor'] = $color[$i];
					array_push($coursedata,$tempdata);
				}
			}
		
		return $coursedata;
	}
	public function updateFbResp($data, &$errormessage)
	{
		$fbid = 0;
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $fbid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $fbid;
		}
		else 
		{
			$this->db->select('stud_email,stud_name');
			$this->db->from('student');
			$this->db->where(array('stud_id' => $data['studid'], 'active' => '1'));
			$query = $this->db->get();
	        $emaildata = $query->row_array();	
	        
			$userdata = array('reply_msg' => $data['resptext'],'fbreply_date' => $data['createddate']);
			$this->db->where(array('feedback_id'=>(int)$data['fbid']));
			$result = $this->db->update('fbdetails', $userdata);
			
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$fbid = $data['fbid'];

				$to = $emaildata['stud_email'];
				$from = 'contact@mockexam.org';
				
				$subject = 'Feedback Response on mockexam';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$message = '<h3>Hello '.$emaildata['stud_name'].', </h3><br>';				
				$message .= '<p>You get Response from mockexam :</p><br>';
				$message .= '<p>Response Details</p>';
				$message .= '<p>Response : '.$data['resptext'].'</p>';
				$message .= '<br />';
				$message .= '<br>Regards,<br>mockexam Team.';
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);
					
				/*if(!$sendmail)
				{
					$fbid = 0;
					$errormessage = "Some unknown error has occurred. Please try again.";
				}*/
			}
			return $fbid;
		}
	}
	
	 
	public function dashCountMaster($data, &$errormessage)
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
			// get upcoming exam count
			$this->db->select('es.schedule_id AS id, e.exam_name AS exam_name, es.exam_duration AS duration, es.fee AS fee,
								e.exam_id, es.start_time, es.end_time, es.exam_date
								',false);
			$this->db->from('exam_schedule AS es');
			$this->db->join('exam AS e', 'e.exam_id = es.exam_id', 'inner');
			$this->db->join('subject_group AS sg', 'sg.subject_group_id = es.sub_group_id', 'inner');
			$this->db->where('es.exam_date >=', $curDate);
			$this->db->group_by('es.schedule_id');
			$this->db->order_by('es.exam_date',asc);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
	        $countdata['examcount'] = count($resultdata);


//print_r($resultdata);


			/*$this->db->select('c.course_id');
			$this->db->from('course AS c');
			$this->db->where(array('c.active' => '1'));
			$this->db->group_by('c.course_id');
			$query = $this->db->get();
	        $resultdata = $query->result_array();*/
	        /*if($resultdata){
				$countdata['coursecount'] = count($resultdata);
			}else{
				$countdata['coursecount'] = 0;
			}*/
			
	        //$this->db->select('count(sbe.*) as masterstudentcount',false);
	        $this->db->select('sbe.stud_course_batch_id,es.schedule_id,es.exam_mode',false);
			$this->db->from('student_buy_exam as sbe');
			$this->db->join('exam_schedule AS es', 'es.schedule_id = sbe.exam_schedule_id', 'inner');
			$this->db->where(array('es.exam_mode' => '0'));//AND 'es.exam_mode' => '2'
			$query = $this->db->get();
	        $bothstud = $query->result_array();
	        $countdata['bothStud'] = count($bothstud);

	        $this->db->select('sbe.stud_course_batch_id,es.schedule_id,es.exam_mode',false);
			$this->db->from('student_buy_exam as sbe');
			$this->db->join('exam_schedule AS es', 'es.schedule_id = sbe.exam_schedule_id', 'inner');
			$this->db->where(array('es.exam_mode' => '0'));//AND 'es.exam_mode' => '2'
			$query = $this->db->get();
	        $onlinestud = $query->result_array();
	        $countdata['onlineStudent'] = count($onlinestud)+$countdata['bothStud'];

	        $this->db->select('sbe.stud_course_batch_id,es.schedule_id,es.exam_mode',false);
			$this->db->from('student_buy_exam as sbe');
			$this->db->join('exam_schedule AS es', 'es.schedule_id = sbe.exam_schedule_id', 'inner');
			$this->db->where(array('es.exam_mode' => '0'));//AND 'es.exam_mode' => '2'
			$query = $this->db->get();
	        $offlineStud = $query->result_array();
	        $countdata['offlineStudent'] = count($offlineStud)+$countdata['bothStud'];
	      
	       
	      	$this->db->select('c.course_id');
			$this->db->from('course AS c');
			$this->db->where(array('c.active' => '1'));
			$this->db->group_by('c.course_id');
			$query = $this->db->get();
	        $courseresultdata = $query->result_array();
	    //    print_r($courseresultdata);
	        if($courseresultdata){
				$countdata['coursecount'] = count($courseresultdata);
			}else{
				$countdata['coursecount'] = 0;
			}
			
		}				
		return $countdata;
	}
	
	public function courseDashMaster($data, &$errormessage)
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
			$this->db->from('course AS c');
			$this->db->join('course_category AS cat','cat.category_id = c.category_id','inner');
			$this->db->where(array('c.active' => '1'));
			$this->db->order_by('c.course_id','desc');
			$this->db->limit($data['limit'], 0);
			$query = $this->db->get();
	        $resultdata = $query->result_array();
			if($resultdata)
			{
				$coursedata = $resultdata;
			}
			else
			{
				$errormessage = "This courses is either deleted or not found.";
			}
		}				
		return $coursedata;
	}
	
	
	
	
	public function studentRegMonth($data, &$errormessage)
	{		
		$studdata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $studdata;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $studdata;
		}
		else
		{
			$year = $data['year'];
			$startday = 1;
			$endday = 31;
			$portalcount = array();
			$instcount = array();
			for($i=1;$i<=12;$i++){
				$startdate = $year."-".$i."-".$startday;
				$enddate = $year."-".$i."-".$endday;
				
				$this->db->select('count(*) as masterstudentcount',false);
				$this->db->from('student');
				$where = "date(submitdate) between '".$startdate."' and '".$enddate."' ";
				$this->db->where($where);

				$query = $this->db->get();
		        $resultdata = $query->row_array();
		        if($resultdata){
					$portalcount[] = (int)$resultdata['masterstudentcount'];
				}else{
					$portalcount[] = (int)0;
				}
				
				$this->db->select('count(*) as inststudentcount',false);
				$this->db->from('student');
				$where = "date(submitdate) between '".$startdate."' and '".$enddate."' ";
				$this->db->where($where);
				
				$query = $this->db->get();
		        $resultdata = $query->row_array();
		        if($resultdata){
					$instcount[] = (int)$resultdata['inststudentcount'];
				}else{
					$instcount[] = (int)0;
				}
			}
			$studdata['portalstudent'] = $portalcount;
			$studdata['inststudent'] = $instcount;
		}	
		return $studdata;
	}
	
	
	
	
}	
/* End of file Master_model.php */

/* Location: ./application/models/Master_model.php */
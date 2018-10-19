<?php

require "Go_model.php";
class Question_model extends Go_model 
{ 
	function __construct() 
	{ 
		//Call the Model constructor 
		parent::__construct(); 
	}
	
	public function createQuestion($data, &$errormessage)
	{
		

		$questionid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questionid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $questionid;
		}
		else
		{
			$topicid = 0;
			if($data['topicid'] != null && $data['topicid'] != '')
			$topicid = $data['topicid'];
			if(isset($data['paragraph_id']) && $data['paragraph_id'] > 0){
				$paraid = (int)$data['paragraph_id'];
				$questype = '1';
			}else{
				$paraid = 0;
				$questype = '0';
			}
			
			/*$result = $this->db->query("call insertQuestion(".(int)$data['chapterid'].",
													'".addslashes($data['text'])."',
													'".$data['createddate']."',
													'".(int)$data['courseid']."',
													'".$data['is_final']."',
													'".$data['sequence']."',
													'".$paraid.",'".$questype."',
													
													'".(int)$data['question_mark']."',
													'".(int)$data['negative_mark']."',
													'".(int)$data['userid']."',
													@id)");*/
			//print_r($result);
		/*	SELECT `ques_id`, `author_id`, `chapter_id`, `course_id`, `topic_id`, `is_final`, `ques_text`, `ques_type`, `paraghaph_id`, `is_sequence`, `submitdate`, `active`, `qun_mark`, `qun_neg_mark` FROM `vid_question` WHERE 1

*/				if (!$data['is_final']) {
					$data['is_final'] = 0;
				}
			$insdata = array('ques_text' => $data['text'],
							'chapter_id'=>$data['chapterid'],
							'course_id'=>$data['courseid'],
							'submitdate'=>$data['createddate'],
							/*'is_sequence' => $data['sequence'],*/
							'is_final'=> $data['is_final'],
							'qun_mark'=>$data['question_mark'],
							'active' => '1',
							'qun_neg_mark'=>$data['negative_mark']);
			$result = $this->db->insert('question',$insdata);
			if($result)
			{
				//$query = $this->db->query("SELECT @id");
				//$result = $query->row_array();
				//$questionid = $result['@id'];
				$questionid = $this->db->insert_id();
				
					$cnt = 1;
					for($i = 0;$i < count($data['options']);$i++){
						$text = $data['options'][$i];
						if($text != '' || $data['optionsimg'][$i] == 1){
							$result = $this->db->query("call insertOption(".(int)$questionid.",'".addslashes($text)."',@id)");
							if($cnt == (int)$data['correct_opt']){
									if($result){
										$query = $this->db->query("SELECT @id");
										$result = $query->row_array();
										$optionid = $result['@id'];
										$result = $this->db->delete('question_correct_answer',array('ques_id'=>$questionid));
										$insdata = array('ques_id' => $questionid,'option_id' => $optionid,'ans_explanation' => $data['explanation']);
										$result = $this->db->insert('question_correct_answer',$insdata);
									}
								}
							}
						$cnt++;
					}
			}
			else{
					$errormessage = "Some unknown error has occurred. Please try again.";
			}
		}
		
		return $questionid;
	}
	
/*	public function createParaghaph($data, &$errormessage){
		$paraid = 0;
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0){
			$errormessage = Go_model::$loggedinerror;
			return $paraid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid']){
			$errormessage = Go_model::$usermismatcherror;
			return $paraid;
		}
		else{
			$topicid = 0;
			if($data['topicid'] != null)
			$topicid = $data['topicid'];
			
			$paraArr = array('author_id' => (int)$data['userid'],'user_type' => (int)$data['usertype'],'chapter_id' => (int)$data['chapterid'],'course_id' => (int)$data['courseid'],'topic_id' => $topicid,'para_text' => $data['paragraph_text'],'submitdate' => $data['createddate'], 'active' => '1');
			$result = $this->db->insert('question_paragraph', $paraArr);
			
			if($result){
				$paraid = $this->db->insert_id();
			}
			else{
					$errormessage = "Some unknown error has occurred. Please try again.";
			}
		}
		
		return $paraid;
	}*/
	
	public function getQuestionDetails($data, &$errormessage)
	{
		$questiondata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questiondata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $questiondata;
		}
		else
		{
			
			$this->db->select('count(ques_id) as totalcount');
			if($data['topicid'] != null)
			$this->db->where(array('topic_id' => $data['topicid']));
			if($data['searchtext'] != null)
			{
				$this->db->like('ques_text',$data['searchtext']);
			}	
			$query = $this->db->get_where('question',array('chapter_id' => $data['chapterid'],'active' => '1'));
		    $result = $query->row_array();
			if($result){
				$questiondata['totalcount'] = $result['totalcount'];
				$this->db->select('q.ques_id as id,q.ques_text as text,q.ques_type as questype,q.paraghaph_id as paraid,q.is_final,q.qun_neg_mark,q.qun_mark');
				if($data['searchtext'] != null)
				{
					$this->db->like('q.ques_text',$data['searchtext']);
				}
				
				$this->db->from('question as q');
			
				
				if($data['topicid'] != null)
				$this->db->where(array('q.topic_id' => $data['topicid']));
				
				$this->db->order_by("q.ques_id", "desc");
				if($data['start'] != null && $data['limit'] != null ){
					$this->db->limit($data['limit'], $data['start']);
				}
				$this->db->where(array('q.chapter_id' => $data['chapterid'],'q.active' => '1'));
		        $query = $this->db->get();
		        $resultdata = $query->result_array();
				
				if(!$resultdata)
				{
					$questiondata = array();
					$errormessage = "Questions are either deleted or not inserted.";
				}
				else{
					$questiondata['question'] = $resultdata;
				}
			}
			else{
				$questiondata = array();
				$errormessage = "Questions are either deleted or not inserted.";
			}
			
		}
				
		return $questiondata;
	}
	
/*	public function getParagraphList($data, &$errormessage){
		$paradata = array();		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0){
			$errormessage = Go_model::$loggedinerror;
			return $paradata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid']){
			$errormessage = Go_model::$usermismatcherror;
			return $paradata;
		}
		else{			
				$this->db->select('para_id as paraid,para_text as paratext,img_path as paraimg');
				$this->db->from('question_paragraph');
				if($data['topicid'] != null)
				$this->db->where(array('topic_id' => $data['topicid']));
				$this->db->order_by("para_id", "desc");
				$this->db->where(array('chapter_id' => $data['chapterid'],'user_type' => $data['usertype'],'active' => '1'));
		        $query = $this->db->get();
		        $resultdata = $query->result_array();
				if(!$resultdata){
					$paradata = array();
					$errormessage = "Paragraphs are either deleted or not inserted.";
				}
				else{
					$paradata = $resultdata;
				}	
		}				
		return $paradata;
	}*/
	public function getCheckIsFinalExamModel($data, &$errormessage){
		$paradata = array();	
		$jsonData =[];	
		$jsonData['qun_status']= 'hide';
				$this->db->select('no_of_ques');
				$this->db->from('exam_chapter_questions');
				$this->db->where(array('chapter_id' => $data['chapterid']));
				$this->db->order_by("ecq_id", "desc");
				$this->db->limit('ecq_id', 1);
		        $query1 = $this->db->get();
		        $resultdata1 = $query1->row_array();
		        if ($resultdata1['no_of_ques'] > 0 ) {
		        	$this->db->select('ques_id,is_final');
					$this->db->from('question');
					$this->db->where(array('chapter_id' => $data['chapterid'],'is_final' => '1','active' => '1'));
			        $query = $this->db->get();
			        $resultdata = $query->result_array();
			        $status_count = count($resultdata);
			       
			        if ($resultdata1['no_of_ques'] > $status_count) {
			        	$jsonData['qunCount']= $resultdata1['no_of_ques'];
			        	$jsonData['qun_status']= 'show';
			        }
			        else{
			        	$jsonData['qunCount']= $resultdata1['no_of_ques'];
			        	$jsonData['qun_status']= 'hide';
			        }

		        }
				
		return $jsonData;
	}
	public function getQuestionByID($data, &$errormessage)
	{
		$questiondata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questiondata;
		}
		/*else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $questiondata;
		}*/
		else
		{
			$this->db->select('ques_id as id,ques_text as text,is_sequence,is_final,qun_mark,qun_neg_mark');
			$query = $this->db->get_where('question',array('ques_id' => (int)$data['id'], 'active' => '1'));
	        $questionsdata = $query->row_array();
	        
	        if($questionsdata){
				$this->db->select('qo.option_id as optionid,qo.option_text as optiontext,qco.ans_explanation as explanation,qco.ques_correct_ans_id as correctid,qco.option_id as correct_opt');
				$this->db->from('question_options AS qo');
				$this->db->join('question_correct_answer AS qco', 'qco.option_id = qo.option_id', 'left');
				$this->db->where(array('qo.ques_id' => (int)$data['id']));
				 $this->db->group_by('qo.option_id'); 
				$query = $this->db->get();
		        $resultdata = $query->result_array();
				if($resultdata)
				{
					$questiondata["id"] = (int)$questionsdata['id'];
					$questiondata["text"] = $questionsdata['text'];
					$questiondata["sequence"] = $questionsdata['is_sequence'];
					$questiondata["is_final"] = $questionsdata['is_final'];
					$questiondata["qunMark"] = $questionsdata['qun_mark'];
					$questiondata["qunNegMark"] = $questionsdata['qun_neg_mark'];
					$questiondata["options"] = $resultdata;
				}
				else
				{
					$errormessage = "This question is either deleted or not found.";
				}
			}
			else
				{
					$errormessage = "This question is either deleted or not found.";
				}
		}
				
		return $questiondata;
	}
	
	/*public function getParagraphById($data, &$errormessage)
	{
		$paradata = array();
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0){
			$errormessage = Go_model::$loggedinerror;
			return $paradata;
		}
		
		else{
			$this->db->select('para_id as id,para_text as text,img_path as imgpath,img_size as imgsize');
			$query = $this->db->get_where('question_paragraph',array('para_id' => (int)$data['id'], 'active' => '1'));
	        $result = $query->row_array();
	        
	        if($result){
				$this->db->select('ques_id as id,ques_text as text');
				$this->db->from('question');
				$this->db->where(array('paraghaph_id' => (int)$result['id']));
				 $this->db->group_by('ques_id'); 
				$query = $this->db->get();
		        $resultdata = $query->result_array();
				if($resultdata){
					$paradata["questions"] = $resultdata;
				}
				else{
					$paradata["questions"] = array();
				}
				$paradata["id"] = (int)$result['id'];
				$paradata["text"] = $result['text'];
				$paradata["imgpath"] = $result['imgpath'];
				$paradata["imgsize"] = $result['imgsize'];				
			}
			else{
				$errormessage = "This paraghaph is either deleted or not found.";
			}
		}				
		return $paradata;
	}*/

	public function getQuestionChapter($data, &$errormessage)
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
			$this->db->select('chapter_id,chapter_name,subject_id');
			if($data['topicid'] != null)
			$this->db->where(array('is_topic' => '1'));
			else
			$this->db->where(array('is_topic' => '0'));
			
			$query = $this->db->get_where('chapter',array('chapter_id' => $data['id'],'active' => '1'));
	        $result = $query->row_array();
			//print_r($result);
			if($result){
				if($data['topicid'] != null)
				{
					$this->db->select('topic_id,topic_name');
					$query = $this->db->get_where('topic',array('topic_id' => $data['topicid'],'chapter_id' => $data['id'], 'active' => '1'));
					$topicdata = $query->row_array();
					if($topicdata){
						$chapterdata["topicid"] = (int)$topicdata['topic_id'];
						$chapterdata["topicname"] = $topicdata['topic_name'];
					}
					else{
						$errormessage = "Topic are either deleted or not inserted.";
						return $chapterdata;
					}
				}
				
				if($result['course_id'] != 0)
				{
					$chapterdata["courseid"] = (int)$result['course_id'];
				}
				else
				if($result['level_id'] != 0)
				{					
					$this->db->select('course_id');
					$query = $this->db->get_where('course_level',array('level_id' => $result['level_id'],'level' => '3', 'active' => '1'));
					$resultdata = $query->row_array();
					$chapterdata["courseid"] = (int)$resultdata['course_id'];
				}
				else
				if($result['subject_id'] != 0)
				{
					$this->db->select('level_id,course_id');
					$query = $this->db->get_where('subject',array('subject_id' => $result['subject_id'], 'active' => '1'));
	        		$resultdata = $query->row_array();
	        		if($resultdata)
	        		{
						if($resultdata['course_id'] != 0)
						{
							$chapterdata["courseid"] = $resultdata['course_id'];
						}
						else{
							$this->db->select('course_id');
							$query = $this->db->get_where('course_level',array('level_id' => (int)$resultdata['level_id']));
		        			$resultch = $query->row_array();
		        			$chapterdata["courseid"] = $resultch['course_id'];
						}
					}
				}
					$chapterdata["id"] = (int)$result['chapter_id'];
					$chapterdata["name"] = $result['chapter_name'];
			}
			else
			{
				$errormessage = "Chapter are either deleted or not inserted.";
			}
		}
				
		return $chapterdata;
	}

	public function updateQuestion($data, &$errormessage)
	{
		
		$questiondata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questiondata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $questiondata;
		}
		else
		{
					
			$upddata = array('ques_text' => $data['text'],'is_sequence' => $data['sequence'],'is_final'=>$data['is_final'],'qun_mark'=>$data['question_mark'],'qun_neg_mark'=>$data['negative_mark']);
			//print_r($upddata);
			$this->db->where(array('ques_id'=> (int)$data['id'],/*'author_id'=> (int)$data['userid'],*/'active' => '1'));
			$result = $this->db->update('question', $upddata);
			
			if($result)
			{
				//if option image was deleted
				if(count($data['option_img_delete_id']) > 0){
					for($i=0;$i < count($data['option_img_delete_id']);$i++){
						$this->db->select('img_path');
						$query = $this->db->get_where('question_options',array('ques_id'=>(int)$data['id'],'option_id' => (int)$data['option_img_delete_id'][$i]));
						$result = $query->row_array();
						if($result){
							unlink($result['img_path']);
							//update option image
							$upddata = array('img_path' => null);
							$this->db->where(array('option_id'=> (int)$data['option_img_delete_id'][$i]));
							$updresult = $this->db->update('question_options', $upddata);	
						}						
					}
				}
			
				
				$result = $this->db->delete('question_options',array('ques_id'=>(int)$data['id']));
				if($result)
				{
					$cnt = 1;
					for($i=0;$i<count($data['options']);$i++){
						$text = $data['options'][$i];
						if($text != '' || $data['optionsimg'][$i] == 1){
							$insdata = array('ques_id' => (int)$data['id'],'option_text' => $text);
							//$index = $cnt-1;
							//if($cnt == $data['optIdArr'][$index])
							if($data['optIdArr'] != null && $data['optIdArr'] != '')
							{
								if(in_array($cnt, $data['optIdArr']))
								{
									$key = array_search($cnt, $data['optIdArr']);
									$insdata['img_path'] = $data['optNameArr'][$key];
								}
							}
							$result = $this->db->insert('question_options',$insdata);
							$optionid = $this->db->insert_id();
							
							if($cnt == $data['correct_opt']){
								if($result){
									
									//if explanation image was deleted
									if((int)$data['expl_img_delete_id'] > 0){
										$this->db->select('img_path');
										$query = $this->db->get_where('question_correct_answer',array('ques_id' => (int)$data['id'],'option_id' => (int)$data['expl_img_delete_id']));
										$result = $query->row_array();
										if($result){
											unlink($result['img_path']);	
											
											//update explanation image
											$upddata = array('img_path' => null);
											$this->db->where(array('option_id'=> (int)$data['expl_img_delete_id']));
											$updresult = $this->db->update('question_correct_answer', $upddata);										
										}
									}
			
									$result = $this->db->delete('question_correct_answer',array('ques_id'=>(int)$data['id']));
									$insdata1 = array('ques_id' => (int)$data['id'],'option_id' => $optionid,'ans_explanation' => $data['explanation']);
									if($data['expimg'] != null){
										$insdata1['img_path'] = $data['expimg'];
									}
									$result = $this->db->insert('question_correct_answer',$insdata1);
								}
							}
						}
						$cnt++;
					}
					if($result){
						$this->db->select('ques_id as id,ques_text as text');
						$query = $this->db->get_where('question',array('ques_id' => (int)$data['id'], 'active' => '1'));
				        $questionsdata = $query->row_array();
				        
				        if($questionsdata){
							$this->db->select('qo.option_id as optionid,qo.option_text as optiontext,qco.ans_explanation as explanation,qco.ques_correct_ans_id as correctid');
							$this->db->from('question_options AS qo');
							$this->db->join('question_correct_answer AS qco', 'qco.option_id = qo.option_id', 'left');
							$this->db->where(array('qo.ques_id' => (int)$data['id']));
							 $this->db->group_by('qo.option_id'); 
							$query = $this->db->get();
					        $resultdata = $query->result_array();
							if($resultdata){
								$questiondata["id"] = (int)$questionsdata['id'];
								$questiondata["text"] = $questionsdata['text'];
								$questiondata["options"] = $resultdata;
							}
							else{
								$errormessage = "This question is either deleted or not found.";
							}
						}
						else{
								$errormessage = "This question is either deleted or not found.";
							}
					}
				}
				else{
					$errormessage = "Some unknown error has occurred. Please try again.";
				}
					
			}
			else{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
		}
		
		return $questiondata;
	}
	public function changeFinalStatusModel($data, &$errormessage){
			
				$upddata = array('is_final' => (int)$data['finalstatus']);
			
			$this->db->where(array('ques_id'=> (int)$data['qunid']));
			$paradata = $this->db->update('question', $upddata);
			//print_r($upddata);
		return $paradata;
	}
	/*public function editParaghraph($data, &$errormessage)
	{
		$paradata = array();
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $paradata;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $paradata;
		}
		else
		{
			//if question image was deleted
			if((int)$data['para_img_delete_id'] > 0){
				$this->db->select('img_path');
				$query = $this->db->get_where('question_paragraph',array('para_id' => (int)$data['id'],'para_id' => (int)$data['para_img_delete_id']));
				$result = $query->row_array();
				if($result){
					unlink($result['img_path']);	
					
					//update question image
					$upddata = array('img_path' => null);
					$this->db->where(array('para_id'=> (int)$data['id']));
					$updresult = $this->db->update('question_paragraph', $upddata);				
				}
			}
			
			$upddata = array('para_text' => $data['paragraph_text']);
			$this->db->where(array('para_id'=> (int)$data['id'],'branch_id'=> (int)$data['userid'],'active' => '1'));
			$result = $this->db->update('question_paragraph', $upddata);
			if($result)
			{			
				$this->db->select('para_id as id,para_text as text,img_path as imgpath,img_size as imgsize');
				$query = $this->db->get_where('question_paragraph',array('para_id' => (int)$data['id'], 'active' => '1'));
	        	$result = $query->row_array();	        
		        if($result){
						$this->db->select('ques_id as id,ques_text as text');
						$this->db->from('question');
						$this->db->where(array('paraghaph_id' => (int)$data['id']));
						 $this->db->group_by('ques_id'); 
						$query = $this->db->get();
				        $resultdata = $query->result_array();
						if($resultdata){
							$paradata["questions"] = $resultdata;
						}
						else{
							$paradata["questions"] = array();
						}
						$paradata["id"] = (int)$result['id'];
						$paradata["text"] = $result['text'];
						$paradata["imgpath"] = $result['imgpath'];
						$paradata["imgsize"] = $result['imgsize'];				
				}
				else{
					$errormessage = "This paraghaph is either deleted or not found.";
				}
			}
			else{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
		}		
		return $paradata;
	}*/

	/*public function uploadQueImg($data, &$errormessage)
	{
		$quesid = 0;
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0){
			$errormessage = Go_model::$loggedinerror;
			return $quesid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid']){
			$errormessage = Go_model::$usermismatcherror;
			return $quesid;
		}
		else{			
			$upddata = array('img_path' => $data['imgpath']);
			$this->db->where(array('ques_id'=> (int)$data['quesid'] ,'inst_id'=> (int)$data['userid'] , 'active' => '1'));
			$result = $this->db->update('question', $upddata);
			if(!$result){
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$quesid = (int)$data['quesid'];
			}
		}
		return $quesid;
	}*/
	
	/*public function uploadExplImg($data, &$errormessage)
	{
		$quesid = 0;		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0){
			$errormessage = Go_model::$loggedinerror;
			return $quesid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid']){
			$errormessage = Go_model::$usermismatcherror;
			return $quesid;
		}
		else{
			$upddata = array('img_path' => $data['imgpath']);
			$this->db->where(array('ques_id'=> (int)$data['quesid']));
			$result = $this->db->update('question_correct_answer', $upddata);
			if(!$result){
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$quesid = (int)$data['quesid'];
			}
		}		
		return $quesid;
	}*/
	/*
	public function uploadParaImg($data, &$errormessage)
	{
		$paraid = 0;
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0){
			$errormessage = Go_model::$loggedinerror;
			return $paraid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid']){
			$errormessage = Go_model::$usermismatcherror;
			return $paraid;
		}
		else{
			$upddata = array('img_path' => $data['imgpath']);
			$this->db->where(array('para_id'=> (int)$data['paraid']));
			$result = $this->db->update('question_paragraph', $upddata);
			if(!$result){
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else{
				$paraid = (int)$data['paraid'];
			}
		}
		
		return $paraid;
	}
*/	
/*	public function uploadOptImg($data, &$errormessage)
	{
		$quesid = 0;
		
		$userdata = $this->GetLoggedinUserData($data['usersessionid']);
		if(count($userdata) <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $quesid;
		}
		else if((int)$userdata['userid'] != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $quesid;
		}
		else
		{
			$upddata = array('img_path' => $data['imgpath']);
			$this->db->where(array('ques_id'=> (int)$data['quesid'] ,'option_id'=> (int)$data['optid']));
			$result = $this->db->update('question_options', $upddata);
			if(!$result)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$quesid = (int)$data['quesid'];
			}
		}
		
		return $quesid;
	}*/
	
	public function getOptionId($data, &$errormessage)
	{
		$quesid = array();
			$this->db->select('qo.option_id as optionid');
			$this->db->from('question_options AS qo');
			$this->db->where(array('qo.ques_id' => (int)$data['quesid']));
			 $this->db->group_by('qo.option_id'); 
			$query = $this->db->get();
			$resultdata = $query->result_array();
			if(!$resultdata)
			{
				$errormessage = "Some unknown error has occurred. Please try again.";
			}
			else
			{
				$quesid = $resultdata;
			}
			
		return $quesid;
	}

	public function deleteQuestionByID($data, &$errormessage)
	{
		$questionid = 0;
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questionid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $questionid;
		}
		else
		{
			//$upddata = array('active'=>'0');
			$query = $this->db->where(array('ques_id' => $data['id']));
			$result = $this->db->delete('question');
			if($result)
			{
				$questionid = (int)$data['id'];	
			}
			else
			{
				$errormessage = "This question is either deleted or not found.";
			}
		}	
		return $questionid;
	}

	public function deleteMultipleQuestion($data, &$errormessage)
	{
		$questionid = 0;
		
		$userid = $this->GetLoggedinUserid($data['usersessionid']);
		if($userid <= 0)
		{
			$errormessage = Go_model::$loggedinerror;
			return $questionid;
		}
		else if($userid != (int)$data['userid'])
		{
			$errormessage = Go_model::$usermismatcherror;
			return $questionid;
		}
		else
		{
			$result = false;
			foreach($data['ids'] as $id){
				//$upddata = array('active'=>'0');
				$query = $this->db->where(array('ques_id' => $id));
				$result = $this->db->delete('question');
			}
			if($result)
			{
				$questionid = $data['ids'];	
			}
			else
			{
				$errormessage = "This question is either deleted or not found.";
			}
		}
				
		return $questionid;
	}
		
}

/* End of file Question_model.php */

/* Location: ./application/models/Question_model.php */
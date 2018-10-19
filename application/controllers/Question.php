<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Question extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->library('excel');
	    $this->load->model('Question_model');
	}
	
	public function _remap($method, $param)
	{
		$type = strtolower($_SERVER['REQUEST_METHOD']);
		$method = $method."_".$type;
		if (method_exists($this, $method))
		{
			return $this->$method($param);
		}
		else
		{
			$this->load->view('pagenotfound',null);
		}
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
	
	public function uploadquestion_post()
    {
    	$data = array();
    	$data['userid'] = $this->post('userid');
    	$data['email'] = 'support@mockexam.org';
		$data['usertype'] = $this->post('usertype');
		$data['chapterid'] = $this->post('chapterid');
		$data['topicid'] = $this->post('topicid');
		$data['courseid'] = $this->post('courseid');
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$data['usersessionid'] = PageBase::GetHeader("authcode");
    	
    	if(isset($_FILES['ques_file']['name']))
			{
				$inputFileName = $_FILES['ques_file']['tmp_name'];
				try {
				    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
				    $objPHPExcel = $objReader->load($inputFileName);
				} catch (Exception $e) {
				    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
				    . '": ' . $e->getMessage());
				}

				//  Get worksheet dimensions
				$sheet = $objPHPExcel->getSheet(0);
				$highestRow = $sheet->getHighestRow();
				$highestColumn = $sheet->getHighestColumn();
				
				//  Loop through each row of the worksheet in turn
				$resErr = array();
				$resSuc = array();
				
				for ($row = 2; $row <= $highestRow; $row++)
				{
						//  Read a row of data into an array
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
						
						//print_r($rowData[0]);
						$flag = 0;
						$optionreduce = 0;
					    $continue = TRUE;
					    $columName = array();
					    $isDataAvailable = array();			    
					   // echo "<pre>"; print_r($rowData[0]); exit;
					   	foreach($rowData[0] as $colindex=>$columndata)
					    {
					    	if($colindex == 0 && ($columndata == '' || $columndata == null))
					    	{
								$continue = false;
								$columName[] = "Question";
							}
							if($colindex == 2 && ($columndata == '' || $columndata == null))
					    	{
								$continue = false;
								$columName[] = "Option 1";
							}
							if($colindex == 3 && ($columndata == '' || $columndata == null))
					    	{
								$continue = false;
								$columName[] = "Option 2";
							}
							if($columndata != '' || $columndata != null)
							{
								$isDataAvailable[] = 'yes';
							}
					    }
					  	if($rowData[0][7] != null && $rowData[0][7] != '')
					    {
					    	$colval1 = $rowData[0][7];
					    	if((int)$colval1 > 0){
						    	$colval = $colval1 + 1;
						    	if($rowData[0][$colval] != null || $rowData[0][$colval] != '')
						    	{
									//echo $colval;
									for($i=4;$i < $colval;$i++)
									{
										if($rowData[0][$i] == null || $rowData[0][$i] == '')
						    			{
						    				$optionreduce++;
						    			}
									}
								}
								else{
									$continue = false;
									$columName[] = "Correct Answer Option";
								}
							}
							else{
									$continue = false;
									$columName[] = "Correct answer option enter wrong input. Enter only option id like 1/2/3";
							}
					    }
					    else{
								$continue = false;
								$columName[] = "Correct Answer";
						}
							
					   if($continue)
					   {
					   		$errormessage = '';
					   		$options = array();
					   		$data['text'] = $rowData[0][0];
					   		/*if($rowData[0][2] != null) $data['sequence'] = $rowData[0][2];
					   		else $data['sequence'] = '0';*/
					   		
					   		if($rowData[0][2] != null) $options[] = $rowData[0][2];
					   		if($rowData[0][3] != null) $options[] = $rowData[0][3];
					   		if($rowData[0][4] != null) $options[] = $rowData[0][4];
					   		if($rowData[0][5] != null) $options[] = $rowData[0][5];
					   		if($rowData[0][6] != null) $options[] = $rowData[0][6];
							$data['options'] = $options;
							$data['correct_opt'] = $rowData[0][7];
							$data['explanation'] = $rowData[0][1];
							$data['question_mark'] = $rowData[0][8];
							$data['negative_mark'] = $rowData[0][9];
							$questionid = $this->Question_model->createQuestion($data, $errormessage);
							$valid = ((int)$questionid > 0);
							if(!$valid)
							{
								$inserterrors = array();
								$inserterrors[] = $row;
								$inserterrors[] = $errormessage;
								$resErr[] = $inserterrors;
							}
					   }
						else
						{
							$flag = 1;
							$errors[] = $row;
							//print_r($columName);
							$errors[] = " ".(implode(",",$columName))." column should not be empty";
						}
					
					if($flag == 1)
					{
						// Error Comes
						if(count($errors)>0)
						{
							$resErr[] = $errors;
							$errors = array();
							continue;		
						}				
					}
					else
					{
						$resSuc[] = $row;
					}
					
				}
				
				//if($inserterrors)
				//$resErr[] = array_unique($inserterrors);
				$res = array();
				if($highestRow == 1)
				{
					$res[] = 'Please first add data into downloaded excel file and then upload here';
					
				}
				
				if(count($resErr) > 0)
				{
					$excelArr = array();
					for($i=0;$i< count($resErr);$i++)
					{
						$row = $resErr[$i][0];
						$tempArr = array();
						$tempArr[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
						$tempArr[] = $resErr[$i][1];
						$excelArr[] = $tempArr;
						//print_r($excelArr);
					}
					$this->generateErrorExcel($excelArr,$data['userid'],$data['email']);
					$finalRes['errorexcel'] = "Please check your mail (".$data['email'].") to send the error excel.";
					$finalRes['error'] = count($resErr);
				}
				else{
					$finalRes['error'] = $res;
				}
						
				//print_r($excelArr);
				$finalRes['success'] = count($resSuc);
				header('Content-type: application/json');
				echo json_encode($finalRes); exit;
			}
    }	
    
    public function generateErrorExcel($excelArr,$userid,$email)
    {
    			//$excelArr = $this->post('errorexcel');
    			//$userid = $this->post('userid');
    			//$email = $this->post('email');
    	        $this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Question Bulk Upload');
                
                $this->excel->getActiveSheet()->getStyle('A:A')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('B:B')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('C:C')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('D:D')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('E:E')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('F:F')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('G:G')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('H:H')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('I:I')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('J:J')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                
                
                //set cell A1 content with some text
                $this->excel->getActiveSheet()->setCellValue('A1', 'Question');
                $this->excel->getActiveSheet()->setCellValue('B1', 'Option-1 (correct answer)');
                $this->excel->getActiveSheet()->setCellValue('C1', 'Explanation');
                $this->excel->getActiveSheet()->setCellValue('D1', 'Option-2');
                $this->excel->getActiveSheet()->setCellValue('E1', 'Option-3');
                $this->excel->getActiveSheet()->setCellValue('F1', 'Option-4');
                $this->excel->getActiveSheet()->setCellValue('G1', 'Option-5');
                $this->excel->getActiveSheet()->setCellValue('H1', 'Question Marks');
                $this->excel->getActiveSheet()->setCellValue('I1', 'Question Negative Marks');
                $this->excel->getActiveSheet()->setCellValue('J1', 'Error');
                //merge cell A1 until C1
                
                $count = 2;
                for($i=0;$i < count($excelArr);$i++)
                {
                	$data = $excelArr[$i][0];
					$this->excel->getActiveSheet()->setCellValue('A'.$count, $data[0][0]);
	                $this->excel->getActiveSheet()->setCellValue('B'.$count, $data[0][1]);
	                $this->excel->getActiveSheet()->setCellValue('C'.$count, $data[0][2]);
	                $this->excel->getActiveSheet()->setCellValue('D'.$count, $data[0][3]);
	                $this->excel->getActiveSheet()->setCellValue('E'.$count, $data[0][4]);
	                $this->excel->getActiveSheet()->setCellValue('F'.$count, $data[0][5]);
	                $this->excel->getActiveSheet()->setCellValue('G'.$count, $data[0][6]);
	                $this->excel->getActiveSheet()->setCellValue('H'.$count, $data[0][8]);
	                $this->excel->getActiveSheet()->setCellValue('I'.$count, $data[0][9]);
	                $this->excel->getActiveSheet()->setCellValue('J'.$count, $excelArr[$i][1]);
	                $count++;
				}
				
				
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                
                $filename='docs/'.uniqid().'_error.xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 				
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
               
           		//$objWriter->save('php://output');
           		$objWriter->save($filename);
           		
           		$content = file_get_contents($filename);
			    $content = chunk_split(base64_encode($content));

				$separator = md5(time());
				$eol = PHP_EOL;
    			
    		    $to = $email;
    		     
    		    
    			$uid = md5(uniqid(time()));
				$from = 'contact@mockexam.org';
				
				$subject = 'Question Error Excel';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";				
				$file = str_replace("docs/","",$filename);			    
				
				$body = '<h3>Hello '.$email.', </h3><br>';				
				$body.= '<p>Please check the attached excel file for errors occurred while uploading questions.</p>';
				$body.= '<br>Regards,<br>mockexam Team.';
			
				$message = "--".$uid."\r\n";
				$message .= "Content-type:text/html; charset=utf-8\r\n";
				$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
				$message .= $body."\r\n\r\n";
				$message .= "--".$uid."\r\n";
				$message .= "Content-Type: application/octet-stream; name=\"".$file ."\"\r\n"; 
				$message .= "Content-Transfer-Encoding: base64\r\n";
				$message .= "Content-Disposition: attachment; filename=\"".$file ."\"\r\n\r\n";
				$message .= $content."\r\n\r\n";
				$message .= "--".$uid."--";
				
				$sendmail = $this->sendEmail($to, $subject, $message, $headers);
				if($sendmail)
				{
					unlink($filename);
				}
           		
           		//header("Refresh:0; url=$filename");
				
           		//$objWriter->save($filename);
				//header("Refresh:0; url=$filename");
               //return true;
    }

	public function excel_get()
    {
    	//$instid = $this->get('instid');
		$chapterid = $this->get('chapterid');
    	
                $this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Question Bulk Upload');
                //set cell A1 content with some text
                $this->excel->getActiveSheet()->setCellValue('A1', 'Question');
                $this->excel->getActiveSheet()->setCellValue('B1', 'Explanation');
                $this->excel->getActiveSheet()->setCellValue('C1', 'Option-1');
                $this->excel->getActiveSheet()->setCellValue('D1', 'Option-2');
                $this->excel->getActiveSheet()->setCellValue('E1', 'Option-3');
                $this->excel->getActiveSheet()->setCellValue('F1', 'Option-4');
                $this->excel->getActiveSheet()->setCellValue('G1', 'Option-5');
                $this->excel->getActiveSheet()->setCellValue('H1', 'Correct Answer (option id)');
                $this->excel->getActiveSheet()->setCellValue('I1', 'Question Marks');
                $this->excel->getActiveSheet()->setCellValue('J1', 'Question Negative Marks');
                //merge cell A1 until C1
                $this->excel->getActiveSheet()->getStyle('A:A')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('B:B')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('C:C')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('D:D')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('E:E')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('F:F')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('G:G')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('H:H')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('I:I')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('J:J')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                
                
                $filename='question_bulk_upload.xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 				
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
           		$objWriter->save('php://output');
                // echo json_encode(readfile($filename));
    }
    
	public function create_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		
		$data['chapterid'] = $this->post('chapterid');
		$data['courseid'] = $this->post('courseid');
		$data['text'] = $this->post('text');
		
		$data['options'] = $this->post('options');
		$data['correct_opt'] = $this->post('correctid');
		$data['sequence'] = $this->post('sequence');
		$data['question_mark'] = $this->post('qunMark');
		$data['negative_mark'] = $this->post('qunNegMark');
	
		$errormessage = "";
		$valid = $this->validateCreateQuestion($data, $errormessage);
		$data['is_final'] = $this->post('is_final');
		if ($data['is_final'] == 'true') {
			$data['is_final'] = 1;
		}else{
			$data['is_final'] = 0;
		}
		if($valid)
		{
			$data['topicid'] = $this->post('topicid');
			$data['paragraph_id'] = $this->post('paragraph_id');
			$data['explanation'] = $this->post('explanation');
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");

			$questionid = $this->Question_model->createQuestion($data, $errormessage);
			$valid = ((int)$questionid > 0);	
		}
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $questionid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function createParaghraph_post()
	{
		$data = array();
		$data['userid'] = $this->post('userid');
		//$data['instid'] = $this->post('instid');
		$data['usertype'] = $this->post('usertype');
		$data['chapterid'] = $this->post('chapterid');
		$data['courseid'] = $this->post('courseid');
		$data['paragraph_text'] = $this->post('paragraph_text');
		$errormessage = "";
			$data['topicid'] = $this->post('topicid');			
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			$data['usersessionid'] = PageBase::GetHeader("authcode");
			$paraid = $this->Question_model->createParaghaph($data, $errormessage);
			$valid = ((int)$paraid > 0);	
		
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $paraid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function index_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['userid'] = $this->get('userid');
		$data['usertype'] = $this->get('usertype');
		//$data['instid'] = $this->get('instid');
		$data['chapterid'] = $this->get('chapterid');
		$data['topicid'] = $this->get('topicid');
		$data['searchtext'] = $this->get('searchtext');
		$data['start'] = $this->get('start');
		$data['limit'] = $this->get('limit');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$questiondata = $this->Question_model->getQuestionDetails($data, $errormessage);
		if(isset($questiondata) && count($questiondata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['question'] = $questiondata['question'];
			$json['totalcount'] = (int)$questiondata['totalcount'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getParagraphList_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['userid'] = $this->get('userid');
		$data['usertype'] = $this->get('usertype');
	//	$data['instid'] = $this->get('instid');
		$data['chapterid'] = $this->get('chapterid');
		$data['topicid'] = $this->get('topicid');		
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$paradata = $this->Question_model->getParagraphList($data, $errormessage);
		if(isset($paradata) && count($paradata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['paralist'] = $paradata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function getIsFinalQuestion_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		//$data['userid'] = $this->get('userid');
		$data['chapterid'] = $this->get('chapterid');

		$masterJson['qun_status'] = $this->Question_model->getCheckIsFinalExamModel($data, $errormessage);
		if($masterJson['qun_status'])
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['qunStatus'] = $masterJson['qun_status'];
			$json['qunCount'] = $masterJson['qunCount'];

		}
		else
		{
			$json = array("status"=>0,"message"=>"Max question selected for final exam.");
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function questionById_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		//$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$questionata = $this->Question_model->getQuestionByID($data, $errormessage);
		if(isset($questionata) && count($questionata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($questionata as $key=>$value)
			{
				$json[$key] = $value;
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getParagraphById_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		//$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$questionata = $this->Question_model->getParagraphById($data, $errormessage);
		if(isset($questionata) && count($questionata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($questionata as $key=>$value)
			{
				$json[$key] = $value;
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}

	public function questionchapter_get($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->get('id');
		$data['topicid'] = $this->get('topicid');
	//	$data['instid'] = $this->get('instid');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$chapterdata = $this->Question_model->getQuestionChapter($data, $errormessage);
		if(isset($chapterdata) && count($chapterdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($chapterdata as $key=>$value)
			{
				$json[$key] = $value;
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function update_put($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->put('id');
		$data['userid'] = $this->put('userid');
		
		$data['text'] = $this->put('text');
		$data['options'] = $this->put('options');
		$data['question_mark'] = $this->put('qunMark');
		//$data['optionsimg'] = '';
		$data['correct_opt'] = $this->put('correctid');		
		$data['sequence'] = $this->put('sequence');		
		
				$errormessage = "";
		$valid = $this->validateCreateQuestion($data, $errormessage);
		$data['negative_mark'] = $this->put('qunNegMark');

		$data['is_final'] = $this->put('is_final');
		if ($data['is_final'] == 'true') {
			$data['is_final'] = '1';
		}else{
			$data['is_final'] = '0';
		}
		if($valid)
		{	
			
			$data['optIdArr'] = $this->put('optIdArr');
			$data['optNameArr'] = $this->put('optNameArr');
			$data['option_img_size_temp'] = $this->put('option_img_size_temp');
			$data['expl_img_size_temp'] = $this->put('expl_img_size_temp');
			$data['option_img_delete_id'] = $this->put('option_img_delete_id');
			$data['explanation'] = $this->put('explanation');
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$questiondata = $this->Question_model->updateQuestion($data, $errormessage);
			if(isset($questiondata) && count($questiondata) > 0)
			{
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($questiondata as $key=>$value)
				{
					$json[$key] = $value;
				}
			}
			else
			{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		$json['is_final'] =$this->put('is_final');
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function editParaghraph_put($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->put('id');
		$data['userid'] = $this->put('userid');
		//$data['instid'] = $this->put('instid');
		$data['paragraph_text'] = $this->put('paragraph_text');	

			
		$errormessage = "";
		
			$data['paraimg'] = $this->put('paraimg');
			$data['para_img_delete_id'] = $this->put('para_img_delete_id');
			$data['usersessionid'] = PageBase::GetHeader("authcode");			
			$paradata = $this->Question_model->editParaghraph($data, $errormessage);
			if(isset($paradata) && count($paradata) > 0){
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				foreach($paradata as $key=>$value)
				{
					$json[$key] = $value;
				}
			}
			else{
				$json = array("status"=>0,"message"=>$errormessage);
			}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function updateForFinalQun_put($qunId)
	{
		$data = array();
			$data['qunid'] = $this->put('qunId');	
			$data['finalstatus'] = $this->put('finalstatus');	

			/*if ($data['finalstatus'] == 1) {
				$data['finalstatus'] =0;
			}elseif ($data['finalstatus'] == 0) {
				$data['finalstatus']=1;
			}*/
		$errormessage = "";

			$paradata = $this->Question_model->changeFinalStatusModel($data, $errormessage);
			
			if(isset($paradata) && count($paradata) > 0){
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				
			}
			else{
				$json = array("status"=>0,"message"=>$errormessage);
			}

		header('Content-type: application/json');
		echo json_encode($json);
	}

	public function delete_delete($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['id'] = $this->delete('id');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$questionid = $this->Question_model->deleteQuestionByID($data, $errormessage);
		if((int)$questionid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $questionid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function deleteMultiple_delete($userid)
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		$data['ids'] = $this->delete('ids');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$questionid = $this->Question_model->deleteMultipleQuestion($data, $errormessage);
		if($questionid != 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $questionid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	private function validateCreateQuestion($data, &$errormessage)
	{
		$success = true;
		foreach($data as $key=>$val)
		{
			if($val == null)
			{
				$errormessage = "$key cannot be empty.";
				$success = false;
				return $success;
			}
		}
			if($data['options'][0] == '')
			{
				$errormessage = "Please enter option 1 value ";
				$success = false;
				return $success;
			}
			
			if($data['options'][1] == '')
			{
				$errormessage = "Please enter option 2 value";
				$success = false;
				return $success;
			}
			
			$optid = 0;
			for($i=0 ;$i<count($data['options']);$i++)
			{
				if($data['options'][$i] != null || $data['options'][$i] == 1)
				{
					$optid = $i+1;
				}
			}
			
			for($i=0 ; $i < $optid ; $i++ )
			{
				if($data['options'][$i] == '')
				{
					$i++;
					$errormessage = "Please enter option $i value";
					$success = false;
					return $success;
				}
			}
						
			if($data['correct_opt'] != null && $data['correct_opt'] != '')
			{
				$correctid = (int)$data['correct_opt'] - 1;
				if($data['options'][$correctid] != '')
				{
					if($correctid > 1)
					{
						for($i=2 ; $i < $correctid ; $i++ )
						{
							if($data['options'][$i] == '')
							{
								$i++;
								$errormessage = "Please enter option $i value";
								$success = false;
								return $success;
							}
						}
					}
				}
				else{
					$correctid = $correctid +1;
					$errormessage = "Please enter option $correctid value";
					$success = false;
					return $success;
				}
			}
			else{
				$errormessage = "select correct option.";
				$success = false;
				return $success;
			}
		
		return $success;
	}
	
}

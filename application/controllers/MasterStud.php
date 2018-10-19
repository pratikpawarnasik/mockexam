<?php
defined('BASEPATH') OR exit('No direct script access allowed');


require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
require "PageBase.php";
class MasterStud extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->library('excel');
	    $this->load->model('Master_stud_model');
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
	public function reSendMail_get($userid)
	{	
		$data = array();
		$data['stuId'] = $this->get('id');


		$errormessage = "";
		
		//$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		
		$getstudenta = $this->Master_stud_model-> resendMailMessage($data, $errormessage);
		
		if($getstudenta > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			
		}
		else
		{
			$json = array("status"=> 0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function sendExamAlert_get($userid)
	{	
		$data = array();
		$errormessage = "";
		$getstudenta = $this->Master_stud_model-> examAlert($data, $errormessage);
		if($getstudenta > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
		}
		else
		{
			$json = array("status"=> 0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function studentById_get($userid)
	{
		$data = array();
		$errormessage = "";
		
		$data['userid'] = $this->get('userid');
		$userdata = $this->Master_stud_model->getStudentByID($data, $errormessage);
		if(isset($userdata) && count($userdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($userdata as $key=>$value)
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
	public function getNewStudctl_get()
	{
		$errormessage = "";
		$data = array();
		$data['mailStatus'] = $this->get('mailStatus');
		$data['searchtext'] = $this->get('searchtext');
		$data['searchmail'] = $this->get('searchmail');
		$data['searchmobile'] = $this->get('searchmobile');
		$data['startdate'] = $this->get('startdate');
		$data['enddate'] = $this->get('enddate');

		
		//$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		
		$getstudent = $this->Master_stud_model->getNewStudModel($data, $errormessage);
                	//print_r($getstudent);


		if(isset($getstudent) && count($getstudent) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage, "list"=>$getstudent);
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function getNewStudpayment_get()
	{
		$errormessage = "";
		$data = array();
		$data['mailStatus'] = $this->get('mailStatus');

		$data['searchtext'] = $this->get('searchtext');
		$data['adminCollect'] = $this->get('adminCollect');
		$data['paytmCollect'] = $this->get('paytmCollect');
		$data['startdate'] = $this->get('startdate');
		$data['enddate'] = $this->get('enddate');
		$data['onlineStud'] = $this->get('onlineStud');
		$data['OfflineStud'] = $this->get('OfflineStud');

	//	print_r($data);
		//$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		
		$getstudent = $this->Master_stud_model->getNewPaymentStudModel($data, $errormessage);
                	//print_r($getstudent);


		if(isset($getstudent) && count($getstudent) > 0)
		{
				$sub1_id=0;
	        	for ($qw=0; $qw < count($getstudent); $qw++) { 
		  			$sub1_id =$sub1_id+ $getstudent[$qw]['fees'];

		  		}
			$json = array("status"=>200, "message"=>PageBase::$successmessage, "list"=>$getstudent, "TotalAmt" => $sub1_id);
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function getNewSubscribeList_get()
	{
		$errormessage = "";
		$data = array();
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		
		$getstudent = $this->Master_stud_model->getNewSubscribeList($data, $errormessage);
                	//print_r($getstudent);


		if(isset($getstudent) && count($getstudent) > 0)
		{
				$sub1_id=0;
	        	for ($qw=0; $qw < count($getstudent); $qw++) { 
		  			$sub1_id =$sub1_id+ $getstudent[$qw]['fees'];

		  		}
			$json = array("status"=>200, "message"=>PageBase::$successmessage, "list"=>$getstudent, "TotalAmt" => $sub1_id);
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function gettestimonialList_get()
	{
		$errormessage = "";
		$data = array();
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		
		$getstudent = $this->Master_stud_model->gettestimonialList($data, $errormessage);
                	//print_r($getstudent);


		if(isset($getstudent) && count($getstudent) > 0)
		{
				
			$json = array("status"=>200, "message"=>PageBase::$successmessage, "list"=>$getstudent);
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}public function gettestimonialListForDashboard_get()
	{
		$errormessage = "";
		$data = array();
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		
		$getstudent = $this->Master_stud_model->gettestimonialListForAdmin($data, $errormessage);
                	//print_r($getstudent);


		if(isset($getstudent) && count($getstudent) > 0)
		{
				
			$json = array("status"=>200, "message"=>PageBase::$successmessage, "list"=>$getstudent);
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
		public function testimonialStatusUpdate_put()
	{

		$data = array();
			$data['testId'] = $this->put('testiId');	
			$data['finalstatus'] = $this->put('finalstatus');	

		$errormessage = "";

			$paradata = $this->Master_stud_model->changeStatusTestModel($data, $errormessage);
			
			if(isset($paradata) && count($paradata) > 0){
				$json = array("status"=>200, "message"=>PageBase::$successmessage);
				
			}
			else{
				$json = array("status"=>0,"message"=>'Testimonial status change.');
			}

		header('Content-type: application/json');
		echo json_encode($json);
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

	public function deleteMultiStudent_delete()
	{
		
		
		//$data['userid'] = $userid[0];
		$data['ids'] = $this->delete('ids');
		$data['userid'] = $this->delete('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$questionid = $this->Master_stud_model->deleteMultipleStudentModel($data, $errormessage);
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
	//create new student by admin	
	public function addStudent_post()
	{
		$data = array();
		
		$data["studname"] = $this->post('studname');
		$data["gender"] = $this->post('gender');
		$data["email"] = $this->post('email');
		$data["contact"] = $this->post('contact');
		$data["address"] = $this->post('address');
		$data["mother_name"] = $this->post('mother_name');		
		$data["standard"] = $this->post('standard');
		$data["dob"] = $this->post('dob');

		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);

		$data["college_name"] = $this->post('college_name');
		$data["coordinator"] = $this->post('coordinator');
		$data["how_to_know"] = $this->post('how_to_know');
		$data["country"] = $this->post('country');
		$data["other_country"] = $this->post('other_country');
		$data["district"] = $this->post('district');
		$data["pin"] = $this->post('pin');		
		$data["state"] = $this->post('state');
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		if($valid)
		{
			/*$result = $this->Student_model->checkUsername($data, $errormessage);
			if(isset($result) && $result > 0){*/
				$studId = $this->Master_stud_model->addStudModel($data, $errormessage);
				if($studId > 0){
					$json = array("status"=>200, "message"=>PageBase::$successmessage);
					/*foreach($instdata as $key=>$value){
						$json[$key] = $value;
					}*/
				}
				else{
					$json = array("status"=>0,"message"=>$errormessage);
				}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function updateStudent_put()
	{
		$data = array();
		
		$data["studname"] = $this->put('studname');
		$data["gender"] = $this->put('gender');
		$data["email"] = $this->put('email');
		$data["contact"] = $this->put('contact');
		$data["address"] = $this->put('address');
		$data["mother_name"] = $this->put('mother_name');		
		$data["standard"] = $this->put('standard');
		$data["dob"] = $this->put('dob');

		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);

		$data["college_name"] = $this->put('college_name');
		$data["coordinator"] = $this->put('coordinator');
		$data["how_to_know"] = $this->put('how_to_know');
		$data["country"] = $this->put('country');
		$data["other_country"] = $this->put('other_country');
		$data["district"] = $this->put('district');
		$data["pin"] = $this->put('pin');		
		$data["state"] = $this->put('state');
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		if($valid)
		{
			/*$result = $this->Student_model->checkUsername($data, $errormessage);
			if(isset($result) && $result > 0){*/
				$studId = $this->Master_stud_model->updateStudModel($data, $errormessage);
				if($studId > 0){
					$json = array("status"=>200, "message"=>PageBase::$successmessage);
					/*foreach($instdata as $key=>$value){
						$json[$key] = $value;
					}*/
				}
				else{
					$json = array("status"=>0,"message"=>$errormessage);
				}
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	private function ValidateCreateUser($data, &$errormessage)
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
		return $success;
	}
		
   public function uploadstudent_post()
    {
    	$data = array();
    	/*$course = array();
    	$data['userid'] = $this->post('userid');
    	$data['emailid'] = $this->post('email');
		$data['instid'] = $this->post('instid');
		$data['instname'] = $this->Institute_model->getInstituteName($data, $errormessage);
		$course[] = $this->post('courseid');
		$data['course'] = $course;*/
		//$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['emailid'] = 'support@mockexam.org';
		$data['userid'] = $this->post('userid');
    	$data['email'] = $this->post('email');
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$data['usersessionid'] = PageBase::GetHeader("authcode");
    	
    	if(isset($_FILES['stud_file']['name']))
			{
				$inputFileName = $_FILES['stud_file']['tmp_name'];
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
					    $continue = TRUE;
					    $columName = array();
					    $isDataAvailable = array();			    
					    //echo "<pre>"; print_r($rowData[]); exit;
					   	foreach($rowData[0] as $colindex=>$columndata)
					    {
					    	
					    	if($colindex == 0 && ($columndata == '' || $columndata == null))
					    	{
								$continue = false;
								$columName[] = "Name is required";
							}
							
							if($colindex == 3)
							{
								if($columndata == '' || $columndata == null)
						    	{
									$continue = false;
									$columName[] = "Email is required";
								}
								else{
									if (!filter_var($columndata, FILTER_VALIDATE_EMAIL)) {
									    $continue = false;
										$columName[] = "Enter valid Email";
									}
								}
							}
							if($colindex == 10)
							{
								if($columndata == '' || $columndata == null)
						    	{
									$continue = false;
									$columName[] = "Fees are required";
								}
								else{
									 if(!preg_match('/^[0-9]/',(int)$columndata)) // phone number is invalid
									    {
									      $continue = false;
										  $columName[] = "Enter fees in Rs.";
									    }
								}
							}
							if($colindex == 4)
							{
								if($columndata == '' || $columndata == null)
						    	{
									$continue = false;
									$columName[] = "Contact number is required";
								}
								else{
									 if(!preg_match('/^\d{10}$/',(int)$columndata)) // phone number is invalid
									    {
									      $continue = false;
										  $columName[] = "Enter 10 digits valid contact number";
									    }
								}
							}
							
							if($columndata != '' || $columndata != null)
							{
								$isDataAvailable[] = 'yes';
							}
					    }
					  	$data['list']= $rowData;
					   if($continue)
					   {
					   		$errormessage = '';
					   		$options = array();
					   		
					   		//$data['password'] = PageBase::generatePassword();
					   		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
							
							//$userid = $this->Institute_model->createStudent($data, $errormessage);
							
							$userid = $this->Master_stud_model->createMasterStudentList($data, $errormessage);
							$valid = ((int)$userid > 0);
						
							
					  
							if(!$valid)
							{
								$inserterrors = array();
								$inserterrors[] = $row;
								$inserterrors[] = $errormessage;
								$resErr[] = $inserterrors;
							}
							else{
								$resSuc[] = $row;
							}
					   }
						else
						{
							$flag = 1;
							$errors[] = $row;
							$errors[] = " ".(implode(",",$columName))." ";
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
					}

					//$this->excel_get();
					//$this->generateErrorExcel($excelArr,$data['userid'],$data['emailid']);
					$finalRes['errorexcel'] = "Please check your Mail (".$data['emailid'].") to send the error excel.";
					$finalRes['error'] = count($resErr);
				}
				else{
					$finalRes['emptyerror'] = $res;
					$finalRes['error'] = 0;

				}
						
				
				$finalRes['success'] = count($resSuc);
				//echo "<pre>";
				//print_r($excelArr);
				header('Content-type: application/json');
				echo json_encode($finalRes); exit;
			}
    }
   	    public function generateErrorExcel($excelArr,$userid,$emailid)
    {
    				//echo "<pre>";
					//print_r($excelArr);
    			//$excelArr = $this->get('errorexcel');
    	        $this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Student Bulk List Upload');
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
                $this->excel->getActiveSheet()->getStyle('K:K')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('L:L')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('M:M')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                
                
                
                //set cell A1 content with some text
                $this->excel->getActiveSheet()->setCellValue('A1', 'Student Name');
                $this->excel->getActiveSheet()->setCellValue('B1', 'Gender(M/F)');
                $this->excel->getActiveSheet()->setCellValue('C1', 'Birth Date(yyyy-mm-dd)');
                $this->excel->getActiveSheet()->setCellValue('D1', 'Email Address');
                $this->excel->getActiveSheet()->setCellValue('E1', 'Contact Number');
                $this->excel->getActiveSheet()->setCellValue('F1', 'Mother Name');
                $this->excel->getActiveSheet()->setCellValue('G1', 'Address');
                $this->excel->getActiveSheet()->setCellValue('H1', 'Pin Code');
                $this->excel->getActiveSheet()->setCellValue('I1', 'Standard');
                $this->excel->getActiveSheet()->setCellValue('J1', 'College Name');
                $this->excel->getActiveSheet()->setCellValue('K1', 'Cordinator Name');
                $this->excel->getActiveSheet()->setCellValue('L1', 'Fees(Rs.)');
                $this->excel->getActiveSheet()->setCellValue('M1', 'Course ID');
                $this->excel->getActiveSheet()->setCellValue('N1', 'Schedule ID');
                $this->excel->getActiveSheet()->setCellValue('O1', 'Error');

                //merge cell A1 until C1
                
                $count = 2;
                for($i=0;$i < count($excelArr);$i++)
                {
                	$data = $excelArr[$i][0];
                	//print_r($data);
					$this->excel->getActiveSheet()->setCellValue('A'.$count, $data[0][0]);
	                $this->excel->getActiveSheet()->setCellValue('B'.$count, $data[0][1]);
	                $this->excel->getActiveSheet()->setCellValue('C'.$count, $data[0][2]);
	                $this->excel->getActiveSheet()->setCellValue('D'.$count, $data[0][3]);
	                $this->excel->getActiveSheet()->setCellValue('E'.$count, $data[0][4]);
	                $this->excel->getActiveSheet()->setCellValue('F'.$count, $data[0][5]);
	                $this->excel->getActiveSheet()->setCellValue('G'.$count, $data[0][6]);
	                $this->excel->getActiveSheet()->setCellValue('H'.$count, $data[0][7]);
	                $this->excel->getActiveSheet()->setCellValue('I'.$count, $data[0][8]);
	                $this->excel->getActiveSheet()->setCellValue('J'.$count, $data[0][9]);
	                $this->excel->getActiveSheet()->setCellValue('K'.$count, $data[0][10]);
	                $this->excel->getActiveSheet()->setCellValue('L'.$count, $data[0][11]);
	                $this->excel->getActiveSheet()->setCellValue('M'.$count, $data[0][12]);
	                $this->excel->getActiveSheet()->setCellValue('NN'.$count, $data[0][13]);
	                $this->excel->getActiveSheet()->setCellValue('O'.$count, $excelArr[$i][1]);
	                $count++;
				}
				
				
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
                $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(5);
                $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
                
                //$filename='docs/'.uniqid().'_error.xls'; //save our workbook as this file name
                $filename='upload_error.xls'; //save our workbook as this file name

                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 				
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
           		$objWriter->save('php://output');
           		$objWriter->save($filename);
           		
				//header("Refresh:0; url=$filename");
               //return true;
           		



               $content = file_get_contents($filename);
			    $content = chunk_split(base64_encode($content));

				$separator = md5(time());
				$eol = PHP_EOL;
    			
    			$to = $emailid;
    			$uid = md5(uniqid(time()));
				$from = 'contact@mockexam.org';			
				$subject = 'Student Upload Error Excel';
				
				$headers = "From: " . strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";				
				$file = str_replace("docs/","",$filename);			    
				
				$body = '<h3>Hello '.$emailid.', </h3><br>';				
				$body.= '<p>Please Checked Attechment File for Student upload error.</p>';							
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
    }

	public function excel_get()
    {
    	$data['schedule'] = $this->get('schedule');
    	$data['courseId'] = $this->get('courseId');


		//$chapterid = $this->get('chapterid');
    	
                $this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Student_upload_excel');
                //set cell A1 content with some text
                $this->excel->getActiveSheet()->setCellValue('A1', 'Student Name*');
                $this->excel->getActiveSheet()->setCellValue('B1', 'Gender(M/F)');
                $this->excel->getActiveSheet()->setCellValue('C1', 'Birth Date(yyyy-mm-dd)');
                $this->excel->getActiveSheet()->setCellValue('D1', 'Email Address*');
                $this->excel->getActiveSheet()->setCellValue('E1', 'Contact Number*');
                $this->excel->getActiveSheet()->setCellValue('F1', 'Mother Name');
                $this->excel->getActiveSheet()->setCellValue('G1', 'Address');
                $this->excel->getActiveSheet()->setCellValue('H1', 'Pin Code');
                $this->excel->getActiveSheet()->setCellValue('I1', 'Standard');
                $this->excel->getActiveSheet()->setCellValue('J1', 'College Name');
                $this->excel->getActiveSheet()->setCellValue('K1', 'Cordinator Name');
                $this->excel->getActiveSheet()->setCellValue('L1', 'Fees(Rs.)');
                $this->excel->getActiveSheet()->setCellValue('M1', 'Course ID');
                $this->excel->getActiveSheet()->setCellValue('N1', 'Schedule ID');
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
                $this->excel->getActiveSheet()->getStyle('K:K')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('L:L')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('M:M')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('N:N')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
                // Dummy record add
                $this->excel->getActiveSheet()->setCellValue('A2', 'Demo Student Name');
                $this->excel->getActiveSheet()->setCellValue('B2', 'M / F');
                $this->excel->getActiveSheet()->setCellValue('C2', '1990-01-01');
                $this->excel->getActiveSheet()->setCellValue('D2', 'demo@mail.com');
                $this->excel->getActiveSheet()->setCellValue('E2', '8888888888');
                $this->excel->getActiveSheet()->setCellValue('F2', 'Demo Name');
                $this->excel->getActiveSheet()->setCellValue('G2', 'DemoAddress');
                $this->excel->getActiveSheet()->setCellValue('H2', '666666');
                $this->excel->getActiveSheet()->setCellValue('I2', '12 th');
                $this->excel->getActiveSheet()->setCellValue('J2', 'Demo College Name');
                $this->excel->getActiveSheet()->setCellValue('K2', 'Cordinator Name');
                $this->excel->getActiveSheet()->setCellValue('L2', 'Fees(Rs.)');
                $this->excel->getActiveSheet()->setCellValue('M2', $data['courseId']);
                $this->excel->getActiveSheet()->setCellValue('N2', $data['schedule']);
                

 				$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
               
                
                $filename='student_upload_excel.xls'; //save our workbook as this file name
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
    // student list for Admin
    public function studentListExcel_get()
    {
    	$errormessage = "";
		$data = array();
		$data['mailStatus'] = $this->get('mailStatus');

		$data['searchtext'] = $this->get('searchtext');
		$data['searchmail'] = $this->get('searchmail');
		$data['searchmobile'] = $this->get('searchmobile');
		$data['startdate'] = $this->get('startdate');
		$data['enddate'] = $this->get('enddate');

    	$getstudent = $this->Master_stud_model->getNewStudModel($data, $errormessage);
    	

    			$this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Student List Excel');
                
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
                $this->excel->getActiveSheet()->getStyle('K:K')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('L:L')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('M:M')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                //set cell A1 content with some text
                $this->excel->getActiveSheet()->setCellValue('A1', 'Sr.No.');
                $this->excel->getActiveSheet()->setCellValue('B1', 'Student Name');
                $this->excel->getActiveSheet()->setCellValue('C1', 'Birth date');
                $this->excel->getActiveSheet()->setCellValue('D1', 'Email Address');
                $this->excel->getActiveSheet()->setCellValue('E1', 'Contact Number');
                $this->excel->getActiveSheet()->setCellValue('F1', 'Mother Name');
                $this->excel->getActiveSheet()->setCellValue('G1', 'Address');
                $this->excel->getActiveSheet()->setCellValue('H1', 'Pin Code');
                $this->excel->getActiveSheet()->setCellValue('I1', 'Standard');
                $this->excel->getActiveSheet()->setCellValue('J1', 'College Name');
               

                //merge cell A1 until C1
                
                $count = 2;
                for($i=0;$i < count($getstudent);$i++)
                {
                	$data = $getstudent[$i];
                	
					$this->excel->getActiveSheet()->setCellValue('A'.$count, $count -1);
	                $this->excel->getActiveSheet()->setCellValue('B'.$count, $data['stud_name']);
	                $this->excel->getActiveSheet()->setCellValue('C'.$count, $data['dob']);
	                $this->excel->getActiveSheet()->setCellValue('D'.$count, $data['stud_email']);
	                $this->excel->getActiveSheet()->setCellValue('E'.$count, $data['stud_contact']);
	                $this->excel->getActiveSheet()->setCellValue('F'.$count, $data['mother_name']);
	                $this->excel->getActiveSheet()->setCellValue('G'.$count, $data['address']);
	                $this->excel->getActiveSheet()->setCellValue('H'.$count, $data['pin_code']);
	                $this->excel->getActiveSheet()->setCellValue('I'.$count, $data['standard']);
	                $this->excel->getActiveSheet()->setCellValue('J'.$count, $data['college_name']);
	                
	                $count++;
				}
			
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

                //$filename='docs/'.uniqid().'_error.xls'; //save our workbook as this file name
                $filename='student list.xls'; //save our workbook as this file name

                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 				
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
           		$objWriter->save('php://output');
    }
      public function paymentstudentListExcel_get()
    {
    	$errormessage = "";
		$data = array();
		$data['searchtext'] = $this->get('searchtext');
		$data['adminCollect'] = $this->get('adminCollect');
		$data['paytmCollect'] = $this->get('paytmCollect');
		$data['startdate'] = $this->get('startdate');
		$data['enddate'] = $this->get('enddate');
		$data['onlineStud'] = $this->get('onlineStud');
		$data['OfflineStud'] = $this->get('OfflineStud');

    	$getstudent = $this->Master_stud_model->getNewPaymentStudModel($data, $errormessage);
    	

    			$this->excel->setActiveSheetIndex(0);
                //name the worksheet
                $this->excel->getActiveSheet()->setTitle('Student Payment List Excel');
                
                $this->excel->getActiveSheet()->getStyle('A:A')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('B:B')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('C:C')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('D:D')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('E:E')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('F:F')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('G:G')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('H:H')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getStyle('I:I')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );


                //set cell A1 content with some text
                $this->excel->getActiveSheet()->setCellValue('A1', 'Sr.No.');
                $this->excel->getActiveSheet()->setCellValue('B1', 'Student Name');
                $this->excel->getActiveSheet()->setCellValue('C1', 'Exam Name');
                $this->excel->getActiveSheet()->setCellValue('D1', 'Exam Mode');
                $this->excel->getActiveSheet()->setCellValue('E1', 'Buy Date');
                $this->excel->getActiveSheet()->setCellValue('F1', 'Amount(Rs.)');
               

                //merge cell A1 until C1
                
                $count = 2;
                for($i=0;$i < count($getstudent);$i++)
                {

                	$data = $getstudent[$i];
                	if($data['stud_email']==0){
                		$examMode= 'Online';
                	}else
                	if($data['stud_email']==1){
                		$examMode= 'Offline';
                	}
					$this->excel->getActiveSheet()->setCellValue('A'.$count, $count -1);
	                $this->excel->getActiveSheet()->setCellValue('B'.$count, $data['stud_name']);
	                $this->excel->getActiveSheet()->setCellValue('C'.$count, $data['exam_name']);
	                $this->excel->getActiveSheet()->setCellValue('D'.$count, $examMode);
	                $this->excel->getActiveSheet()->setCellValue('E'.$count, $data['submitdate']);
	                $this->excel->getActiveSheet()->setCellValue('F'.$count, $data['fees']);
	                
	                $count++;
				}
			
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

                //$filename='docs/'.uniqid().'_error.xls'; //save our workbook as this file name
                $filename='student_payment_list.xls'; //save our workbook as this file name

                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 				
                //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
           		$objWriter->save('php://output');
    }
	public function studPdfList_get() 
	{

		$errormessage = "";
		$data = array();
		$data['mailStatus'] = $this->get('mailStatus');
		
		$data['searchtext'] = $this->get('searchtext');
		$data['searchmail'] = $this->get('searchmail');
		$data['searchmobile'] = $this->get('searchmobile');
		$data['startdate'] = $this->get('startdate');
		$data['enddate'] = $this->get('enddate');

		$studentdata = $this->Master_stud_model->getNewStudModel($data, $errormessage);
		//echo "<pre>";
		//print_r($studentdata);

		
		if(isset($studentdata) && count($studentdata) > 0)
		{
				
                $htmltoPDF = '<table width="99%" cellpadding="5" border="1" class="displaytable">
							<thead>
								<tr>
									<td width="10%">Sr.No.</td>
									<td width="30%">Student Name</td>
									<td width="30%">Email Address</td>
									<td width="20%">Contact Number</td>
								</tr>
							</thead>'; 
                
                for($i = 0 ; $i < count($studentdata);$i++){
                	$row = $i + 1 ;
                	$htmltoPDF.="<tr>";
                	$htmltoPDF.="<td>".$row."</td>";
                	$htmltoPDF.="<td>".$studentdata[$i]['stud_name']."</td>";
                	$htmltoPDF.="<td>".$studentdata[$i]['stud_email']."</td>";
                	$htmltoPDF.="<td>".$studentdata[$i]['stud_contact']."</td>";
                	$htmltoPDF.="</tr>";
				}
				
				$htmltoPDF .= "</table>"; 
				//echo $htmltoPDF;
				include APPPATH.'third_party/html2fpdf.php';
				$filename='Student_List.pdf';
				$pdf=new HTML2FPDF();
				$pdf->AddPage("P", "A4");
				$pdf->WriteHTML($htmltoPDF);
				$pdf->Output($filename,'D');
			
		}		
		else{
			$json = array("status"=>0,"message"=>$errormessage);
			header('Content-type: application/json');
			echo json_encode($json);
		}
	}
	public function demostudsendotp_post()

	{
		$data = array();
		$data['name'] = $this->post('name');
		
		$data['contact'] = $this->post('contact');
		$errormessage = "";
		$valid = $this->ValidateCreateUser($data, $errormessage);
		if($valid)
		{
			$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
			
			$otp = $this->Master_stud_model->createDemoStudModelotpsend($data, $errormessage);
			$valid = ((int)$otp > 0);	
		}
		
		if($valid)
		{
			//$message = "We have sent instructions on ".$data['email']." which will help you to generate your password and access your account. Please check your email now.";
			$message = PageBase::$successmessage;
			$json = array("status"=>200, "message"=>$message);
			$json["otp"] = $otp;
			$json["name"] = $data['name'];
			$json["contact"] = $data['contact'];
			
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function allStudentRanksCtr_get()
	{
		$data = array();
		$data['selectExam'] = $this->get('selectExam');
		$data['selectSchedule'] = $this->get('selectSchedule');
		
		$studData = $this->Master_stud_model->getAllStudentRanks($data, $errormessage);
		if(isset($studData) && count($studData) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['studRank'] = $studData;
		}
		else{
			$json = array("status"=>0,"message"=>'Exam report not available.');
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function rankExamList_get()
	{
		$data = array();
		
		$examList = $this->Master_stud_model->getAllExamRanks($data, $errormessage);
		if(isset($examList) && count($examList) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examList'] = $examList;
		}
		else{
			$json = array("status"=>0,"message"=>'Exam report not available.');
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
	public function rankScheduleList_get()
	{
		$data = array();
		$data['selectExam'] = $this->get('selectExam');
		$scheduleList = $this->Master_stud_model->rankScheduleList($data, $errormessage);
		if(isset($scheduleList) && count($scheduleList) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['scheduleList'] = $scheduleList;
		}
		else{
			$json = array("status"=>0,"message"=>'Exam report not available.');
		}
		header('Content-type: application/json');
		echo json_encode($json);
	}
}

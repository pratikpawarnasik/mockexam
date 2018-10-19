<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require "PageBase.php";
require(APPPATH.'libraries/Format.php');
require(APPPATH.'libraries/REST_Controller.php');
class Master extends REST_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->database();
	    $this->load->helper('form');
	    $this->load->library('session');
	    $this->load->library('excel');
	    $this->load->model('Master_model');
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
		
	public function feedback_get()
	{
		$data = array();
		
		//$data['userid'] = $userid[0];
		//$data['instid'] = $this->get('instid');
		$data['userid'] = $this->get('userid');
		$data['usertype'] = $this->get('usertype');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$feeddata = $this->Master_model->getFeedbackDetails($data, $errormessage);
		if(isset($feeddata) && count($feeddata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['feedback'] = $feeddata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function feedbackById_get($userid)
	{
		$data = array();

		$data['id'] = $this->get('id');
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$feedbackdata = $this->Master_model->getFeedbackByID($data, $errormessage);
		if(isset($feedbackdata) && count($feedbackdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			foreach($feedbackdata as $key=>$value)
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
	public function allExamsDataforcalender_get(){
		$data = array();
		
		//$data['usersessionid'] = PageBase::GetHeader("authcode");

		$coursedata = $this->Master_model->getallexamsdatamodelcalender($data, $errormessage);
		if(isset($coursedata) && count($coursedata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examdata'] = $coursedata;
			$object = (object) $json['examdata'];

		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		//print_r($object);
		header('Content-type: application/json');
		echo json_encode($json);
	}
		
	public function updateFbResp_put()
	{
		$data = array();
		$data['userid'] = $this->put('userid');
		$data['instid'] = $this->put('instid');
		$data['name'] = $this->put('name');
		$data['email'] = $this->put('email');
		$data['studid'] = $this->put('studid');
		$data['fbid'] = $this->put('fbid');
		$data['resptext'] = $this->put('resptext');	
		
		$errormessage = "";
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$data['createddate'] = PageBase::GetLocalDate()->format("Y-m-d H:i:s");
		$feedbackid = $this->Master_model->updateFbResp($data, $errormessage);
		$valid = ((int)$feedbackid > 0);	
	
		if($valid)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $feedbackid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	
	
	
	//get top student report final exam
	public function getStudentScore_get($userid)
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['branchid'] = $this->get('instid');
		$data['courseid'] = $this->get('courseid');
		$data['institute'] = $this->get('institute');
		$data['selectinstid'] = $this->get('selectinstid');
		$data['selectbranchid'] = $this->get('selectbranchid');
		$data['order'] = $this->get('order');
		$data['start'] = $this->get('start');
		$data['limit'] = $this->get('limit');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$studentdata = $this->Master_model->getStudentScore($data, $errormessage);
		if(isset($studentdata) && count($studentdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['student'] = $studentdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	
	//download student report in ecxel formate
	public function downloadReportExcel_get($userid)
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['branchid'] = $this->get('instid');
		$data['courseid'] = $this->get('courseid');
		$data['searchtext'] = $this->get('searchtext');
		$data['startdate'] = $this->get('startdate');
		$data['enddate'] = $this->get('enddate');
		
		$studentdata = $this->Master_model->downloadReportExcel($data, $errormessage);
		if(isset($studentdata) && count($studentdata) > 0)
		{
			$this->excel->setActiveSheetIndex(0);
            $this->excel->getActiveSheet()->setTitle('Student Report');
            $this->excel->getActiveSheet()->setCellValue('A1', 'Sr.No.');
            $this->excel->getActiveSheet()->setCellValue('B1', 'Student Name');
            $this->excel->getActiveSheet()->setCellValue('C1', 'Email Address');
            $this->excel->getActiveSheet()->setCellValue('D1', 'Contact Number');
                
                for($i = 0 ; $i < count($studentdata);$i++){
                	$row = $i +2;
				    $this->excel->getActiveSheet()->SetCellValue('A'.$row, $i+1);
				    $this->excel->getActiveSheet()->SetCellValue('B'.$row, $studentdata[$i]['name']);
				    $this->excel->getActiveSheet()->SetCellValue('C'.$row, $studentdata[$i]['email']);
				    $this->excel->getActiveSheet()->SetCellValue('D'.$row, $studentdata[$i]['contact']);
				}
				
			$this->excel->getActiveSheet()->getStyle('A:A')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
            $this->excel->getActiveSheet()->getStyle('B:B')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
            $this->excel->getActiveSheet()->getStyle('C:C')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
            $this->excel->getActiveSheet()->getStyle('D:D')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
                $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
                $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
                $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                
                
                $filename='Student_List.xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
                header('Cache-Control: max-age=0'); //no cache
 				
                //if you want to save it as .XLSX Excel 2007 format
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
                //force user to download the Excel file without writing it to server's HD
           		$objWriter->save('php://output');
                // echo json_encode(readfile($filename));
            $json = array("status"=>200, "message"=>PageBase::$successmessage);
            $json['student'] = $studentdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function getStudentDetail_get($userid)
	{
		$data = array();
		$data['userid'] = $this->get('userid');
		$data['studid'] = $this->get('studid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$studentdata = $this->Master_model->getStudentDetail($data, $errormessage);
		if(isset($studentdata) && count($studentdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['student'] = $studentdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	
	private function validateCreateMaster($data, &$errormessage)
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
	
	/**
	* 
	* @param undefined $data
	* @param undefined $errormessage
	* 
	* @ Dashboard reports
	*/	
	
	public function dashCountMaster_get()
	{
		$data = array();

		$data['userid'] = $this->get('userid');
		//$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$countdata = $this->Master_model->dashCountMaster($data, $errormessage);
		if(isset($countdata) && count($countdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['examcount'] = $countdata['examcount'];
			$json['coursecount'] = $countdata['coursecount'];
			$json['bothStud'] = $countdata['bothStud'];
			$json['onlineStudent'] = $countdata['onlineStudent'];
			$json['offlineStudent'] = $countdata['offlineStudent'];
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function courseDashMaster_get()
	{
		$data = array();

		$data['userid'] = $this->get('userid');
		//$data['instid'] = $this->get('instid');
		$data['limit'] = $this->get('limit');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$coursecount = $this->Master_model->courseDashMaster($data, $errormessage);
		if(isset($coursecount) && count($coursecount) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['course'] = $coursecount;
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	public function scopeDashMaster_get()
	{
		$data = array();

		$data['userid'] = $this->get('userid');
		//$data['instid'] = $this->get('instid');
		$data['limit'] = $this->get('limit');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$scoreData = $this->Master_model->scopeDashMaster($data, $errormessage);
		if(isset($scoreData) && count($scoreData) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['score'] = $scoreData;
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	
	
	public function studentRegMonth_get()
	{
		$data = array();

		$data['userid'] = $this->get('userid');
		$data['instid'] = $this->get('instid');
		$data['year'] = $this->get('year');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$studData = $this->Master_model->studentRegMonth($data, $errormessage);
		if(isset($studData) && count($studData) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['student'] = $studData;
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	

	public function getDoubts_get()
	{
		$data = array();
		
		$data['userid'] = $this->get('userid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$doubtsdata = $this->Master_model->getDoubtsDetails($data, $errormessage);
		if(isset($doubtsdata) && count($doubtsdata) > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['doubtsArr'] = $doubtsdata;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
	// delete exam
	public function solved_delete($userid)
	{
		$data = array();
		
		$data['id'] = $this->delete('id');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$queryid = $this->Master_model->solvedDoubtById($data, $errormessage);
		if((int)$queryid > 0)
		{
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json["id"] = $queryid;
		}
		else
		{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);
	}
/*	public function courseStudentGraph_get()
	{
		$data = array();

		$data['userid'] = $this->get('userid');
		$data['instid'] = $this->get('instid');
		$data['usersessionid'] = PageBase::GetHeader("authcode");
		$courseData = $this->Master_model->courseStudentGraph($data, $errormessage);
		if(isset($courseData) && count($courseData) > 0){
			$json = array("status"=>200, "message"=>PageBase::$successmessage);
			$json['course'] = $courseData;
		}
		else{
			$json = array("status"=>0,"message"=>$errormessage);
		}
		
		header('Content-type: application/json');
		echo json_encode($json);

	}*/
	
	
}

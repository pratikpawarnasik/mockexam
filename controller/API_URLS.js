//Base URL
var url1 = window.location.href;     // Returns full URL
var array1 = url1.split('.');
var sundomain1 = array1[0].split('//');
//alert(masterUrl);
var server_baseurl = "http://localhost/mockexam/index.php";
//var server_baseurl = "http://siddhiglobal.net/acceptance/mockexam/index.php";

//******************    Student  URl  ************************************//
	var cronJob = server_baseurl+"/MasterStud/sendExamAlert";
	var url_StudentRanks = server_baseurl+"/student/studentRanksCtr?";
	var url_getExamForRankList = server_baseurl+"/MasterStud/rankExamList";
	var url_getScheduleList = server_baseurl+"/MasterStud/rankScheduleList?";
	var url_getStudRankList = server_baseurl+"/MasterStud/allStudentRanksCtr?";
	//get state
	var url_getstate = server_baseurl+"/student/allState";
	//get district
	var url_getdistrict = server_baseurl+"/student/getDistrict?";
	//Student Register API
	var url_studregister = server_baseurl+"/student/create";
	//Student Demo Register API
	var url_studdemosendotp = server_baseurl+"/MasterStud/demostudsendotp";
	
	var url_studdemoregister = server_baseurl+"/student/createDemoStud";
	//reg check OTP
	var url_checkregotp = server_baseurl+"/student/checkotp";
	// student set password
	var url_setpassword = server_baseurl+"/student/setPassword";
	// forgetpassword master student
	var url_forgetpasswordMasterStud = server_baseurl+"/user/forgetPasswordMasterStudent";
	//reg check forget token
	var url_regchecktoken = server_baseurl+"/user/checktoken";
	//reg student/institute forget
	var url_forgetsend = server_baseurl+"/user/forgetpassword";
	//Contact Form
	var url_contactform = server_baseurl+"/user/contactForm";
	//Feedback Form
	var url_feedbackform = server_baseurl+"/user/feedbackForm";
	//Contact Form
	var url_subscribeform = server_baseurl+"/user/subcribeForm";
	//get all feedback
	var url_getfeedback = server_baseurl+"/student/feedback?";

	//get feedback by id
	var url_getfeedbackbyid = server_baseurl+"/student/feedbackById?";
	//get all concern
	var url_getfbconcern = server_baseurl+"/student/concern?";
	//add feedback
	var url_addfeedback = server_baseurl+"/student/feedbackcreate";


	//get student course
	var url_getstudentcourse = server_baseurl+"/student/course?";
	//get student profile by id
	var url_getstudentbyid = server_baseurl+"/student/studentById?";
	//student upload photo
	var url_uploadphoto = server_baseurl+"/student/uploadPhoto";
	// check user type to change password
	var url_getusertype = server_baseurl+"/student/getusertype?";
	//get final test results
	var url_getexamresult = server_baseurl+"/student/getExamResult?";
	//get final test results by id
	var url_getexamresultById = server_baseurl+"/student/getExamResultById?";
	//get single question details
	var url_singleQuesDet = server_baseurl+"/student/getQuestionDetail?";
	// get student course exam result
	var url_getstudcourseresult = server_baseurl+"/student/studCourseExamResult?";
	var url_getcartDetail = server_baseurl+"/student/getcartDetail?";
	var url_testimonialStatusChange = server_baseurl+"/masterStud/testimonialStatusUpdate";
	var url_gettestimonialStatusChange = server_baseurl+"/masterStud/gettestimonialListForDashboard";
	
	//student profile change password
	var url_changepasswordstudent = server_baseurl+"/student/changepasswordprofile";

	//get student exams
	var url_getexamdata = server_baseurl+"/student/examsData?";
	
	var url_getexamdatabyid = server_baseurl+"/student/examsDataById?";
	var url_getallexamdata = server_baseurl+"/student/allExamsDat";
	var url_getallCoursedata = server_baseurl+"/course/courseByIdDashboard?";
	//get merchangt details
	var url_getmerchantdetail = server_baseurl+"/user/MerchantDetail?";
	//create prepair test
	var url_ceatepreparationtest = server_baseurl+"/student/preparationtest?";
	//get demo test question
	var url_getpreparationquestion = server_baseurl+"/student/question?";
	//update prepair test
	var url_updatepreparationtest = server_baseurl+"/student/updatetest";
	//retest prepair test
	var url_retest = server_baseurl+"/student/retest";
	//download student Hall Ticket
	var url_downloadhallticketpdf = server_baseurl+"/student/downloadHallTicketPDF?";
	//create final test
	var url_createfinaltest = server_baseurl+"/student/createFinalTest";
	//create hall ticket
	var url_viewhallticket = server_baseurl+"/student/hallTicket?";
	//get final test details
	var url_getFinalTest = server_baseurl+"/student/getFinalTest?";
	//submit final test details
	var url_submitfinaltest = server_baseurl+"/student/submitFinalTest";
	//submit final test details
	var url_submitdoubt = server_baseurl+"/student/submitdoubtForm";
	//update final test details
	var url_updatefinaltest = server_baseurl+"/student/updateFinalTest";
	//student update
	var url_updatestud = server_baseurl+"/student/updatestud";
	//Student social Register API
 	var url_getsocialuser = server_baseurl+"/student/getsocialuser";
 	//create student using social
	var url_socialcreate = server_baseurl+"/student/socialCreate";



//******************    Student Login URl  ******************************//
	//Login API
	var url_login = server_baseurl+"/user";
	//Master Student Login API
	var url_masterstudentlogin = server_baseurl+"/user/masterStudentLogin";
	// get normal notification count
	var url_getnormalnoti = server_baseurl+"/user/getNormalNoti?";
	// get indivisual notification count
	var url_getindivisualnoti = server_baseurl+"/user/getIndivisualNoti?";
	//change password master
	var url_changepasswordmaster = server_baseurl+"/user/changepassword";
	//user get config values
	var url_getconfigvalue = server_baseurl+"/user/getConfigValue";

	/*//email verification
	var url_emailverify = server_baseurl+"/user/emailverify";*/

	//otp resend password
	var url_otpresend = server_baseurl+"/user/otpresend";

	/*//check username is available or not
	var url_checkusername = server_baseurl+"/user/checkUsername?";*/

	/*// get normal notification count
	var url_getnormalnoti = server_baseurl+"/user/getNormalNoti?";

	// get indivisual notification count
	var url_getindivisualnoti = server_baseurl+"/user/getIndivisualNoti?";

	// pdate read flag notification
	var url_notireadupdate = server_baseurl+"/user/updateNotiReadFlag?";*/





//************************** Master URL ********************************//

	//get all feedback for master
	var url_getfeedbackmaster = server_baseurl+"/master/feedback?";
	//get all feedback for master
	var url_getStudQuery = server_baseurl+"/master/getDoubts?";

	var url_getallexamdatafordashboard = server_baseurl+"/master/allExamsDataforcalender";
	
	//get all feedbackbyid  for master
	var url_getfbmasterbyid = server_baseurl+"/master/feedbackById?";

	//get all feedbackbyid  for master
	var url_updatefbresponce = server_baseurl+"/master/updateFbResp?";


	//get student score by course final test
	var url_getstudentscore = server_baseurl+"/master/getStudentScore?";


	//get student other portals
	var url_getstudentother = server_baseurl+"/master/getStudentOther?";

	
	//get course and brach wise unittest
	var url_getunittestmaster = server_baseurl+"/master/getUnittest?";

	//download student excel report
	var url_downloadreportexcel = server_baseurl+"/master/downloadReportExcel?";

	//download top student final exam excel report
	var url_downloadtopstudentexcel = server_baseurl+"/master/TopStudentExcel?";

	//download top student final exam pdf report
	var url_downloadtopstudentpdf = server_baseurl+"/master/TopStudentPDF?";

	//download top student unit test excel report
	var url_downloadtopstudunittestexcel = server_baseurl+"/master/TopStudentUnitTestExcel?";

	//download top student unit test pdf report
	var url_downloadtopstudunittestpdf = server_baseurl+"/master/TopStudentUnitTestPDF?";

	//download student pdf report
	var url_downloadreportpdf = server_baseurl+"/master/downloadReportPDF?";

	//download other student (institute) excel report
	var url_reportexcelinst = server_baseurl+"/master/reportExcelInst?";

	//download other student (institute) pdf report
	var url_reportpdfinst = server_baseurl+"/master/reportPDFInst?";

	//download institute excel report
	var url_downloadinstexcel = server_baseurl+"/master/instReportExcel?";

	//download institute pdf report
	var url_downloadinstpdf = server_baseurl+"/master/instReportPDF?";

	//institute test uploaded report
	var url_getinsttestupload = server_baseurl+"/master/instTestUpload?";

	//institute test uploaded report
	var url_uploadtestexcel = server_baseurl+"/master/uploadTestExcel?";

	//institute test uploaded report
	var url_uploadtestpdf = server_baseurl+"/master/uploadTestPDF?";


	//get institute branches 
	var url_getinstbranch = server_baseurl+"/master/instituteBranch?";

	// get master dashboard count 
	var url_getdashcountmaster = server_baseurl+"/master/dashCountMaster?";

	// get course for master dashboard
	var url_getcoursedashmaster = server_baseurl+"/master/courseDashMaster?";

	// get top score student for master dashboard
	var url_getscoredashmaster = server_baseurl+"/master/scopeDashMaster?";

	// get latest add 4 institute for master dashboard
	var url_getinstitutedashmaster = server_baseurl+"/master/instituteDashMaster?";

	// get student records month wise
	var url_getstudentrecordmonth = server_baseurl+"/master/studentRegMonth?";

	// get institute records month wise
	var url_getinstrecordmonth = server_baseurl+"/master/instituteRegMonth?";

	// todays availabe student for particular courses
	var url_getcoursestudentgraph = server_baseurl+"/master/courseStudentGraph?";

	//delete single url_solvedoubt
	var url_solvedoubt = server_baseurl+"/master/solved";



//************************** Dashboard URL ***************************//

	//get dashboard count
	var url_getdashcount = server_baseurl+"/dashboard/dashboardCounts?";
	//get course
	var url_getcoursedash = server_baseurl+"/dashboard/course?";
	// insert temp student exam new by pra
	var url_tempstudexam = server_baseurl+"/dashboard/tempStudentExam";

	// remove order
	var url_removeOrder = server_baseurl+"/dashboard/removeOrder";

	//get demo test question
	var url_getdemoquestion = server_baseurl+"/dashboard/question?";

	//get course hirarchy
	var url_getcoursehirarchy = server_baseurl+"/course/getCourseHirarchy?";

	//get course chapters/topic only
	var url_getcoursechaptetopic = server_baseurl+"/course/getCourseAllList?";
	// get order summary
	var url_getordersummary = server_baseurl+"/dashboard/orderSummary?";

//************************** Start Course api urls *********************
	//get course hirarchy
	var url_getcoursesubjecthirarchy = server_baseurl+"/course/courseSubjectHirachy?";

	//get course chapters/topic only
	var url_getcoursechaptetopic = server_baseurl+"/course/getCourseAllList?";
	//add course
	var url_addcourse = server_baseurl+"/course/create";

	//upload profile picture
	var uploadUrl = server_baseurl+"/course/upload";

	//get All courses
	var url_getcourse = server_baseurl+"/course?";
	//get All courses to home page
	var url_getcourseHomepage = server_baseurl+"/course/getAllCourse";

	//get course by id
	var url_getcoursebyid = server_baseurl+"/course/courseById?";

	//update course
	var url_updatecourse = server_baseurl+"/course/update";

	//delete single course
	var url_deletecourse = server_baseurl+"/course/delete";

	//delete multiple course
	var url_deletemultiplecourse = server_baseurl+"/course/deleteMultiple";
	//get All category
	var url_getcoursecategory = server_baseurl+"/course/category?";
	/*--------------------------end course api urls--------------------------------------*/
	//************************** Start Subject group api urls ***********************************
	//get All subject group
	var url_getsubjectgroup = server_baseurl+"/subjectgroup?";
	//get subjectgroup by id
	var url_getsubjectgroupbyid = server_baseurl+"/subjectgroup/subjectGroupById?";
	//add subject group
	var url_addsubjectgroup = server_baseurl+"/subjectgroup/create";
	//update subject group
	var url_updatesubjectgroup = server_baseurl+"/subjectgroup/update";
	//delete single course
	var url_deletesubjectgroup = server_baseurl+"/subjectgroup/delete";
	/*--------------------------end subject group api urls--------------------------------------*/

//************************* category ********************************//
	//add category
	var url_addcategory = server_baseurl+"/category/create";

	//get all category
	var url_getcategory = server_baseurl+"/category?";

	//get category by id
	var url_getcategorybyid = server_baseurl+"/category/categoryById?";

	//update category
	var url_updatecategory = server_baseurl+"/category/update";

	//delete single category
	var url_deletecategory = server_baseurl+"/category/delete";

	//delete multiple category
	var url_deletemultiplecategory = server_baseurl+"/category/deleteMultiple";


//************************* exam **************************************


	//add exam
	var url_addexam = server_baseurl+"/exam/create";
	//add exam
	var url_allExams = server_baseurl+"/exam/allExams?";

	//get all exam by ID
	var url_getexam = server_baseurl+"/exam?";
	//get all exam
	var url_getallexam = server_baseurl+"/exam/allexam?";
	//get all exam course
	var url_getexamcourse = server_baseurl+"/exam/examCourse?";

	//get exam by id
	var url_getexambyid = server_baseurl+"/exam/examById?";

	//update exam
	var url_updateexam = server_baseurl+"/exam/update";

	//delete single exam
	var url_deleteexam = server_baseurl+"/exam/delete";

	//delete multiple exam
	var url_deletemultipleexam = server_baseurl+"/exam/deleteMultiple";

	// get chapter wise paragraph question
	var url_getchaptetopicpara = server_baseurl+"/exam/chapteTopicParagraph?";
	//get subject group
	var url_getsubgroup = server_baseurl+"/exam/subGroup?";

	//get student exams
	var url_getstudentexams = server_baseurl+"/student/exams?";



//************************* chapter ***********************************


	//add chapter
	var url_addchapter = server_baseurl+"/chapter/create";

	//get all chapter
	var url_getchapter = server_baseurl+"/chapter?";

	//get chapter course
	var url_getchaptercourse = server_baseurl+"/chapter/chaptercourse?";

	//get chapter level
	var url_getchapterlevel = server_baseurl+"/chapter/chapterlevel?";

	//get chapter subject
	var url_getchaptersubject = server_baseurl+"/chapter/chapterSubject?";

	//get chapter by id
	var url_getchapterbyid = server_baseurl+"/chapter/chapterById?";

	//update chapter
	var url_updatechapter = server_baseurl+"/chapter/update";

	//delete single chapter
	var url_deletechapter = server_baseurl+"/chapter/delete";

	//delete multiple chapter
	var url_deletemultiplechapter = server_baseurl+"/chapter/deleteMultiple";
	//************************* subject ********************************

	//add subject
	var url_addsubject = server_baseurl+"/subject/create";

	//get all subject
	var url_getsubject = server_baseurl+"/subject?";

	//get subject course
	var url_getsubjectcourse = server_baseurl+"/subject/subjectcourse?";

	//get subject level
	var url_getsubjectlevel = server_baseurl+"/subject/subjectlevel?";

	//get subject by id
	var url_getsubjectbyid = server_baseurl+"/subject/subjectById?";

	//update subject
	var url_updatesubject = server_baseurl+"/subject/update";

	//delete single subject
	var url_deletesubject = server_baseurl+"/subject/delete";

	//delete multiple subject
	var url_deletemultiplesubject = server_baseurl+"/subject/deleteMultiple";

	//************************* question ********************************

	//add paragraph question
	var url_addparaquestion = server_baseurl+"/question/createParaghraph";

	//edit paragraph
	var url_editparaquestion = server_baseurl+"/question/editParaghraph?";
	//edit paragraph
	var url_setForFinal = server_baseurl+"/question/updateForFinalQun?";

	//get Paragraph by id
	var url_getparabyid = server_baseurl+"/question/getParagraphById?";

	//get Paragraph list
	var url_getparalist = server_baseurl+"/question/getParagraphList?";
	//get Paragraph list
	var url_is_final = server_baseurl+"/question/getIsFinalQuestion?";
//************************* Question **********************************

	//add question
	var url_addquestion = server_baseurl+"/question/create";

	//get all question
	var url_getquestion = server_baseurl+"/question?";

	//download question excel formate
	var url_excel = server_baseurl+"/question/excel?";
	//get student
	var url_getmasterstud = server_baseurl+"/MasterStud/getNewStudctl?";
	//get student
	var url_getStudentPayment = server_baseurl+"/MasterStud/getNewStudpayment?";
	//get Subscribe list
	var url_getSubscribeList = server_baseurl+"/MasterStud/getNewSubscribeList";
	//get Subscribe list
	var url_getTestimonialList = server_baseurl+"/MasterStud/gettestimonialList";
	//send mail function 
	var url_resend_mail = server_baseurl+"/MasterStud/reSendMail?";
	//send mail function 
	var url_deletemultipleStudent = server_baseurl+"/masterStud/deleteMultiStudent";

	//download student excel formate
	var url_stud_excel = server_baseurl+"/MasterStud/excel?";
	//download studentListExcel formate
	var url_studentListExcel = server_baseurl+"/MasterStud/studentListExcel?";
	//download payment studentListExcel formate
	var url_paymentstudentListExcel = server_baseurl+"/MasterStud/paymentstudentListExcel?";
	//upload students excel
	var url_uploadstudents = server_baseurl+"/MasterStud/uploadstudent";
	//Add new student
	var url_add_student = server_baseurl+"/MasterStud/addStudent";
	//download other student pdf report
	var url_studlistpdf = server_baseurl+"/MasterStud/studPdfList?";
	//get student profile by id
	var url_getstudentbyidAdmin = server_baseurl+"/MasterStud/studentById?";

	var url_updateStudAdmin = server_baseurl+"/MasterStud/updateStudent";
	//get question chapter
	var url_questionchapter = server_baseurl+"/question/questionchapter?";

	//upload question excel
	var url_uploadquestion = server_baseurl+"/question/uploadquestion";

	//download error excel
	var url_downerrorexcel = server_baseurl+"/question/downloadErrorExcel";

	//get question by id
	var url_getquestionbyid = server_baseurl+"/question/questionById?";

	//update question
	var url_updatequestion = server_baseurl+"/question/update";

	//delete single question
	var url_deletequestion = server_baseurl+"/question/delete";

	//delete multiple question
	var url_deletemultiplequestion = server_baseurl+"/question/deleteMultiple";

//************************* notes ********************************

//add notes
var url_addnotes = server_baseurl+"/notes/create";

//get all notes
var url_getnotes = server_baseurl+"/notes?";

//get notes by id
var url_getnotesbyid = server_baseurl+"/notes/notesById?";

//update notes
var url_updatenotes = server_baseurl+"/notes/update";

//delete single notes
var url_deletenotes = server_baseurl+"/notes/delete";

//delete multiple notes
var url_deletemultiplenotes = server_baseurl+"/notes/deleteMultiple";


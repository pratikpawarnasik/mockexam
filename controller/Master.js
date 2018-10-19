angular.module('ngApp.masterCtrl', [])

.controller('vidMasterCtrl', function($scope,$interval,$cookies,$rootScope,$window,$http,$location){
	$rootScope.loading = false;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.email = $cookies.get('email');
	$scope.type = $cookies.get('type');
	getExam_data();
	$('#quote-carousel').carousel({
			pause: true, 
			interval: 10000,
	});
	
	$scope.coursecount = 0;
	$scope.masterstudentcount = 0;									
	$scope.feedbackArr = [];	
	$scope.scoreArr = [];
	$scope.courseArr = [];

	// generate year array
	$scope.yearArr = [];
	for(var i=0;i<10;i++){
		var current = new Date().getFullYear();
		var year = current - i;
		if(i==0) {
			$scope.graph_1_year = current+ "";
			$scope.graph_2_year = current+ "";
			$scope.graph_3_year = current+ "";
		}
		$scope.yearArr.push(year);
	} 
	
//get QueryList
	$scope.getQueryList = function(){
		//alert('you are in controller');
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#queryListMessage").removeClass('has-success');
		$scope.queryListMessage = '';
		$scope.getmessage = '';
		
		$scope.param = {
			
			'userid' : $scope.userid
		}
		var my_url = url_getStudQuery+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.doubtsArr = response.doubtsArr;
	     		//console.log($scope.doubtsArr);
			}
			else{
				$scope.doubtsArr = [];
				$("#queryListMessage").addClass('has-success');
				$scope.queryListMessage = "No doubts from student.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	//get total count student dashboard
	$scope.getMasterDashCount = function(){
		$scope.param = {
			'userid' : $scope.userid
		}
		var my_url = url_getdashcountmaster+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.examcount = response.examcount;
	     		$scope.coursecount = response.coursecount;
	     		$scope.onlineofflinestudent = response.bothStud;
	     		$scope.onlinestudent = response.onlineStudent;
	     		$scope.offlinestudent = response.offlineStudent;
			}
			else{
				$scope.examcount = 0;
				$scope.coursecount = 0;
	     		$scope.onlineofflinestudent = 0;
	     		$scope.onlinestudent = 0;
	     		$scope.offlinestudent = 0;													
			}
	    }).error(function(error){
	    		$rootScope.loading = false;
	    });
	}

	//get latest 4 courses
	$scope.getCourse = function(){
			$("#getcourse").removeClass('has-error');
			$scope.getcourse = '';
			$scope.param = {
				'userid' : $scope.userid,
				'limit' : 4,
			}
			var my_url = url_getcoursedashmaster+$.param($scope.param);	
			$rootScope.loading = true;
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	$rootScope.loading = false;
		     	if(response.status == 200)
		     	{
		     		$scope.courseArr = response.course;
				}
				else{
					$scope.courseArr = [];	
					$scope.getcourse = response.message;			
				}
		    }).error(function(error){
		    		$rootScope.loading = false;
		    		$("#getcourse").addClass('has-error');
					$scope.getcourse = "Some unknown error has occurred. Please try again.";
		    });
		}
	
//get getNewStud list
	$scope.getNewStud = function(){
		$scope.NewStudList = [];
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		var startDate = new Date($('#startdate').val());
		var endDate = new Date($('#enddate').val());
		if(endDate < startDate)
        {
            alert('End date should be greater than start date');
            return false;
        }
		$scope.param = {
				'searchtext' : $scope.searchtext,
				'mailStatus' : $scope.mailStatus,
				'startdate' : $scope.startdate,
				'searchmail' : $scope.searchmail,
				'searchmobile' : $scope.searchmobile,
				'enddate' : $scope.enddate
		}

		//console.log($scope.param);
		var my_url = url_getmasterstud+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	//console.log(response);
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.NewStudList = response.list;
			}
			else{
				$scope.NewStudList = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Students are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	// send Mail function here



		$('#demo').dcalendarpicker();
		//$('#calendar-demo').dcalendar();
	function getExam_data(){
		
		var my_url = url_getallexamdatafordashboard;
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.calenderData = response.examdata;
		            		loadCalender();
		            		//$scope.calenderData = JSON.stringify(response.examdata);
		            		//console.log($scope.calenderData);
						}
						else{
							$scope.calenderData = [];
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		
		           });
	}
	//get feedback list
	$scope.getFeedback = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
			
		$scope.param = {
			
			'userid' : $scope.userid,
			'usertype' : $scope.type
		}

		var my_url = url_getfeedbackmaster+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.feedbackArr = response.feedback;
			}
			else{
				$scope.feedbackArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Feedback are either deleted or not inserted.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	function loadCalender(){
  		 $('#calendaradmin').fullCalendar({
    	//getExam_data();

    	 height: 400,
	    
	      header: {
	        left: 'prev,next today',
	        center: 'title of exam',
	        right: 'month,agendaWeek,agendaDay'
	      },
	      buttonText: {
	        today: 'Today',
	        month: 'Month',
	        week: 'Week',
	        day: 'Day'
	      },
	      //Random default events
	      events: $scope.calenderData,
	      
	      editable: false,
	      droppable: false, // this allows things to be dropped onto the calendar !!!
	      drop: function (date, allDay) { // this function is called when something is dropped

	        // retrieve the dropped element's stored Event Object
	        var originalEventObject = $(this).data('eventObject');

	        // we need to copy it, so that multiple events don't have a reference to the same object
	        var copiedEventObject = $.extend({}, originalEventObject);

	        // assign it the date that was reported
	        copiedEventObject.start = date;
	        copiedEventObject.allDay = allDay;
	        copiedEventObject.backgroundColor = $(this).css("background-color");
	        copiedEventObject.borderColor = $(this).css("border-color");

	        // render the event on the calendar
	        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
	        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

	        // is the "remove after drop" checkbox checked?
	        if ($('#drop-remove').is(':checked')) {
	          // if so, remove the element from the "Draggable Events" list
	          $(this).remove();
	        }

	      }
		});
  	}	
})
.controller('vidManageFeedbackCtrl', function($scope,$interval,$cookies,$rootScope,$window,$http,$location){
	$rootScope.loading = false;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.email = $cookies.get('email');
	$scope.type = $cookies.get('type');
	
	$rootScope.header_show = $scope.type;
	$scope.feedbackArr = [];
	$scope.responceDet = {};
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
	
	 $scope.viewby = 10;
		  $scope.currentPage = 1;
		  $scope.itemsPerPage = $scope.viewby;
		  $scope.maxSize = 5;
		  $scope.totalcount = 0;
		
		$scope.pageChange = function(currentPage) {
			$scope.currentPage = currentPage;
		};
		
	if($scope.type == null || ($scope.type != 1 && $scope.type != 2 && $scope.type != 4))
	{
		$window.location.href = $rootScope.base_url;
	}
	
	//view feedback model
	$scope.viewModal = function(id)
	{
		$scope.action = "add";
		$scope.formname = "View Feedback";
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = id;		
		$scope.param = {
			'id' : $scope.id,
			'userid' : $scope.userid
		}
		
		var my_url = url_getfbmasterbyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
	              method : 'GET',
	              url : my_url,
	              headers : {'authcode': $scope.authcode}
		  	}).success(function(response){
           		$rootScope.loading = false;
            	if(response.status == 200)
            	{
					$scope.feedbackDet = {};
					$scope.feedbackForm.reset();
					$("#view_feedback").modal('show');
					
            		$scope.feedbackDet.coursename = response.coursename;
            		$scope.feedbackDet.concerntext = response.concerntext;
            		$scope.feedbackDet.feedback = response.feedback;
            		$scope.feedbackDet.reply = response.reply;
            		$scope.feedbackDet.feedbackdate = response.feedbackdate;
            		$scope.feedbackDet.replydate = response.replydate;
				}
				else{
					$("#getmessage").addClass('has-error');
					$scope.getmessage = response.message;
				}
           }).error(function(error){
           		$rootScope.loading = false;
           		$("#getmessage").addClass('has-error');
				$scope.getmessage = "Some unknown error has occurred. Please try again.";
           });			
	}
	  
	//add Responce pop up
	$scope.addResponse = function(fbid,studid)
	{
		$scope.responceDet.fbid = fbid;
		$scope.responceDet.studid = studid;
		$("#add_new_feedback").modal('show');
	}  
	
	//submit Response
	$scope.submitCreateFeedback = function()
	{
		$scope.responceDet.userid = $scope.userid;
		//console.log($scope.responceDet);
		
			$scope.responceDet.instid = $scope.instid;
			$scope.responceDet.name = $scope.name;
			$scope.responceDet.email = $scope.email;
						
			var my_url = url_updatefbresponce;
			$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : my_url,
		              data: $.param($scope.responceDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		alert("Response added successfully.");
		            		$window.location.reload();
						}
						else{
							$("#message").addClass('has-error');
							$scope.message = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#message").addClass('has-error');
						$scope.message = "Some unknown error has occurred. Please try again.";
		           });
	}  
	    
	//get feedback list
	$scope.getFeedback = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		if($scope.type == 2) $scope.usertype = "institute";
		else if($scope.type == 4) $scope.usertype = "branch";
		else $scope.usertype = "master";
			
		$scope.param = {
			
			'userid' : $scope.userid,
			'usertype' : $scope.usertype
		}
		var my_url = url_getfeedbackmaster+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.feedbackArr = response.feedback;
			}
			else{
				$scope.feedbackArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Feedback are either deleted or not inserted.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }	
	
})
.controller('vidCngPwdMasterCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$rootScope.loading = false;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.type = $cookies.get('type');
	
	if($scope.type == null || $scope.type != 1)
	{
		$window.location.href = $rootScope.base_url;
	}
	
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
	
	$scope.submitchangeForm = function()
	{
		$("#changemessage").removeClass('has-success');
		$("#changemessage").removeClass('has-error');
		
		if($scope.changeDet.oldpassword == $scope.changeDet.password){
			$("#changemessage").addClass('has-error');
			$scope.changemessage = "Old password and New password both are same.Please try another.";
			return false;
		}
			$scope.changemessage = "Change password...";
			$scope.changeDet.userid = $scope.userid;
				$http({
		              method : 'PUT',
		              url : url_changepasswordmaster,
		              data: $.param($scope.changeDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	if(response.status == 200)
		            	{
		            		$scope.changeDet = {};
		            		$scope.changeForm.reset();
		            		$("#changemessage").addClass('has-success');
		            		$scope.changemessage = "Password change successfully.";
						}
						else{
							$("#changemessage").addClass('has-error');
							$scope.changemessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$("#changemessage").addClass('has-error');
						$scope.changemessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	//password validation function for check atlist one alphabet,one number,min length 6.
		$scope.passwordValidator = function(password) {
			if (!password) {
				return;
			}
			else if (password.length < 6) {
				return "Password must be at least " + 6 + " characters long";
			}
			else if (password.match(' ')) {
				return "Password don't allow blank space";
			}
			return true;
		};	
		
	 
})


angular.module('ngApp.studentCtrl', [])
.filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
}])
.controller('welcomeStudentJS', function($scope,$state,$cookies,$rootScope,$window,$http,$location,$stateParams){
		$rootScope.loading = false;
		$scope.authcode = $cookies.get('authcode');
		$scope.instid = $cookies.get('instid');
		$scope.userid = $cookies.get('userid');
		$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
		$scope.type = $cookies.get('type');
		//window.history.forward();
		$scope.coursecount = 0;
		$scope.examcount = 0;
		$scope.scoreArr = [];	
		$scope.noteArr = [];
		$scope.examDet = [];
		$scope.examsArr = [];
		$scope.courseArr = [];
		$scope.examDetail =[];
		$scope.selectedExam = [];
		//alert('modal open');
		//$('#exam_buy').show('modal');
		//$("#exam-filter").hide();
		$("#groupDiv").hide();	
		$("#exammsg").removeClass('has-error');
		$scope.exammsg = "";
		//$('#demo').dcalendarpicker();
		$('#calendarasdf').dcalendar();	
		   var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();
     
	$scope.calenderData = [];

		//get exam result
	$scope.getExamResult = function(){
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.message = '';
		var startDate = new Date($('#startdate').val());
		var endDate = new Date($('#enddate').val());
		if(endDate < startDate)
        {
            alert('End date should be greater than start date');
            return false;
        }
		$scope.param = {
			'userid' : $scope.userid,
			'searchtext' : $scope.searchtext,
			'startdate' : $scope.startdate,
			'enddate' : $scope.enddate
		}
		var my_url = url_getexamresult+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.examArr = response.examresult;
			}
			else{
				$scope.examArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Exam result are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
  	function loadCalender(){
  		 $('#calendar').fullCalendar({
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
	      events:$scope.events,
	      
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
   
		$scope.portal = $rootScope.portal;
		$scope.purmodal = $stateParams.purmodal;
		if ($scope.purmodal == 111){
			// toggle selection

			$('#exam_buy').modal({
	       	 show: 'true'
	   		 });
		}
		
		//$scope.portal = 'main';
		//$scope.portal = 'other';
		if ($scope.authcode == null && $scope.type != 3){
			$window.location.href = $rootScope.base_url;
		}
	    var wrapper_ht = $(window).height();
    	$('.wrapper').css('min-height',wrapper_ht-246);
		//log out
	$scope.logout = function(){
		$cookies.remove('userid');
	    $cookies.remove('name');
	    $cookies.remove('email');
	    $cookies.remove('type');
	    $cookies.remove('authcode');
	    $cookies.remove('buycourse');
	    //$state.reload();
	    $window.location.href = $rootScope.logout_url;
	}

	$scope.toggleSelection = function toggleSelection(id) {
	 		var id = parseInt(id);
	 		var idx = $scope.selectedExam.indexOf(id);
		    // is currently selected
		    if (idx > -1) {
		      $scope.selectedExam.splice(idx, 1);
		      //console.log($scope.selection);
		    }
		    // is newly selected
		    else {
		      $scope.selectedExam.push(parseInt(id));
		     /// console.log($scope.selection);
		    }
	};
	$scope.getExam_data = function()
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		
		var my_url = url_getallexamdata;
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.examdataArr = response.examdata;
		            		//console.log($scope.examdataArr);
		            		
						}
						else{
							$scope.examdataArr = [];
							$("#tablemessage").addClass('has-success');
							$scope.tablemessage = "Exams are not available.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	$scope.studentDashboardInformation = function(){

		$rootScope.loading = true;
		/*$scope.examDet.mode= false;	*/
		$scope.examDetail =[];
		$scope.param = {
			'userid' : $scope.userid
		}
		console.log($scope.param);
		var my_url = url_getdashcount+$.param($scope.param);	
		$http({
	       method : 'GET',
	       url : my_url,
		   headers : {'authcode': $scope.authcode}
		   

	    }).success(function(response){
	    	
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.countdata = response.countdata;
			}
			else{
				$scope.courseArr = [];
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	}
	$scope.exams_buy_flash = function(){
		$rootScope.loading = true;
		$scope.examDetail =[];
		var my_url = url_getcoursedash;	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.courseArr = response.course;
			}
			else{
				$scope.courseArr = [];
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	}	
	$scope.getStudentRanks = function(){
		$rootScope.loading = true;
		$scope.examDetail =[];
		$scope.param = {
			'userid' : $scope.userid
		}
		var my_url = url_StudentRanks+$.param($scope.param);	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.studRank = response.studRank;
			}
			else{
				$scope.courseArr = [];
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	}
	$scope.load_sub_group = function(id){
			$scope.examDet.subGroup = false;
			$("#modeId").hide();
			$scope.examDet.mode = false;
			$scope.subGroupDetail = [];	
			$scope.examDetail = [];
			//$("#exam-filter").hide();
			$rootScope.loading = true;	
				$scope.param = {
					'course_id' : id
			}
				//console.log($scope.param);
			var my_url = url_getsubgroup+$.param($scope.param);	
			$http({
		       method : 'GET',
		       url : my_url
		    }).success(function(response){
		    	//console.log('hi, response data of exam is');
		    	$rootScope.loading = false;
		    	if(response.status == 200)
		     	{
		     		$scope.subGroupDetail = response.subGroup;
		     		$("#groupDiv").show();		
		     	}
				else{	
					$("#groupDiv").hide();			
					$("#message").addClass('has-error');
					$scope.message = response.message;
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		    });
	} 
	
	$scope.uncheck_node = function(){
		//alert($scope.examDet.mode);
		$scope.examDet.mode=false;
		$scope.examDet.selectedExam = false;
		$scope.examDetail = [];

		//$("#exam-filter").hide();
		$("#modeId").show();
	}

	$scope.load_exam_data = function(){
				$("#exammsg").removeClass('has-error');
				$scope.exammsg = "";
			$scope.examDetail = [];
			var a='';
			if($scope.examDet.mode == '11'){
               a='0';
			} else if($scope.examDet.mode == '22'){
               a='1';
			}else{
               a='2';
			}
			$scope.param = {
				'group_id' : $scope.examDet.subGroup,
				'mode' : a
			}
			$rootScope.loading = true;	
			var my_url = url_getallexam+$.param($scope.param);	 
			$http({
		       method : 'GET',
		       url : my_url,
		        headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	//console.log(response);
		    	$rootScope.loading = false;
		    	if(response.status == 200)
		     	{
		     		$scope.examDetail = response.exam;
		     		//console.log($scope.examDetail);
		     		//$("#exam-filter").show();
				}
				else{
					//$("#exam-filter").hide();
					$("#exammsg").addClass('has-error');
					 $("#exammsg").css("color", "red");
					$scope.exammsg ='Exam not available';
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#dataNull").addClass('has-error');
				$scope.dataNull = "Some unknown error has occurred. Please try again.";
		    });
	}
	$scope.getStudentRanks();
	$scope.events = [];
	$scope.getExams = function(){
		//alert('getExa.');
		$scope.param = {
			'id' : $scope.userid
		}
		var my_url = url_getexamdata+$.param($scope.param);
		//var my_url = cronJob;

		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	$scope.message = '';
	    	if(response.status == 200)
	     	{	
	     		$scope.examsArr = response.examdata;
	     		$rootScope.loading = false;
	     		$scope.graph_1_data();
	     		
	            angular.forEach($scope.examsArr,function(event,key){
	                //console.log(event);
	                $scope.events.push({
	                    title: event.exam_name,
	                    start: event.exam_date
	                });
	            });
	            //console.log($scope.events);
	     		loadCalender();

			}

			else{
				
				loadCalender();
				$scope.examsArr = [];
				$scope.graph_1_data();
				$(".message").append('<h3 style="text-align:center; font-size: 26px;"><b>You have currently not purchased any exams. &nbsp;&nbsp;</b></h3><h4 style="text-align: center; font-size: 22px;"><p><a href="#/schuduled" ui-sref="root.schuduled" style="color:#4b6d88;">Click here to buy new exam.</a></p></h4>');
				$rootScope.loading = false;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$(".message").append('<h3 style="text-align:center; font-size: 26px;"><b>You have currently not purchased any exams. &nbsp;&nbsp;</b></h3><h4 style="text-align: center; font-size: 22px;"><p><a href="#/schuduled" ui-sref="root.schuduled" style="color:#4b6d88;">Click here to buy new exam.</a></p></h4>');
	    });
	}
	$scope.getMyExamsResult = function(){
		$scope.message = " ";
		//alert('getExa.');
		$scope.param = {
			'id' : $scope.userid
		}
		var my_url = url_getexamdata+$.param($scope.param);

		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	
	    	$scope.message = '';
	    	if(response.status == 200)
	     	{	
	     		$scope.examsArr = response.examdata;

	     		$rootScope.loading = false;
			}
			else{
				$scope.examsArr = [];
				$rootScope.loading = false;
				$("#getExamMessage").addClass('has-error');
				$scope.getExamMessage = response.message;

			}
	    }).error(function(error){
	    	$rootScope.loading = false;
       		$("#getExamMessage").addClass('has-error');
			$scope.getExamMessage = "Some unknown error has occurred. Please try again.";
	    });
	}
								
						

	$scope.submitBuyExaForm = function(){
			$scope.param = {
				'schedule' : $scope.selectedExam,								
				'userid' : $scope.userid
			}
			//console.log($scope.param);
			my_url = url_tempstudexam;
			$rootScope.loading = true;
			$http({
		              method : 'POST',
		              url : my_url,
		              data: $.param($scope.param) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$cookies.remove('buycourse');
		            		$window.location.href = $rootScope.base_url+'payment';
						}
						else{
							alert(response.message);
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#message").addClass('has-error');
						$scope.message = "Some unknown error has occurred. Please try again.";
		           });
		
		
	}

	
	//graph report 1
	$scope.graph_1_data = function(){
		$scope.param = {
					'userid' : $scope.userid,
					'limit' : 6
				}
				//console.log($scope.param);
				var my_url = url_getstudcourseresult+$.param($scope.param);	
				$rootScope.loading = true;
				$http({
			       method : 'GET',
			       url : my_url,
			       headers : {'authcode': $scope.authcode}
			    }).success(function(response){
			    	console.log(response);

			    	//alert('cancel');
			    	$rootScope.loading = false;
			     	if(response.status == 200){
			     		$scope.examResultData = response.examResultData;
			     		graphReport_1(response.studresult);
					}
					else{
						//graphReport_1();
						$scope.studresult = [];
			     		graphReport_1($scope.studresult);

					}
			    }).error(function(error){
			    		$rootScope.loading = false;
			    });
	}

	function graphReport_1(data){
		
	
		Highcharts.chart('graph_1', {
		    chart: {
		        type: 'column'
		    },
		    title: {
		    	text: 'Recent Exam Self Analysis',
		    	//text: 'Exam Report ('+$("#graph_1_year option:selected").text()+')',
		    },
		    subtitle: {
		        text: ''
		    },
		    xAxis: {
		        categories: data.examattemp,
		        crosshair: true
		    },
		    yAxis: {
		        min: 0,
		        allowDecimals: false,
		        title: {
		            text: 'No.Of.Questions'
		        }
		    },
		    tooltip: {
		        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
		        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
		            '<td style="padding:0"><b>{point.y:f} </b></td></tr>',
		        footerFormat: '</table>',
		        shared: true,
		        useHTML: true
		    },
		    plotOptions: {
		        column: {
		            pointPadding: 0.2,
		            borderWidth: 0
		        }
		    },
		   series: [{
		       name: 'Correct Question',
			   data: data.correct
		    }, {
		        name: 'Wrong Question',
			    data: data.wrong
		    }, {
		        name: 'Attempt Question',
			    data: data.attempt
		    }, {
		        name: 'Not Attempt Question',
			    data: data.notattempt
		    }, {
		        name: 'No.Of.Question',
			    data: data.noofquestion
		    }]
		});	
	}	
	
	
})
.controller('vidPrepairTestCtrl', function($scope,$interval,$state,$cookies,$compile,$stateParams,$rootScope,$window,$http,$location){
		$scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rootScope.loading = false;
		$scope.authcode = $cookies.get('authcode');
		$scope.userid = $cookies.get('userid');
		$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 

		$scope.type = $cookies.get('type');
		
		window.history.forward();
	    function noBack() { 
	         window.history.forward(); 
	    }
	    $(this).on("keydown", function(){
		    	return false;
		})
	
		if($scope.type != 3)
		{
			$window.location.href = $rootScope.base_url+'studcourse';
		}
		
		$scope.questionArr = [];
		$scope.optionArr = [];
		$scope.totalItems = 0;
		$scope.queansid = [];
		$scope.queanstext = [];
		$scope.selection = [];
		$scope.questionsortArr = [];
		$scope.singleQuestionDetails = [];
		$scope.attemptArr = [];
		$scope.show = false;
		$scope.next = false;
		$scope.chapter_id = $stateParams.chapter_id;
		$scope.exam_id = $stateParams.exam_id;
		$scope.subName = $stateParams.subName;
		$scope.seloption = [];
		//alert($scope.exam_id);

		if($scope.chapter_id == null)
	    {
			$window.location.href = $rootScope.base_url+'studcourse';
		}
		
$scope.popimglink = null;
//show images on pop in edit question
		$scope.showPopup = function(imgpath){
			if(imgpath != null && imgpath != ''){
				$scope.popimglink = imgpath;
				$("#imagepopupshow").modal('show');
			}
		}

		$scope.insertPrepair = function()
		{
			$scope.param = {
				'chapterid' : $scope.chapter_id,
				'exam_id' : $scope.exam_id,
				'userid': $scope.userid
			}
			$rootScope.loading = true;	
			var my_url = url_ceatepreparationtest+$.param($scope.param);	
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	$rootScope.loading = false;
		    	if(response.status == 200)
		     	{
		     		$scope.chaptername = response.chaptername;
		     		$scope.currentPage = parseInt(response.totalattempt) + 1;
		     		for(i = 0; i < response.totalattempt;i++)
		     		{
						$scope.attemptArr.push(i);
					}
		     		$scope.getQuestion();
				}
				else{
					if(response.notallow == "notallow")
					{
						//alert(response.message);
						$window.location.href = $rootScope.base_url+'studcourse';
					}
					$("#message").addClass('has-error');
					$scope.message = response.message;
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		    });
		}
		
		$scope.getQuestion = function()
		{
			$scope.param = {
				'chapter_id' : $scope.chapter_id,
				'userid':$scope.userid,
				'exam_id':$scope.exam_id
			}
			$rootScope.loading = true;	
			var my_url = url_getpreparationquestion+$.param($scope.param);	
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	$rootScope.loading = false;
		    	if(response.status == 200)
		     	{
		     		$scope.show = true;
		     		//$scope.chaptername = response.chaptername;
		     		$scope.questionArr = response.question;
		     		$scope.totalItems = $scope.questionArr.length;
		     		for(i=0;i<$scope.questionArr.length;i++)
		     		{
		     			
						$scope.optionArr.push($scope.questionArr[i]['optionid']);
					}
				}
				else{
					
					$scope.chaptername = response.chaptername;
					$scope.totalItems = 0;
					$scope.questionArr = [];
					$("#message").addClass('has-error');
					$scope.message = response.message;
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		    });
		 }
			
		  $scope.viewby = 1;
		  $scope.currentPage = 1;
		  $scope.itemsPerPage = $scope.viewby;
		  $scope.maxSize = 5; //Number of pager buttons to show
			
		 $scope.checkoption = function(id,quesid)
		 {
		 	//alert(id);
		 	var idx = $scope.optionArr.indexOf(id);
			    if (idx > -1) {
			      $scope.next = true;
			      //$("#check"+id).css('color','green');
			      $("#check"+id).empty();
			      $("#check"+id).append('<img src="images/green.png" style="height: 17px;width: 17px;padding: 0;">');
			      $("#expl"+quesid).empty();
			      var explanation = '';
			      if($scope.questionArr[idx]['expl'] != null && $scope.questionArr[idx]['explimg'] != null)
			      {
				  	//explanation = '</br></br></br><h4>Explanation:100</h4>';
				  }
				  if($scope.questionArr[idx]['expl'] != null)
			      {
			      	explanation += '</br></br></br><h4>Explanation:</h4>';
			      	explanation += '<p>'+$scope.questionArr[idx]['expl']+'</p>';
			      	
			      	
			      }
			      if($scope.questionArr[idx]['explimg'] != null && $scope.questionArr[idx]['explimg'] != '')
			      {
			      	explanation += '<p><img src="'+$scope.questionArr[idx]['explimg']+'" width="100px" heigth="100px">';
			      	explanation += '<a href="javascript:;" ng-click="showPopup(&#39;'+$scope.questionArr[idx]['explimg']+'&#39;)"><i class="fa fa-expand"></i></a></p>';
			      	
			      }
			      	//$("#expl"+quesid).append(explanation);
			      	var temp = $compile(explanation)($scope);
				 	angular.element(document.getElementById("expl"+quesid)).append(temp);
			    }
			    else {
			    //$("#check"+id).css('color','red');
			      $("#check"+id).empty();
			      $("#check"+id).append('<img src="images/red.png" style="height: 17px;width: 17px;padding: 0;">');
			    }
		 }
		 
		  $scope.selectPageNext = function (pageNo) {
		  	
		  	var cquesid = $scope.questionArr[pageNo-2]['id'];
		  	/*var optionid = $scope.seloption[cquesid];
		  	
		  	if(optionid != null)
		  	{
				var optiontxt = $("#text"+optionid).val();
				$scope.addAns(cquesid,optionid,optiontxt);
			}*/
			$scope.updatePrepair(cquesid,$scope.currentPage);
			$scope.next = false;
		  	$scope.currentPage = pageNo;
		  	$scope.attemptArr.push(pageNo);
		  };
		  
		  $scope.openModel = function(id)
		  {
		  	//alert(id);
		  	$scope.singleQuestionDetails = $scope.questionArr[id];
		  	$("#questionDetail").modal('show');
		  }
		  
		  $scope.updatePrepair = function(quesid,count)
		  {
			  	$scope.param = {
					'chapterid' : $scope.chapter_id,
					
					'userid':$scope.userid,
					'quesid':quesid,
					'count':count
				}
				$rootScope.loading = true;	
				var my_url = url_updatepreparationtest;	
				$http({
			       method : 'PUT',
			       url : my_url,
			       data : $.param($scope.param),
			       headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
			    }).success(function(response){
			    	$rootScope.loading = false;
			    	if(response.status == 200)
			     	{
			     		
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
		  
		  $scope.startRetest = function()
		  {
			  	$scope.param = {
					'chapterid' : $scope.chapter_id,					
					'userid':$scope.userid
				}
				$rootScope.loading = true;	
				var my_url = url_retest;	
				$http({
			       method : 'PUT',
			       url : my_url,
			       data : $.param($scope.param),
			       headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
			    }).success(function(response){
			    	$rootScope.loading = false;
			    	if(response.status == 200)
			     	{
			     		/*$state.reload();*/
			     		$scope.attemptArr = [];
			     		$scope.lastid = response.chaptername;
			     		$scope.currentPage = parseInt(response.totalattempt) + 1;
			     		for(i = 0; i < response.totalattempt;i++)
			     		{
							$scope.attemptArr.push(i);
						}
						$scope.next = false;
						$scope.getQuestion();
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
		  
		  $scope.addAns = function(quesid,optid,opttext)
		  {
			  	var id = quesid;
		 		var idx = $scope.selection.indexOf(id);
			    if (idx > -1) {
			      $scope.queansid.splice(idx, 1);
			      $scope.queanstext.splice(idx, 1);
			      $scope.selection.splice(idx, 1);
			      $scope.queansid.push(optid);
			      $scope.queanstext.push(opttext);
			      $scope.selection.push(id);
			    }
			    else {
			      $scope.queansid.push(optid);
			      $scope.queanstext.push(opttext);
			      $scope.selection.push(id);
			    }
		  		  }
		  
})	
.controller('vidStudentHallTicketCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.type = $cookies.get('type');
	$scope.levelArr = [];
	$scope.mastercourseid = 0;

	$scope.exam_schedule_id = $stateParams.e_data;
	$scope.exam_id = $stateParams.exam_id;
	if($scope.type == null || $scope.type != 3)
	{
		$window.location.href = $rootScope.base_url;
	}
	
	//get level
	$scope.getHallTicketData = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'exam_schedule_id' : $scope.exam_schedule_id,
			'userid':$scope.userid
		}
		var my_url = url_viewhallticket+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	
	    	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{

	     		$scope.hallticketdata = response;
	     		//console.log($scope.hallticketdata);
	     		$scope.examTime = $scope.hallticketdata.start_time;
	     		$scope.examDate = $scope.hallticketdata.exam_date;
	     		$scope.examStatus = $scope.hallticketdata.examStatus;
	     		/*var date1 = new Date($scope.hallticketdata.exam_date);
	     		var date2 = new Date();
	     		if (date1 > date2) {
				    //date1 = date1.setDate(date1.getDate() + 30);startBtn
					$scope.examStartMsg
					alert(date1-date2);
					//startBtn

				  }
				  console.log(date1,date2);*/

			}
			else{
				$scope.levelArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Exam are not available.";
			}
	    }).error(function(error){
	    		$rootScope.loading = false;
	    		$("#getmessage").addClass('has-error');
				$scope.getmessage = "Some unknown error has occurred. Please try again.";
	    });
	 }

		//submit final exam
	$scope.finalExam = function(rollNo)
	{	//$scope.getHallTicketData();
		
		$scope.examStartMessage = [];		
		$scope.examDateMessage = [];
	
		if ($scope.examStatus == 0) {		
			
			if($scope.exam_schedule_id == null)
			{
				alert("This course exam not created yet. please contact to admin");
				return false;
			}
			
			$scope.param = {
					'exam_schedule_id' : $scope.exam_schedule_id,
					'userid':$scope.userid,
					'roll_no' : rollNo
			}

				$rootScope.loading = true;	
				var my_url = url_createfinaltest;	
				$http({
			       method : 'POST',
			       url : my_url,
			       data : $.param($scope.param),
			       headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
			    }).success(function(response){
			    	
			    	$rootScope.loading = false;
			    	if(response.status == 200)
			     	{
			     		$window.location.href = $rootScope.base_url+'finaltest/'+response.exam_schedule_id+"/"+response.student_exam_id+"/"+$scope.exam_id+"/"+response.rollno;
					}
					else{
						$("#message").addClass('has-error');
						$scope.message = response.message;
					}
			    }).error(function(error){
			    	//alert('error');
			    	$rootScope.loading = false;
			    	$("#message").addClass('has-error');
					$scope.message = "Some unknown error has occurred. Please try again.";
			    });
		}
		
		else{
			//alert('show time');
			$scope.examDateMessage = '111';
			$("#show_exam_message").modal('show');

		}
		
	}
	$scope.downloadpdf = function()
	{		
		$scope.param = {
				'exam_schedule_id' : $scope.exam_schedule_id,
				'userid':$scope.userid
			}
			var my_url = url_downloadhallticketpdf+$.param($scope.param);	
			$rootScope.loading = true;	
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	$rootScope.loading = false;
		    	//alert('your response is');
		    	//alert(response);
		    	//window.open(my_url,'_blank' );
		    	if(response.status != 0){
					window.open(my_url,'_blank' );
				}else{
					alert("Records are not available.");
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#getmessage").addClass('has-error');
				$scope.getmessage = "Some unknown error has occurred. Please try again.";
		    });
	}	 	
})


.controller('vidStudentCourseCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$rootScope.loading = true;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.type = $cookies.get('type');
	var sortBy = '>=';
	$scope.message = '';

	if($scope.type == null || $scope.type != 3)
	{
		$window.location.href = $rootScope.base_url;
	}
	$scope.courseArr = [];
	$scope.courseHirarchi = [];
	$scope.doTheBack = function() {
	  window.history.back();
	};
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
	
	//get course
	$scope.getExams = function(){
		$scope.param = {
			'id' : $scope.userid
		}

		var my_url = url_getstudentexams+$.param($scope.param);

		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	//console.log(response);
	    	$scope.message = '';
	    	if(response.status == 200)
	     	{	
	     		$scope.examsArr = response.exams;
	     		$rootScope.loading = false;
			}
			else{
				$scope.examsArr = [];
				$(".message").append('<h3 style="text-align:center; font-size: 26px;"><b>You have currently not purchased any exams. &nbsp;&nbsp;</b></h3>');
				//$(".message").append('<h3 style="text-align:center; font-size: 26px;"><b>You have currently not purchased any exams. &nbsp;&nbsp;</b></h3><h4 style="text-align: center; font-size: 22px;"><p><a href="#/schuduled" ui-sref="root.schuduled" style="color:#4b6d88;" ng-click="goesToPurchase()">Click here to buy new exam.</a></p></h4>');
				$rootScope.loading = false;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$(".message").append('<h3 style="text-align:center; font-size: 26px;"><b>You have currently not purchased any exams. &nbsp;&nbsp;</b></h3><h4 style="text-align: center; font-size: 22px;"><p><a href="#/schuduled" ui-sref="root.schuduled" style="color:#4b6d88;">Click here to buy new exam.</a></p></h4>');
			
	    });
	 }
	 $scope.goesToPurchase = function()
	{
		alert('hhhhh');
	}
	$scope.getUpcomingExam = function()
	{
		sortBy = '>=';
		$scope.getExam_data(sortBy);
	}
	$scope.getPreviousExam = function()
	{	
		sortBy = '<=';
		$scope.getExam_data(sortBy);
	}

	$scope.getExam_data = function(sortBy)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		
		$scope.param = {
			'id' : $scope.userid,
			'sortBy': sortBy
		}
		var my_url = url_getexamdata+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.examdataArr = response.examdata;
		            		
		            		
						}
						else{
							$scope.examdataArr = [];
							$("#tablemessage").addClass('has-success');
							$scope.tablemessage = "Exams not available.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}

	
// get CourseSubjectHirachy
	$scope.getCourseSubjectHirachy = function(id,examid){
		
		//$(".collapse").removeClass('in');
		if($("#collapseOne"+id).hasClass('in'))
		{
			setTimeout(function(){
				$(this).attr('aria-expanded',false);
			});
			return false;
		} else {
			setTimeout(function(){
			   $(this).attr('aria-expanded',true);
			   $('.iscollapsed').attr('aria-expanded',false);
			   $(".collapse").removeClass('in');
			   $("#collapseOne"+id).addClass('in');
			});			
		}
		
		$scope.courseHirarchi = [];
		$scope.param = {
			'schedule_id' : id,
			'exam_id'   : examid,
			'id' : $scope.userid
		}
		//alert(examid);
		var my_url = url_getcoursesubjecthirarchy+$.param($scope.param);
		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	//alert(response);
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.courseHirarchi = response.courseHirarchi;
			}
			else{
				$scope.courseHirarchi = [];
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	}
	
})
.controller('vidCngPwdStudentCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$rootScope.loading = false;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.type = $cookies.get('type');
	$scope.show = true;
	
	if($scope.type == null || $scope.type != 3)
	{
		$window.location.href = $rootScope.base_url;
	}
	
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
	//check user type to password change
	$scope.getUsertype = function(){
		
		$scope.param = {
			'id' : $scope.userid
		}
		var my_url = url_getusertype+$.param($scope.param);
		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		/*$scope.show = false;*/
			}
			else{
				$scope.show = true;
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	 
	$scope.submitchangeForm = function()
	{
		$("#changemessage").removeClass('has-success');
		$("#changemessage").removeClass('has-error');
		$scope.changeDet.oldpassword = $("#oldpass").val();
		if($scope.changeDet.oldpassword == null && $scope.show == true){
			$("#changemessage").addClass('has-error');
			$scope.changemessage = "Please enter old password";
			return false;
		}
		if($scope.changeDet.oldpassword == $scope.changeDet.password && $scope.show == true){
			$("#changemessage").addClass('has-error');
			$scope.changemessage = "Old password and New password both are same.Please try another.";
			return false;
		}
			$scope.changemessage = "Change password...";
			$scope.changeDet.userid = $scope.userid;
			$scope.changeDet.show = $scope.show;
				$http({ 
		              method : 'PUT',
		              url : url_changepasswordstudent,
		              data: $.param($scope.changeDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		           	//alert($scope.changemessage);
		            	if(response.status == 200)
		            	{
		            		$scope.changeDet = {};
		            		$scope.changeForm.reset();
		            		//$scope.getUsertype();
		            		$scope.changemessage = "Password change successfully.";
		            		$("#changemessage").addClass('has-success');
		            		//$window.location.href = $rootScope.base_url+'welcome';
		            		
						}
						else{
							$scope.changemessage = response.message;
							$("#changemessage").addClass('has-error');
						}
		            	//alert($scope.changemessage);
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
.controller('vidStudentProfileCtrl', function($scope,$interval,$state,ServerService,$cookies,$rootScope,$window,$http,$location){
	$rootScope.loading = true;
	$scope.studProfileDet = {};
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.imgpath = $cookies.get('imgpath');
	$scope.instid = $cookies.get('instid');
	$scope.userid = $cookies.get('userid');
	$scope.type = $cookies.get('type');
	$scope.emailFormat = /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
	$scope.valid = false;
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    if($scope.type != 3)
		{
			$window.location.href = $rootScope.base_url;
		}
    /*file upload*/
   
    function readURL(input) {
    var fileTypes = ['jpeg','jpg','png','gif']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if (isSuccess) { 
	            var reader = new FileReader();
	            reader.onload = function (e) {
	                $('#blah').attr('src', e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);
            }
            else{
            	alert("please select only jpg,jpeg,png,gif files");
				$scope.myFile = null;
				document.getElementById('myFile').value = null;
			}
        }
    }
    $("#myFile").change(function(){
        readURL(this);
    });
    
    var formdata = new FormData();
            $scope.getTheFiles = function ($files) {
                angular.forEach($files, function (value, key) {
                    formdata.append(key, value);
                });
    };
    
    $scope.getStudent = function()
    {	
    	$scope.get_state();
    	$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.param = {
				'id' : $scope.instid,
				'userid' : $scope.userid
			}
		var my_url = url_getstudentbyid+$.param($scope.param);		
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.studProfileDet = {};
							$scope.instituteForm.reset();					
		            		
		            		$scope.get_district(response.state);

		            		$scope.studProfileDet = response;
		            		if (response.gender == 'Female') {
		            			$scope.studProfileDet.gender = 2;
		            		}
		            		else{
		            			$scope.studProfileDet.gender = 1;

		            		}
		            		if (response.country == 'Other') {
		            			$scope.studProfileDet.country = 2;
		            		}
		            		else{
		            			$scope.studProfileDet.country = 1;

		            		}
		            		
		            		
		            		var path = response.imgpath;
		            		if(path != null)
		            		{
								$('#blah').attr('src', path);
							}
		            		
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
    

	$scope.get_state = function(){
		$rootScope.loading = true;	
			var my_url = url_getstate;	
			$http({
		       method : 'GET',
		       url : my_url
		    }).success(function(response){
		    	//console.log(response);
		    	$rootScope.loading = false;
		    	if(response.status == 200)
		     	{
		     		$scope.stateDetail = response.stateData;
		     		//$scope.districtDetail = response.district;
		     		//console.log($scope.stateDetail);
		     		//$("#exam-filter").show();
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

		
	$scope.get_district = function(id){
		$scope.examDetail = [];
		$rootScope.loading = true;	
		$scope.param = {
				'state_id' : id
			}
		var my_url = url_getdistrict+$.param($scope.param);	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	//console.log(response);
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.distDetail = response.districtData;
	     		//$scope.districtDetail = response.district;
	     		//console.log($scope.distDetail);
	     		//$("#exam-filter").show();
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
	$scope.cleanError = function()
	{
		$("#usernamemessagestud").removeClass('has-error');
		$("#usernamemessagestud").removeClass('has-success');
		$scope.usernamemessagestud = "";
	}	
		
    $scope.upload = function(){

    	$scope.message = '';
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		if($scope.myFile == null)
			{
				$("#message").addClass('has-error');
				$scope.message = "Please upload photo.";
				return false;
			}
		var fd = new FormData();


       	var file = $scope.myFile;
       	var docid = docid;
        fd.append('userphoto', file);
        fd.append('instid', $scope.instid);
        fd.append('userid', $scope.userid);
       // console.log(fd);
        $rootScope.loading = true;
       	$http.post(url_uploadphoto, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined,'authcode':$scope.authcode,'Process-Data': false}
        })
        .success(function(resourse){
        		$rootScope.loading = false;
				if(resourse.status == 200)
				{   
						$("#message").addClass('has-success');
						$scope.message = 'Student photo updated.';
        		}
        		else
        		{ 
						$("#message").addClass('has-error');	
						$scope.message = resourse.message;
        		}
					            	
        })
        .error(function(error){
        	$rootScope.loading = false;
        	$("#message").addClass('has-error');	
			$scope.message = "Some unknown error has occurred. Please try again.";
        });
	}
	     
	//edit student
	$scope.editStudent = function()
	{
		
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "student update...";
			var birthDate = $("#dob").val();
			$scope.studProfileDet.dob=birthDate;
			
			var my_url = url_updatestud;
			$scope.studProfileDet.branch_id = $scope.instid;
			$scope.studProfileDet.userid = $scope.userid;
			if ($scope.studProfileDet.country == 1) {
				$scope.studProfileDet.country = 'India';
			}
			else{
				$scope.studProfileDet.country = 'Other';				
			}
			$scope.studProfileDet.country
			$rootScope.loading = true;
			$http({
		              method : 'PUT',
		              url : my_url,
		              data: $.param($scope.studProfileDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		if(response.contact_flag == 1){
		            			alert("Profile updated successfully. please login for mobile verification.");
								$cookies.remove('userid');
							    $cookies.remove('name');
							    $cookies.remove('email');
							    $cookies.remove('type');
							    $cookies.remove('authcode');
							    $cookies.remove('buycourse');
							    $window.location.href = $rootScope.logout_url;
							}
		            		$("#message").addClass('has-success');
							$scope.message = "Student profile updated successfully.";
							alert('Student profile updated successfully.');
							$window.location.href = $rootScope.welcome_url;
							
							//$window.location.href = $rootScope.buyexam;


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

		 $("#dob").datetimepicker({format: 'Y-m-d',timepicker:false, maxDate : new Date(),scrollMonth : false});
	
})

.controller('vidFinalTestCtrl', function($scope,$interval,$timeout,$state,$cookies,$stateParams,$rootScope,$window,$http,$location){
		$scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rootScope.loading = false;
		$scope.authcode = $cookies.get('authcode');
		$scope.userid = $cookies.get('userid');
		$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
		$scope.instid = $cookies.get('instid');
		$scope.type = $cookies.get('type');
		
		window.history.forward();
	    function noBack() { 
	         window.history.forward(); 
	    }
    	$("#final_keypress").on("keydown", function(){
		    	return false;
		})
		
		var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
		if($scope.type != 3)
		{
			$window.location.href = $rootScope.base_url+'studcourse';
		}
		
		//question timer	
		  $scope.value = 0;
		  function countdown() {
		    $scope.value++;
		    $scope.timeout = $timeout(countdown, 1000);
		  }
  
		$scope.questionArr = [];
		$scope.totalItems = 0;
		$scope.queansid = [];
		$scope.queanstext = [];
		$scope.selection = [];
		$scope.attemtQue = [];
		$scope.questionTime = [];
		$scope.questionsortArr = [];
		$scope.optionArr = [];
		$scope.attempt = [];
		$scope.exam = true;
		$scope.result = false;
		
		$scope.examid = $stateParams.examid;
		$scope.student_exam_id = $stateParams.student_exam_id;
		$scope.exam_schedule_id = $stateParams.exam_schedule_id;
		$scope.roll_no = $stateParams.rollno;
		$scope.seloption = [];
		//alert($scope.id);
		$("#doubt_btn").show();
		$("#getresemessage").hide();
		if($scope.examid == null || $scope.student_exam_id == null)
	    {
			$window.location.href = $rootScope.base_url+'studcourse';
		}
		
		$scope.popimglink = null;


		$scope.getFinalTest = function()
		{
			$scope.param = {
				'examid' : $scope.examid,
				'userid':$scope.userid,
				'student_exam_id':$scope.student_exam_id,
				'exam_schedule_id':$scope.exam_schedule_id,
				'roll_no' : $scope.roll_no
			}
			$rootScope.loading = true;	
			var my_url = url_getFinalTest+$.param($scope.param);	
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	$rootScope.loading = false;
		    	if(response.status == 200)
		     	{
		     		$scope.show = true;
		     		$scope.examName = response.examDetail['exam_name'];
		     		$scope.questionArr = response.quesDetail;
		     		$scope.timecount = response.examDetail['timecount'];
		     		$scope.totalItems = $scope.questionArr.length;
		     		for(i=0;i<$scope.questionArr.length;i++)
		     		{
						$scope.optionArr.push($scope.questionArr[i]['optionid']);
					}
					$scope.attempt = response.examDetail['attempt']
					var attempttime = 0;
					for(i=0;i<$scope.attempt.length;i++)
					{
						attempttime = attempttime + parseInt($scope.attempt[i]['time_taken']);
						$scope.questionTime.push(parseInt($scope.attempt[i]['time_taken']));
						$scope.attemtQue.push($scope.attempt[i]['question_id']);
						if($scope.attempt[i]['ques_option_id'] != '0')
						{
							$scope.queansid.push($scope.attempt[i]['ques_option_id']);
							$scope.selection.push($scope.attempt[i]['question_id']);
						}
					}
					$scope.config = response.examDetail['exam_duration'];
					
					if($scope.timecount != 0)
					{
						$scope.config = $scope.timecount;
					}
		     		mins = parseInt($scope.config);
					secs = parseInt(mins * 60);
					$timeout(Decrement,1000);
					countdown();
		     		//$scope.getQuestion();
				}
				else{
					if(response.notallow == "notallow")
					{
						alert(response.message);
						$window.location.href = $rootScope.base_url+'studcourse';
					}
					$("#message").addClass('has-error');
					$scope.message = response.message;
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		    });
		}

		var mins = 0;		
		var secs = 0;	
		var endtimetest = '';		
		function Decrement(){
	
			$scope.minutes = getminutes();
			$scope.seconds = getseconds();
		
			secs--;
			if(secs >= 0)
			{
				endtimetest = $timeout(Decrement,1000);
			} 
			else { 
				//setTimeout(function(){alert("time Out")},1000);
				/*alert("Time Out");
				$scope.submitResult();*/
				$("#show_timeout").modal('show');
				$timeout(function(){
					$("#show_timeout").modal('hide');
					$scope.submitResult();
				},2000);
			}
		}
		
		function getminutes() {
			mins = Math.floor(secs / 60);
			return mins;
		}
		
		function getseconds() {
			return secs-Math.round(mins *60);
		}
		 
		  $scope.viewby = 1;
		  $scope.currentPage = 1;
		  $scope.itemsPerPage = $scope.viewby;
		  $scope.maxSize = 5; //Number of pager buttons to show
		$scope.manageQunList = function(masterList){
			//console.log(masterList);
			$scope.masterSub = [];
			var tempSub = '';
			$scope.subMaster = [];
			//$scope.tempSubjects['qun']= [];
			var indexId = 0;
			$scope.tempSubjects = [];
			for(i=0; i< masterList.length; i++){
				tempSub = masterList[i].subject_name;
				

				if ($scope.masterSub[i-1]!== tempSub) {
					//console.log(tempSub);
					$scope.tempSubjects['sub'] = tempSub;
					$scope.tempSubjects['qun'] = 1;
					$scope.subMaster[indexId] = $scope.tempSubjects;
					var indId = 0;
					indexId ++;
				}else{
					//$scope.tempSubjects['qun'] = $scope.tempSubjects['qun']+1;
					//$scope.subMaster[indexId].push($scope.tempSubjects[indId+1]['qun']);
				}
				//console.log(masterList[i].subject_name);
				$scope.masterSub[i] = tempSub;
				
			}
		}
		$scope.newGrouping = function(group_list, group_by, index) {
		  if (index > 0) {
		    prev = index - 1;
		    if (group_list[prev][group_by] !== group_list[index][group_by]) {
		      return true;
		    } else {
		      return false;
		    }
		  } else {
		    return true;
		  }
		  };	
		 $scope.checkoption = function(id,quesid)
		 {
		 	var idx = $scope.optionArr.indexOf(id);
			    if (idx > -1) {
			      $scope.next = true;
			      $("#check"+id).css('color','green');
			      $("#expl"+quesid).empty();
			      var explanation = '<h3>Explanation:</h3>';
			      explanation += '<p>'+$scope.questionArr[idx]['expl']+'</p>';
			      $("#expl"+quesid).append(explanation);
			    }
			    else {
			      $("#check"+id).css('color','red');
			    }
		 }
		 
		  $scope.updateFinalTest = function(quesid,optionid,time) 
		  {
		  	
			  	$scope.param = {
					'student_exam_id' : $scope.student_exam_id,
					'userid':$scope.userid,
					'quesid':quesid,
					'optionid':optionid,
					'time':time
				}
				//alert(time);
				//alert(optionid);
				$rootScope.loading = true;	
				var my_url = url_updatefinaltest;	
				$http({
			       method : 'PUT',
			       url : my_url,
			       data : $.param($scope.param),
			       headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
			    }).success(function(response){
			    	$rootScope.loading = false;
			    	if(response.status == 200)
			     	{
			     		
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
		  
		  $scope.selectPageNext = function (pageNo) {
			$("#doubt_btn").show();
		  	$("#getresemessage").hide();
		  	var cquesid = $scope.questionArr[pageNo-2]['id'];
		  	var optionid = $scope.seloption[cquesid];
		  	$timeout.cancel($scope.timeout);
		  	$scope.timer(cquesid,optionid);
		  	if(optionid != null)
		  	{
				var optiontxt = $("#text"+optionid).val();
				$scope.addAns(cquesid,optionid,optiontxt);
			}
			$scope.value=0;
			countdown();
		  	$scope.currentPage = pageNo;
		  };
		  
		  $scope.selectPageSkip = function (pageNo) {
			$("#doubt_btn").show();
			$("#getresemessage").hide();
		  	var cquesid = $scope.questionArr[pageNo-2]['id'];
		  	var optionid = $scope.seloption[cquesid];
		  	$timeout.cancel($scope.timeout);
		  	$scope.timer(cquesid,optionid);
			$scope.value=0;
			countdown();
		  	$scope.currentPage = pageNo;
		  };
		  
		  $scope.saveSelect = function (pageNo) {
			/*$("#doubt_btn").show();
			$("#getresemessage").hide();*/
		  	var cquesid = $scope.questionArr[pageNo-2]['id'];
		  
		  	var optionid = $scope.seloption[cquesid];
		  	$timeout.cancel($scope.timeout);
		  	$scope.timer(cquesid,optionid);
		  	if(optionid != null)
		  	{
				var optiontxt = $("#text"+optionid).val();
				$scope.addAns(cquesid,optionid,optiontxt);
			}
		  };
		  
		  $scope.multiSelect = function(pageNo)
		  {
		  	$("#doubt_btn").show();
			$("#getresemessage").hide();
		  		$scope.currentPage = pageNo;
		  }
		  
		  
		  $scope.selectPagePrev = function (pageNo) {
			$("#doubt_btn").show();
			$("#getresemessage").hide();
		  	var cquesid = $scope.questionArr[pageNo]['id'];
		  	var optionid = $scope.seloption[cquesid];
		  	$scope.timer(cquesid,optionid);
		  	if(optionid != null)
		  	{
				var optiontxt = $("#text"+optionid).val();
				$scope.addAns(cquesid,optionid,optiontxt);
			}
		  	/*var checkid = $scope.questionArr[pageNo]['id'];
		  	var optionid = $scope.seloption[checkid];
		  	var optiontxt = $("#text"+optionid).val();*/
		  	 
		  	 $scope.currentPage = pageNo;
		  };
		  
		  $scope.timer = function(quesid,optionid)
		  {
		  		var id = quesid;
		 		var idx = $scope.attemtQue.indexOf(id);
			    if (idx > -1) {
			     $scope.value = $scope.questionTime[idx] + $scope.value;
			      $scope.attemtQue.splice(idx, 1);
			      $scope.questionTime.splice(idx, 1);
			      $scope.attemtQue.push(id);
			      $scope.questionTime.push($scope.value);
			      $scope.updateFinalTest(quesid,optionid,$scope.value);
			    }
			    else {
			      $scope.attemtQue.push(id);
			      $scope.questionTime.push($scope.value);
			      $scope.updateFinalTest(quesid,optionid,$scope.value);
			    }
		  	/*console.log($scope.attemtQue);
		  	console.log($scope.questionTime);*/
		  }
		  
		  $scope.addAns = function(quesid,optid,opttext)
		  {
			  	var id = quesid;
		 		var idx = $scope.selection.indexOf(id);
			    if (idx > -1) {
			      $scope.queansid.splice(idx, 1);
			      $scope.queanstext.splice(idx, 1);
			      $scope.selection.splice(idx, 1);
			      
			      $scope.queansid.push(optid);
			      $scope.queanstext.push(opttext);
			      $scope.selection.push(id);
			    }
			    else {
			      $scope.queansid.push(optid);
			      $scope.queanstext.push(opttext);
			      $scope.selection.push(id);
			    }
		  	/*console.log($scope.queansid);
		  	console.log($scope.queanstext);
		  	console.log($scope.selection);*/
		  }
		  
		  $scope.submitResult = function()
		  {
		  	$timeout.cancel(endtimetest);
		  	$scope.param = {
					'student_exam_id' : $scope.student_exam_id,
					'userid':$scope.userid
				}
				$rootScope.loading = true;	
				var my_url = url_submitfinaltest;	
				$http({
			       method : 'PUT',
			       url : my_url,
			       data : $.param($scope.param),
			       headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
			    }).success(function(response){
			    	$rootScope.loading = false;
			    	if(response.status == 200)
			     	{
			     		$window.location.href = $rootScope.base_url+'singleexamresult/'+$scope.student_exam_id;
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
		$scope.submitdoubts = function(currentPage)
		  {
		  	var cquesid = $scope.questionArr[currentPage-2]['id'];
		  	
		  	$scope.param = {
					'exam_qun_Pid' : cquesid,
					'examid':$scope.examid,
					'exam_schedule_id':$scope.exam_schedule_id,
					'userid':$scope.userid
				}
				$rootScope.loading = true;	
				var my_url = url_submitdoubt
				$http({
			       method : 'PUT',
			       url : my_url,
			       data : $.param($scope.param),
			       headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
			    }).success(function(response){
			    	$rootScope.loading = false;
			    	if(response.status == 200)
			     	{
			     		$("#getresemessage").show();
			     		$("#doubt_btn").hide();
			     		$scope.resesmessage = "Question raises successfully.";
			     		$("#getresemessage").addClass('has-success');
				          
			     		//indow.location.href = $rootScope.base_url+'welcome';
			     		//$window.location.href = $rootScope.base_url+'singleexamresult/'+response.id;
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

})

.controller('vidExamResultCtrl', function($scope,$interval,$timeout,$state,$cookies,$stateParams,$rootScope,$window,$http,$location){
		$rootScope.loading = false;
		$scope.authcode = $cookies.get('authcode');
		$scope.userid = $cookies.get('userid');
		$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
		$scope.instid = $cookies.get('instid');
		$scope.type = $cookies.get('type');
		$rootScope.header_show = $scope.type;
		if($scope.type == null)
		{
			$window.location.href = $rootScope.base_url+'studcourse';
		}
		$scope.urlid = $stateParams.id;
		if($stateParams.id != null && $stateParams.id != '')
		$scope.userid = $stateParams.id;
		
		var wrapper_ht = $(window).height();
    	$('.wrapper').css('min-height',wrapper_ht-246);
    
		$scope.examArr = [];
		
		$scope.viewby = 10;
		$scope.currentPage = 1;
		$scope.itemsPerPage = 10;
		$scope.maxSize = 5;
		$scope.totalcount = 0;
			  
		$scope.pageChange = function(currentPage) {
			$scope.currentPage = currentPage;
		};
		$scope.doTheBack = function() {
		  window.history.back();
		};
	//reset page
	$scope.reset = function()
	{
		 $state.reload();
	}	
		//get exam result
	$scope.getExamResult = function(){
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.message = '';
		var startDate = new Date($('#startdate').val());
		var endDate = new Date($('#enddate').val());
		if(endDate < startDate)
        {
            alert('End date should be greater than start date');
            return false;
        }
		$scope.param = {
			'userid' : $scope.userid,
			'searchtext' : $scope.searchtext,
			'startdate' : $scope.startdate,
			'enddate' : $scope.enddate
		}
		var my_url = url_getexamresult+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.examArr = response.examresult;
			}
			else{
				$scope.examArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Exam result are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	 
	$("#startdate").datetimepicker({format: 'Y-m-d',timepicker:false,scrollMonth : false});
	$("#enddate").datetimepicker({format: 'Y-m-d',timepicker:false, scrollMonth : false});

	$scope.adddate = function(type)
	{
		if (type == 'startdate') {
			var sdate = $("#startdate").val();
			$scope.startdate = sdate;
			var edate = $("#enddate").val();
			if(edate == null || edate == '')
			{
				$("#enddate").val(sdate);
				$scope.enddate = sdate;
			}
			
		} else {
			var edate = $("#enddate").val();
			$scope.enddate = edate;
			var sdate = $("#startdate").val();
			if(sdate == null || sdate == '')
			{
				$("#startdate").val(edate);
				$scope.startdate = edate;
			}
		}
	}
	
})

.controller('vidSingleExamResultCtrl', function($scope,$interval,$timeout,$state,$cookies,$stateParams,$rootScope,$window,$http,$location){
		$scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rootScope.loading = false;
		$scope.authcode = $cookies.get('authcode');
		$scope.userid = $cookies.get('userid');
		$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
		$scope.type = $cookies.get('type');
		$rootScope.header_show = $scope.type;
		$scope.studentExamId = $stateParams.id;
		if($scope.type == null)
		{
			$window.location.href = $rootScope.base_url+'studcourse';
		}
		
		var wrapper_ht = $(window).height();
    	$('.wrapper').css('min-height',wrapper_ht-246);
    
		$scope.examArr = [];
		$scope.quesArr = [];
		$scope.questionArr = [];
		$scope.totalscore=0;
		
		  $scope.viewby = 10;
		  $scope.currentPage = 1;
		  $scope.itemsPerPage = $scope.viewby;
		  $scope.maxSize = 5;
		  $scope.totalcount = 0;
		
		$scope.pageChange = function(currentPage) {
			$scope.currentPage = currentPage;
		};
		$scope.doTheBack = function() {
		  window.history.back();
		};
	  
	//get exam result
	$scope.getExamResult = function(){
		//alert($scope.studentExamId);
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.param = {
			'userid' : $scope.userid,
			'studentExamId' : $scope.studentExamId
		}
		var my_url = url_getexamresultById+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.examArr = response.examresult;
				$scope.totalscore = $scope.examArr['totalscore'];
				$scope.exam_mark_total = $scope.examArr['exam_mark'];
	     		$scope.viewQuesDet($scope.examArr['ques'][0]['question_id'],$scope.examArr['ques'][0]['ques_option_id']);
	     		
			}
			else{
				$scope.examArr = [];
				$("#message").addClass('has-success');
				$scope.message = "exam result are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	}
	
	 //open modal
	 $scope.showQuesDet = function(questionid, myoptionid)
	 {
	 	$scope.param = {
			'userid' : $scope.userid,
			'questionid' : questionid
		}
		var my_url = url_singleQuesDet+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.quesArr = response.questionresult;
	     		$scope.quesArr['myoptionid'] = myoptionid;
	     		$("#questionDetail").modal('show');
			}
			else{
				$scope.quesArr = [];
				$("#message").addClass('has-success');
				$scope.message = "question are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	 
	 //view question details
	 $scope.viewQuesDet = function(questionid, myoptionid)
	 { 
	 	$scope.param = {
			'userid' : $scope.userid,
			'questionid' : questionid
		}
		var my_url = url_singleQuesDet+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.questionArr = response.questionresult;
	     		$scope.questionArr['myoptionid'] = myoptionid;
			}
			else{
				$scope.questionArr = [];
				$("#message").addClass('has-success');
				$scope.message = "question are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
})


.filter('secondsToHHmmss', function($filter) {
    return function(seconds) {
        return $filter('date')(new Date(0, 0, 0).setSeconds(seconds), 'HH:mm:ss');
    };
})
.controller('vidStudentFeedbackCtrl', function($scope,$interval,$state,$cookies,$rootScope,$window,$http,$location){
	$rootScope.loading = true;

	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');

	$scope.userid = $cookies.get('userid');
	$scope.name = $cookies.get('name');
	$scope.email = $cookies.get('email');
	$scope.type = $cookies.get('type');

	$scope.feedbackArr = [];
	$scope.courseArr = [];
	$scope.concernArr = [];
	$scope.selection = [];
	$scope.courseDet = {};
	$scope.action = "add";
	$scope.formname = "Add New Feedback";
	$scope.add = true;


	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    if($scope.type != 3)
		{
			$window.location.href = $rootScope.base_url;
		}
    /*file upload*/
   
    function readURL(input) {
    var fileTypes = ['jpeg','jpg','png','gif']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if (isSuccess) { 
	            var reader = new FileReader();
	            reader.onload = function (e) {
	                $('#blah').attr('src', e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);
            }
            else{
            	alert("please select only jpg,jpeg,png,gif files");
				$scope.myFile = null;
				document.getElementById('myFile').value = null;
			}
        }
    }
    $("#myFile").change(function(){
        readURL(this);
    });
    
    var formdata = new FormData();
            $scope.getTheFiles = function ($files) {
            angular.forEach($files, function (value, key) {
                formdata.append(key, value);
            });
    };
		
		$scope.viewby = 10;
		$scope.currentPage = 1;
		$scope.itemsPerPage = 10;
		$scope.maxSize = 5;
		$scope.totalcount = 0;
			  
		$scope.pageChange = function(currentPage) {
			$scope.currentPage = currentPage;
		};
		
    //model open
	$scope.openModal = function()
	{
		$scope.action = "add";
		$scope.formname = "Add New Feedback";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.feedbackDet = {};
		$scope.feedbackForm.reset();
		$("#add_new_feedback").modal('show');
	}
 
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
		
		var my_url = url_getfeedbackbyid+$.param($scope.param);
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
	    
	//get feedback list
	$scope.getFeedback = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'userid' : $scope.userid
		}
		var my_url = url_getfeedback+$.param($scope.param);	
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
	     		$scope.feedbackArr = response.feedback;
			}
			else{
				$scope.feedbackArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Feedback are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }

	//get concern
	$scope.getConcern = function(){
		
		$scope.param = {
			'id' : $scope.userid
		}
		var my_url = url_getfbconcern+$.param($scope.param);
		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.concernArr = response.concern;
			}
			else{
				$scope.concernArr = [];
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 } 

	//add feedback		
	$scope.submitCreateFeedback = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "Sending feedback...";
			var my_url = url_addfeedback;
			var method = "POST";
			var dispmessage = "Feedback successfully sent.";
			
			$scope.feedbackDet.userid = $scope.userid;
			$scope.feedbackDet.name = $scope.name;
			$scope.feedbackDet.email = $scope.email;
			
			$rootScope.loading = true;
				$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.feedbackDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.getFeedback();

	            			$scope.feedbackDet = {};
							$scope.feedbackForm.reset();

							$("#message").addClass('has-success');
							$scope.message = dispmessage;
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
})
.controller('vidViewNotesCtrl', function($scope,$interval,$cookies,$state,$stateParams,$rootScope,$window,$http,$location){
	$scope.notesArr = [];
	$scope.selection = [];
	$scope.courseArr = [];
	$scope.courseSArr = [];
	
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$rootScope.loading = false;
	if($scope.authcode == null)
	{
		$window.location.href = $rootScope.base_url;
	}
		
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    $scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
   		 }, 36000);
   	

	
	//reset page
	$scope.reset = function(type)
	{
		 $state.reload();
	}

	$scope.viewby = 10;
	$scope.currentPage = 1;
	$scope.itemsPerPage = 10;
	$scope.maxSize = 5;
	$scope.totalcount = 0;
		  
	$scope.pageChange = function(currentPage) {
		$scope.currentPage = currentPage;
	};
	
	//get notes list
	$scope.getNotes = function(){
		//alert($scope.userid);
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'userid' : $scope.userid,
			'searchtext' : $scope.searchtext
		}
		var my_url = url_getstudentcourse+$.param($scope.param);
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.notesArr = response.course;
			}
			else{
				$scope.notesArr = [];
				/*$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Notes are not available.";*/
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	

})
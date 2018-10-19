angular.module('ngApp.examCtrl', [])
.filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
}])
.controller('vidManageExamCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$scope.examArr = [];
	$scope.selection = [];
	$scope.examDet = {};
	$scope.examDet.examSchedule = [];
	$scope.examGroupDet = {};
	$scope.examDet.checkSubjectGroup=[];
	$scope.examDet.subjectGroup=[];
	$scope.action = "add";

	//$scope.formname = "Add New Exam";
	$scope.formname = "Final Exams";
	$scope.schedule_name = "Add Exam Schedule";
	$scope.schedule_exam_name = "";
	$scope.edit = false;
	$scope.add = true;
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$("#add_new_exam").hide();
	$("#table_exam").show();
	$('#minus_btn_id').hide();
	$('#plus_btn_id').show();
	
	//$scope.instid = $cookies.get('instid');
	$rootScope.loading = true;
	$scope.chapterArr = [];
	$scope.quesCount = [];
	$scope.quesCountTopic = [];
	$scope.courseHArr = [];
	$scope.paraArr = [];
	$scope.paragrapQuestionArr = [];
	$scope.subjectGroupArr = [];
	$scope.doTheBack = function() {
	  window.history.back();
	};

	// datetimepicker for select exam date
	$("#exam_date").datetimepicker({
             format: 'd-m-Y',
             timepicker:false, 
             minDate : new Date(),
             scrollMonth : false,
             onChangeDateTime : function(dp,$input) {
             	$scope.examGroupDet.exam_date=$input.val();
             }
		   });
// timepicker for select exam start time
	$('.start_time').timepicker({
		    timeFormat: 'h:mm p',
		    interval: 15,
		    minTime: '6',
		    maxTime: '6:00pm',
		    startTime: '6:00',
		    dynamic: false,
		    dropdown: true,
		    scrollbar: true,
		    change: function(time) {
            $scope.examGroupDet.start_time=$(this).val();
            $('.end_time').timepicker('option', 'minTime', $(this).val());
            var str=$(this).val();
            var res = str.split(" ",1).toString();
            $('.end_time').timepicker('option', 'startTime', res);
            $('.end_time').val('');
            $scope.examGroupDet.end_time='';
        }
     });

// timepicker for select exam end time
	$('.end_time').timepicker({
		    timeFormat: 'h:mm p',
		    interval: 15,
		    maxTime: '6:00pm',
		    dynamic: false,
		    dropdown: true,
		    scrollbar: true,
		    change: function(time) {
		    	$scope.examGroupDet.end_time=$(this).val();
		    }
     });
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    $scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
   		 }, 36000);
   	
   	$scope.viewby = 10;
	$scope.currentPage = 1;
	$scope.itemsPerPage = 10;
	$scope.maxSize = 5;
	$scope.totalcount = 0;
		  
	$scope.pageChange = function(currentPage) {
		$scope.currentPage = currentPage;
	};
	$scope.openExamList = function()
	{
		$("#add_new_exam").hide();
		$("#table_exam").show();
		$scope.action = "add";
		$scope.formname = "Final Exams";

	}
    //model open
	$scope.openModal = function(action=null)
	{
		$scope.action = "add";
		$scope.formname = "Add Final Exam";
		$scope.edit = false;
		$scope.add = true;
		$scope.message = '';
		$scope.id = '';
		$scope.selection = [];
	/*	$scope.paraQuestionCount = [];
		$scope.paragrapQuestionArr = [];*/
		$scope.quesCount = [];
		$scope.final_quesCount = [];
	    $scope.quesCountTopic = [];
		if(action == 'edit'){
           $scope.action = action;
		   $scope.formname = "Edit New Final Exam";
		   $scope.edit = true;
		   $scope.add = false;
		   $scope.getExamCourse('edit');
		}else{
		   $scope.examDet = {};
		   $scope.examForm.reset();
           $scope.getExamCourse();
		}
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		//$("#add_new_exam").modal('show');
		$("#add_new_exam").show();
		$("#table_exam").hide();
	}
	//get Subject Group list 
$scope.getSubjectGrouplist = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		
		$scope.param = {
			'userid' : $scope.userid
		}
		var my_url = url_getsubjectgroup+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		//$scope.subjectGroupArr = response.subjectgroup;
		            		$scope.subjectGroupArr = [];
		            		$scope.checkSubjectGroupArr=[];
		            		var j=false;
		            		if(response.subjectgroup.length > 0){
		            		for(var i=0;i < response.subjectgroup.length;i++){
		            			if(response.subjectgroup[i].course_id == id){
                                   $scope.subjectGroupArr.push(response.subjectgroup[i]);
                                   if($scope.action == 'add'){
                                        $scope.checkSubjectGroupArr.push(j);
                                   } 
                                   
		            			}
		            		}
		            		//console.log($scope.subjectGroupArr);
		            		if($scope.action == 'add' && $scope.checkSubjectGroupArr.length > 0){
                               	$scope.examDet.checkSubjectGroup=$scope.checkSubjectGroupArr;
                               	$scope.examDet.subjectGroup=[];
		            		}
		            	  }else{
		            	  	$scope.subjectGroupArr = [];
		            	  	$scope.examDet.subjectGroup=[];
		            	  }
						}
						else{
							$scope.subjectGroupArr = [];
							$("#tablemessage").addClass('has-success');
							$scope.tablemessage = "Subject Group are either deleted or not inserted.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}	
	$scope.checkSubjectGroup = function(index,id)
	{
		$scope.examDet.subjectGroup[index]={};
		if($scope.examDet.checkSubjectGroup[index] == true){
				$scope.examDet.subjectGroup[index].subgroup_id=id;
				$scope.examDet.subjectGroup[index].examSchedule=[];
		}else{
                $scope.examDet.subjectGroup[index]={};
		}
	}
	//get exam list
	$scope.getExam = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			//'instid' : $scope.instid,
			'userid' : $scope.userid
		}
		var my_url = url_getexam+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.examArr = response.exam;
			}
			else{
				$scope.examArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Exam are either deleted or not inserted.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	 
	//get exam course
	$scope.getExamCourse = function(action=null){
		/*$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';*/
		$scope.course_action='add';
		if(action == 'edit'){
            $scope.course_action=action;
		}
		$scope.param = {
		//	'instid' : $scope.instid,
			'userid' : $scope.userid,
			'action' : $scope.course_action
		}
		var my_url = url_getexamcourse+$.param($scope.param);
		//$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.courseArr=[];
	     		$scope.courseArr = response.course;
			}
			else{
				$scope.courseArr = [];
				/*$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Exam are either deleted or not inserted.";*/
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	/*$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";*/
	    });
	 }
	
	$scope.insertChapterId = function(id)
	{
		$scope.chapterArr.push(id);
	}
	//get course hirarchy
	$scope.getCourseHirarchy = function(id,data){
	
	//console.log(data);
		if(data == null)
		{
			$scope.quesCount = [];
	     	$scope.quesCountTopic = [];
	     	$scope.final_quesCount = [];
		}
		if(id == null || id == ''){
			return false;
		}
		$scope.param = {
			'courseid' : id
		}
		var my_url = url_getcoursehirarchy+$.param($scope.param);
		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.courseHirarchi = response.courseHirarchi;
	     		//console.log($scope.courseHirarchi);
	     		if(data != null)
	     		{
					setTimeout(function() {
					for(i=0;i < data.length;i++){
						
							$scope.quesCount[data[i]['chapter_id']] = data[i]['no_of_ques'];
							$scope.final_quesCount[data[i]['chapter_id']] = data[i]['final_qun'];
							$("#question_"+data[i]['chapter_id']).val(data[i]['no_of_ques']);
							//console.log($scope.final_quesCount);
					}
					},1000)
				}
			}
			else{
				$scope.courseHirarchi = [];
				//$("#message").addClass('has-error');
				//$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	
	var questionCount = 0;
	$scope.paraQuestionCount = [];
	
	$scope.isNumeric = function(i)
	{

		var val = $('#question_'+i).val();
		if(val == '' || val == null) return false;
		    //val = val.replace(/[^0-9\.]/g,'');
		    questionCount = 0;
		    finalquestionCount = 0;
		    angular.forEach($scope.quesCount, function(value, key) {
		    	
		    	if(key != i)
		    	{
					questionCount = questionCount + parseInt(value);

				}
				
			});
			angular.forEach($scope.final_quesCount, function(values, keys) {
		    	//console.log(key + ': ' + value);
		    	if(keys != i)
		    	{
					finalquestionCount = finalquestionCount + parseInt(values);
					//console.log(finalquestionCount);
				}
				
			});
			if(questionCount > parseInt($scope.examDet.noofques) || finalquestionCount < parseInt($scope.examDet.noofques))
			{
				//alert("All chapters question count is greater than to no of question in exam. Please check again.");
				$('#questionTopic_'+i).val('');
				return false;
			}
		    if(val <= 0 || val > 999)
		    {
				alert("Invalid count of No Of Question");
				$('#question_'+i).val('');
			}
			else
			if((questionCount + parseInt(val)) > parseInt($scope.examDet.noofques)){
				questionCount = questionCount + parseInt(val);
				//alert("All chapters question count is greater than to no of question in exam. Please check again..");
				$('#question_'+i).val('');
				return false;
			}
			if((finalquestionCount - parseInt(val)) < parseInt($scope.examDet.noofques)){
				finalquestionCount = finalquestionCount - parseInt(val);
				alert("All final exam question count is less than to no of question in exam. Please check again...");
				$('#question_'+i).val('');
				return false;
			}
	}
	

	$scope.marlCal = function()
	{
		//val1 = $scope.examDet.markperques;
		//val2 = $scope.examDet.noofques;
		//$scope.examDet.mark = parseInt(val1) * parseInt(val2);
	}
// open modal for add/edit exam schedule
	$scope.openExamScheduleModal = function(index,exam_name,action,edit_id)
	{
		$scope.index_id = '';
		$scope.edit_id = '';
		$scope.schedule_action = '';
		$scope.schedule_action = action;
		$scope.schedule_name = "Add Exam Schedule";
	    $scope.schedule_exam_name = exam_name;
	    $scope.index_id = index;
	    $scope.edit_id = edit_id;
        $scope.examGroupDet={};
		$scope.examScheduleForm.reset();

	    if(action == 'edit'){
              $scope.schedule_name = "Edit Exam Schedule";
              $scope.examGroupDet.exam_date=$scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].exam_date;
              $scope.examGroupDet.fee=$scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].fee;
              $scope.examGroupDet.start_time=$scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].start_time;
              //$scope.examGroupDet.end_time=$scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].end_time;
              $scope.examGroupDet.mode=$scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].mode;
              $scope.examGroupDet.exam_duration=$scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].exam_duration;		
	//console.log($scope.examGroupDet);
	    }
		$("#add_exam_schedule").modal('show');
	}
// add & update exam schedule
$scope.submitAddExamSchedule=function(index,action,edit_id){
		$scope.index_id='';
		$scope.edit_id='';
		$scope.index_id=index;
		$scope.edit_id=edit_id;
		if(action == 'add'){
			//$scope.examDet.subjectGroup[index]=[];
            $scope.examDet.subjectGroup[index].examSchedule.push($scope.examGroupDet);
			$scope.examGroupDet={};
			$scope.examScheduleForm.reset();
		}else{
              $scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].exam_date=$scope.examGroupDet.exam_date;
              $scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].fee=$scope.examGroupDet.fee;
              $scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].start_time=$scope.examGroupDet.start_time;
              //$scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].end_time=$scope.examGroupDet.end_time;
              $scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].mode=$scope.examGroupDet.mode;		
              $scope.examDet.subjectGroup[$scope.index_id].examSchedule[$scope.edit_id].exam_duration=$scope.examGroupDet.exam_duration;		
		}
		$("#add_exam_schedule").modal('hide');

	}
// delete exam schedule
$scope.deleteExamSchedule=function(index,edit_id){
		$scope.index_id='';
		$scope.edit_id='';
		$scope.index_id=index;
		$scope.edit_id=edit_id;	
		var deleteUser = $window.confirm('Are you sure to delete exam schedule?');
		 if(deleteUser)
		   {
		       $scope.examDet.subjectGroup[$scope.index_id].examSchedule.splice($scope.edit_id,1);
	       }
}
// toggle exam schedule list
$scope.openGetExamScheduleList=function(index,type){
		if(type == 'plus'){
           $('#getAllScheduleList'+index).show();
           $('#plus_btn_id'+index).hide();
           $('#minus_btn_id'+index).show();
		}else{
		   $('#getAllScheduleList'+index).hide();
		   $('#plus_btn_id'+index).show();
           $('#minus_btn_id'+index).hide();
		}
	}
//add/edit final exam		
$scope.submitCreateExam = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "exam adding...";
			var my_url = url_addexam;
			var method = "POST";
			var dispmessage = "Exam added successfully.";
			if($scope.action == "edit")
			{
				my_url = url_updateexam;
				method = "PUT";
				$scope.message = "exam editing...";
				$scope.examDet.id = $scope.id;
				var dispmessage = "Exam updated successfully.";
			}
			var questionCount = 0;
		    angular.forEach($scope.quesCount, function(value, key) {
		    	if(value != ""){
					questionCount = questionCount + parseInt(value);
				}		    		
			});

			if(questionCount != parseInt($scope.examDet.noofques))
			{
				if(questionCount > parseInt($scope.examDet.noofques))
				{
					$("#message").addClass('has-error');
					$scope.message = "All chapters question count is greater than to no of question in exam. Please check again";
					return false;
				}
				else
				{
					$("#message").addClass('has-error');
					$scope.message = "All chapters question count is less than to no of question in exam. Please check again";
					return false;
				}
			}
		
			$scope.examDet.quesCount = $scope.quesCount;
			
			$scope.examDet.userid = $scope.userid;
			$rootScope.loading = true;
				$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.examDet),
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.getExam();
		            		if($scope.action == "add")
							{
								$scope.selection = [];
							    $scope.quesCount = [];
							    $scope.courseHirarchi = [];
								$scope.examDet = {};
								$scope.examForm.reset();
								$("#add_new_exam").hide();
								$("#table_exam").show();
								$scope.action = "add";
								$scope.formname = "Final Exams";
		            		}
							$("#message").addClass('has-success');
							$scope.message = dispmessage;
							alert($scope.message);
							$window.location.href = $rootScope.base_url+'masterexam';
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
	
	//edit exam           
	$scope.editExam = function(id)     
	{
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
			$scope.id = id;
			$scope.action = "edit";
			$scope.formname = "Edit New Exam";
			$scope.edit = true;
			$scope.add = false;
			$scope.param = {
				'id' : $scope.id,
				'userid' : $scope.userid
			}
		var my_url = url_getexambyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		//$scope.selection = [];
							//$scope.paraQuestionCount = [];
							//$scope.paragrapQuestionArr = [];
							$scope.quesCount = [];
	     					//$scope.quesCountTopic = [];
							$scope.courseHirarchi = [];
		            		$scope.examDet = {};
							$scope.examForm.reset();
							$scope.openModal('edit');
		
							$scope.id=response.id;
		            		$scope.examDet.name = response.name;
		            		$scope.examDet.course = response.cid;
		            		$scope.examDet.examGroupDet = response.duration;
		            		//$scope.examDet.markperques = response.markperques;
		            		$scope.examDet.noofques = response.noofques;
		            		//$scope.examDet.mark = response.mark;
		            		$scope.examDet.subjectGroup = response.subjectGroup;
		            		$scope.examDet.checkSubjectGroup = response.checkSubjectGroup;

		            		$scope.getCourseHirarchy($scope.examDet.course,response.quesCount);
		            		$scope.getSubjectGrouplist($scope.examDet.course);
		            		/*$scope.paragrapQuestionArr = response.paragraphQuestion;
		            		for(i=0;i < $scope.paragrapQuestionArr.length;i++){
								$scope.selection.push($scope.paragrapQuestionArr[i]['para_id']);
							    $scope.paraQuestionCount.push(parseInt($scope.paragrapQuestionArr[i]['questioncount']));
							}*/
						/*	for(i=0;i < response.checkSubjectGroup.length;i++){
								alert(response.checkSubjectGroup[i]);
                              if(response.checkSubjectGroup[i] == '1')
                                 {
		            		        $scope.examDet.checkSubjectGroup.push(true);
		            		      }
							}*/	            		
							if(response.isnegative == '1'){
		            		      $scope.examDet.isnegative = true;
		            		}else{
		            		    $scope.examDet.isnegative = false;
		            		}
		            		$scope.examDet.negativewt = response.negativewt;
		            		
		            		
		            		/*setTimeout(function() {
			            		for(i=0;i < response.quesCount.length;i++){
			            			//console.log("#question_"+response.quesCount[i]['chapter_id']);
									$("#question_"+response.quesCount[i]['chapter_id']).val(response.quesCount[i]['no_of_ques']);
								}
							},3000);*/
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
		
	//delete single exam
	$scope.deleteExam = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete exam?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'id' : id,
					'userid' : $scope.userid
				}
				var my_url = url_deleteexam;
				$rootScope.loading = true;
				$http({
				              method : 'DELETE',
				              url : my_url,
				              data : $.param($scope.param),
				              headers : {'authcode': $scope.authcode}
				           }).success(function(response){
				           		$rootScope.loading = false;
				            	if(response.status == 200)
				            	{
				            		$scope.getExam();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Exam deleted successfully.";
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
	}
	
	// toggle selection paragrapQuestionArr
	$scope.toggleSelection = function toggleSelection(id) {
		
	 		var id = id;
	 		var idx = $scope.selection.indexOf(id);
	 		var tempPara = {};
		    // is currently selected
		    if (idx > -1) {
		      $scope.selection.splice(idx, 1);
		      $scope.paraQuestionCount.splice(idx, 1);
		      /*var quesCount = $scope.paragrapQuestionArr[idx]['questioncount'];
		      paraQuestionCount = paraQuestionCount - parseInt(quesCount);*/
		      $scope.paragrapQuestionArr.splice(idx, 1);
		      /*console.log($scope.selection);
		      console.log($scope.paraQuestionCount);
		      console.log($scope.paragrapQuestionArr);*/
		    }
		    else {		      
		      for(i=0;i < $scope.paraArr.length;i++){
			  		if($scope.paraArr[i]['para_id'] == id){
						//paraQuestionCount = paraQuestionCount + parseInt($scope.paraArr[i]['questioncount']);
						var questionCount = 0;
					    angular.forEach($scope.quesCount, function(value, key) {
					    	if(value != ""){
								questionCount = questionCount + parseInt(value);
							}		    		
						});
						angular.forEach($scope.quesCountTopic, function(value, key) {
							if(value != ""){
					    		questionCount = questionCount + parseInt(value);
					    	}
						});
						angular.forEach($scope.paraQuestionCount, function(value, key) {
							if(value != ""){
					    		questionCount = questionCount + parseInt(value);
					    	}
						});
						
						if(parseInt($scope.paraArr[i]['questioncount']) > 0){
							questionCount = questionCount + parseInt($scope.paraArr[i]['questioncount']);
						}else{
							$('#para_'+id).attr('checked', false);	
							return false;
						}
						
						if(questionCount > parseInt($scope.examDet.noofques)){
							alert("All chapters question count is greater than to no of question in exam. Please check again");
							//paraQuestionCount = paraQuestionCount - parseInt($scope.paraArr[i]['questioncount']);						
							$('#para_'+id).attr('checked', false);	
							return false;
						}
						$scope.selection.push(id);
						$scope.paraQuestionCount.push(parseInt($scope.paraArr[i]['questioncount']));
						tempPara.para_id = $scope.paraArr[i]['para_id'];
						tempPara.para_text = $scope.paraArr[i]['para_text'];
						tempPara.questioncount = $scope.paraArr[i]['questioncount'];
						tempPara.chaptername = $scope.paraArr[i]['chaptername'];
						tempPara.topicname = $scope.paraArr[i]['topicname'];
						$scope.paragrapQuestionArr.push(tempPara);
					}
			  }
		      /*console.log($scope.selection);
		      console.log($scope.paraQuestionCount);
		      console.log($scope.paragrapQuestionArr);*/
		    }
	};
		  
	//delete multiple exam
	$scope.delSelectExam = function()
	{
		$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		 var length = $scope.selection.length;
		 if(length > 0)
		 {
		 	var deleteUser = $window.confirm('Are you sure to delete exam?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'ids' : $scope.selection,
					'userid' : $scope.userid
				}
				var my_url = url_deletemultipleexam;
				$rootScope.loading = true;
				$http({
				              method : 'DELETE',
				              url : my_url,
				              data : $.param($scope.param),
				              headers : {'authcode': $scope.authcode}
				           }).success(function(response){
				           		$rootScope.loading = false;
				            	if(response.status == 200)
				            	{
				            		$scope.getExam();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Exam deleted successfully.";
				            		$scope.selection = [];
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
		 }
		 else{
		 	$("#getmessage").addClass('has-error');
		    $scope.getmessage = "Please select atleast one exam";
		 }
	}
	
	$("#select_exams").change(function(){
	    	$(".categories").prop('checked', $(this).prop("checked"));
	    	if($scope.allselect)
	    	{
				$scope.selection = [];
		    	angular.forEach($scope.examArr, function(exam) {
		    		$scope.selection.push(parseInt(exam.id));
				});
				//console.log($scope.selection);
			}
			else{
				$scope.selection = [];
				//console.log($scope.selection);
			}
	    	
	});
	
})

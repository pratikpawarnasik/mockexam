
angular.module('ngApp.masterStudCtrl', [])

.controller('vidMasterStudentCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){

	/*$scope.categoryArr = [];
	$scope.id = '';*/
	$scope.selection = [];
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$scope.email = $cookies.get('email');
	$rootScope.loading = true;
	$scope.coursecount = 0;
	$scope.examcount = 0;
	$scope.scoreArr = [];	
	$scope.noteArr = [];
	$scope.examDet = [];
	$scope.examsArr = [];
	$scope.courseArr = [];
	$scope.examDetail =[];
	$scope.selectedExam = '';
	$scope.subGroupDetail = [];
	$scope.CourseIdSelctByAdmin = '';
	$scope.examDet.subGroup = false;
	$scope.examDet.selectedExam = '';
	//$scope.examDet.subGroup = '';
	
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
	
	$scope.doTheBack = function() {
	  window.history.back();
	};  
	$scope.pageChange = function(currentPage) {
		$scope.currentPage = currentPage;
	};
		 
    //model open
	$scope.openModal = function()
	{
		$scope.action = "add";
		$scope.formname = "Add New Student";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.studProfileDet = {};
		$scope.studentForm.reset();
		$("#add_new_student").modal('show');
	}
	$("#myFile").change(function(){
        readURL(this);
    });

    function readURL(input) {
    	var fileTypes = ['xls','xlsx']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if (isSuccess) { 
	            /*var reader = new FileReader();
	            reader.onload = function (e) {
	                //$('#blah').attr('src', e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);*/
            }
            else{
            	alert("please select only excel file. eg. xls,xlsx");
				$scope.myFile = null;
				$("#upload-file-info").empty();
				document.getElementById('myFile').value = null;
			}
        }
    }

    $scope.downloadexcel = function()
    {
    	$('#admin_exam_buy').modal({
	       	 show: 'true'
	   	});
    	/// get course information
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
	     		//console.log($scope.courseArr);
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
	$scope.load_sub_group = function(id) {
		$scope.CourseIdSelctByAdmin = id;
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
					$scope.subGroupDetail = [];
					$scope.message = response.message;
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		    });
	} 
	$scope.load_exam_data = function(){
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
		       url : my_url
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
					$("#message").addClass('has-error');
					$scope.message = response.message;
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		    });
	}
	$scope.getExcellFormat = function(){
		var setMessage =  'Are you confirm to get Excel format?';
		if ($scope.examDet.selectedExam == '') {
			var setMessage = 'Are you sure to not purchase any exam?';
		}
			
		var formatDownload = $window.confirm(setMessage);
		 if(!formatDownload){
		   	//$("#admin_exam_buy").hide();
		   		$('#admin_exam_buy').modal('toggle');
		   		$('#admin_exam_buy').modal('hide');
				//$('#admin_exam_buy').hide();

	  			//$window.location.reload();
	  			return false;
		      
	    }
		$scope.param = {
				'schedule' : $scope.examDet.selectedExam,								
				'courseId' : $scope.CourseIdSelctByAdmin
		}
		
		$rootScope.loading = true;
		var my_url = url_stud_excel+$.param($scope.param);
		$http({
	              method : 'get',
	              url : my_url,
	              headers : {'authcode': $scope.authcode},
	              responseType: 'arraybuffer'
	           }).success(function (data, status, headers, config) {
	           	
				$rootScope.loading = false;
	           	window.open(my_url,'_blank' );
		    	$('#admin_exam_buy').modal('toggle');
		   		$('#admin_exam_buy').modal('hide');
		}).error(function(error){
			$rootScope.loading = false;
       		$("#getmessage").addClass('has-error');
			$scope.getmessage = "Some unknown error has occurred. Please try again.";
		});
		
	}
	$scope.downloadStudentExcel = function()
    {
    	var startDate = new Date($('#startdate').val());
		var endDate = new Date($('#enddate').val());
		if(endDate < startDate)
        {
            alert('End date should be greater than start date');
            return false;
        }
		$scope.param = {
			'searchtext' : $scope.searchtext,
			'searchmail' : $scope.searchmail,
			'mailStatus' : $scope.mailStatus,
			'searchmobile' : $scope.searchmobile,
			'startdate' : $scope.startdate,
			'enddate' : $scope.enddate
		}
    	$rootScope.loading = true;
    	
		var my_url = url_studentListExcel+$.param($scope.param);
		$http({
				              method : 'get',
				              url : my_url,
				              headers : {'authcode': $scope.authcode},
				              responseType: 'arraybuffer'
				           }).success(function (data, status, headers, config) {
				           	
    						$rootScope.loading = false;
				           	window.open(my_url,'_blank' );
		    
		}).error(function(error){
								$rootScope.loading = false;
				           		$("#getmessage").addClass('has-error');
								$scope.getmessage = "Some unknown error has occurred. Please try again.";
	    });
	}
	$scope.uploadStudList = function(){
    	$("#uploadmessage").removeClass('has-error');
    	$("#uploadmessage").empty();
		var file = $scope.myFile;
		var fd = new FormData();
       	if($scope.myFile == null)
		{
				$("#uploadmessage").addClass('has-error');
				$("#uploadmessage").html("Please upload student list");
				return false;
		}
       	var file = $scope.myFile;
        fd.append('stud_file', file);
        fd.append('userid', $scope.userid);
        fd.append('email', $scope.email);
        //fd.append('usertype', $scope.type);
        //fd.append('courseid', $scope.courseid);
        //fd.append('chapterid', $scope.chapterid);
        //fd.append('topicid', $scope.topicid);
        //console.log(fd);
        $rootScope.loading = true;
       	$http.post(url_uploadstudents, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined,'authcode':$scope.authcode,'Process-Data': false}
        })
        .success(function(resourse){
        	alert('Student list uploaded successfully.');
        	$rootScope.loading = false;
        	//$scope.getQuestion();
        	$scope.myFile = null;
        	$("#upload-file-info").empty();
			document.getElementById('myFile').value = null;
			if(resourse.error != 0)
			{
				$("#uploadmessage").append("<span style='color:red;'>Total Student for upload : "+(resourse.error+resourse.success) +"</span>");
				$("#uploadmessage").append("<br /><span style='color:red;'>Error student in list : "+resourse.error +"</span>");
				$("#uploadmessage").append("<br /><span style='color:red;'>"+resourse.errorexcel+"</span>");
			}
			else{
				$("#uploadmessage").append("<span style='color:red;'>Total questions : "+(resourse.success) +"</span>");
			}
			if(resourse.success != '')
			{
				$("#uploadmessage").append("<br /><span style='color:green;'>Successfully added questions : "+resourse.success +".</span><br />");
			}
        })
        .error(function(error){
        	$rootScope.loading = false;
        	return false;
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
	// toggle selection
	$scope.toggleSelection = function toggleSelection(id) {
	 		var id = parseInt(id);
	 		var idx = $scope.selection.indexOf(id);
		    // is currently selected
		    if (idx > -1) {
		      $scope.selection.splice(idx, 1);
		      //console.log($scope.selection);
		    }
		    // is newly selected
		    else {
		      $scope.selection.push(parseInt(id));
		     /// console.log($scope.selection);
		    }
			//console.log($scope.selection);    
	};
		  
	//delete multiple question
	$scope.delSelectStudent = function()
	{
		$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		 var length = $scope.selection.length;
		 if(length > 10000)
		 {
		 	$("#getmessage").addClass('has-error');
		    $scope.getmessage = "Please select less than or equal to 10000 questions";
		 	return false;
		 }
		if(length > 0)
		{
		 	var deleteUser = $window.confirm('Are you sure to delete Student?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'ids' : $scope.selection
				}
				//console.log($scope.param);
				var my_url = url_deletemultipleStudent;
				$rootScope.loading = true;
				$http({
				              method : 'DELETE',
				              url : my_url,
				              data : $.param($scope.param),
				              headers : {'authcode': $scope.authcode}
				           }).success(function(response){
				           	//console.log(response);
				           	$rootScope.loading = false;
				            	if(response.status == 200)
				            	{
				            		//$window.location.reload();
				            		$scope.getNewStud();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Student deleted successfully.";
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
		    $scope.getmessage = "Please select atleast one student";
		 }
	}	
			$("#select_student").change(function(){
	    	$(".categories").prop('checked', $(this).prop("checked"));
	    	if($scope.allselect)
	    	{
				$scope.selection = [];
		    	angular.forEach($scope.questionArr, function(question) {
		    		$scope.selection.push(parseInt(question.id));
				});
				//console.log($scope.selection);
			}
			else{
				$scope.selection = [];
				//console.log($scope.selection);
			}	    	
	});	
		
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
	 $("#dob").datetimepicker({format: 'Y-m-d',timepicker:false, maxDate : new Date(),scrollMonth : false});
	$scope.submitNewStudent = function()
	{
		
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "student update...";
			var birthDate = $("#dob").val();
			$scope.studProfileDet.dob=birthDate;
			
			var my_url = url_add_student;
			$scope.studProfileDet.userid = $scope.userid;
			if ($scope.studProfileDet.country == 1) {
				$scope.studProfileDet.country = 'India';
			}
			else{
				$scope.studProfileDet.country = 'Other';				
			}
			if ($scope.studProfileDet.gender == 1) {
				$scope.studProfileDet.gender = 'Male';
			}
			else{
				$scope.studProfileDet.gender = 'Female';				
			}
			$scope.studProfileDet.country;
			$rootScope.loading = true;
			//console.log($scope.studProfileDet);
			var method = "POST";
			var dispmessage = "Student successfully registered.";
			if($scope.action == "edit")
			{
				my_url = url_updateStudAdmin;
				method = "PUT";
				$scope.message = "Student editing...";
				var dispmessage = "Student updated successfully.";
				
			}
			$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.studProfileDet),
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){ 
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$("#message").addClass('has-success');
							$scope.message = dispmessage;
							//alert('Student profile updated successfully.');
							//$window.location.href = $rootScope.welcome_url;
							
							//$window.location.href = $rootScope.buyexam;
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

	$scope.mailSendFun = function(id){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param ={
			'id' : id
		}
		var my_url = url_resend_mail+$.param($scope.param);	
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
	     		$window.location.reload();
	     		$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Mail has been send successfully.";
			}
			else{
				$("#tablemessage").addClass('has-error');
				$scope.tablemessage = "Mail has been not send.";				
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });	
	}
	$scope.downloadStudentPdf = function()
	{	
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
				'searchmail' : $scope.searchmail,
				'searchmobile' : $scope.searchmobile,
				'startdate' : $scope.startdate,
				'enddate' : $scope.enddate
		}
    	$rootScope.loading = true;
    	
		var my_url = url_studlistpdf+$.param($scope.param);
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	$rootScope.loading = false;
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
	$scope.reset = function(type)
	{
		$scope.searchtext = '';
		$scope.startdate = '';
		$scope.enddate = '';
		$scope.searchmail = '';
		$scope.searchmobile = '';

		$scope.mailStatus = null;
		$scope.getNewStud();
		//$window.location.reload();
		 //$state.reload();
	}
	$scope.getStudent = function(stud_id)
    {	$scope.action = "edit";
		$scope.formname = "Edit Student";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.studProfileDet = {};
		$scope.studentForm.reset();
		

    	$scope.get_state();

		$scope.getmessage = '';
		
		$scope.param = {
				
				'userid' : stud_id
			}
		var my_url = url_getstudentbyidAdmin+$.param($scope.param);		
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           //	console.log(response);
		           	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
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
		            		
		            	$("#add_new_student").modal('show');
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
})
.controller('vidpaymentManageCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){

	$scope.selection = [];
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$scope.email = $cookies.get('email');
	$rootScope.loading = true;

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
	
	$scope.doTheBack = function() {
	  window.history.back();
	};  
	$scope.pageChange = function(currentPage) {
		$scope.currentPage = currentPage;
	};

	$scope.reset = function(type)
	{
		$scope.searchtext = '';
		$scope.startdate = '';
		$scope.enddate = '';
		$scope.adminCollect = false;
		$scope.paytmCollect = false;

		$scope.mailStatus = null;
		$scope.getNewStud();
		//$window.location.reload();
		 //$state.reload();
	}	 
    //model open
	$scope.openModal = function()
	{
		$scope.action = "add";
		$scope.formname = "Add New Student";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.studProfileDet = {};
		$scope.studentForm.reset();
		$("#add_new_student").modal('show');
	}
	$("#myFile").change(function(){
        readURL(this);
    });

    function readURL(input) {
    	var fileTypes = ['xls','xlsx']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if (isSuccess) { 
	            /*var reader = new FileReader();
	            reader.onload = function (e) {
	                //$('#blah').attr('src', e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);*/
            }
            else{
            	alert("please select only excel file. eg. xls,xlsx");
				$scope.myFile = null;
				$("#upload-file-info").empty();
				document.getElementById('myFile').value = null;
			}
        }
    }


	$scope.downloadStudentExcel = function()
    {
    	var startDate = new Date($('#startdatepayment').val());
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
				'adminCollect' : $scope.adminCollect,
				'paytmCollect' : $scope.paytmCollect,
				'onlineStud' : $scope.onlineStud,
				'OfflineStud' : $scope.OfflineStud,
				'enddate' : $scope.enddate
		}
    	$rootScope.loading = true;
    	
		var my_url = url_paymentstudentListExcel+$.param($scope.param);
		$http({
				              method : 'get',
				              url : my_url,
				              headers : {'authcode': $scope.authcode},
				              responseType: 'arraybuffer'
				           }).success(function (data, status, headers, config) {
				           	
    						$rootScope.loading = false;
				           	window.open(my_url,'_blank' );
		    
		}).error(function(error){
								$rootScope.loading = false;
				           		$("#getmessage").addClass('has-error');
								$scope.getmessage = "Some unknown error has occurred. Please try again.";
	    });
	}


	// toggle selection
	$scope.toggleSelection = function toggleSelection(id) {
	 		var id = parseInt(id);
	 		var idx = $scope.selection.indexOf(id);
		    // is currently selected
		    if (idx > -1) {
		      $scope.selection.splice(idx, 1);
		    }
		    else {
		      $scope.selection.push(parseInt(id));
		    }
	};
		  

	 $("#dob").datetimepicker({format: 'Y-m-d',timepicker:false, maxDate : new Date(),scrollMonth : false});
	
	//get getNewStud list
	$scope.getpaymentHistory = function(){
    	//alert($scope.paytmCollect);
    	$scope.TotalAmt = 0;
		$scope.NewStudList = [];
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		var startDate = new Date($('#startdatepayment').val());
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
				'adminCollect' : $scope.adminCollect,
				'paytmCollect' : $scope.paytmCollect,
				'onlineStud' : $scope.onlineStud,
				'OfflineStud' : $scope.OfflineStud,
				'enddate' : $scope.enddate
		}

		var my_url = url_getStudentPayment+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.NewStudList = response.list;
	     		$scope.TotalAmt = response.TotalAmt;
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


	$scope.downloadStudentPdf = function()
	{	
		var startDate = new Date($('#startdatepayment').val());
		var endDate = new Date($('#enddate').val());
		if(endDate < startDate)
        {
            alert('End date should be greater than start date');
            return false;
        }
		$scope.param = {
			'searchtext' : $scope.searchtext,
				'mailStatus' : $scope.mailStatus,
				'searchmail' : $scope.searchmail,
				'searchmobile' : $scope.searchmobile,
				'startdate' : $scope.startdate,
				'enddate' : $scope.enddate
		}
    	$rootScope.loading = true;
    	
		var my_url = url_studlistpdf+$.param($scope.param);
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	$rootScope.loading = false;
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
	$("#startdatepayment").datetimepicker({format: 'Y-m-d',timepicker:false,scrollMonth : false});
	$("#enddate").datetimepicker({format: 'Y-m-d',timepicker:false, scrollMonth : false});
	$scope.adddate = function(type)
	{
		if (type == 'startdate') {
			var sdate = $("#startdatepayment").val();
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
			var sdate = $("#startdatepayment").val();
			if(sdate == null || sdate == '')
			{
				$("#startdatepayment").val(edate);
				$scope.startdate = edate;
			}
		}
	}
	
	
})
.controller('vidStudentRankCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){

	$scope.selection = [];
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.email = $cookies.get('email');
	$rootScope.loading = true;
	$scope.examList = [];
	$scope.selectSchedule = '';

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
	
	$scope.doTheBack = function() {
	  window.history.back();
	};  
	$scope.pageChange = function(currentPage) {
		$scope.currentPage = currentPage;
	};
	$scope.reset= function(){
		$scope.selectSchedule = '';
	}
	$scope.getExamList = function(){
		$scope.examList = [];
		$scope.selectSchedule = '';
		var my_url = url_getExamForRankList;	
			$rootScope.loading = true;
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		    	
		     	$rootScope.loading = false;
		     	if(response.status == 200)
		     	{
		     		$scope.examList = response.examList;
		     		console.log($scope.examList);
				}
				else{
					$scope.getStudList();
					$scope.examList = [];
					$("#tablemessage").addClass('has-success');
					$scope.tablemessage = "Students are not available.";
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	}
	$scope.scheduleList = [];
	$scope.getExamSchedule = function(){
		
		$scope.param = {
			'selectExam' : $scope.selectExam
		}

		var my_url = url_getScheduleList+$.param($scope.param);	
			$rootScope.loading = true;
			$http({
		       method : 'GET',
		       url : my_url,
		       headers : {'authcode': $scope.authcode}
		    }).success(function(response){
		     		console.log(response);
		    	
		     	$rootScope.loading = false;
		     	if(response.status == 200)
		     	{
		     		$scope.scheduleList = response.scheduleList;
		     		$scope.getStudList();
				}
				else{
					$scope.studRank = [];
					$scope.scheduleList = [];
					$("#tablemessage").addClass('has-success');
					$scope.tablemessage = "Students are not available.";
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	}
	//get getNewStud list
	$scope.getStudList = function(){
    	//alert($scope.paytmCollect);
    	
		$scope.studRank = [];
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'selectExam' : $scope.selectExam,
			'selectSchedule' : $scope.selectSchedule
		}
		var my_url = url_getStudRankList+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.studRank = response.studRank;
	     		
			}
			else{
				$scope.studRank = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Students are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	
})
.controller('vidSubscribeManageCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){

	$scope.selection = [];
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.email = $cookies.get('email');
	$rootScope.loading = true;

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
	
	$scope.doTheBack = function() {
	  window.history.back();
	};  
	$scope.pageChange = function(currentPage) {
		$scope.currentPage = currentPage;
	};

	
	//get getNewStud list
	$scope.getSubList = function(){
    	//alert($scope.paytmCollect);
    	$scope.TotalAmt = 0;
		//$scope.NewStudList = [];
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		
		$scope.param = {
				'searchtext' : $scope.searchtext,
				'mailStatus' : $scope.mailStatus,
				'startdate' : $scope.startdate,
				'adminCollect' : $scope.adminCollect,
				'paytmCollect' : $scope.paytmCollect,
				'enddate' : $scope.enddate
		}

		var my_url = url_getSubscribeList;	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.NewStudList = response.list;
	     		$scope.TotalAmt = response.TotalAmt;
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
	
})
.controller('vidTestimonialManageCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){

	$scope.selection = [];
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.email = $cookies.get('email');
	$rootScope.loading = true;

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
	
	$scope.doTheBack = function() {
	  window.history.back();
	};  
	$scope.pageChange = function(currentPage) {
		$scope.currentPage = currentPage;
	};

	$scope.activateStatus = function(testiId,finalstatus)
	
	{	
		//console.log(isShow);
		//$scope.finaldata.testiId = testiId;
		//$scope.finaldata.finalstatus = finalstatus;
		$scope.finaldata = {
			'testiId' : testiId,
			'finalstatus' : finalstatus
		}
		//alert(finalstatus);
		$rootScope.loading = true;
		$http({
		     method : "PUT",
		     url : url_testimonialStatusChange,
		     data: $.param($scope.finaldata),
		     headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode,'Process-Data': false}
		}).success(function(response){
			//console.log(response);
			$rootScope.loading = false;
		     if(response.status == 200){
		     	$scope.NewStudList = [];
				$scope.getTestimonialList();
		     }
		     else{
			 	$("#paramessage").addClass('has-error');
				$scope.paramessage = response.message;
			 }
			
		}).error(function(error){
		     $rootScope.loading = false;
		     $("#paramessage").addClass('has-error');
			 $scope.paramessage = "Some unknown error has occurred. Please try again.";
		});
		
				//getQuestion();
		//$window.location.reload();
	}
	//get getNewStud list
	$scope.getTestimonialList = function(){
    	//alert($scope.paytmCollect);
    	$scope.TotalAmt = 0;
		//$scope.NewStudList = [];
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		
		
		var my_url = url_getTestimonialList;	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.NewStudList = response.list;
	     		$scope.TotalAmt = response.TotalAmt;
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
	
})
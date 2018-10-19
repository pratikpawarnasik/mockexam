angular.module('ngApp.courseCtrl', [])

.controller('vidManageCourseCtrl', function($scope,$interval,$compile,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$scope.categoryArr = [];
	$scope.courseArr = [];
	$scope.selection = [];
	$scope.courseDet = {};
	$scope.courseMonthFee = [];
	$scope.action = "add";
	$scope.formname = "Add New Course";
	$scope.edit = false;
	$scope.add = true;
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$rootScope.loading = true;
	
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    
    $scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
   		 }, 36000);
   	$scope.month1 = '';
   	$scope.month2 = '';
   	$scope.doTheBack = function() {
	  window.history.back();
	};

   	$scope.displayBlock = function(id)
   	{
		$("#displayDiv").empty();
		//$scope.courseMonthFee = [];
		var html = '<div class="row">';
		$month = "Month";
		for(i=0;i<id;i++)
		{	
		if(i != 0) 	$month = "Months";
			html += "<div class='col-md-2 col-sm-3 col-xs-6'><label>"+(i+1)+" "+$month+" Fee :  </label><span class='mandatory'>*</span>";
			html += " <input type='text' id='fee"+i+"' class='dashboard_text' name='mfee"+i+"' ng-model='courseMonthFee["+i+"]' ng-change='isNumeric("+i+")' required-message=\"'Please enter "+(i+1)+" "+$month+" fee.'\" validate-on='dirty' required='true' autocomplete='off' > </div>";
		}
		html += '</div>';
		//$("#displayDiv").append(html);
		angular.element(document.getElementById('displayDiv')).append($compile(html)($scope));
		$scope.isNumeric = function(i)
		{
			var val = $('#fee'+i).val();
			var numbers = /^[0-9]+$/;  
		      if(val.match(numbers))  
		      { 
			    //val = val.replace(/[^0-9\.]/g,'');
			    if(val <= 0 || val > 999999)
			    {
					alert("Invalid Amount");
					$('#fee'+i).val('');
				}
			  }
			  else{
			  		alert("Invalid Amount");
					$('#fee'+i).val('');
			  }
		} 
	}
	
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
		$scope.formname = "Add New Course";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.courseMonthFee = [];
		$scope.id = '';
		$scope.courseDet = {};
		$scope.courseForm.reset();
	 	$scope.courseDet.level = "2";
		$("#add_new_course").modal('show');
	}
	
	//get category
	$scope.getCategory = function(){
		$scope.param = {
		//	'instid' : $scope.instid,
			'userid' : $scope.userid
		}
		var my_url = url_getcategory+$.param($scope.param);	
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	if(response.status == 200)
	     	{
	     		$scope.categoryArr = response.category;
			}
			else{
				$scope.categoryArr = [];
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	
	//get course list 
	$scope.getCourse = function()
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		
		$scope.param = {
		//	'instid' : $scope.instid,
			'userid' : $scope.userid
		}
		var my_url = url_getcourse+$.param($scope.param);
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
							$("#tablemessage").addClass('has-success');
							$scope.tablemessage = "Courses are not available.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	CKEDITOR.replace('courseDesc', {height: 120});
	//add course		
	$scope.submitCreateCourse = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "course adding...";
			$scope.courseDet.desc = CKEDITOR.instances.courseDesc.getData();
			//console.log($scope.courseDet.desc);
			var my_url = url_addcourse;
			var method = "POST";
			var dispmessage = "Course added successfully.";
			if($scope.action == "edit")
			{
				my_url = url_updatecourse;
				method = "PUT";
				$scope.message = "course editing...";
				$scope.courseDet.id = $scope.id;
				var dispmessage = "Course updated successfully.";
				
			}
			$scope.courseDet.userid = $scope.userid;
			$scope.courseDet.courseMonthFee = $scope.courseMonthFee;
			//console.log($scope.courseDet);
			$rootScope.loading = true;
			$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.courseDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		CKEDITOR.instances.courseDesc.setData('');
		            		$scope.getCourse();
		            		if($scope.action == "add")
							{

								$scope.courseMonthFee = [];
		            			$scope.courseDet = {};

								$scope.courseForm.reset();
								$scope.courseDet.level = "1";
		            		}
		            		$("#add_new_course").modal('hide');
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
	
	//edit course	           
	$scope.editCourse = function(id)     
	{
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.courseDet.desc = CKEDITOR.instances.courseDesc.getData();

		$scope.message = '';
			$scope.id = id;
			$scope.action = "edit";
			$scope.formname = "Edit Course";
			$scope.edit = true;
			$scope.add = false;
			$scope.param = {
				'id' : $scope.id,
				'userid' : $scope.userid
			}
		var my_url = url_getcoursebyid+$.param($scope.param);
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
		            		$scope.courseDet = {};
							$scope.courseForm.reset();
							
		            		$scope.courseDet.name = response.name;
		            		$scope.courseDet.duration = response.duration;
		            		$scope.courseDet.category = response.category;
		            		//$scope.courseDet.desc = response.desc;
		            		$scope.courseDet.level = response.level;
		            		$("#add_new_course").modal('show');
		            		$scope.courseDet.fee = response.fee;
		            		$scope.courseDet.syllabus = response.syllabus;		            		
		            		CKEDITOR.instances.courseDesc.setData(response.desc); 
		            		$scope.courseMonthFee = [];
		            		
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
	  
	//delete single course
	$scope.deleteCourse = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete course?');
		if(deleteUser)
		{
			$scope.getmessage = "delete...";
			$scope.param = {
				'id' : id,
				'userid' : $scope.userid
			}
			var my_url = url_deletecourse;
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
			            		$scope.getCourse();
			            		$("#getmessage").addClass('has-success');
			            		$scope.getmessage = "Course deleted successfully.";
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
	
	// toggle selection
	$scope.toggleSelection = function toggleSelection(id) {
			 var id = parseInt(id);
	 		 var idx = $scope.selection.indexOf(id);
			 // is currently selected
		    if (idx > -1) {
		      $scope.selection.splice(idx, 1);
		    }
		    // is newly selected
		    else {
		      $scope.selection.push(parseInt(id));
		    }
	};
		  
	//delete multiple course
	$scope.delSelectCourse = function()
	{
		$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		 var length = $scope.selection.length;
		 if(length > 0)
		 {
		 	var deleteUser = $window.confirm('Are you sure to delete courses?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'ids' : $scope.selection,
					'userid' : $scope.userid
				}
				var my_url = url_deletemultiplecourse;
				$rootScope.loading = true;
				$http({
				              method : 'DELETE',
				              url : my_url,
				              data : $.param($scope.param),
				              headers : {'authcode': $scope.authcode}
				           }).success(function(response){
				           		$rootScope.loading = false;
				           		$scope.selection = [];
				            	if(response.status == 200)
				            	{
				            		$scope.getCourse();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Courses deleted successfully.";
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
		    $scope.getmessage = "Please select atleast one course";
		 }
	}
	
	$("#select_courses").change(function(){
	    	$(".courses").prop('checked', $(this).prop("checked"));
	    	if($scope.allselect)
	    	{
				$scope.selection = [];
		    	angular.forEach($scope.courseArr, function(course) {
		    		$scope.selection.push(parseInt(course.id));
				});
			}
			else{
				$scope.selection = [];
			}
	    	
	});
	
})

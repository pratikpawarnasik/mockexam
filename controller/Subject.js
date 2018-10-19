
angular.module('ngApp.subjectCtrl', [])

.controller('vidManageSubjectCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$scope.subjectArr = [];
	$scope.selection = [];
	$scope.subjectDet = {};
	$scope.action = "add";
	$scope.colname = "Course";
	$scope.formname = "Add New Subject";
	$scope.edit = false;
	$scope.add = true;
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$rootScope.loading = true;
	$scope.doTheBack = function() {
	  window.history.back();
	};
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    $scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
   		 }, 36000);
   		 
    $scope.addid = $stateParams.id;
    $scope.name = $stateParams.name;
    if($scope.courseid == '')
    {
		$window.location.href = $rootScope.base_url+'mastercourse';
	}
	
	$scope.check = function(my_url,content)
	{
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
		            		$scope.coursename = response.name+content;
							if($scope.addid != response.id)
							{
								$window.location.href = $rootScope.base_url+'mastercourse';
							} 
						}
						else{
							$window.location.href = $rootScope.base_url+'mastercourse';
							$("#message").addClass('has-error');
							$scope.message = response.message;
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#message").addClass('has-error');
						$scope.message = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	if($scope.name == 'course')
	{
		$scope.colname = "Course";
		$scope.courseid = $scope.addid;
		$scope.levelid = null;
		var content = " (course)";
		$scope.param = {
			'id' : $scope.addid,
			'userid' : $scope.userid
		}
		var my_url = url_getsubjectcourse+$.param($scope.param);
		$scope.check(my_url,content);
	}
	else if($scope.name == 'level')
	{
		$scope.colname = "Level";
		$scope.courseid = null;
		$scope.levelid = $scope.addid;
		var content = " (level)";
		$scope.param = {
			'id' : $scope.addid,
			'userid' : $scope.userid
		}
		var my_url = url_getsubjectlevel+$.param($scope.param);
		$scope.check(my_url,content);
	}
	else{
		$window.location.href = $rootScope.base_url+'mastercourse';
	}
	
	//model open
	$scope.openModal = function()
	{
		$scope.action = "add";
		$scope.formname = "Add New Subject";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.subjectDet = {};
		$scope.subjectForm.reset();
		$("#add_new_subject").modal('show');
	}
	
	
	//get subject
	$scope.getSubject = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'courseid' : $scope.courseid,
			'levelid' : $scope.levelid,
			'userid' : $scope.userid
		}
		var my_url = url_getsubject+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.subjectArr = response.subject;
			}
			else{
				$scope.subjectArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Subject are either deleted or not inserted.";
			}
	    }).error(function(error){
	    		$rootScope.loading = false;
	    		$("#getmessage").addClass('has-error');
				$scope.getmessage = "Some unknown error has occurred. Please try again.";
	    });
	 }
	
	
	//add subject		
	$scope.submitCreateSubject = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "subject adding...";
			var my_url = url_addsubject;
			var method = "POST";
			var dispmessage = "Subject added successfully.";
			if($scope.action == "edit")
			{
				my_url = url_updatesubject;
				method = "PUT";
				$scope.message = "subject editing...";
				$scope.subjectDet.id = $scope.id;
				var dispmessage = "Subject updated successfully.";
			}
			
			$scope.subjectDet.courseid = $scope.courseid;
			$scope.subjectDet.levelid = $scope.levelid;
			$scope.subjectDet.userid = $scope.userid;
				$rootScope.loading = true;
				$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.subjectDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.getSubject();
		            		if($scope.action == "add")
							{
		            			$scope.subjectDet = {};
								$scope.subjectForm.reset();
		            		}
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
	
	//edit subject	           
	$scope.editSubject = function(id)     
	{
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
			$scope.id = id;
			$scope.action = "edit";
			$scope.formname = "Edit New Subject";
			$scope.edit = true;
			$scope.add = false;
			$scope.param = {
				'id' : $scope.id,
				'userid' : $scope.userid
			}
		var my_url = url_getsubjectbyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.subjectDet = {};
							$scope.subjectForm.reset();
							$("#add_new_subject").modal('show');
		            		$scope.subjectDet.name = response.name;
		            		$scope.subjectDet.desc = response.desc;
		            		//$scope.subjectDet.weightage = response.weightage;
		            		$scope.subjectDet.courseid = response.courseid;
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
	
	//delete single subject
	$scope.deleteSubject = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete subject?');
		if(deleteUser)
		{
			$scope.getmessage = "delete...";
			$scope.param = {
				'id' : id,
				'userid' : $scope.userid
			}
			var my_url = url_deletesubject;
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
			            		$scope.getSubject();
			            		$("#getmessage").addClass('has-success');
			            		$scope.getmessage = "Subject deleted successfully.";
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
		  
	//delete multiple subject
	$scope.delSelectSubject = function()
	{
		$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		 var length = $scope.selection.length;
		 if(length > 0)
		 {
		 	var deleteUser = $window.confirm('Are you sure to delete subjects?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'ids' : $scope.selection,
					'userid' : $scope.userid
				}
				var my_url = url_deletemultiplesubject;
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
				            		$scope.getSubject();
				            		$scope.selection = [];
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Subject deleted successfully.";
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
		    $scope.getmessage = "Please select atleast one subject";
		 }
	}
	
	$("#select_subject").change(function(){
	    	$(".subject").prop('checked', $(this).prop("checked"));
	    	if($scope.allselect)
	    	{
				$scope.selection = [];
		    	angular.forEach($scope.subjectArr, function(subject) {
		    		$scope.selection.push(parseInt(subject.id));
				});
			}
			else{
				$scope.selection = [];
			}
	    	
	});
	
})

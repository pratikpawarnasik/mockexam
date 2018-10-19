angular.module('ngApp.subjectGroupCtrl', [])

.controller('vidManageSubjectGroupCtrl', function($scope,$interval,$compile,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$scope.courseArr = [];
	$scope.subjectGroupArr = [];
	$scope.selection = [];
	$scope.subjectGroupDet = {};
	$scope.subjectArr=[];
	$scope.subjectGroupDet.subject=[];
	$scope.subjectGroupDet.subject_check=[];
	$scope.action = "add";
	$scope.formname = "Add New Subject Group";
	$scope.edit = false;
	$scope.add = true;
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$rootScope.loading = true;
	
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    
    $scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
   		 }, 36000);
/*   	$scope.month1 = '';
   	$scope.month2 = '';*/
	
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
	
//model open add group subject modal / edit group subject modal
$scope.openModal = function()
	{
		$scope.subjectGroupDet.subject=[];
	    $scope.subjectGroupDet.subject_check=[];
		$scope.action = "add";
		$scope.formname = "Add New Subject Group";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.courseMonthFee = [];
		$scope.id = '';
		$scope.courseDet = {};
		$scope.subjectGroupForm.reset();
	 	$scope.courseDet.level = "2";
		$("#add_new_subjectgroup").modal('show');
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
							$scope.tablemessage = "Course are either deleted or not inserted.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
//get subject
$scope.getSubject = function(){
	if(typeof $scope.subjectGroupDet.course_id != 'undefined' && $scope.subjectGroupDet.course_id != ''){
	    $('#append_subject').hide();
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'courseid' : $scope.subjectGroupDet.course_id,
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
	     		$scope.subjectArr = [];
	     		$scope.subjectGroupDet.subject_check=[];
	     		$scope.subjectArr = response.subject;
	     		if ($scope.gtNewData == 'show') {
	     			$scope.gtNewData = 'hide';
		     		if($scope.subjectGroupDet.subject.length > 0){
		     			for(var i=0;i<$scope.subjectGroupDet.subject.length;i++){
	                      for(var j=0;j<$scope.subjectArr.length;j++){
	                      	if($scope.subjectArr[j].id == $scope.subjectGroupDet.subject[i]){
		                           $scope.subjectGroupDet.subject_check[j]=true;
	                      	}
	                       }
		     			}
		     		}else{
		     			$scope.subjectGroupDet.subject=[];
		     			$scope.subjectGroupDet.subject_check=[];
		     		}
	     		}else{
		     		$scope.subjectGroupDet.subject=[];
		     		$scope.subjectGroupDet.subject_check=[];
	     		}

	     		$('#append_subject').show();
			}
			else{
				//$scope.subjectArr = [];
				$("#getmessage").addClass('has-error');
				$scope.getmessage = "Subject is either deleted or not inserted.";
			}
	    }).error(function(error){
	    		$rootScope.loading = false;
	    		$("#getmessage").addClass('has-error');
				$scope.getmessage = "Some unknown error has occurred. Please try again.";
	    });
		}else{
			$scope.subjectArr=[];
		}
	 }
$scope.check_subject = function(index,id)
	{
		if($scope.subjectGroupDet.subject_check[index]==true){
			$scope.subjectGroupDet.subject.push(id);
		}else{
             $scope.subjectGroupDet.subject_check.splice(index, 1);
			 var index_id = $scope.subjectGroupDet.subject.indexOf(id);
             $scope.subjectGroupDet.subject.splice(index_id, 1);
		}
	}
//get Subject Group list 
$scope.getSubjectGrouplist = function()
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
		            		$scope.subjectGroupArr = response.subjectgroup;
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
	
//add Subject Group		
$scope.submitCreateSubjectGroup = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "Subject group adding...";
			var my_url = url_addsubjectgroup;
			var method = "POST";
			var dispmessage = "Subject group added successfully.";
			if($scope.action == "edit")
			{
				my_url = url_updatesubjectgroup;
				method = "PUT";
				$scope.message = "Subject group editing...";
				$scope.subjectGroupDet.id = $scope.id;
				var dispmessage = "Subject group updated successfully.";
				
			}
			$scope.subjectGroupDet.userid = $scope.userid;
			//console.log($scope.subjectGroupDet);
			$rootScope.loading = true;
			$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.subjectGroupDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.getSubjectGrouplist();
		            		if($scope.action == "add")
							{
		            			$scope.subjectGroupDet = {};
								$scope.subjectGroupForm.reset();
								$scope.subjectGroupDet.subject=[];
								$scope.subjectGroupDet.subject_check=[];
								$scope.subjectArr=[];
		            		}
		            		if($scope.action == "edit")
							{
								
								//console.log(response);
								$scope.subjectGroupDet.subject_check=[];
			            		$scope.subjectGroupDet.sub_group_name=response.subject_group_name;
			            		$scope.subjectGroupDet.course_id=response.course_id;
			            		$scope.subjectGroupDet.subject=response.subjectids;
			            	   if($scope.subjectGroupDet.subject.length > 0){
					     			for(var i=0;i<$scope.subjectGroupDet.subject.length;i++){
				                      for(var j=0;j<$scope.subjectArr.length;j++){
				                      	if($scope.subjectArr[j].id == $scope.subjectGroupDet.subject[i]){
					                           $scope.subjectGroupDet.subject_check[j]=true;
				                      	}
				                       }
					     			}
					     		}
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
	
//Update Subject Group	           
$scope.editSubjectGroup = function(id)     
	{
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = id;
		$scope.action = "edit";
		$scope.formname = "Edit Subject Group";
		$scope.edit = true;
		$scope.add = false;
		$scope.param = {
			'id' : $scope.id,
			'userid' : $scope.userid
		}
		var my_url = url_getsubjectgroupbyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{	   $scope.gtNewData = 'show';
		            		$scope.subjectGroupDet = {};
							$scope.subjectGroupForm.reset();         		
		            		$scope.subjectGroupDet.sub_group_name=response.subject_group_name;
		            		$scope.subjectGroupDet.course_id=response.course_id;
		            		$scope.subjectGroupDet.subject=response.subjectids;
		            		$scope.getSubject();
		            		$("#add_new_subjectgroup").modal('show');
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
	
//delete single subject group
$scope.deleteSubjectGroup = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete subject group?');
		if(deleteUser)
		{
			$scope.getmessage = "delete...";
			$scope.param = {
				'id' : id,
				'userid' : $scope.userid
			}
			var my_url = url_deletesubjectgroup;
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
			            		$scope.getSubjectGrouplist();
			            		$("#getmessage").addClass('has-success');
			            		$scope.getmessage = "Subject Group deleted successfully.";
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
})

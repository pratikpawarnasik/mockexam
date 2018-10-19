
angular.module('ngApp.chapterCtrl', [])

.controller('vidManageChapterCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$scope.chapterArr = [];
	$scope.courseArr = [];
	$scope.selection = [];
	$scope.chapterDet = {};
	$scope.action = "add";
	$scope.formname = "Add New Chapter";
	$scope.edit = false;
	$scope.add = true;
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.instid = $cookies.get('instid');
	$scope.colname = "Course";
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
	/*else{
		
		$scope.param = {
			'id' : $scope.addid,
			'userid' : $scope.userid
		}
		var my_url = url_getchaptercourse+$.param($scope.param);
		
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
		           
	}*/
	
	$scope.check = function(my_url,content)
	{
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	//console.log(response);
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
		           		$("#message").addClass('has-error');
						$scope.message = error;
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
		var my_url = url_getchaptercourse+$.param($scope.param);
		$scope.check(my_url,content);
	}
	else if($scope.name == 'level')
	{
		$scope.colname = "Level";
		$scope.courseid = null;
		$scope.subjectid = null;
		$scope.levelid = $scope.addid;
		var content = " (level)";
		$scope.param = {
			'id' : $scope.addid,
			'userid' : $scope.userid
		}
		var my_url = url_getchapterlevel+$.param($scope.param);
		$scope.check(my_url,content);
	}
	else if($scope.name == 'subject')
	{
		$scope.colname = "Subject";
		$scope.courseid = null;
		$scope.levelid = null;
		$scope.subjectid = $scope.addid;
		var content = " (subject)";
		$scope.param = {
			'id' : $scope.addid,
			'userid' : $scope.userid
		}
		var my_url = url_getchaptersubject+$.param($scope.param);
		$scope.check(my_url,content);
	}
	else{
		$window.location.href = $rootScope.base_url+'mastercourse';
	}
	
	//model open
	$scope.openModal = function()
	{
		$scope.action = "add";
		$scope.formname = "Add New Chapter";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.chapterDet = {};
		$scope.chapterForm.reset();
		$scope.chapterDet.topic = "0";
		$("#add_new_chapter").modal('show');
	}
	
	
	//get chapter
	$scope.getChapter = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'courseid' : $scope.courseid,
			'subjectid' : $scope.subjectid,
			'levelid' : $scope.levelid,
			'userid' : $scope.userid
		}
		var my_url = url_getchapter+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	    	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.chapterArr = response.chapter;
			}
			else{
				$scope.chapterArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Chapter are either deleted or not inserted.";
			}
	    }).error(function(error){
	    		$rootScope.loading = false;
	    		$("#getmessage").addClass('has-error');
				$scope.getmessage = "Some unknown error has occurred. Please try again.";
	    });
	 }
	
	//add chapter		
	$scope.submitCreateChapter = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "chapter adding...";
			var my_url = url_addchapter;
			var method = "POST";
			var dispmessage = "Chapter added successfully.";
			if($scope.action == "edit")
			{
				my_url = url_updatechapter;
				method = "PUT";
				$scope.message = "chapter editing...";
				$scope.chapterDet.id = $scope.id;
				var dispmessage = "Chapter updated successfully.";
			}
			
			$scope.chapterDet.courseid = $scope.courseid;
			$scope.chapterDet.levelid = $scope.levelid;
			$scope.chapterDet.subjectid = $scope.subjectid;
			$scope.chapterDet.userid = $scope.userid;
			$rootScope.loading = true;
				$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.chapterDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.getChapter();
		            		if($scope.action == "add")
							{
		            			$scope.chapterDet = {};
								$scope.chapterForm.reset();
								$scope.chapterDet.topic = "0";
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
	
	//edit chapter	           
	$scope.editChapter = function(id)     
	{
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
			$scope.id = id;
			$scope.action = "edit";
			$scope.formname = "Edit New Chapter";
			$scope.edit = true;
			$scope.add = false;
			$scope.param = {
				'id' : $scope.id,
				'userid' : $scope.userid
			}
		var my_url = url_getchapterbyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.chapterDet = {};
							$scope.chapterForm.reset();
							$("#add_new_chapter").modal('show');
		            		$scope.chapterDet.name = response.name;
		            		$scope.chapterDet.desc = response.desc;
		            		$scope.chapterDet.topic = response.topic;
		            		//$scope.chapterDet.weightage = response.weightage;
		            		$scope.chapterDet.courseid = response.courseid;
						}
						else{
							$("#getmessage").addClass('has-error');
							$scope.getmessage = response.message;
						}
		           }).error(function(error){
		           	alert('in error response');

		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	//delete single chapter
	$scope.deleteChapter = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete chapter?');
		if(deleteUser)
		{
			$scope.getmessage = "delete...";
			$scope.param = {
				'id' : id,
				'userid' : $scope.userid
			}
			var my_url = url_deletechapter;
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
			            		$scope.getChapter();
			            		$("#getmessage").addClass('has-success');
			            		$scope.getmessage = "Chapter deleted successfully.";
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
		  
	//delete multiple chapter
	$scope.delSelectChapter = function()
	{
		$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		 var length = $scope.selection.length;
		 if(length > 0)
		 {
		 	var deleteUser = $window.confirm('Are you sure to delete chapters?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'ids' : $scope.selection,
					'userid' : $scope.userid
				}
				var my_url = url_deletemultiplechapter;
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
				            		$scope.getChapter();
				            		$scope.selection = [];
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Chapter deleted successfully";
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
		    $scope.getmessage = "Please select atleast one chapter";
		 }
	}
	
	$("#select_chapter").change(function(){
	    	$(".chapter").prop('checked', $(this).prop("checked"));
	    	if($scope.allselect)
	    	{
				$scope.selection = [];
		    	angular.forEach($scope.chapterArr, function(chapter) {
		    		$scope.selection.push(parseInt(chapter.id));
				});
			}
			else{
				$scope.selection = [];
			}
	    	
	});
	
})

angular.module('ngApp.notesCtrl', [])
.controller('vidManageNotesCtrl', function($scope,$interval,$cookies,$state,$stateParams,$rootScope,$window,$http,$location){
	$scope.notesArr = [];
	$scope.selection = [];
	$scope.courseArr = [];
	$scope.courseHArr = [];
	$scope.courseSArr = [];
	$scope.notesDet = {};
	$scope.action = "add";
	$scope.formname = "Add New Notes";
	$scope.edit = false;
	$scope.add = true;
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$rootScope.loading = false;

	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    $scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
   		 }, 36000);
   		 
    //model open
	$scope.openModal = function()
	{
		$scope.action = "add";
		$scope.formname = "Add New Notes";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.notesDet = {};
		$scope.notesForm.reset();
		$("#add_new_notes").modal('show');
	}
	//get course
	$scope.getCourse = function(){
		
		$rootScope.loading = true;	
		var my_url = url_getcoursedash;	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		console.log(response);
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
	

	$scope.courseArr = [];

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
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
			'userid' : $scope.userid,
			'courseid' : $scope.searchcourse,
			'searchtext' : $scope.searchtext,
			'searchtype' : $scope.searchtype
		}
		var my_url = url_getnotes+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.notesArr = response.notes;
			}
			else{
				$scope.notesArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Notes are not available.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	

    function upddoc(input) {
    	var fileTypes = ['jpeg','jpg','png','pdf','docx','txt','xls','xlsx','ppt','pptx']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if (isSuccess) { 
				if(input.files[0].size <= 15048576)
 				{
				}
		        else{
					alert("File size is less than 15 MB");
					$scope.upddoc = null;
					$("#upload_doc").empty();
					document.getElementById('upddoc').value = null;
				}
            }
            else{
            	alert("please select only jpg,jpeg,png,pdf,docx,txt,xls,xlsx files");
				$scope.upddoc = null;
				$("#upload_doc").empty();
				document.getElementById('upddoc').value = null;
			}
        }
    }
    
  
    $("#upddoc").change(function(){
        upddoc(this);
    });
    
    $scope.setType = function(id)
    {
		for(var i=0;i<$scope.courseHArr.length;i++)
		{
			if($scope.courseHArr[i]['id'] == id)
			{
				$scope.notesDet.type = $scope.courseHArr[i]['type'];
			}
		}
	}
    
	//add notes		
	$scope.submitCreateNotes = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "";
			
		var fd = new FormData();
       	if($scope.upddoc == null && ($scope.notesDet.url == null || $scope.notesDet.url == ''))
		{
				$("#message").addClass('has-error');
				$scope.message = "please upload pdf document.";
				return false;
		}
			
			fd.append('docfile', $scope.upddoc);
        fd.append('userid', $scope.userid);
        fd.append('courseid', $scope.notesDet.course);
        
        fd.append('title', $scope.notesDet.title);
        fd.append('url', $scope.notesDet.url);
        
       $rootScope.loading = true;
       	$http.post(url_addnotes, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined,'authcode':$scope.authcode,'Process-Data': false}
        })
        .success(function(response){
        	$rootScope.loading = false;
        	 if(response.status == 200)
		    {
	        	$scope.getNotes();
	        	$scope.upddoc = null;
	        	$("#upload_doc").empty();
				document.getElementById('upddoc').value = null;
				$scope.notesDet = {};
				$scope.notesForm.reset();
				$scope.courseHArr = [];
				$("#message").addClass('has-success');
				$scope.message = "Notes added successfully.";
			}
			else{
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
			
        })
        .error(function(error){
        	$rootScope.loading = false;
        	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
        });
	}
	
	
	
	//delete single notes
	$scope.deleteNotes = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete notes?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'id' : id,
					'userid' : $scope.userid
				}
				var my_url = url_deletenotes;
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
				            		$scope.getNotes();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Notes deleted successfully.";
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
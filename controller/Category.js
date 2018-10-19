
angular.module('ngApp.categoryCtrl', [])

.controller('vidManageCategoryCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){
	$scope.categoryArr = [];
	$scope.selection = [];
	$scope.categoryDet = {};
	$scope.action = "add";
	$scope.formname = "Add New Category";
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
		$scope.formname = "Add New Category";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
		$scope.id = '';
		$scope.categoryDet = {};
		$scope.categoryForm.reset();
		$("#add_new_category").modal('show');
	}
		
	//get category list
	$scope.getCategory = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
		$scope.getmessage = '';
		$scope.param = {
		//	'instid' : $scope.instid,
			'userid' : $scope.userid
		}
		var my_url = url_getcategory+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	if(response.status == 200)
	     	{
	     		$scope.categoryArr = response.category;
			}
			else{
				$scope.categoryArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Category are either deleted or not inserted.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	
	//add category		
	$scope.submitCreateCategory = function()
	{
			$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "category adding...";
			var my_url = url_addcategory;
			var method = "POST";
			var dispmessage = "Category added successfully.";
			if($scope.action == "edit")
			{
				my_url = url_updatecategory;
				method = "PUT";
				$scope.message = "category editing...";
				$scope.categoryDet.id = $scope.id;
				var dispmessage = "Category updated successfully.";
			}
			
			$scope.categoryDet.instid = $scope.instid;
			$scope.categoryDet.userid = $scope.userid;
			$rootScope.loading = true;
				$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.categoryDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.getCategory();
		            		if($scope.action == "add")
							{
		            			$scope.categoryDet = {};
								$scope.categoryForm.reset();
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
	
	//edit category           
	$scope.editCategory = function(id)     
	{
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$scope.message = '';
			$scope.id = id;
			$scope.action = "edit";
			$scope.formname = "Edit New Category";
			$scope.edit = true;
			$scope.add = false;
			$scope.param = {
				'id' : $scope.id,
				'userid' : $scope.userid
			}
		var my_url = url_getcategorybyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.categoryDet = {};
							$scope.categoryForm.reset();
							$("#add_new_category").modal('show');
		            		$scope.categoryDet.name = response.name;
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
		
	//delete single category
	$scope.deleteCategory = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete category?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'id' : id,
					'userid' : $scope.userid
				}
				var my_url = url_deletecategory;
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
				            		$scope.getCategory();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Category deleted successfully.";
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
		      //console.log($scope.selection);
		    }
		    // is newly selected
		    else {
		      $scope.selection.push(parseInt(id));
		      //console.log($scope.selection);
		    }
	};
		  
	//delete multiple category
	$scope.delSelectCategory = function()
	{
		$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		 var length = $scope.selection.length;
		 if(length > 0)
		 {
		 	var deleteUser = $window.confirm('Are you sure to delete category?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'ids' : $scope.selection,
					'userid' : $scope.userid
				}
				var my_url = url_deletemultiplecategory;
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
				            		$scope.getCategory();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Category deleted successfully.";
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
		    $scope.getmessage = "Please select atleast one category.";
		 }
	}
	
	$("#select_categorys").change(function(){
	    	$(".categories").prop('checked', $(this).prop("checked"));
	    	if($scope.allselect)
	    	{
				$scope.selection = [];
		    	angular.forEach($scope.categoryArr, function(category) {
		    		$scope.selection.push(parseInt(category.id));
				});
				//console.log($scope.selection);
			}
			else{
				$scope.selection = [];
				//console.log($scope.selection);
			}
	    	
	});
	
})

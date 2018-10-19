
angular.module('ngApp.extraCtrl', [])

.controller('vidExtraCtrl', function($scope,$interval,$cookies,$stateParams,$rootScope,$window,$http,$location){

	$rootScope.loading = false;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.email = $cookies.get('email');
	$scope.type = $cookies.get('type');

	$('#quote-carousel').carousel({
			pause: true, 
			interval: 10000,
	});
	$rootScope.loading = false;
	$scope.authcode = $cookies.get('authcode');
		$scope.portal = $rootScope.portal;
		$scope.subdomain = $rootScope.subdomain;
		$scope.cover = '';
	/**header fixed */
	setTimeout(function(){ 
	var header_ht = $('.page_header').height();
	$('#search_input').css('top',header_ht);
	var bg_ht = $('.banner_content').height();
	$('.banner_background').css('padding-top',bg_ht);
	}, 1000);

	$(window).scroll(function() {
	var header_ht = $('.page_header').height();
	$('#search_input').css('top',header_ht);
	});

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

	//get QueryList
	$scope.getQueryList = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.tablemessage = '';
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
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "No doubts from student.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	$scope.SolvedDoubt = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$scope.getmessage = '';
		var deleteUser = $window.confirm('Are you sure to delete exam?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'id' : id
				}
				var my_url = url_solvedoubt;
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
				            		$scope.getQueryList();
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
})

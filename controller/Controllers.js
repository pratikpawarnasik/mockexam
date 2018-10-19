angular.module('ngApp.controllers', ['angularValidator'])
.controller('vidHeaderCtrl', function($scope,$interval,$state,md5,$cookies,ServerService,$rootScope,$window,$http,$location,$compile){
		//$('body').trigger('click');
		//$(window).scrollTop(0); 
		/*if ($scope.authcode == !null || $scope.type != 3){
			$window.location.href = $rootScope.base_url;
		}*/
		$scope.togglePassword = function () { $scope.typePassword = !$scope.typePassword; };
		
		$('.modal-backdrop').show();
        setTimeout(function()
		{ 
			var header_ht = $('.page_header').height();
			$('#search_input').css('top',header_ht);
			
		}, 500);
		
		
		
		$scope.authcode = $cookies.get('authcode');
		$scope.name = $cookies.get('name'); 
		$scope.imgpath = $cookies.get('imgpath'); 
		//alert($scope.imgpath);
		if ($scope.imgpath == null) {
			$scope.imgpath = 'images/man.png';
		}
		$scope.email = $cookies.get('email');
		$scope.login_type = $cookies.get('type');
		if ($scope.authcode != null){
			if($cookies.get('type') == 1)
		    {
				$window.location.href = $rootScope.base_url+'masterdashboard';
			}
			else if($cookies.get('type') == 2){
				$window.location.href = $rootScope.base_url+'instdashboard';
			}
			else if($cookies.get('type') == 4){
				$window.location.href = $rootScope.base_url+'branchdashboard';
			}
			else if($cookies.get('type') == 3){
				//$window.location.href = $rootScope.base_url+'welcome';
			}
		}
		
		$scope.subdomain = "www";
		$scope.portal = "main";
		
		
		$scope.emailFormat = /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
		$scope.facebookDet ={};
		$scope.otpuserid = '';
		$scope.id = '';
		$scope.typeid = '';
		$scope.contact = '';
		//$rootScope.loading = false;
		$scope.resend = {};
		$scope.registerDet = [];
		$scope.loginDet = {};
		$scope.registerInstDet = {};
		$scope.normalNotiArr = [];
		$scope.cartCount = 0;
		$scope.indivisualNotiArr = [];
		$scope.indivisualNotiCount = 0;
		$scope.registerDet.check = false;
		$scope.valid = true;
		$scope.registerInstDet.check = false;
		$scope.stateDetail = [];
		$scope.distDetail = [];
		$scope.btnName = 'Verify mobile number';
		this.loginUserGlobalmodel = function()
		{
			$("#login").modal('show');

			//$scope.formClean();
			$('.register-form').hide();
			$('#forgot-form').hide();
			$('#forgot-form-other').hide();
			$('#login-form').show();
			$('#facebook-form').hide();
			$('#otp-form').hide();
			$('#otp-inst-form').hide();
			$('#login-form-other').hide();
			$('#set_password').hide();
			
		}
		this.testing = function()
		{
			//$scope.formClean();
			$('#login').show();
			$('#login').modal('show');
			$('.register-form').hide();
			$('#forgot-form').hide();
			$('#forgot-form-other').hide();
			$('#login-form').show();
			$('#facebook-form').hide();
			$('#otp-form').hide();
			$('#otp-inst-form').hide();
			$('#login-form-other').hide();
		}
		this.testingOther = function()
		{
			$('#login').modal('show');
			$scope.login_user = "student";
			$('#login-form-other').show();
			$('#login-form').hide();
		   	$('#forgot-form').hide();
		   	$('#forgot-form-other').hide();
		   	$('.register-form').hide();
		   	$('#facebook-form').hide();
		   	$('#otp-form').hide();
		   	$('#otp-inst-form').hide();
		}
		$scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
   		 }, 18000);
    	
    	var wrapper_ht = $(window).height();
    	$('.wrapper').css('min-height',wrapper_ht-246);
    	
    	$scope.usernameValidator = function(username) {
				return ServerService.usernameValidate(username);
		};
		
		$scope.checkUsername = function(username)
		{
			var result = $scope.usernameValidator(username);
			if(result == true)
			{
				ServerService.checkUsername(username).then(function(response){
					response = response.data;
			        if(response.status == 200)
			        {
						//$("#usernamemessage").addClass('has-success');
						//$scope.usernamemessage = "That username is available.";
						$scope.valid = false;
					}
					else
					{
						$("#usernamemessage").addClass('has-error');
						$scope.usernamemessage = "This username is taken. Try another.";
						$scope.valid = true;
					}
			    });
			}
		}

		$scope.cleanError = function()
		{
			$("#usernamemessage").removeClass('has-error');
			$("#usernamemessage").removeClass('has-success');
			$scope.usernamemessage = "";
		}	
		$scope.param = {
			'userid' : $cookies.get('userid')
		}
		//$rootScope.loading = true;
		var my_url = url_getcartDetail+$.param($scope.param);
		
	           	$http({
		        method : 'GET',
		        url : my_url,
		        headers : {'authcode': $scope.authcode}
		     }).success(function(response){
	           	
	            	if(response.status == 200){
	            		$rootScope.loading = false;
	            		$scope.cartCount = response.cartcount;
					}
					else{
						$rootScope.loading = false;

						
					}
	           })
		//Login Function
	$scope.submitLoginForm = function(){
		//console.log($scope.loginDet);
		$("#loginmessage").removeClass('has-success');
		$("#loginmessage").removeClass('has-error');
			$scope.loginDet.portal = $scope.portal;
			$scope.loginDet.subdomain = $scope.subdomain;
			$rootScope.loading = true;
			$http({
	              method : 'POST',
	              url : url_masterstudentlogin,
	              data: $.param($scope.loginDet) ,
	              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
	           }).success(function(response){
	            	$rootScope.loading = false;
	            	//console.log(response);
	            	if(response.status == 200)
	            	{
	            		if(response.type == 3){		            			
	            			if(response.regtype == 3 && response.verify == 0)
	            			{
	            				$('#login').modal('hide');
								$window.location.href = $rootScope.base_url+'change/fs3/'+md5.createHash(response.userid.toString());
							}
							else
							if(response.verify == 0) 
							{			
								$scope.otpuserid = response.userid;
								$scope.id = response.userid;
								$scope.typeid = response.type;
								$scope.contact = response.contact;
			            		$scope.loginDet = {};
			            		$scope.loginForm.reset();
			            		$('#login-form').hide();
			            		$scope.otpForm.reset();
			            		$('#otp-form').show();
							}
							else{
								$('#login').modal('hide');
								$cookies.put('userid',response.userid);
								$cookies.put('imgpath',response.imgpath);
			            		
			            		$cookies.put('name',response.name);
			            		$cookies.put('email',response.email);
			            		$cookies.put('type',response.type);
			            		$cookies.put('authcode',response.authcode);
			            		//$state.reload();
			            		if($rootScope.pagename != null)
			            		{
									$window.location.href = $rootScope.base_url+'demotest/'+$rootScope.id;
								}
								else if($cookies.get('buycourse') != null)
								{
									$window.location.href = $rootScope.base_url+'payment';
								}
								else
								{
									$window.location.href = $rootScope.base_url+'welcome';
								}
							}
						}
	            		else{
							alert("Invalid user");
						}
					}
					else{
						$("#loginmessage").addClass('has-error');
						$("#loginmessageother").addClass('has-error');
						$scope.loginmessage = response.message;
						$scope.loginmessageother = response.message;
					}
	            	
	           }).error(function(error){
	           		$rootScope.loading = false;
	           		$("#loginmessage").addClass('has-error');
	           		$("#loginmessageother").addClass('has-error');
					$scope.loginmessage = "Some unknown error has occurred. Please try again.";
					$scope.loginmessageother = "Some unknown error has occurred. Please try again.";
	           });
	};
	
	$scope.getNoti = function()
	{
	}
	$scope.reloadPage = function()
	{
		$state.reload();
	}


	if($cookies.get('authcode') != null)
	{
		$scope.getNoti();
		setInterval(function(){
			$scope.getNoti();
		}, 36000)
	}
	//Other Login Function
	$scope.submitLoginFormOther = function() {
		$("#loginmessage").removeClass('has-success');
		$("#loginmessage").removeClass('has-error');
			$scope.loginmessage = "login...";
			$scope.loginDet.subdomain = $scope.subdomain;
			$rootScope.loading = true;
			$http({
	              method : 'POST',
	              url : url_login,
	              data: $.param($scope.loginDet) ,
	              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
	           }).success(function(response){
	            	$rootScope.loading = false;
	            	//console.log(response);
	            	if(response.status == 200)
	            	{
	            		if(response.type == 2){
							if(response.packageid == 0)
							{
								$("#loginmessageother").addClass('has-error')
								$scope.loginmessageother = "Purches the course package";
							}
							else
							if(response.verify == 0) 
							{
								$('#login').modal('hide');
								$window.location.href = $rootScope.base_url+'change/fi2/'+md5.createHash(response.userid.toString());
							}
							else{
								$('#login').modal('hide');
								$cookies.put('userid',response.userid);
								$cookies.put('imgpath',response.imgpath);
			            		$cookies.put('instid',response.instid);
			            		$cookies.put('name',response.name);
			            		$cookies.put('email',response.email);
			            		$cookies.put('type',response.type);
			            		$cookies.put('authcode',response.authcode);
								$window.location.href = $rootScope.base_url+'instdashboard';
							}
						}
						else if(response.type == 4){
							if(response.verify == 0) 
							{
								$('#login').modal('hide');
								$window.location.href = $rootScope.base_url+'change/fb4/'+md5.createHash(response.userid.toString());
							}
							else{
								$('#login').modal('hide');
								$cookies.put('userid',response.userid);
								$cookies.put('imgpath',response.imgpath);
			            		$cookies.put('instid',response.instid);
			            		$cookies.put('name',response.name);
			            		$cookies.put('email',response.email);
			            		$cookies.put('type',response.type);
			            		$cookies.put('authcode',response.authcode);
								$window.location.href = $rootScope.base_url+'branchdashboard';
							}
						}
	            		else 
	            		{
							alert("Invalid User");
						}
					}
					else{
						$("#loginmessageother").addClass('has-error')
						$scope.loginmessageother = response.message;
					}
	            	
	           }).error(function(error){
	           		$rootScope.loading = false;
	           		$("#loginmessageother").addClass('has-error')
					$scope.loginmessageother = "Some unknown error has occurred. Please try again.";
	           });
	};
		
		//Student Register Function
		$scope.submitRegForm = function() {
			$("#registermessage").removeClass('has-success');
			$("#registermessage").removeClass('has-error');
			$scope.registermessage = '';
			
			if($scope.registerDet.check == false || $scope.registerDet.check == null){
				$("#registermessage").addClass('has-error');
				$scope.registermessage = "Please accept the terms and conditions.";
				return false;
			}
			
		
			
			//console.log($scope.registerDet);
			$scope.registerDet.instid = $rootScope.masterid;
				$rootScope.loading = true;
				$http({
		              method : 'POST',
		              url : url_studregister,
		              data: $.param($scope.registerDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.registerDet.check = false;
		            		//$cookies.put('otpuserid',response.id);
		            		$scope.otpuserid = response.id;
		            		$scope.id = response.id;
							$scope.typeid = response.type;
							$scope.contact = response.contact;
		            		$scope.registerDet = {};
		            		$scope.regstudForm.reset();
		            		$('.register-form').hide();
		            		$scope.otpForm.reset();
		            		$('#otp-form').show();
						}
						else{
							$("#registermessage").addClass('has-error');
							$scope.registermessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
						$scope.registerDet.check = false;
		           		$("#registermessage").addClass('has-error');
						$scope.registermessage = "Some unknown error has occurred. Please try again.";
		           });
		}
		
		//check OTP student
		$scope.submitOtpForm = function() {
			$("#otpmessage").removeClass('has-error');
			$scope.otpmessage = "";
			//$scope.otpDet.userid = $cookies.get('otpuserid');
			$scope.otpDet.userid = $scope.otpuserid;
				$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_checkregotp,
		              data: $.param($scope.otpDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		//alert('set password form open');
		            		$('#otp-form').hide();
		            		//$scope.passwordForm.reset();
		            		$('#set_password').show();
						}
						else{
							$scope.otpDet = {};
		            		$scope.otpForm.reset();
							$("#otpmessage").addClass('has-error');
							$scope.otpmessage = response.message;
						}		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#otpmessage").addClass('has-error');
						$scope.otpmessage = "Some unknown error has occurred. Please try again.";
		           });
		}
		$scope.submitSetPasswordForm = function() {
			$("#passwordmessage").removeClass('has-error');
			$scope.pwdmessage = "";
			//$scope.otpDet.userid = $cookies.get('otpuserid');
			$scope.passwordForm.userid = $scope.otpuserid;
			//console.log($scope.passwordForm);
				$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_setpassword,
		              data: $.param($scope.passwordForm) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$('#login').modal('hide');
		            		$cookies.put('userid',response.userid);
		            		$cookies.put('imgpath',response.imgpath);
		            		$cookies.put('instid',response.instid);
		            		$cookies.put('name',response.name);
		            		$cookies.put('email',response.email);
		            		$cookies.put('type',response.type);
		            		$cookies.put('authcode',response.authcode);
		            		//$cookies.remove('otpuserid');
		            		$window.location.href = $rootScope.base_url+'studprofile';
		            		//$state.go('welcome');
						}
						else{
							$scope.passwordForm = {};
		            		$scope.passwordForm.reset();
							$("#passwordmessage").addClass('has-error');
							$scope.pwdmessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#passwordmessage").addClass('has-error');
						$scope.pwdmessage = "Some unknown error has occurred. Please try again.";
		           });
		}
		
		//Forget Password Function
		$scope.submitForgetForm = function() {
			$("#forgetmessage").removeClass('has-success');
			$("#forgetmessageother").removeClass('has-success');
			$("#forgetmessage").removeClass('has-error');
			$("#forgetmessageother").removeClass('has-error');
			//$scope.forgetmessage = "forget...";
				//console.log($.param($scope.forgetDet));
				$scope.forgetDet.branchid = $rootScope.masterid;
				$scope.forgetDet.subdomain = $scope.subdomain;
				$scope.forgetDet.portal = $scope.portal;
				$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_forgetpasswordMasterStud,
		              data: $.param($scope.forgetDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.forgetForm.reset();
		            		$('#login').modal('hide');
		            		if(response.type == 2)
		            		{
								$window.location.href = $rootScope.base_url+'forget/fi'+response.type+'/'+response.id;
							}
							else
							if(response.type == 3){
								$window.location.href = $rootScope.base_url+'forget/fs'+response.type+'/'+response.id;
							}
							else
							if(response.type == 4){
								$window.location.href = $rootScope.base_url+'forget/fb'+response.type+'/'+response.id;
							}
							
						}
						else{
							$("#forgetmessage").addClass('has-error');
							$("#forgetmessageother").addClass('has-error');
							$scope.forgetmessage = response.message;
							$scope.forgetmessageother = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#forgetmessage").addClass('has-error');
		           		$("#forgetmessageother").addClass('has-error');
						$scope.forgetmessage = "Some unknown error has occurred. Please try again.";
						$scope.forgetmessageother = "Some unknown error has occurred. Please try again.";
		           });
		}
		
		//Forget other Password Function
		$scope.submitForgetFormOther = function() {
			$("#forgetmessageother").removeClass('has-success');
			$("#forgetmessageother").removeClass('has-error');
			$scope.forgetmessageother = "";
				//console.log($.param($scope.forgetDet));
				$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_forgetpassword,
		              data: $.param($scope.forgetDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.forgetForm.reset();
		            		$('#login').modal('hide');
		            		if(response.type == 2)
		            		{
								$window.location.href = $rootScope.base_url+'forget/fi'+response.type+'/'+response.id;
							}
							else
							if(response.type == 3){
								$window.location.href = $rootScope.base_url+'forget/fs'+response.type+'/'+response.id;
							}
							else
							if(response.type == 4){
								$window.location.href = $rootScope.base_url+'forget/fb'+response.type+'/'+response.id;
							}
							
						}
						else{
							$("#forgetmessageother").addClass('has-error');
							$scope.forgetmessageother = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#forgetmessageother").addClass('has-error');
						$scope.forgetmessageother = "Some unknown error has occurred. Please try again.";
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
		$("#search_input").hide();
	     $scope.show_search = function()
	     {
	         $("#search_input").slideToggle();
	     };
		
		setTimeout(function()
		{ 
			var header_logg = $('.logg').height();
			$('.dashboard_menu .navmenu-fixed-left').css('top',header_logg);
		}, 1000);
    	
		$scope.login = function(){
			$scope.formClean();
			$('.register-form').hide();
			$('#forgot-form').hide();
			$('#forgot-form-other').hide();
			$('#login-form').show();
			$('#facebook-form').hide();
			$('#otp-form').hide();
			$('#otp-inst-form').hide();
			$('#login-form-other').hide();
			$('#set_password').hide();
		};
		
		$scope.register = function(){
			$('.register-form').show();
			$('#login-form').hide();
		   	$('#forgot-form').hide();
		   	$('#forgot-form-other').hide();
		   	$('#facebook-form').hide();
		   	$('#otp-form').hide();
		   	$('#otp-inst-form').hide();
			$('#login-form-other').hide();
			$('#set_password').hide();
			
			$scope.formClean();
		   	
		};
		$scope.demoTestNewVisitor = function() {
			$('#exam_buy').modal({
	       	 show: 'true'
	   		 });
	   		 //$('#exam_buy').show();
			$scope.formClean();
			$('.register-form').hide();
			$('#forgot-form').hide();
			$('#forgot-form-other').hide();
			$('#login-form').hide();
			//$('#exam_buy').show();
			$('#facebook-form').hide();
			$('#otp-form').hide();
			$('#otp-inst-form').hide();
			$('#login-form-other').hide();
			$('#set_password').hide();
			$scope.getCourse();
		};
		
		//get course
		$scope.courseArr = [];
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
		     		$scope.courseArr = response.course;
		     		console.log($scope.courseArr);
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
			$scope.democourse = [];
		$scope.startDemoTest = function(){
				$scope.registermessage = '';
		 	$scope.OTPmessage= '';
		 	$("#registermessage").removeClass('has-success');
				$("#registermessage").removeClass('has-error');

			if ($scope.sentotp !=  $scope.registerDet.conformOTP) {
				
				$("#OTPmessage").addClass('has-error');
				$scope.OTPmessage = 'OTP does not match.';
		        return false;
			}	
		 	if(typeof $scope.democourse.coursename === 'undefined' || $scope.democourse.coursename === ''){
		 		$("#registermessage").addClass('has-error');
				$scope.registermessage = 'Please select course for Demo Test.';
		        return false;
		    } 
		    	var my_url = url_studdemosendotp;

		    if ($scope.registerDet.conformOTP) {
		    	var my_url = url_studdemoregister;
		    }
		      	//Student Demo Register Function
				//console.log($scope.registerDet);
					$rootScope.loading = true;
					$cookies.put('demoTest','start');
					$http({
			              method : 'POST',
			              url : my_url,
			              data: $.param($scope.registerDet),
			              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
			           }).success(function(response){
			            	$rootScope.loading = false;
			            	if(response.status == 200)
			            	{
			            		$scope.btnName = 'Start Demo Test';
			            		$scope.sentotp = response.otp;
			            		if (my_url == url_studdemoregister) {
			            		$window.location.href = $rootScope.base_url+'demotest/'+$scope.democourse.coursename;
			            		

			            		}
							}
							else{
								$("#OTPmessage").addClass('has-error');
								$scope.OTPmessage = response.message;
							}
			           }).error(function(error){
			           		$rootScope.loading = false;
							$scope.registerDet.check = false;
			           		$("#registermessage").addClass('has-error');
							$scope.registermessage = "Some unknown error has occurred. Please try again.";
			           });
		}
		$scope.formClean = function()
		{
			$("#loginmessage").removeClass('has-success');
			$("#loginmessageother").removeClass('has-success');
			$("#loginmessage").removeClass('has-error');
			$("#loginmessageother").removeClass('has-error');
			$("#registermessage").removeClass('has-success')
			$("#registermessage").removeClass('has-error')
			$("#registerinstmessage").removeClass('has-success')
			$("#registerinstmessage").removeClass('has-error')
			$("#forgetmessage").removeClass('has-success');
			$("#forgetmessage").removeClass('has-error');
			$scope.forgetmessage = '';
			$scope.loginmessage = '';
			$scope.loginmessageother = '';
			$scope.registermessage = '';
			$scope.registerinstmessage = '';
			$scope.forgetDet = {};
			$scope.forgetForm.reset();
			$scope.registerDet = {};
			$scope.regstudForm.reset();
			$scope.registerInstDet = {};
			
			$scope.loginDet = {};
			$scope.loginForm.reset();
		}
		
		$scope.forget = function(){
			$scope.formClean();
		  	$('#login-form').hide();
		  	$('#facebook-form').hide();
		   	$('.register-form').hide();
		   	$('#forgot-form').show();
		   	$('#forgot-form-other').hide();
		   	$('#otp-form').hide();
		   	$('#otp-inst-form').hide();
			$('#login-form-other').hide();
		};
		
		$scope.forgot_other = function(){
			$scope.forget_user = "student";
			$scope.formClean();
		  	$('#login-form').hide();
		  	$('#facebook-form').hide();
		   	$('.register-form').hide();
		   	$('#forgot-form').hide();
		   	$('#forgot-form-other').show();
		   	$('#otp-form').hide();
		   	$('#otp-inst-form').hide();
			$('#login-form-other').hide();
		};
		
		 /*register switch*/
         
         $('input[type="radio"]').click(function()
         {
	         if($(this).attr("value")=="student"){
	              $scope.formClean();
	              $(".student").show();
	             
	         }
	         
        });
        
        //log out
		$scope.logout = function()
		{
			$cookies.remove('userid');
			$cookies.remove('instid');
		    $cookies.remove('name');
		    $cookies.remove('email');
		    $cookies.remove('type');
		    //$cookies.remove('ci');
		    $cookies.remove('authcode');
		    $cookies.remove('buycourse');
		    
		    //$state.reload();
		    $window.location.href = $rootScope.logout_url;
		   /* if($scope.instid == $rootScope.masterid)
		    {
				$window.location.href = $rootScope.logout_url;
			}
		    else{
				$window.location.href = $rootScope.base_url+"/login";
			}*/
		}
		
	//resend otp
	$scope.resendOTP = function()
	{
		$("#registermessage").removeClass('has-success');
		$("#registermessage").removeClass('has-error');
			$scope.resend.id = $scope.id;
			$scope.resend.type = $scope.typeid;
			$scope.resend.contact = $scope.contact;
			$scope.resend.message = "Your OTP for registration is -";
			$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_otpresend,
		              data: $.param($scope.resend) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
						}
						else{
							$("#registermessage").addClass('has-error');
							$scope.registermessage = response.message;
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#registermessage").addClass('has-error');
						$scope.registermessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	$scope.tremsCondition = function(){
		$('#login').modal('hide');
		$myurl = $rootScope.base_url+"term&conditions";
		//$window.location.href = $rootScope.base_url+"term&conditions";
		$window.open(myurl, '_blank');
	}
	
	/*********************************   FaceBook Login  ************************************/
	
  
	//FB login
	// This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    //console.log('statusChangeCallback');
    if (response.status === 'connected') {
      // Logged into your app and Facebook.	 
     	testAPI();
    } else if (response.status === 'not_authorized') {
      
    } else {
     
    }
  }
   
  function checkLoginState() {   
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
	  FB.init({
	    appId      : '1792230341067058',//pratikpawarnasik
	    cookie     : true,  // enable cookies to allow the server to access  520379134996388
	                        // the session
		status  : false, // check login status					
	    xfbml      : true,  // parse social plugins on this page
	    version    : 'v2.10', // use version 2.1
		oauth: true
		
	  });
  };
 
  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  
  function testAPI() {
    //console.log('Welcome!  Fetching your information.... ');
    FB.api('/me?fields=id,name,picture,email,location', function(response) {
    	$scope.social = "facebook";
    	sendFacebookData(response);
    });
  }
	
	$scope.fb_login = function(){
		$scope.social_source = 1;
		 FB.login(function(response) {
			if (response.authResponse) {
			  checkLoginState();
			} else {
				//user hit cancel button
				console.log('User cancelled login or did not fully authorize.');
			}
		}, 
		{scope: 'public_profile,email' }); /**/
	}	
	
	/**
	### gmail
	*/
      
      $scope.startApp = function() {
	    gapi.load('auth2', function(){
	      // Retrieve the singleton for the GoogleAuth library and set up the client.
	      auth2 = gapi.auth2.init({
	        client_id: '981650214684-p8f3jgth4ertnnpj1j22d0plertl1c4k557qi3.apps.googleusercontent.com',

	        cookiepolicy: 'single_host_origin',
	        // Request scopes in addition to 'profile' and 'email'
	        //scope: 'additional_scope'
	      });

	      $id = document.getElementById('customBtn');
	      if($id != null)
	      {
		  	$scope.attachSignin($id);
		  }
	      
	    });
	  };
	  $scope.attachSignin = function(element) {
	  	
	    auth2.attachClickHandler(element, {},
	        function(googleUser) {
	        	profile = googleUser.getBasicProfile();
	        	$scope.social = "googleplus";
	        	sendFacebookData(profile);
	        }, function(error) {
	          //alert(JSON.stringify(error, undefined, 2));
	        });
	  }


	function sendFacebookData(response){
		
		$("#facebookmessage").removeClass('has-error');
		$scope.facebookmessage = '';
		$scope.facebookDet = {};
		$scope.formClean();	
			if($scope.social == "facebook")
			{
				var obj = {
					    faceid: response.id
					};
			}
			else if($scope.social == "googleplus")
			{
				var obj = {
					    faceid: response.getId()
					};
			}

				$rootScope.loading = true;
				$http({
		              method : 'POST',
		              url : url_getsocialuser,
		              data: $.param(obj) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(resp){
		           		$rootScope.loading = false;
		            	if(resp.status == 200)
		            	{
		            		if(resp.verify == 0)
		            		{
								$scope.otpuserid = resp.userid;
			            		//$scope.facebookDet = {};
			            		//$scope.facebookForm.reset();
			            		$scope.id = resp.userid;
								$scope.typeid = resp.type;
								$scope.contact = resp.contact;
			            		$('#login-form').hide();
			            		$scope.otpForm.reset();
			            		$('#otp-form').show();
							}
							else{
								$('#login-form').hide();
								$('#otp-form').hide();
								$cookies.put('userid',resp.userid);
			            		$cookies.put('name',resp.name);
			            		$cookies.put('instid',resp.instid);
			            		$cookies.put('email',resp.email);
			            		$cookies.put('type',resp.type);
			            		$cookies.put('authcode',resp.authcode);					    
							    $window.location.href = $rootScope.base_url+'welcome';
								//$state.reload();
							}
		            		
						}
						else{
							if($scope.social == "facebook")
								{
								   	var username = response.name.split(" "); 
								   	$scope.facebookDet.faceid = response.id;
								   	$scope.facebookDet.name = username[0];
								   	$scope.facebookDet.email = response.email;
								   	$scope.facebookDet.regtype = 1;
								}
								else if($scope.social == "googleplus"){
									$scope.facebookDet.faceid = response.getId();
								   	$scope.facebookDet.name = response.getName();
								   	$scope.facebookDet.email = response.getEmail();
								   	$scope.facebookDet.regtype = 2;
								}
						  	$('#login-form').hide();
						  	$('#facebook-form').show();
						   	$('.register-form').hide();
						   	$('#forgot-form').hide();
						   	$('#otp-form').hide();
						   	$('#otp-inst-form').hide();
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#loginmessage").addClass('has-error');
						$scope.loginmessage = "Some unknown error has occurred. Please try again.";
		           });
	}	
	
	$scope.submitfacebookForm = function()
	{
		$scope.facebookDet.instid = 123456789;
		$rootScope.loading = true;
		$http({
		              method : 'POST',
		              url : url_socialcreate,
		              data: $.param($scope.facebookDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		/*$cookies.put('userid',response.userid);
		            		$cookies.put('imgpath',response.imgpath);
		            		$cookies.put('name',response.name);
		            		$cookies.put('email',response.email);
		            		$cookies.put('type',response.type);
		            		$cookies.put('authcode',response.authcode);
		            		$scope.facebookDet = '';
						    $state.reload();*/
						    $scope.otpuserid = response.userid;
						    $scope.id = response.userid;
							$scope.typeid = response.type;
							$scope.contact = response.contact;
		            		$scope.facebookDet = {};
		            		$scope.facebookForm.reset();
		            		$('#facebook-form').hide();
		            		$scope.otpForm.reset();
		            		$('#otp-form').show();
						}
						else{
							$("#facebookmessage").addClass('has-error');
							$scope.facebookmessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#facebookmessage").addClass('has-error');
						$scope.facebookmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	/********************************    Script by Aniket ******************************************/
		
		
})
.controller('vidForgetCtrl', function($scope,$rootScope,$stateParams,$window,$http,$location){
	
	$scope.valid = true;
	$scope.invalid = false;
	$scope.id = 0;
	$scope.contact = '';
	$scope.token = $stateParams.token;
	$scope.type = $stateParams.type;
	$scope.resend = {};
	
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    
	if($scope.token == '')
	{
		$window.location.href = $rootScope.base_url;
	}
	else{
		var obj = {
					    token: $scope.token,
					    type: $scope.type
					};
					//console.log($.param(obj))
				$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_regchecktoken,
		              data: $.param(obj) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	//console.log(response);
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.id = parseInt(response.id);
		            		$scope.contact = response.contact;
		            		$scope.typeid = parseInt(response.type);
						}
						else
						{
							$scope.valid = false;
							$scope.invalid = true;
							$scope.message = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		console.log(error);
		           		$scope.registermessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	//submit forget password form
	$scope.submitforgetForm = function()
	{
		$("#registermessage").removeClass('has-success');
		$("#registermessage").removeClass('has-error');
			$scope.registermessage = "Forget...";
			$scope.registerDet.id = $scope.token;
			$scope.registerDet.type = $scope.type;
				$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_forgetsend,
		              data: $.param($scope.registerDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.valid = false;
		            		$scope.success = true;
						}
						else{
							$("#registermessage").addClass('has-error');
							$scope.registermessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#registermessage").addClass('has-error');
						$scope.registermessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	//resend otp
	$scope.resendOTP = function()
	{
		$("#registermessage").removeClass('has-success');
		$("#registermessage").removeClass('has-error');
			$scope.resend.id = $scope.id;
			$scope.resend.type = $scope.typeid;
			$scope.resend.contact = $scope.contact;
			$scope.resend.message = "Your OTP for forgot password is -";
			$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_otpresend,
		              data: $.param($scope.resend) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
						}
						else{
							$("#registermessage").addClass('has-error');
							$scope.registermessage = response.message;
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#registermessage").addClass('has-error');
						$scope.registermessage = "Some unknown error has occurred. Please try again.";
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

.controller('vidFooterCtrl', function($scope,$cookies,$rootScope,$state,$rootScope,$window,$http,$location){
	$scope.authcode = $cookies.get('authcode');
	$scope.portal = $rootScope.portal;
})
.controller('vidMasterHeaderCtrl', function($scope,$state,$cookies,$rootScope,$window,$http,$location){
		$scope.authcode = $cookies.get('authcode');
		$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
		$scope.email = $cookies.get('email');
		$scope.type = $cookies.get('type');
		
		//$('body').trigger('click');
		//$(window).scrollTop(0); 
		
		if ($scope.authcode == null || $scope.type != 1){
			$window.location.href = $rootScope.base_url;
		}
	   setTimeout(function()
		{ 
			var header_logg = $('.logg').height();
			$('.dashboard_menu .navmenu-fixed-left').css('top',header_logg);
		}, 1000);
    	
		//log out
		$scope.logout = function()
		{
			$cookies.remove('userid');
		    $cookies.remove('name');
		    $cookies.remove('email');
		    $cookies.remove('type');
		    $cookies.remove('authcode');
		    $cookies.remove('buycourse');
		    //$state.reload();
		    $window.location.href = $rootScope.base_url+"adminp8AamG6ueHFNGAAp";
		}
})
.controller('vidMasterFooterCtrl', function($scope,$rootScope,$window,$http,$location){
	
})


.controller('vidAdminLoginCtrl', function($scope,$interval,$state,md5,$cookies,$rootScope,$window,$http,$location){
		
		
		$scope.subdomain = 'www';
		if($scope.subdomain != 'www')
		{
			$window.location.href = $rootScope.base_url;
		}
		$scope.authcode = $cookies.get('authcode');
		$scope.type = $cookies.get('type');
		
		if ($scope.authcode != null && $scope.type == 1){
			$window.location.href = $rootScope.base_url+"masterdashboard";
		}
		
    	var wrapper_ht = $(window).height();
    	$('.wrapper').css('min-height',wrapper_ht-120);
    	$rootScope.loading = false;
		//Login Function
		$scope.submitLoginForm = function() {
			$("#loginmessage").removeClass('has-success')
			$("#loginmessage").removeClass('has-error')
				$scope.loginmessage = "login...";
				$scope.loginDet.subdomain = "www";
				$rootScope.loading = true;
				$http({
		              method : 'POST',
		              url : url_login,
		              data: $.param($scope.loginDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	//console.log(response);
		            	if(response.status == 200)
		            	{
		            		if(response.type == 1)
		            		{
		            			$('#login').modal('hide');
		            			//alert(response.instid);
		            			$cookies.put('userid',response.userid);
		            			$cookies.put('imgpath',response.imgpath);
			            		//$cookies.put('instid',response.instid);
			            		$cookies.put('name',response.name);
			            		$cookies.put('email',response.email);
			            		$cookies.put('type',response.type);
			            		$cookies.put('authcode',response.authcode);
								$window.location.href = $rootScope.base_url+'masterdashboard';
							}
							else if(response.type == 2){
								if(response.verify == 0) 
								{
									$('#login').modal('hide');
									$window.location.href = $rootScope.base_url+'change/fi2/'+md5.createHash(response.userid.toString());
									/*$("#loginmessage").addClass('has-error')
									$scope.loginmessage = "Institute are not verified. please verify.";
									$scope.otpuserid = response.userid;
									$scope.id = response.userid;
									$scope.typeid = response.type;
									$scope.contact = response.contact;
				            		$scope.loginDet = {};
				            		$scope.loginForm.reset();
				            		$('#login-form').hide();
				            		$scope.otpInstForm.reset();
				            		$('#otp-inst-form').show();*/
								}
								else{
									$('#login').modal('hide');
									$cookies.put('userid',response.userid);
									$cookies.put('imgpath',response.imgpath);
				            		$cookies.put('instid',response.instid);
				            		$cookies.put('name',response.name);
				            		$cookies.put('email',response.email);
				            		$cookies.put('type',response.type);
				            		$cookies.put('authcode',response.authcode);
									$window.location.href = $rootScope.base_url+'instdashboard';
								}
							}
							else if(response.type == 4){
								if(response.verify == 0) 
								{
									$('#login').modal('hide');
									$window.location.href = $rootScope.base_url+'change/fb4/'+md5.createHash(response.userid.toString());
								}
								else{
									$('#login').modal('hide');
									$cookies.put('userid',response.userid);
									$cookies.put('imgpath',response.imgpath);
				            		$cookies.put('instid',response.instid);
				            		$cookies.put('name',response.name);
				            		$cookies.put('email',response.email);
				            		$cookies.put('type',response.type);
				            		$cookies.put('authcode',response.authcode);
									$window.location.href = $rootScope.base_url+'branchdashboard';
								}
							}
		            		else if(response.type == 3){
		            			
		            			if(response.regtype == 3 && response.verify == 0)
		            			{
		            				$('#login').modal('hide');
									$window.location.href = $rootScope.base_url+'change/fs3/'+md5.createHash(response.userid.toString());
								}
								else
								if(response.verify == 0) 
								{
									//$("#loginmessage").addClass('has-error')
									//$scope.loginmessage = "Student are not verified. please verify.";
									$scope.otpuserid = response.userid;
									$scope.id = response.userid;
									$scope.typeid = response.type;
									$scope.contact = response.contact;
				            		$scope.loginDet = {};
				            		$scope.loginForm.reset();
				            		$('#login-form').hide();
				            		$scope.otpForm.reset();
				            		$('#otp-form').show();
				            		//$scope.otpForm.reset();
		            				//$('#otp-form').show();
								}
								else{
									$('#login').modal('hide');
									$cookies.put('userid',response.userid);
									$cookies.put('imgpath',response.imgpath);
				            		$cookies.put('instid',response.instid);
				            		$cookies.put('name',response.name);
				            		$cookies.put('email',response.email);
				            		$cookies.put('type',response.type);
				            		$cookies.put('authcode',response.authcode);
				            		//$state.reload();
				            		if($rootScope.pagename != null)
				            		{
										$window.location.href = $rootScope.base_url+'demotest/'+$rootScope.id;
									}
									else if($cookies.get('buycourse') != null)
									{
										$window.location.href = $rootScope.base_url+'payment';
									}
									else
									{
										$window.location.href = $rootScope.base_url+'welcome';
									}
									
								}
		            			
							}
		            		
						}
						else{
							$("#loginmessage").addClass('has-error')
							$scope.loginmessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#loginmessage").addClass('has-error')
						$scope.loginmessage = "Some unknown error has occurred. Please try again.";
		           });
		};
		
	

		//Forget Password Function
		$scope.submitForgetForm = function() {
			$("#forgetmessage").removeClass('has-success');
			$("#forgetmessage").removeClass('has-error');
			$scope.forgetmessage = "forget...";
				//console.log($.param($scope.forgetDet));
				$rootScope.loading = true;
				$http({
		              method : 'PUT',
		              url : url_forgetpassword,
		              data: $.param($scope.forgetDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.forgetForm.reset();
		            		$('#login').modal('hide');
		            		if(response.type == 2)
		            		{
								$window.location.href = $rootScope.base_url+'forget/fi'+response.type+'/'+response.id;
							}
							else
							if(response.type == 3){
								$window.location.href = $rootScope.base_url+'forget/fs'+response.type+'/'+response.id;
							}
							else
							if(response.type == 4){
								$window.location.href = $rootScope.base_url+'forget/fb'+response.type+'/'+response.id;
							}
							
						}
						else{
							$("#forgetmessage").addClass('has-error');
							$scope.forgetmessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#forgetmessage").addClass('has-error');
						$scope.forgetmessage = "Some unknown error has occurred. Please try again.";
		           });
		}
		
})


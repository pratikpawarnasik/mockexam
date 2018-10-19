angular.module('ngApp.dashCtrl', [])

.controller('vidDashboardCtrl', function($scope,$cookies,$controller,$rootScope,$window,$http,$location){
	$scope.subsriber = {};
	$scope.emailFormat = /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
	$scope.mobileNoFormat =/(^[1-9]\d{9}$)/;
	/*$('#quote-carousel').carousel({
			pause: true, 
			interval: 10000,
	});*/
	$rootScope.loading = false;
	$scope.authcode = $cookies.get('authcode');
		$scope.portal = $rootScope.portal;
		$scope.subdomain = $rootScope.subdomain;
		$scope.cover = '';
$scope.showhide = function(){
	$scope.activeReadMore = 'hide';
}
$(window).scroll(function() {
    var hT = $('#circle').offset().top,
        hH = $('#circle').outerHeight(),
        wH = $(window).height(),
        wS = $(this).scrollTop();
    console.log((hT - wH), wS);
    if (wS > (hT + hH - wH)) {
        $('.count').each(function() {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 900,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                }
            });
        }); {
            $('.count').removeClass('count').addClass('counted');
        };
    }
});


/**header fixed */
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 400 || document.documentElement.scrollTop > 400) {
        document.getElementById("myBtn").style.display = "block";
    } else {
        document.getElementById("myBtn").style.display = "none";
    }
}

//made by vipul mirajkar thevipulm.appspot.com
var TxtType = function(el, toRotate, period) {
        this.toRotate = toRotate;
        this.el = el;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 2000;
        this.txt = '';
        this.tick();
        this.isDeleting = false;
    };

    TxtType.prototype.tick = function() {
        var i = this.loopNum % this.toRotate.length;
        var fullTxt = this.toRotate[i];

        if (this.isDeleting) {
        this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
        this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

        var that = this;
        var delta = 200 - Math.random() * 100;

        if (this.isDeleting) { delta /= 2; }

        if (!this.isDeleting && this.txt === fullTxt) {
        delta = this.period;
        this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
        this.isDeleting = false;
        this.loopNum++;
        delta = 500;
        }

        setTimeout(function() {
        that.tick();
        }, delta);
    };

    window.onload = function() {
        var elements = document.getElementsByClassName('typewrite');
        for (var i=0; i<elements.length; i++) {
            var toRotate = elements[i].getAttribute('data-type');
            var period = elements[i].getAttribute('data-period');
            if (toRotate) {
              new TxtType(elements[i], JSON.parse(toRotate), period);
            }
        }
        // INJECT CSS
        var css = document.createElement("style");
        css.type = "text/css";
        css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #fff}";
        document.body.appendChild(css);
    };


// When the user clicks on the button, scroll to the top of the document
$scope.topFunction = function(){
	 $('html, body').animate({scrollTop:0}, 'slow');
	//document.getElementById("myBtn").style.display = "block";
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}

$('#userexam_modal').on('show.bs.modal', function () {
  $('body').css("overflow", "hidden");
  $(".close").click(function(){
        $('body').css("overflow-y", "scroll");
    });
});

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


(function(){
  // setup your carousels as you normally would using JS
  // or via data attributes according to the documentation
  // https://getbootstrap.com/javascript/#carousel
  $('#media').carousel({ interval: 2000 });
  $('#media1').carousel({ interval: 3600 });
}());

(function(){
  $('.carousel-showmanymoveone .item').each(function(){
    var itemToClone = $(this);

    for (var i=1;i<4;i++) {
      itemToClone = itemToClone.next();

      // wrap around if at end of item collection
      if (!itemToClone.length) {
        itemToClone = $(this).siblings(':first');
      }

      // grab item, clone, add marker class, add to collection
      itemToClone.children(':first-child').clone()
        .addClass("cloneditem-"+(i))
        .appendTo($(this));
    }
  });
}());

	
$scope.courseArr = [];
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
	//get course
	$scope.getExams = function(){
		$rootScope.loading = true;	
		var my_url = url_allExams;	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		//console.log(response);
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
setTimeout(function()
{ 
      /*testimonial slider*/
 $("#testimonial").flexisel({
        visibleItems: 3,
        animationSpeed: 400,
        autoPlay: false,
        autoPlaySpeed: 500,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 1
            }, 
            landscape: { 
                changePoint:640,
                visibleItems: 1
            },
            tablet: { 
                changePoint:768,
                visibleItems: 2
            }
        }
    });
    /*partners slider*/
    $("#partners").flexisel({
        visibleItems: 3,
        animationSpeed: 400,
        autoPlay: true,
        autoPlaySpeed: 3000,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 1
            }, 
            landscape: { 
                changePoint:640,
                visibleItems: 1
            },
            tablet: { 
                changePoint:768,
                visibleItems: 2
            }
        }
    });
   }, 1000);

	
	$scope.submitSubsribeForm = function()
	{
		$("#submsg").removeClass('has-error')
		$("#submsg").removeClass('has-success')
		$scope.submsg = "";
		
		if ($scope.subsriber.contact =='' || $scope.subsriber.email =='' || $scope.subsriber.email ==undefined) {
			$scope.submsg = "Please check email and contact no.";
			$("#submsg").addClass('has-error')
			return false;
		}

		//alert($scope.contactmessage);
		console.log($scope.subsriber);
				$rootScope.loading = true;
				$http({
		              method : 'POST',
		              url : url_subscribeform,
		              data: $.param($scope.subsriber) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{

		            		$scope.submsg = response.message;
							$("#submsg").addClass('has-success');
		 						//$state.reload();
		            		$scope.subsriber = {};
		            		//$scope.subsribeform.reset();
						}
						else{
							$("#submsg").addClass('has-error');
							$scope.submsg = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#submsg").addClass('has-error');
						$scope.submsg = response.error;
		           });
	}
	//get testimonial list
	setInterval(function(){
	  $scope.getTestimonial();

	}, 10000)
	$scope.testimonial = [];
	$scope.getTestimonial = function(){

		//alert('its dynamic testimonial');
	//	$rootScope.loading = true;	
		var my_url = url_gettestimonialStatusChange;	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.testimonial = [];
	     		//console.log(response);
	     		$scope.testimonial = response.list;
			}
			else{
				$scope.testimonial = [];
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 } 


	 //get course list 
	$scope.getCoursePage = function()
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		
		
		var my_url = url_getcourseHomepage;
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

		            		$scope.getCourseData($scope.courseArr[0].id);
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
	$scope.courseList = [];
	$scope.getCourseData = function(id,)
	{
		

		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		$scope.param = {
		//	'instid' : $scope.instid,
			'id' : id
		}
		var my_url = url_getallCoursedata+$.param($scope.param);
		//var my_url = url_getallCoursedata;
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.courseList = response.courseList;
		            		
		            		
						}
						else{
							$scope.courseList = [];
							$("#tablemessage").addClass('has-success');
							$scope.tablemessage = "Courses are not available.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}

})

.controller('vidAllExamCtrl', function($scope,$controller,$interval,$state,$cookies,$rootScope,$window,$http,$location){
	$rootScope.loading = true;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	
	/*if($scope.instid != 123456789)
		{
			$window.location.href = $rootScope.logout_url;
		}*/
		
	$scope.courseArr = [];
	$scope.allExamArr = [];
	/*window.history.forward();
    function noBack() { 
         window.history.forward(); 
    }*/
    $scope.doTheBack = function() {
	  window.history.back();
	};   
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
	$scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
   	}, 18000);
   	
   	$scope.demoTest = function(id){
   		if($scope.authcode == null){
			$rootScope.pagename = "course";
			$rootScope.id = id;
			var cntr = $controller('vidHeaderCtrl',{$scope});
			cntr.testing();
		}
		else{
			$window.location.href = $rootScope.base_url+'demotest/'+id;
		}
	}
	$scope.getExam_data = function()
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		
		var my_url = url_getallexamdata;
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	
		           		$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.examdataArr = response.examdata;
		            		//console.log($scope.examdataArr);
		            		
						}
						else{
							$scope.examdataArr = [];
							$("#tablemessage").addClass('has-success');
							$scope.tablemessage = "Exams are not available.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	
})
.controller('vidPricingCtrl', function($scope,$controller,$interval,$state,$cookies,$rootScope,$window,$http,$location){
	$rootScope.loading = true;
	
	/*if($scope.instid != 123456789)
		{
			$window.location.href = $rootScope.logout_url;
		}*/
		
	$scope.courseArr = [];
	$scope.allExamArr = [];
	var cntr = $controller('vidHeaderCtrl',{$scope});
		$scope.callLogin=function(){
	    cntr.loginUserGlobalmodel();
	}
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
	$scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
   	}, 18000);
   	//get course
   	//get course list 
	$scope.getCoursePage = function()
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		
		
		var my_url = url_getcourseHomepage;
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
		            		//console.log(response.course[0].id);
		            		$scope.examId =response.course[0].id;
	     					
		            		$scope.getExam_data($scope.examId,$scope.cname);
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

		
	$scope.getExam_data = function(id,ctempname)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#examNotAvailable").removeClass('has-error');
		$scope.getmessage = '';
		$scope.examNotAvailable = '';
		$scope.param = {
				'examCourseId' : id
			}
		
		var my_url = url_getexamdatabyid+$.param($scope.param);	
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
		            	//	console.log(response.examdata[0].course_name);
		            		$scope.examdataArr = response.examdata;
		            		$scope.course_name = response.examdata[0].course_name;
		            		
						}
						else{
		            		$scope.course_name =ctempname;

							$scope.examdataArr = [];
							$("#examNotAvailable").addClass('has-error');
							$scope.examNotAvailable = "Exams not schedule yet.";
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
})
/*.controller('vidPaymentCtrl', function($scope,$rootScope){
	$rootScope.loading = false;
    var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-106);
})*/
.controller('vidPaymentCtrl', function($scope,$interval,$compile,$timeout,$stateParams,$state,$cookies,$rootScope,$window,$http,$location){
	$rootScope.loading = false;
	$scope.success_url = $rootScope.success_url;
	$scope.failure_url = $rootScope.failure_url;
	$scope.authcode = $cookies.get('authcode');
	$scope.userid = $cookies.get('userid');
	$scope.name = $cookies.get('name'); $scope.imgpath = $cookies.get('imgpath'); 
	$scope.email = $cookies.get('email');
	$scope.contact = $cookies.get('contact');
	
	//$scope.promocode = null;
	//$scope.promocode_id = 0;
	$scope.disval = 0;
	
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    
    if($cookies.get('authcode') == null){
		$window.location.href = $rootScope.base_url+'schuduled';
	}
	
	$scope.submitPayment = function()
	{
		$scope.MERCHANT_KEY = '';	
		$scope.SALT = '';	
		$scope.PAYU_BASE_URL = '';	
		$scope.action = '';			
		$scope.txnid = '';	
		if($scope.finalAmount < 1){
			alert("Amount you choosen is not valid. Please kindly contact administrator.");
			return false;
		}
		$scope.param = {
					'name' : $scope.name,
					'email' : $scope.email,
					'fee' : $scope.finalAmount,
					'userid' : $scope.userid
				}
		
			$rootScope.loading = true;	
			$http({
		       method : 'POST',
		       url : url_getmerchantdetail,
		       data: $.param($scope.param) ,
			   headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		    }).success(function(response){
		    	
				$scope.retail = response.retail;
				$scope.amount = response.amount;	
				$scope.userid = response.userid;
				$scope.txnid = response.txnid;
				$scope.paymentId = response.paymentId;
				var formdata = '';

				formdata += '<form action="Paytm/pgRedirect.php" method="post" name="paytmForm" id="paytmForm">';
				formdata += '<input type="hidden" name="ORDER_ID" value="'+$scope.txnid+'" />';
				formdata += '<input type="hidden" name="CUST_ID" value="'+$scope.userid+'"/>';
				formdata += '<input type="hidden" name="INDUSTRY_TYPE_ID" value="'+$scope.retail+'" />';
				formdata += '<input type="hidden" name="CHANNEL_ID" value="WEB" />';
				formdata += '<input type="hidden" name="TXN_AMOUNT" id="firstname" value="'+$scope.amount+'" />';
				formdata += '<input type="hidden" name="TXN_PAYMENT_ID" id="paymentid" value="'+$scope.paymentId+'" />';
				
				formdata += '</form>';
				$("#trytocheck").append(formdata);
				$rootScope.loading = false;
				$("#paytmForm").submit();
		    }).error(function(error){
		    	
		    });
	}
	
	
	$scope.orderSummary = function()
	{ //alert('order summary');
		$scope.param = {
				'userid' : $scope.userid
			}
		
		var my_url = url_getordersummary+$.param($scope.param);	
		$rootScope.loading = true;	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	//console.log(response);
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.orderDetails = response.order;
	     		if(!$scope.orderDetails.length > 0)
	     		{
    				alert('You have not selected any schudule yet.');

					$window.location.href = $rootScope.base_url+'schuduled';
				}
			}
			else{
				$scope.orderDetails = [];
    			alert('You have not selected any schudule yet. Please choose exam first.');

				$window.location.href = $rootScope.buyexam;// this link is use for open the model.
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#ordermessage").addClass('has-error');
			$scope.ordermessage = "Some unknown error has occurred. Please try again.";
	    });
	}
	
	if($cookies.get('buycourse') != null && $cookies.get('authcode') != null)
	{
		$scope.buycourse = $cookies.get('buycourse');
		//console.log($scope.buycourse);
		$scope.param = {
				'id' : $scope.buycourse,
				'userid' : $scope.userid
			}
			my_url = url_tempstudcourseupdate;
			$rootScope.loading = true;
			$http({
		              method : 'PUT',
		              url : my_url,
		              data: $.param($scope.param) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$cookies.remove('buycourse');
		            		$scope.orderSummary();
						}
						else{
							alert(response.message);
							$window.location.href = $rootScope.base_url+'schuduled';
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#message").addClass('has-error');
						$scope.message = "Some unknown error has occurred. Please try again.";
						/*alert("Some unknown error has occurred. Please try again.");
						$window.location.href = $rootScope.base_url+'schuduled';*/
		           });
		           
	}
	else
	if($cookies.get('authcode') != null){
		$scope.orderSummary();
	}
	
	$scope.totalAmount = 0;
	$scope.finalAmount = 0;
	$scope.addTotal = function(amount)
	{	//alert('amout total alert');
		$scope.totalAmount = $scope.totalAmount + amount;
		/*$scope.totalAmount = $scope.totalAmount + parseInt(amount);*/
		$scope.finalAmount = $scope.totalAmount ;
	}
	
	$scope.removeOrder = function(tempid)
	{
		alert('order remove');
		
		$scope.param = {
				'id' : tempid
			}
		
		var my_url = url_removeOrder;	
		//$rootScope.loading = true;	
		$http({
	       method : 'DELETE',
	       url : my_url,
	       data : $.param($scope.param)
	    }).success(function(response){
	    	//$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$window.location.reload();
			}
			else{
				$("#coursemessage").addClass('has-error');
				$scope.coursemessage = response.message;
			}
	    }).error(function(error){
	    	//$rootScope.loading = false;
	    	$("#coursemessage").addClass('has-error');
			$scope.coursemessage = "Some unknown error has occurred. Please try again.";
	    });
	}
		
	$scope.payAmmount = function()
	{
		$scope.param = {
				'userid' : $scope.userid,
				'orderData' : $scope.orderDetails,
				'totalAmount' : $scope.totalAmount,
				'promocodeid': $scope.promocode_id,
				'disval': $scope.disval
			}
			my_url = url_payAmount;
			$rootScope.loading = true;
			$http({
		              method : 'POST',
		              url : my_url,
		              data: $.param($scope.param) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$window.location.href = $rootScope.base_url+'studcourse';
						}
						else{
							
						}
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#message").addClass('has-error');
						$scope.message = "Some unknown error has occurred. Please try again.";
		           });
	}

	
})

.controller('vidStudFeedbackCtrl', function($scope,$interval,$rootScope,$window,$http,$location){
	$rootScope.loading = false;
	$scope.emailFormat = /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
	
	$scope.interval = $interval(function() {
            $("#contactmessage").removeClass('has-error');
			$scope.contactmessage = '';
   		 }, 36000);
   		 
   	$scope.StudFeedback = {};
	$scope.submitStudFeedbackForm = function()
	{
		$("#contactmessage").removeClass('has-error')
		$("#contactmessage").removeClass('has-success')
		$scope.contactmessage = "sending...";
				$rootScope.loading = true;
				$http({
		              method : 'POST',
		              url : url_feedbackform,
		              data: $.param($scope.StudFeedback) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.StudFeedback = {};
		            		$scope.contactForm.reset();
							$("#contactmessage").addClass('has-success');
							$scope.contactmessage = response.message;
						}
						else{
							$("#contactmessage").addClass('has-error')
							$scope.contactmessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#contactmessage").addClass('has-error')
						$scope.contactmessage = response.error;
		           });
	}
})
.controller('vidContactusCtrl', function($scope,$interval,$rootScope,$window,$http,$location){
	$rootScope.loading = false;
	$scope.emailFormat = /^[a-z]+[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
	
	$scope.interval = $interval(function() {
            $("#contactmessage").removeClass('has-error');
			$scope.contactmessage = '';
   		 }, 36000);
   		 
	$scope.submitContactForm = function()
	{
		$("#contactmessage").removeClass('has-error')
		$("#contactmessage").removeClass('has-success')
		$scope.contactmessage = "sending...";
				$rootScope.loading = true;
				$http({
		              method : 'POST',
		              url : url_contactform,
		              data: $.param($scope.contactDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		$scope.contactDet = {};
		            		$scope.contactForm.reset();
							$("#contactmessage").addClass('has-success');
							$scope.contactmessage = response.message;
						}
						else{
							$("#contactmessage").addClass('has-error')
							$scope.contactmessage = response.message;
						}
		            	
		           }).error(function(error){
		           		$rootScope.loading = false;
		           		$("#contactmessage").addClass('has-error')
						$scope.contactmessage = response.error;
		           });
	}
})
.controller('vidDemotestCtrl', function($scope,$interval,$compile,$timeout,$stateParams,$state,$cookies,$rootScope,$window,$http,$location){
		$scope.alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$rootScope.loading = false;
		$scope.authcode = $cookies.get('authcode');
		$scope.userid = $cookies.get('userid');
		$scope.name = $cookies.get('userid');
		$scope.type = $cookies.get('type');
		
		window.history.forward();
        function noBack() { 
             window.history.forward(); 
        }
        $("#demotest_keypress").on("keydown", function(){
		    	return false;
		})
		 
		/*if($scope.type != 3)
		{
			$window.location.href = $rootScope.base_url+'course';
		}*/
$rootScope.pagename = null;
//question timer	
  $scope.value = 0;
  function countdown() {
    $scope.value++;
    $scope.timeout = $timeout(countdown, 1000);
  }
  
		$scope.questionArr = [];
		$scope.totalItems = 0;
		$scope.queansid = [];
		$scope.queanstext = [];
		$scope.selection = [];
		$scope.attemtQue = [];
		$scope.questionTime = [];
		$scope.questionsortArr = [];
		$scope.exam = true;
		$scope.result = false;
		$scope.id = $stateParams.id;
		$scope.seloption = [];
		//alert($scope.id);
		var demoTest = $cookies.get('demoTest');
		if(demoTest == null)
	    {
			$window.location.href = $rootScope.base_url+'pricingpage';
		}
		if($scope.id == null)
	    {
			$window.location.href = $rootScope.base_url+'schuduled';
		}
		else{
			 
			$scope.param = {
				'courseId' : $scope.id
			}
			$rootScope.loading = true;	
			var my_url = url_getdemoquestion+$.param($scope.param);	
			
			$http({
		       method : 'GET',
		       url : my_url
		    }).success(function(response){
		    	//console.log(response);
		    	$rootScope.loading = false;
		    	if(response.status == 200)
		     	{
		     		$cookies.remove('demoTest');
		     		$scope.coursename = response.coursename;
		     		$scope.questionArr = response.question;
		     		$scope.totalItems = $scope.questionArr.length;
		     		$scope.getConfig();
		     		//$scope.getOption($scope.questionArr[0]['id']);
				}
				else{
					$scope.coursename = response.coursename;
					$scope.totalItems = 0;
					$scope.emptymsg = "Questions are not inserted for this course.";
					$scope.questionArr = [];
					$("#message").addClass('has-error');
					$scope.message = response.message;
				}
		    }).error(function(error){
		    	$rootScope.loading = false;
		    	$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		    });
		 }

$scope.popimglink = null;
//show images on pop in edit question
$scope.showPopup = function(imgpath){
	if(imgpath != null && imgpath != ''){
		$scope.popimglink = imgpath;
		$("#imagepopupshow").modal('show');
	}
}
				
var mins = 0;		
var secs = 0;	
var test = '';	
// get demo test counter value (this is config value)
	$scope.getConfig = function(){

		//var my_url = url_getcategory+$.param($scope.param);
		$rootScope.loading = true;	
		var my_url = url_getconfigvalue;	
		$http({
	       method : 'GET',
	       url : my_url
	    }).success(function(response){
	    	$rootScope.loading = false;
	    	if(response.status == 200)
	     	{
	     		$scope.config = response.config[0]['value'];
	     		mins = parseInt($scope.config);
				secs = parseInt(mins * 60);
				$timeout(Decrement,1000);
				$scope.value--;
				countdown();
			}
			else{
				$scope.config = 0;
				$("#message").addClass('has-error');
				$scope.message = response.message;
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }

function Decrement() {
	//console.log(secs);
			$scope.minutes = getminutes();
			$scope.seconds = getseconds();
			secs--;
		if(secs >= 0)
		{
			test = $timeout(Decrement,1000);
		} 
		else { 
			//setTimeout(function(){alert("time Out")},1000);
			//alert("Time Out");
			$("#show_time_out").modal('show');
			$timeout(function(){
				$("#show_time_out").modal('hide');
				$scope.submitResult();
			},2000);
			//$scope.submitResult();
		}
}

function getminutes() {
	// minutes is seconds divided by 60, rounded down
	mins = Math.floor(secs / 60);
	return mins;
}
function getseconds() {
	// take mins remaining (as seconds) away from total seconds remaining
	return secs-Math.round(mins *60);
}
    	 
//pagination
		  $scope.viewby = 1;
		  $scope.currentPage = 1;
		  $scope.itemsPerPage = $scope.viewby;
		  $scope.maxSize = 5; //Number of pager buttons to show

		  $scope.selectPageNext = function (pageNo) {
		  	
		  	var cquesid = $scope.questionArr[pageNo-2]['id'];
		  	var optionid = $scope.seloption[cquesid];
		  	$timeout.cancel($scope.timeout);
		  	$scope.timer(cquesid);
		  	if(optionid != null)
		  	{
				var optiontxt = $("#text"+optionid).val();
				$scope.addAns(cquesid,optionid,optiontxt);
			}
			$scope.value=0;
			countdown();
		  	$scope.currentPage = pageNo;
		  };
		  
		  $scope.selectPageSkip = function (pageNo) {
		  	var cquesid = $scope.questionArr[pageNo-2]['id'];
		  	var optionid = $scope.seloption[cquesid];
		  	$timeout.cancel($scope.timeout);
		  	$scope.timer(cquesid);
			$scope.value=0;
			countdown();
		  	$scope.currentPage = pageNo;
		  };
		  
		  $scope.saveSelect = function (pageNo) {
		  	
		  	var cquesid = $scope.questionArr[pageNo-2]['id'];
		  	var optionid = $scope.seloption[cquesid];
		  	$timeout.cancel($scope.timeout);
		  	$scope.timer(cquesid);
		  	if(optionid != null)
		  	{
				var optiontxt = $("#text"+optionid).val();
				$scope.addAns(cquesid,optionid,optiontxt);
			}
		  };
		  
		  $scope.multiSelect = function(pageNo)
		  {
		  		$scope.currentPage = pageNo;
		  }
		  
		  $scope.selectPagePrev = function (pageNo) {
		  	var cquesid = $scope.questionArr[pageNo]['id'];
		  	var optionid = $scope.seloption[cquesid];
		  	$scope.timer(cquesid);
		  	if(optionid != null)
		  	{
				var optiontxt = $("#text"+optionid).val();
				$scope.addAns(cquesid,optionid,optiontxt);
			}
		  	/*var checkid = $scope.questionArr[pageNo]['id'];
		  	var optionid = $scope.seloption[checkid];
		  	var optiontxt = $("#text"+optionid).val();*/
		  	 
		  	 $scope.currentPage = pageNo;
		  };
		  
		  $scope.timer = function(quesid)
		  {
		  		var id = quesid;
		 		var idx = $scope.attemtQue.indexOf(id);
			    if (idx > -1) {
			     $scope.value = $scope.questionTime[idx] + $scope.value;
			      $scope.attemtQue.splice(idx, 1);
			      $scope.questionTime.splice(idx, 1);
			      $scope.attemtQue.push(id);
			      $scope.questionTime.push($scope.value);
			    }
			    else {
			      $scope.attemtQue.push(id);
			      $scope.questionTime.push($scope.value);
			    }
		  	/*console.log($scope.attemtQue);
		  	console.log($scope.questionTime);*/
		  }
		  
		  $scope.addAns = function(quesid,optid,opttext)
		  {
			  	var id = quesid;
		 		var idx = $scope.selection.indexOf(id);
			    if (idx > -1) {
			      $scope.queansid.splice(idx, 1);
			      $scope.queanstext.splice(idx, 1);
			      $scope.selection.splice(idx, 1);
			      
			      $scope.queansid.push(optid);
			      $scope.queanstext.push(opttext);
			      $scope.selection.push(id);
			    }
			    else {
			      $scope.queansid.push(optid);
			      $scope.queanstext.push(opttext);
			      $scope.selection.push(id);
			    }
		  	/*console.log($scope.queansid);
		  	console.log($scope.queanstext);
		  	console.log($scope.selection);*/
		  }
		  
		  $scope.submitResult = function()
		  {
		  	$timeout.cancel(test);
		  	var cquesid = $scope.questionArr[parseInt($scope.currentPage - 1)]['id'];
		  	$scope.timer(cquesid);
		  	
		  	$scope.attent = $scope.selection.length;
		  	var resulthtml = '';
		  	$scope.count = 0;
		 		resulthtml += '<div class="course_block score_spec">';
		 		resulthtml += '<h3>Answer Specifications</h3><br>';
				for(j=0;j < $scope.questionArr.length;j++)
		     	{
					resulthtml += '<h4>Q. '+(j+1)+'<p> '+$scope.questionArr[j]['question']+' </p><span class="align_right">';
					var isset = $scope.attemtQue.indexOf($scope.questionArr[j]['id']);
			    	if (isset > -1) {
			    		resulthtml += 'Time Taken: <span>'+$scope.questionTime[isset]+' sec</span>&nbsp;&nbsp;';
			    	}
					
					var idx = $scope.queansid.indexOf($scope.questionArr[j]['optionid']);
			    	if (idx > -1) {
			    		resulthtml += 'Your ans: <span class="ans_correct">Correct</span></span> </h4>';
			    		resulthtml += '<h4 class="vm_demo_color">Correct ans: <span>'+$scope.questionArr[j]['optiontext']+'</span></h4>';
			    		if($scope.questionArr[j]['expl'] != null){
							resulthtml +='<h4  class="vm_demo_color_1">Explanation: <span>'+$scope.questionArr[j]['expl']+'</span></h4>';
						}
						$scope.count++;
			    	}
			    	else{
			    		var idx = $scope.selection.indexOf($scope.questionArr[j]['id']);
			    		if (idx > -1) {
							resulthtml += 'Your ans: <span class="ans_wrong">Wrong</span></span> </h4>';
							resulthtml +='<h4>Your ans: <span>'+$scope.queanstext[idx]+'</span></h4>';
						}
						else{
							resulthtml += "<span>NOT ATTEMPT </span></h4>";
						}
						resulthtml += '<h4 class="vm_demo_color">Correct ans: <span>'+$scope.questionArr[j]['optiontext']+'</span></h4>';
						if($scope.questionArr[j]['expl'] != null){
							resulthtml +='<h4 class="vm_demo_color_1">Explanation: <span>'+$scope.questionArr[j]['expl']+'</span></h4>';
						}
					}
					resulthtml += '<hr />';	
				}
				resulthtml += '</div>';
			var data = "";
			data += "<div class='course_block result'>";
    		data += "	<h4>Hello , you have scored </h4>";
    		//data += "	<h4>Hello "+$cookies.get('name')+", you have scored </h4>";
    		data += "	<h2>"+$scope.count+" / "+$scope.questionArr.length+"</h2>";
    		data += "	<h2><span class='correct_ans'>Correct Answers "+$scope.count+"</span>&nbsp;<span class='wrong_ans'>Attempt Question "+$scope.attent+"</span>&nbsp;<span class='no_attempt_ans'>Total Question "+$scope.questionArr.length+"</span></h2>";
    		data += "</div>";
			data +=resulthtml;
			$scope.exam = false;
			$scope.result = true;
			//console.log(data);
			angular.element(document.getElementById('resultdata')).append(data);
			
		  }
})
.directive("owlCarousel", function() {
	return {
		restrict: 'E',
		transclude: false,
		link: function (scope) {
			scope.initCarousel = function(element) {
			  // provide any default options you want
				var defaultOptions = {
				};
				var customOptions = scope.$eval($(element).attr('data-options'));
				// combine the two options objects
				for(var key in customOptions) {
					defaultOptions[key] = customOptions[key];
				}
				// init carousel
				$(element).owlCarousel(defaultOptions);
			};
		}
	};
})
.directive('owlCarouselItem', [function() {
	return {
		restrict: 'A',
		transclude: false,
		link: function(scope, element) {
		  // wait for the last item in the ng-repeat then call init
			if(scope.$last) {
				scope.initCarousel(element.parent());
			}
		}
	};
}]);
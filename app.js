angular.module('ngApp', 
[
'ui.router',
'ngSanitize',
'ui.bootstrap',
'long2know',
'ui',
'angular-md5',
'angularValidator',
'ngCookies',
'ngIdle',
'ngApp.controllers',
'ngApp.dashCtrl',
'ngApp.studentCtrl',
'ngApp.masterCtrl',
'ngApp.courseCtrl',
'ngApp.categoryCtrl',
'ngApp.extraCtrl',
'ngApp.chapterCtrl',
'ngApp.subjectCtrl',
'ngApp.subjectGroupCtrl',
'ngApp.questionCtrl',
'ngApp.examCtrl',
'ngApp.notesCtrl',
'ngApp.masterStudCtrl',
'ngApp.concernCtrl',
'ngApp.services'
])
.run(
[ '$rootScope', '$state','$http', '$stateParams','Idle','$cookies','$window',

  	function ($rootScope, $state, $http, $stateParams,Idle,$cookies,$window) {
	  	Idle.watch();
	  	
	  	$rootScope.idle = 60*30;
        $rootScope.timeout = 60*30;
        
	  	$rootScope.$watch('idle', function(value) {
          if (value !== null){Idle.setIdle(value);} 
        });

        $rootScope.$watch('timeout', function(value) {
          if (value !== null) Idle.setTimeout(value);
        });
	  	$rootScope.loading = true;
	  	$rootScope.pagename = null;
	  	$rootScope.header_show = 0;
	  	$rootScope.id = null;
	  	$rootScope.masterid = 123456789;
	  	$rootScope.$state = $state;
	    $rootScope.$stateParams = $stateParams;
	    
	    var url      = window.location.href;     // Returns full URL
		var array = url.split('.');
        var masterUrl = 'http://localhost/mockexam/';
      //  var masterUrl = 'http://siddhiglobal.net/acceptance/mockexam/';
        $rootScope.base_url = masterUrl;
        $rootScope.buyexam = masterUrl+'welcomepurchase/+111';
		$rootScope.welcome_url = masterUrl+'welcome';
        $rootScope.logout_url = masterUrl+'index.html';
        $rootScope.success_url = masterUrl+'index.php/dashboard/paymentsuccess';
        $rootScope.failure_url = masterUrl+'index.php/dashboard/paymentfailure';

	  	
		$rootScope.subdomain = "www";
		$rootScope.portal = "main";
		
	     $rootScope.$on('IdleStart', function() {
          //console.log("IdleStart");
        });

        $rootScope.$on('IdleEnd', function() {
          //console.log("IdleEnd");
        });

        $rootScope.$on('IdleWarn', function(e, countdown) {
          //console.log("IdleWarn");
        });
        

		$rootScope.checkLogin = function(){
			if($cookies.get('authcode') != null){
	          	var userid = $cookies.get('userid');
	          	var usertype = $cookies.get('type');
	          	var authcode = $cookies.get('authcode');
			  		var my_url = masterUrl+"index.php/user/idelTimeOut?userid="+userid+"&usertype="+usertype;
					$http({
				       method : 'GET',
				       url : my_url,
		              headers : {'authcode': authcode}
				    }).success(function(response){
				    	if(response.status == 200){
				     		
						}
						else{
							alert("Your current session has been expired because you have logged-in on another device or browser.");
							$cookies.remove('userid');
							$cookies.remove('instid');
						    $cookies.remove('name');
						    $cookies.remove('email');
						    $cookies.remove('type');
						    $cookies.remove('authcode');
						    $cookies.remove('buycourse');  
						    $window.location.href = $rootScope.logout_url;
						}
				    })
		  }
		}
		
        $rootScope.$on('IdleTimeout', function() {
          if($cookies.get('authcode') != null){
          	var userid = $cookies.get('userid');
          	var usertype = $cookies.get('type');
          	var authcode = $cookies.get('authcode');
		  	var my_url = masterUrl+"index.php/user/idelTimeOut?userid="+userid+"&usertype="+usertype;
				$http({
			       method : 'GET',
			       url : my_url,
			       headers : {'authcode': authcode}
			    }).success(function(response){
			    	if(response.status == 200){
			     		
					}
					else{
						
					}
			    })
			$cookies.remove('userid');
			$cookies.remove('instid');
		    $cookies.remove('name');
		    $cookies.remove('email');
		    $cookies.remove('type');
		    $cookies.remove('authcode');
		    $cookies.remove('buycourse');  
		    alert("session time out");
		    $window.location.href = $rootScope.logout_url;
		  }
          Idle.watch();
        });
	    
	  
		$state.transitionTo('root.dashboard');
	}
])

.config(
['$stateProvider', '$urlRouterProvider','IdleProvider','$locationProvider',
function ($stateProvider,   $urlRouterProvider,IdleProvider,$locationProvider) {
      $urlRouterProvider.otherwise('/');
      IdleProvider.windowInterrupt('focus');
      // Use $stateProvider to configure your states.
      $stateProvider
      
      // Root state to master all
      .state('root', {
        abstract: true,
        views: {
            'main': {
                template: '<div ui-view="master"></div>'
            }
        }
      })

          
// Dashboard
        .state('root.dashboard', {
            url: '/',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_dashboard.html'
                }
            }
        })
        // Demo test
        .state('root.demotest', {
            url: '/demotest/:id',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_demotest.html'
                }
            }
        })
        // Master Dashboard
        .state('root.masterdashboard', {
            url: '/masterdashboard',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_masterdashboard.html'
                }
            }
        })
         // Master Payment management
        .state('root.paymentmanage', {
            url: '/paymentmanage',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_paymentmanage.html'
                }
            }
        })
         // manage reading material admin
        .state('root.managenotes', {
            url: '/managenotes',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_managenotes.html'
                }
            }
        })
         // manage reading material student
        .state('root.viewnotes', {
            url: '/viewnotes',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_viewnotes.html'
                }
            }
        })
        // Manage Students
        .state('root.masterstudents', {
            url: '/masterstudents',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_masterstudents.html'
                }
            }
        })
        
        // Master Course
        .state('root.mastercourse', {
            url: '/mastercourse',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_mastercourse.html'
                }
            }
        })
        
        // student course list
        .state('root.studcourse', {
            url: '/studcourse',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_studcourse.html'
                }
            }
        })// student course list
        .state('root.hallticket', {
            url: '/hallticket/:e_data/:exam_id',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_hallticket.html'
                }
            }
        })
        
        // Master Category
        .state('root.mastercategory', {
            url: '/mastercategory',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_mastercategory.html'
                }
            }
        })
        
        // Master Chapter
        .state('root.masterchapter', {
            url: '/masterchapter/:name/:id',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_masterchapter.html'
                }
            }
        })
// Master subject
        .state('root.mastersubject', {
            url: '/mastersubject/:name/:id',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_mastersubject.html'
                }
            }
        })
         // Master subject
        .state('root.mastersubjectgroup', {
            url: '/mastersubjectgroup',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_mastersubjectgroup.html'
                }
            }
        })
        
        
        // question
        .state('root.question', {
            url: '/question/:id/:totalQun',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_question.html'
                }
            }
        })
// changepassword master
        .state('root.changepasswordmaster', {
            url: '/changepasswordmaster',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_changepassword.html'
                }
            }
        })
// changepassword student
        .state('root.changepassword', {
            url: '/changepassword',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_changepassword.html'
                }
            }
        })
        
// exam
        .state('root.masterexam', {
            url: '/masterexam',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_masterexam.html'
                }
            }
        })
        /// All exam
        .state('root.allexamsadmin', {
            url: '/allexamsadmin',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_allexamadmin.html'
                }
            }
        })
    // Master doubts solved
        .state('root.masterdoubts', {
            url: '/masterdoubts',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_masterdoubts.html'
                }
            }
        })
        
      
        
        // master dashboard student feedback 
        .state('root.feedback', {
            url: '/feedback',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_feedback.html'
                }
            }
        })
        
        // Course list
        .state('root.schuduled', {
            url: '/allexams',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_all_exams.html'
                }
            }
        })
        // exam history
        .state('root.examhistory', {
            url: '/examhistory',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_examhistory.html'
                }
            }
        })
        
        // prepair test
        .state('root.preparation', {
            url: '/preparation/:chapter_id/:exam_id/:subName',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_preparation.html'
                }
            }
        })
        // Admin login
        .state('root.admin', {
            url: '/admin',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_login.html'
                }
            }
        })
        
        
        
        
        // top scoring student final exam
        .state('root.studentscoring', {
            url: '/studentscoring',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_studentscoring.html'
                }
            }
        })
        
        // about us page
        .state('root.aboutus', {
            url: '/aboutus',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_aboutus.html'
                }
            }
        })
        // contact us page
        .state('root.contactus', {
            url: '/contactus',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_contactus.html'
                }
            }
        })
       
         // term and  condition page
        .state('root.term&conditions', {
            url: '/term&conditions',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_term&conditions.html'
                }
            }
        })
         // privacy and policy
        .state('root.privacypolicy', {
            url: '/privacypolicy',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_privacy_policy.html'
                }
            }
        })
         // disclaimer
        .state('root.refund', {
            url: '/refund',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_refund&cancellation.html'
                }
            }
        })
          // cancwllation policy
        .state('root.disclaimer', {
            url: '/disclaimer',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_disclaimer.html'
                }
            }
        })
   
        // forget
        .state('root.forget', {
            url: '/forget/:type/:token',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_forget.html'
                }
            }
        })
        //////////////////   Student ////////////////////////////////
        // welcome
        .state('root.welcome', {
            url: '/welcome',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_welcome.html'
                }
            }
        })

        .state('root.welcomepurchase', {
            url: '/welcomepurchase/:purmodal',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_welcome.html'
                }
            }
        })
        // payment
        .state('root.payment', {
            url: '/payment',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_payment.html'
                }
            }
        })
        // important link
        .state('root.admissionlinks', {
            url: '/admissionlinks',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_importantlink.html'
                }
            }
        })
        // Payment Success
        .state('root.paymentsuccess', {
            url: '/paymentsuccess',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_payment_success.html'
                }
            }
        })
        // Payment Failure
        .state('root.paymentfailure', {
            url: '/paymentfailure',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_payment_failure.html'
                }
            }
        })
        // student profile
        .state('root.studprofile', {
            url: '/studprofile',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_studprofile.html'
                }
            }
        }) 
        .state('root.finaltest', {
            url: '/finaltest/:exam_schedule_id/:student_exam_id/:examid/:rollno',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_finaltest.html'
                }
            }
        })
        // single exam result
        .state('root.singleexamresult', {
            url: '/singleexamresult/:id',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_singleexamresult.html'
                }
            }
        })
        .state('root.examresult', {
            url: '/examresult',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_examresult.html'
                }
            }
        })

          // course page
        .state('root.coursepage', {
            url: '/coursepage',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_coursepage.html'
                }
            }
        })
          // pricing page
        .state('root.pricingpage', {
            url: '/pricingpage',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_pricing.html'
                }
            }
        })
  
    /*     // important link
        .state('root.admissionlinks', {
            url: '/admissionlinks',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_importantlink.html'
                }
            }
        })*/
        // student feedback page
        .state('root.studfeedback', {
            url: '/studfeedback',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/_studfeedback.html'
                }
            }
        })
        // student feedback concern
        .state('root.fbconcern', {
            url: '/fbconcern',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/student/_fbconcern.html'
                }
            }
        })
        // student feedback concern
        .state('root.subscribe', {
            url: '/subscribe',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_subscribe.html'
                }
            }
        })
        // Master student ranks solved
        .state('root.studentranks', {
            url: '/studentranks',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_studentranks.html'
                }
            }
        }) 

          // student feedback concern
        .state('root.testimonial', {
            url: '/testimonial',
            views: {
                'master@root': {
                    templateUrl: 'views/pages/master/_testimonialManage.html'
                }
            }
        })

     $locationProvider.html5Mode(true);
    }
   
]);
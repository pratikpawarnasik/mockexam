angular.module('ngApp.questionCtrl', [])
.filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
}])
.controller('vidManageQuestionCtrl', function($scope,$interval,$cookies,$state,$stateParams,$compile,$rootScope,$window,$http,$location){
	$scope.questionArr = [];

	$scope.paragraphArr = [];
	$scope.selection = [];
	$scope.optionDet = [];
	$scope.questionPreviewDet = [];
	$scope.excelerror = [];
	$scope.questionDet = {};
	$scope.action = "add";
	$scope.formname = "Add New Question";
	$scope.edit = false;
	$scope.add = true;
	$scope.is_changed_final = '';
	$scope.id = '';
	$scope.authcode = $cookies.get('authcode');
	$scope.type = $cookies.get('type');
	$rootScope.header_show = $scope.type;
	$scope.userid = parseInt($cookies.get('userid'));
	$scope.email = $cookies.get('email');
	//$scope.instid = parseInt($cookies.get('instid'));
	$scope.chapterId = $stateParams.id;
	$scope.totalQun = $stateParams.totalQun;
	$scope.count = 4;
	$rootScope.loading = false;
	$scope.finaldata = {};
	
	$scope.quesOptByIdData = [];
	
	$scope.rem_space = 0;
	
	$scope.ques_img_delete_id = 0;
	$scope.option_img_delete_id = [];
	$scope.expl_img_delete_id = 0;
	$scope.para_img_delete_id = 0;
	getQunStatusFun();
	$scope.doTheBack = function() {
	  window.history.back();
	};
	$scope.deleteQuesImg = function(id){
		$scope.ques_img_delete_id = id;
		$("#editQuestionImgDiv").empty();
	}
	$scope.deleteExplImg = function(){
		$scope.expl_img_delete_id = $scope.correctoptionid;
		$("#editExplImgDiv").empty();
	}
	$scope.deleteParaImg = function(){
		$scope.para_img_delete_id = $scope.paragraph_id;
		$("#editParaImgDiv").empty();
	}	
	$scope.deleteOptionImg = function(optioncount,optionidtemp){
		$scope.option_img_delete_id[optioncount-1] = optionidtemp;
		var idx = $scope.optTempIdArr.indexOf(optioncount);	
		$scope.optTempIdArr.splice(idx,1);
		$scope.optTempNameArr.splice(idx,1);		
		$("#viewoptimg"+optioncount).empty();
	}
		
	var wrapper_ht = $(window).height();
    $('.wrapper').css('min-height',wrapper_ht-246);
    CKEDITOR.replace('questiontext', {height: 120});
   //CKEDITOR.replace( 'questiontext');
   CKEDITOR.replace( 'option1', {height: 80});
   CKEDITOR.replace( 'option2', {height: 80});
   CKEDITOR.replace( 'option3', {height: 80});
   CKEDITOR.replace( 'option4', {height: 80});
   CKEDITOR.replace( 'explanation', {height: 120});
   //CKEDITOR.replace( 'questiontext', {
	   //extraPlugins: 'imageuploader'
	//});
	
    //CKEDITOR.replace( 'paratext', {height: 150});
    
   $scope.interval = $interval(function() {
            $("#message").removeClass('has-error');
			$scope.message = '';
			$("#getmessage").removeClass('has-error');
			$scope.getmessage = '';
    }, 36000);
          
    $scope.currentPage = 1;
	$scope.numPerPage = 20;
	$scope.totalcount = 0;
	$scope.maxSize = 5;
    $scope.begin = 0;
    $scope.previusQunCount=0;
    var flag = 0;
    $("#numPerPage").val($scope.numPerPage);
    $scope.pageChange = function(currentPage) {
			$scope.currentPage = currentPage;
	   		$scope.begin = (($scope.currentPage - 1) * $scope.numPerPage);
	   		$scope.previusQunCount = ((currentPage - 1) * $scope.numPerPage);
	   		$scope.getQuestion();
	};
	/*$scope.setItemsPerPage = function(num) {
		$scope.numPerPage = parseInt(num);
		$scope.currentPage = 1; 
		$scope.begin = 0;
		$scope.getQuestion();
	}*/
	$("#numPerPage").change(function(){
		$scope.numPerPage = parseInt($("#numPerPage").val());
		$scope.currentPage = 1; 
		$scope.begin = 0;
		$scope.getQuestion();
	});
	
	//reset page
	$scope.reset = function(type){
		 $state.reload();
	}
	
	$scope.courseid = '';
    $scope.chapterid = parseInt($stateParams.id);
    $scope.topicid = $stateParams.topicid;
    
    $scope.topicname = '';
    if($scope.chapterid != null)
    {
    	$scope.param = {
			'id' : $scope.chapterid,
			'topicid' : $scope.topicid,
			'userid' : $scope.userid,
			//'instid' : $scope.instid
		}
		var my_url = url_questionchapter+$.param($scope.param);
		$http({
		        method : 'GET',
		        url : my_url,
		        headers : {'authcode': $scope.authcode}
		     }).success(function(response){
		     	if(response.status == 200)
		      	{
		      		if($scope.topicid != null)
		      		$scope.topicname = response.topicname;
		      		
		      		$scope.courseid = response.courseid;
		      		$scope.chaptername = response.name;
					if($scope.chapterid != response.id)
					{
						$window.location.href = $rootScope.base_url+'mastercourse';
					} 
				}
				else{
					//alert(response.message);
					$window.location.href = $rootScope.base_url+'mastercourse';
					$("#message").addClass('has-error');
					$scope.message = response.message;
				}
		     }).error(function(error){
		     		$("#message").addClass('has-error');
				$scope.message = "Some unknown error has occurred. Please try again.";
		     });
	}
	else{
		$window.location.href = $rootScope.base_url+'mastercourse';
	}
	
    $scope.downloadexcel = function()
    {
    	$rootScope.loading = true;
    	$scope.param = {
		//	'instid' : $scope.instid,
			'chapterid' : $scope.chapterid,
			'courseid' : $scope.courseid
		}
		var my_url = url_excel+$.param($scope.param);	
		$http({
				              method : 'get',
				              url : my_url,
				              headers : {'authcode': $scope.authcode},
				              responseType: 'arraybuffer'
				           }).success(function (data, status, headers, config) {
				           	
    						$rootScope.loading = false;
				           	window.open(my_url,'_blank' );
		    
		}).error(function(error){
								$rootScope.loading = false;
				           		$("#getmessage").addClass('has-error');
								$scope.getmessage = "Some unknown error has occurred. Please try again.";
				           });
	}
	
	//check package
	$scope.getPackage = function()
	{
		return true;
		/*ServerService.getPackageInfo($scope.instid).then(function(response){
				response = response.data;
		        if(response.status == 200){
		        	//alert(response.package.rem_space);	
		        	$scope.rem_space = parseFloat(response.package.rem_space);	        	
				}
				else{
					$scope.rem_space = 0;
				}
		 });*/
	 }
	 
	/*file upload*/
    function readURL(input) {
    	var fileTypes = ['xls','xlsx']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if (isSuccess) { 
	            /*var reader = new FileReader();
	            reader.onload = function (e) {
	                //$('#blah').attr('src', e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);*/
            }
            else{
            	alert("please select only excel file. eg. xls,xlsx");
				$scope.myFile = null;
				$("#upload-file-info").empty();
				document.getElementById('myFile').value = null;
			}
        }
    }
    
    /*question img upload*/
    function readImgURL(input) {
    	var fileTypes = ['jpg','jpeg','png','gif','bmp']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if(isSuccess){
			    if(input.files[0].size <= 1048576){
 						if($scope.questionDet.imgpath != null && $scope.questionDet.imgpath != ''){
							$scope.ques_img_delete_id = $scope.id;
							$("#editQuestionImgDiv").empty();
						} 
				}
		        else{		        	
					alert("Please select image size is less than 1 MB");
					$scope.quesImg = null;
					$("#upload-ques-info").empty();
					document.getElementById('quesImg').value = null;
				}
			}
            else{            	
            	alert("please select only jpg,jpeg,png,gif,bmp file.");
				$scope.quesImg = null;
				$("#upload-ques-info").empty();
				document.getElementById('quesImg').value = null;
			}
		}
    }
    
    /*para img upload*/
    function readParaURL(input) {
    	var fileTypes = ['jpg','jpeg','png','gif','bmp']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  
            var isSuccess = fileTypes.indexOf(extension) > -1; 
 			if(isSuccess){
			    if(input.files[0].size <= 1048576)
 				{
 						if($scope.paraImg != null && $scope.paraImg != ''){
							$scope.para_img_delete_id = $scope.paragraph_id;
							$("#editParaImgDiv").empty();
						} 	
				}
		        else{
					alert("Please select image size is less than 1 MB");
					$scope.paraImg = null;
					$("#upload_para_img").empty();
					document.getElementById('paraImg').value = null;
				}
			}
            else{
            	alert("please select only jpg,jpeg,png,gif,bmp file.");
				$scope.paraImg = null;
				$("#upload_para_img").empty();
				document.getElementById('paraImg').value = null;
			}
		}
    }
    
    /*expl img upload*/
    function readExplURL(input) {
    	var fileTypes = ['jpg','jpeg','png','gif','bmp']; 
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if(isSuccess){ 			       
			    if(input.files[0].size <= 1048576){
 						if($scope.questionDet.expimg != null && $scope.questionDet.expimg != ''){
							$scope.expl_img_delete_id = $scope.correctoptionid;
							$("#editExplImgDiv").empty();
						}
				}
		        else{
					alert("Please select image size is less than 1 MB");
					$scope.expimg = null;
					$("#upload_expimg").empty();
					document.getElementById('expimg').value = null;
				}
			}
            else{            	
            	alert("please select only jpg,jpeg,png,gif,bmp file.");
				$scope.expimg = null;
				$("#upload_expimg").empty();
				document.getElementById('expimg').value = null;
			}
		}
    }
    
    $scope.optTempIdArr = [];
    $scope.optIdArr = [];
    $scope.optNameArr = [];
    $scope.optTempNameArr = [];
    
 /*   function readImgOpt(input) {
    	var id = input.id;
    	var optid = parseInt(input.getAttribute('optid'));
    	var optindex = parseInt(optid) - 1;    	
    	//alert(optindex);
    	
    	//console.log($scope.option_img_size);
    	var fileTypes = ['jpg','jpeg','png','gif','bmp']; 
        if (input.files && input.files[0]) {
        	//console.log(input.files[0]);
            var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
            var isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
 			if(isSuccess){ 			       
			    if(input.files[0].size <= 1048576)
 				{
 						if(typeof $scope.quesOptByIdData.options[optindex]['imgpath'] !== 'undefined')							{
 							if($scope.quesOptByIdData.options[optindex]['imgpath'] != null && $scope.quesOptByIdData.options[optindex]['imgpath'] != ''){	 							
								$scope.option_img_delete_id[optindex] = $scope.quesOptByIdData.options[optindex]['optionid'];
								var idx = $scope.optTempIdArr.indexOf(optid);
								$scope.optTempIdArr.splice(idx,1);
								$scope.optTempNameArr.splice(idx,1);
								$("#viewoptimg"+optid).empty();
							} 
						}
				}
		        else{		        	
					alert("Please select image size is less than 1 MB");
					$scope.setOptImgNull(optid);
					$("#upload_optimg"+optid).empty();
					document.getElementById('optimg'+optid).value = null;
				}   
			}
            else{            	
            	alert("please select only jpg,jpeg,png,gif,bmp file.");        	
            	$scope.setOptImgNull(optid);
				$("#upload_optimg"+optid).empty();
				document.getElementById('optimg'+optid).value = null;
			}
		}
    }
    
    $scope.setOptImgNull = function(optid){
				if(optid == 1){
					$scope.optimg1 = null;
				}
				else
				if(optid == 2){
					$scope.optimg2 = null;
				}
				else
				if(optid == 3){
					$scope.optimg3 = null;
				}
				else
				if(optid == 4){
					$scope.optimg4 = null;
				}
				else
				if(optid == 5){
					$scope.optimg5 = null;
				}
				else
				if(optid == 6){
					$scope.optimg6 = null;
				}
				else
				if(optid == 7){
					$scope.optimg7 = null;
				}
				else
				if(optid == 8){
					$scope.optimg8 = null;
				}
				else
				if(optid == 9){
					$scope.optimg9 = null;
				}
				else
				if(optid == 10){
					$scope.optimg10 = null;
				}
				return true;
	}
	*/
    $("#myFile").change(function(){
        readURL(this);
    });
    
    $("#expimg").change(function(){
        readExplURL(this);
    });
    
    $("#quesImg").change(function(){
        readImgURL(this);
    });
    
    $("#paraImg").change(function(){
        readParaURL(this);
    });
    
    /* Option Img Upload*/
    $('#append_options INPUT[type="file"]').change(function () {
    	readImgOpt(this);
        });
    $scope.popimglink = null;
   
   //show images on pop in edit question
    $scope.showPopup = function(imgpath){
    	if(imgpath != null && imgpath != ''){
    		$scope.popimglink = imgpath;
			$("#imagepopupshow").modal('show');
		}
	}
	
    $scope.uploadQuestion = function(){
    	
    	$("#uploadmessage").removeClass('has-error');
    	$("#uploadmessage").empty();
		var file = $scope.myFile;
		var fd = new FormData();
       	if($scope.myFile == null)
		{
				$("#uploadmessage").addClass('has-error');
				$("#uploadmessage").html("Please upload question bank");
				return false;
		}
       	var file = $scope.myFile;
        fd.append('ques_file', file);
        fd.append('userid', $scope.userid);
        fd.append('email', $scope.email);
        //fd.append('instid', $scope.instid);
        fd.append('usertype', $scope.type);
        fd.append('courseid', $scope.courseid);
        fd.append('chapterid', $scope.chapterid);
        fd.append('topicid', $scope.topicid);
        //console.log(fd);
        $rootScope.loading = true;
       	$http.post(url_uploadquestion, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined,'authcode':$scope.authcode,'Process-Data': false}
        })
        .success(function(resourse){
        	$rootScope.loading = false;
        	$scope.getQuestion();
        	$scope.myFile = null;
        	$("#upload-file-info").empty();
			document.getElementById('myFile').value = null;
			if(resourse.error != 0)
			{
				$("#uploadmessage").append("<span style='color:red;'>Total questions : "+(resourse.error+resourse.success) +"</span>");
				$("#uploadmessage").append("<br /><span style='color:red;'>Error questions : "+resourse.error +"</span>");
				$("#uploadmessage").append("<br /><span style='color:red;'>"+resourse.errorexcel+"</span>");
			}
			else{
				$("#uploadmessage").append("<span style='color:red;'>Total questions : "+(resourse.success) +"</span>");
			}
			if(resourse.success != '')
			{
				$("#uploadmessage").append("<br /><span style='color:green;'>Successfully added questions : "+resourse.success +".</span><br />");
			}
        })
        .error(function(error){
        	$rootScope.loading = false;
        	return false;
        });
	}
	
	//download error excel
	$scope.downerrorexcel = function(){
		
		$scope.param = {
			'userid' : $scope.userid,
			'email' : $scope.email,
			'errorexcel' : $scope.excelerror
		}
		//console.log($scope.param);
		var my_url = url_downerrorexcel;	
		$rootScope.loading = true;
		$http({
				              method : 'POST',
				              url : my_url,
				              data : $.param($scope.param),
				              headers : {'authcode': $scope.authcode,'Content-Type': 'application/x-www-form-urlencoded'},
				              responseType: 'arraybuffer'
				           }).success(function (data, status, headers, config) {
				           //	$("#uploadmessage").empty();
    						$rootScope.loading = false;
				           	//window.open(my_url,'_blank' );
		    
		}).error(function(error){
								$rootScope.loading = false;
				           		$("#getmessage").addClass('has-error');
								$scope.getmessage = "Some unknown error has occurred to download error. Please try again.";
				           });
	 }
	
	//get para list
	$scope.getParaList = function()
    {
    	$rootScope.loading = true;
    	$scope.param = {
			//'instid' : $scope.instid,
			'usertype' : $scope.type,
			'userid' : $scope.userid,
			'chapterid' : $scope.chapterid,
			'topicid' : $scope.topicid
		}
		var my_url = url_getparalist+$.param($scope.param);	
		$http({
		      method : 'get',
		      url : my_url,
		      headers : {'authcode': $scope.authcode}
		   }).success(function(response){
		   	$rootScope.loading = false;
		   	if(response.status == 200){
	     		$scope.paragraphArr = response.paralist;
	     		if($scope.paragraphArr.length > 0){
					$("#list_paraghaph").modal('show');
				}else{
					alert("Paragraph are either deleted or not inserted.");
				}
			}
			else{
				$scope.paragraphArr = [];
				alert("Paragraph are either deleted or not inserted.");
			}
		}).error(function(error){
				$rootScope.loading = false;
		    	alert("Some unknown error has occurred. Please try again.");   
		    });
	}
	 
    //close question modal
	$scope.clickclose = function()
	{
		$("#add_new_question").modal('hide');
	}
	//close para modal
	$scope.clickParaClose = function()
	{
		$scope.paragraph_text = '';
		$scope.paragraph_id = 0;
		$scope.paraaction = 'add';
		$("#add_paraghaph_question").modal('hide');
	}

	$scope.clickFinalStatus = function(qunId,finalstatus)
	
	{	
		if (finalstatus === 1) {
			var isShow = 1;
		}else{
			var isShow = 0;
		}
		//console.log(isShow);
		$scope.finaldata.qunId = qunId;
		$scope.finaldata.finalstatus = isShow;
		
		$rootScope.loading = true;
		$http({
		     method : "PUT",
		     url : url_setForFinal,
		     data: $.param($scope.finaldata),
		     headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode,'Process-Data': false}
		}).success(function(response){
			//console.log(response);
			$rootScope.loading = false;
		     if(response.status == 200){
		     	getQunStatusFun();
				$scope.getQuestion();
		     }
		     else{
			 	$("#paramessage").addClass('has-error');
				$scope.paramessage = response.message;
			 }
			
		}).error(function(error){
		     $rootScope.loading = false;
		     $("#paramessage").addClass('has-error');
			 $scope.paramessage = "Some unknown error has occurred. Please try again.";
		});
		
				//getQuestion();
		//$window.location.reload();
	}
	function getQunStatusFun() {
		$scope.statusMessage = '';
		$("#statusMessage").hide();
		$scope.param = {
			'chapterid' : $scope.chapterId
		}
		$scope.statusMessage = '';
		var my_url = url_is_final+$.param($scope.param);	
		$http({
		      method : 'get',
		      url : my_url,
		      headers : {'authcode': $scope.authcode}
		   }).success(function(response){
		   	//console.log(response);
		   	$rootScope.loading = false;
		   	if(response.status == 200){
	     		$scope.qunStatus = response.qunStatus.qun_status;
	     		$scope.qunCount = response.qunStatus.qunCount;
	     		if ($scope.qunCount== null) {$scope.qunCount = 0}
	     		if($scope.qunStatus == 'hide'){
	     	
	     			$("#statusMessage").show();
	     			$("#statusMessage").addClass('has-error');

	     			$scope.statusMessage = '';
					$scope.statusMessage = "Multiple questions already select for final exam.";

				}else{
					$("#statusMessage").hide();
					$scope.statusMessage = '';
					$scope.statusMessage = "Select for final exam.";
					
				}
			}
			else{
				$scope.paragraphArr = [];
				$("#statusMessage").hide();
					$scope.statusMessage = '';
					$scope.statusMessage = "Select for final exam.";
				//alert("Max question selected for final exam.");
			}
		}).error(function(error){
				$rootScope.loading = false;
		    	//alert("Some unknown error has occurred. Please try again.");   
		    });
	}

	$scope.openModal = function(modelid)
	{	
		// is check is final question or not...
		getQunStatusFun();



		if(modelid == 0){
			$scope.paragraph_id = 0;
			$scope.paraaction = 'add';
		}
		//$scope.getPackage();
		$scope.action = "add";
		$scope.formname = "Add New Question";
		$scope.edit = false;
		$scope.add = true;
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$("#uploadmessage").empty();
		$scope.message = '';
		if($scope.count > 4)
		 {
		 	var valu = $scope.count;
			for(var i=5; i <= parseInt(valu) ;i++)
			{
				$scope.removeoption(i);
			}
		}
		$scope.id = '';
		$scope.quesImg = null;
        //$("#upload-ques-info").empty();
		//document.getElementById('quesImg').value = null;
		
		CKEDITOR.instances.questiontext.setData('');
		CKEDITOR.instances.option1.setData('');
		CKEDITOR.instances.option2.setData('');
		CKEDITOR.instances.option3.setData('');
		CKEDITOR.instances.option4.setData('');
			if (document.getElementById('option5')) {
				CKEDITOR.instances.option5.setData('');
			}
			if (document.getElementById('option6')) {
				CKEDITOR.instances.option6.setData('');
			}
			if (document.getElementById('option7')) {
				CKEDITOR.instances.option7.setData('');
			}
			if (document.getElementById('option8')) {
				CKEDITOR.instances.option8.setData('');
			}
			if (document.getElementById('option9')) {
				CKEDITOR.instances.option9.setData('');
			}
			if (document.getElementById('option10')) {
				CKEDITOR.instances.option10.setData('');
			}
		CKEDITOR.instances.explanation.setData('');
	//	CKEDITOR.instances.paratext.setData('');
		$scope.questionDet = {};
		$scope.ques_img_size = 0;
		$scope.option_img_size = [];
		$scope.expl_img_size = 0;		
		$scope.questionForm.reset();
		$scope.count = 4;
		$scope.questionDet.correctid = 1;
		$scope.questionDet.sequence = 0;
		
		$("#add_new_question").modal('show');
	}
	
	$scope.paragraphDet = {};
	$scope.paragraph_id = 0;
	// open paragraph model
	$scope.openParaModal = function(){
		$("#paramessage").removeClass('has-success');
		$("#paramessage").removeClass('has-error');
		$scope.paramessage = "";
		
		$scope.addPara = true;
		$scope.editPara = false;
		$scope.para_img_size = 0;
		$scope.para_img_size_temp = 0;	
		$scope.para_img_delete_id = 0;
		//$scope.getPackage();	
		$scope.paraImg = null;
        $("#upload_para_img").empty();
        $scope.paragraphDet = {};
		$scope.paragraph_id = 0;
		$scope.paraaction = 'add';
		//document.getElementById('paraImg').value = null;
		//CKEDITOR.instances.paratext.setData('');
		$("#add_paraghaph_question").modal('show');
	}
	//get question list
	$scope.getQuestion = function(){
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		$("#tablemessage").removeClass('has-success');
		$scope.getmessage = '';
		$scope.tablemessage = '';
		$scope.param = {
			//'instid' : $scope.instid,
			'usertype' : $scope.type,
			'userid' : $scope.userid,
			'chapterid' : $scope.chapterid,
			'topicid' : $scope.topicid,
			'searchtext' : $scope.searchtext,
			'start' : $scope.begin,
			'limit' : $scope.numPerPage
		}
		//console.log($scope.param);
		var my_url = url_getquestion+$.param($scope.param);	
		$rootScope.loading = true;
		$http({
	       method : 'GET',
	       url : my_url,
	       headers : {'authcode': $scope.authcode}
	    }).success(function(response){
	     	$rootScope.loading = false;
	     	//console.log(response);
	     	if(response.status == 200)
	     	{
	     		$scope.totalcount = response.totalcount;
	     		$scope.questionArr = response.question;
			}
			else{
				$scope.questionArr = [];
				$("#tablemessage").addClass('has-success');
				$scope.tablemessage = "Question are either deleted or not inserted.";
			}
	    }).error(function(error){
	    	$rootScope.loading = false;
	    	$("#message").addClass('has-error');
			$scope.message = "Some unknown error has occurred. Please try again.";
	    });
	 }
	
	

	//add question		
	$scope.submitCreateQuestion = function(){		
		$("#message").removeClass('has-success');
			$("#message").removeClass('has-error');
			$scope.message = "question adding...";
			$scope.questionDet.text = CKEDITOR.instances.questiontext.getData();
			if(!$scope.questionDet.text || $scope.questionDet.text == '')
			{
				$("#message").addClass('has-error');
				$scope.message = "Please add question text.";
				return false;
			}
			var my_url = url_addquestion;
			var method = "POST";
			var dispmessage = "Question added successfully.";

			if($scope.action == "edit")
			{
				my_url = url_updatequestion;
				method = "PUT";
				$scope.message = "Question editing...";
				//$scope.message = "Question updated successfully.";
				$scope.questionDet.id = $scope.id;
				var dispmessage = "Question updated successfully.";
				$scope.optIdArr = [];
			    $scope.optNameArr = [];
			    //$scope.questionDet.option_img_size_temp = $scope.option_img_size_temp;
			    //$scope.questionDet.expl_img_size_temp = $scope.expl_img_size_temp;
			    $scope.questionDet.option_img_delete_id = $scope.option_img_delete_id;
			    $scope.questionDet.ques_img_delete_id = $scope.ques_img_delete_id;
			    $scope.questionDet.expl_img_delete_id = $scope.expl_img_delete_id;
			    
			    //$scope.questionDet.optionsimg = []
				//$scope.questionDet.optionsimg = countImg();
				for(i=0;i < $scope.optTempIdArr.length;i++)
				{
					var idopt = $scope.optTempIdArr[i];
					var idopt1 = parseInt(idopt) - 1;
					//$scope.questionDet.optionsimg[idopt1] = 1;
					var result = checkoptimg(idopt);
					if(!result)
					{
						$scope.optIdArr.push(idopt);
						$scope.optNameArr.push($scope.optTempNameArr[i]);
					}
				}
				$scope.questionDet.optIdArr = $scope.optIdArr;
				$scope.questionDet.optNameArr = $scope.optNameArr;

			}
			else{
				//$scope.questionDet.optionsimg = countImg();
			}
			$scope.questionDet.options=[];
			$scope.questionDet.options[0] = CKEDITOR.instances.option1.getData();
			$scope.questionDet.options[1] = CKEDITOR.instances.option2.getData();
			$scope.questionDet.options[2] = CKEDITOR.instances.option3.getData();
			$scope.questionDet.options[3] = CKEDITOR.instances.option4.getData();
			if (document.getElementById('option5')) {
				$scope.questionDet.options[4] = CKEDITOR.instances.option5.getData();
			}
			if (document.getElementById('option6')) {
				$scope.questionDet.options[5] = CKEDITOR.instances.option6.getData();
			}
			if (document.getElementById('option7')) {
				$scope.questionDet.options[6] = CKEDITOR.instances.option7.getData();
			}
			if (document.getElementById('option8')) {
				$scope.questionDet.options[7] = CKEDITOR.instances.option8.getData();
			}
			if (document.getElementById('option9')) {
				$scope.questionDet.options[8] = CKEDITOR.instances.option9.getData();
			}
			if (document.getElementById('option10')) {
				$scope.questionDet.options[9] = CKEDITOR.instances.option10.getData();
			}
			if (document.getElementById('explanation')) {
				$scope.questionDet.explanation = CKEDITOR.instances.explanation.getData();
			}
			//$scope.questionDet.instid = $scope.instid;
			$scope.questionDet.usertype = $scope.type;
			$scope.questionDet.courseid = $scope.courseid;
			$scope.questionDet.chapterid = $scope.chapterid;
			$scope.questionDet.topicid = $scope.topicid;
			$scope.questionDet.userid = $scope.userid;
			//$scope.questionDet.options = $scope.optionDet;
			$scope.questionDet.paragraph_id = $scope.paragraph_id;
			//console.log($scope.questionDet);
			
			$rootScope.loading = true;
				$http({
		              method : method,
		              url : my_url,
		              data: $.param($scope.questionDet) ,
		              headers : {'Content-Type': 'application/x-www-form-urlencoded','authcode': $scope.authcode}
		           }).success(function(response){
		            	$rootScope.loading = false;
		            	if(response.status == 200)
		            	{
		            		
							
									//$("#add_new_question").modal('hide');
								
       						
							//$scope.upload(response.id);							
		            		$scope.getQuestion();

		            		
		            		if($scope.paraaction == "edit" && $scope.paragraph_id > 0){
		            			$scope.editParaghaph($scope.paragraph_id);
		            			
		            		}
		            		if($scope.action == "edit"){
		            			$scope.ques_img_delete_id = 0;
								$scope.option_img_delete_id = [];
								$scope.expl_img_delete_id = 0;	

							}
							if($scope.action == "add"){
								CKEDITOR.instances.questiontext.setData('');
								CKEDITOR.instances.option1.setData('');
								CKEDITOR.instances.option2.setData('');
								CKEDITOR.instances.option3.setData('');
								CKEDITOR.instances.option4.setData('');
								CKEDITOR.instances.explanation.setData('');
								if (document.getElementById('option5')) {
									$scope.questionDet.options[4] = CKEDITOR.instances.option5.getData();
								}
								if (document.getElementById('option6')) {
									$scope.questionDet.options[5] = CKEDITOR.instances.option6.getData();
								}
								if (document.getElementById('option7')) {
									$scope.questionDet.options[6] = CKEDITOR.instances.option7.getData();
								}
								if (document.getElementById('option8')) {
									$scope.questionDet.options[7] = CKEDITOR.instances.option8.getData();
								}
								if (document.getElementById('option9')) {
									$scope.questionDet.options[8] = CKEDITOR.instances.option9.getData();
								}
								if (document.getElementById('option10')) {
									$scope.questionDet.options[9] = CKEDITOR.instances.option10.getData();
								}
								if (document.getElementById('explanation')) {
									$scope.questionDet.explanation = CKEDITOR.instances.explanation.getData();
								}
								if($scope.count > 4)
								{
									var valu = $scope.count;
									for(var i=5; i <= parseInt(valu) ;i++)
									{
										$scope.removeoption(i);
										/*CKEDITOR.instances.option+i.setData('');*/

									}
								
								}
								
								$scope.optionDet = [];
		            			$scope.questionDet = {};
		            			$scope.questionForm.reset();
								$scope.questionDet.correctid = 1;
								$scope.questionDet.sequence = 0;
		            		}		            		
							$("#message").addClass('has-success');
							//$scope.message = '';
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
	
	function checkoptimg(idopt)
	{
		var result = false;
		
		if(idopt == 1){
			if($scope.optimg1 != null){
				result = true;
			}
		}
		else
		if(idopt == 2){
			if($scope.optimg2 != null){
				result = true;
			}
		}
		else
		if(idopt == 3){
			if($scope.optimg3 != null){
				result = true;
			}
		}
		else
		if(idopt == 4){
			if($scope.optimg4 != null){
				result = true;
			}
		}
		else
		if(idopt == 5){
			if($scope.optimg5 != null){
				result = true;
			}
		}
		else
		if(idopt == 6){
			if($scope.optimg6 != null){
				result = true;
			}
		}
		else
		if(idopt == 7){
			if($scope.optimg7 != null){
				result = true;
			}
		}
		else
		if(idopt == 8){
			if($scope.optimg8 != null){
				result = true;
			}
		}
		else
		if(idopt == 9){
			if($scope.optimg9 != null){
				result = true;
			}
		}
		else
		if(idopt == 10){
			if($scope.optimg10 != null){
				result = true;
			}
		}
		
		return result;
	}
	

	$scope.uploadPara = function(id){		
		
		var fd = new FormData();
       	//var file = $scope.myFile;
       	
        if($scope.paraImg != null){
        	fd.append('paraImg', $scope.paraImg);
        	fd.append('paraid', id);
	        fd.append('userid', $scope.userid);
	        //fd.append('instid', $scope.instid);
	        $rootScope.loading = true;
	       	$http.post(url_uploadparaimg, fd, {
	            transformRequest: angular.identity,
	            headers: {'Content-Type': undefined,'authcode':$scope.authcode,'Process-Data': false}
	        })
	        .success(function(resourse){
	        		$rootScope.loading = false;
					if(resourse.status == 200){
						$scope.paraImg = null;
	        			$("#upload_para_img").empty();
	        			$scope.editParaghaph(id);
	        		}
	        		else{ 
						$("#paramessage").addClass('has-error');	
						$scope.paramessage = resourse.message;
	        		}
						            	
	        })
	        .error(function(error){
	        	$rootScope.loading = false;
	        	$("#paramessage").addClass('has-error');	
				$scope.paramessage = "Some unknown error has occurred. Please try again.";
	        });
        }
        else{
			$scope.editParaghaph(id);
		}
       
	}
	
	$scope.optionImgSizeArr = [];
	
	//edit paraghaph           
/*	$scope.editParaghaph = function(id){
		$("#paramessage").removeClass('has-success');
		$("#paramessage").removeClass('has-error');
		$("#upload_para_img").empty();
		$("#list_paraghaph").modal('hide');
		$scope.paramessage = '';
			$scope.getPackage();
			$scope.para_img_delete_id = 0;
			$scope.paragraphDet = {};
			$scope.paragraph_id = id;
			$scope.paraaction = "edit";
			$scope.addPara = false;
			$scope.editPara = true;
			$scope.paraImg = null;
			$scope.param = {
				'id' : $scope.paragraph_id,
				'userid' : $scope.userid,
				//'instid' : $scope.instid
			}
			
		var my_url = url_getparabyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		           		$scope.paragraphDet = {};
						$scope.paraForm.reset();
						$("#add_paraghaph_question").modal('show');
		            	if(response.status == 200)
		            	{		            		
							CKEDITOR.instances.paratext.setData(response.text);
							$scope.paragraphDet.paraImg = response.imgpath;
							$scope.paragraphDet.queslist = response.questions;
						}
						else{
							$rootScope.loading = false;
							$("#paramessage").addClass('has-error');
							$scope.paramessage = response.message;
						}
		           }).error(function(error){
		           		$("#paramessage").addClass('has-error');
						$scope.paramessage = "Some unknown error has occurred. Please try again.";
		           });
	}	*/
	
	//edit question           
	$scope.editQuestion = function(id,modelid)     
	{
		if(modelid == 0){
			$scope.paragraph_id = 0;
			$scope.paraaction = 'add';
		}
		$scope.optTempIdArr = [];
	    $scope.optTempNameArr = [];
	    $scope.optionImgSizeArr = [];
	    $scope.quesOptByIdData = [];	    
		
		$scope.ques_img_delete_id = 0;
		$scope.option_img_delete_id = [];
		$scope.expl_img_delete_id = 0;
	   
		$("#message").removeClass('has-success');
		$("#message").removeClass('has-error');
		$("#uploadmessage").empty();
		$scope.message = '';
			if($scope.count > 4)
		 	{
		 		var valu = $scope.count;
				for(var i=5; i <= parseInt(valu) ;i++)
				{
					$scope.removeoption(i);
				}
			}
			$scope.count = 4;
			$scope.id = id;
			$scope.correctoptionid = 0;			
			$scope.action = "edit";
			$scope.formname = "Edit New Question";
			$scope.edit = true;
			$scope.add = false;
			$scope.quesImg = null;
			//$scope.questionDet.is_final = true;
			
            $("#upload-ques-info").empty();
			$scope.param = {
				'id' : $scope.id,
				'userid' : $scope.userid,
				//'instid' : $scope.instid
			}
		var my_url = url_getquestionbyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           	//console.log(response);
		           		$rootScope.loading = false;
		           		$scope.questionDet = {};
						$scope.questionForm.reset();
		           		if (response.is_final == 1) {
		           			$scope.qunStatus = 'show';
		           			$scope.questionDet.is_final = true;
		           			
		           		}else{

		           			getQunStatusFun();

		           			$scope.questionDet.is_final = false;
		           		}

		           		
						$("#add_new_question").modal('show');
		            	if(response.status == 200)
		            	{
		            		$scope.quesOptByIdData = response;
		            		
							/*$("#testdd").show();
							$("#sub_question_modal").hide();*/
							//$scope.questionDet.text = response.text;
							CKEDITOR.instances.questiontext.setData(response.text);

							/*CKEDITOR.instances.option1.setData(response.options[0].optiontext);
							CKEDITOR.instances.option2.setData(response.options[1].optiontext);
							CKEDITOR.instances.option3.setData(response.options[2].optiontext);
							CKEDITOR.instances.option4.setData(response.options[3].optiontext);*/
							$scope.questionDet.imgpath = response.imgpath;
							$scope.questionDet.sequence = response.sequence;
							$scope.questionDet.qunMark = response.qunMark;
							$scope.questionDet.qunNegMark = response.qunNegMark;
		            		$scope.optionDet = [];
		            		if(response.options.length > 4)
		            		{
								var extraoption = parseInt(response.options.length) - 4;
								for(var i=0; i < parseInt(extraoption) ;i++)
								{
									$scope.count++;
									$scope.appendOption($scope.count,response);
								}
							}
		            		for(var i=0; i < response.options.length ;i++)
		            		{
		            			if(response.options[i]['explanation'] != null)
		            			{		    
		            				CKEDITOR.instances.explanation.setData(response.options[i].explanation);        				
									//$scope.questionDet.explanation = response.options[i]['explanation'];
								}
								
								if(response.options[i]['imgpath'] != null)
		            			{
		            				$scope.optTempIdArr.push((i+1));
		            				$scope.optTempNameArr.push(response.options[i]['imgpath']);
		            				var path = response.options[i]['imgpath'];
		            				var htmldata = '<a class="btn" href="javascript:;" ng-click="showPopup(&#39;'+path+'&#39;)">View</a>';
		            				htmldata += '<a href="javascript:;" ng-click="deleteOptionImg('+(i+1)+','+response.options[i]['optionid']+')"><i class="fa fa-remove"></i></a>';
		            				var temp = $compile(htmldata)($scope);
								    
								    angular.element(document.getElementById("viewoptimg"+(i+1))).empty();
								    angular.element(document.getElementById("viewoptimg"+(i+1))).append(temp);
								}
								else{
									$("#viewoptimg"+(i+1)).empty();
								}
								if(parseInt(response.options[i]['optionid']) == parseInt(response.options[i]['correct_opt']))
								{
									$scope.questionDet.correctid = parseInt(i)+1;
									$scope.correctoptionid = response.options[i]['optionid'];
								}
								CKEDITOR.instances['option'+(i+1)].setData(response.options[i].optiontext);
								$scope.optionDet[i] = response.options[i]['optiontext'];
							}
						}
						else{
							$rootScope.loading = false;
							$("#getmessage").addClass('has-error');
							$scope.getmessage = response.message;
						}
		           }).error(function(error){
		           		$("#add_new_question").modal('hide');
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	//show images on pop in edit question
    $scope.showQuestionPreview = function(id){
    	$scope.param = {
				'id' : id,
				'userid' : $scope.userid,
				//'instid' : $scope.instid
			}
		var my_url = url_getquestionbyid+$.param($scope.param);
		$rootScope.loading = true;
		$http({
		              method : 'GET',
		              url : my_url,
		              headers : {'authcode': $scope.authcode}
		           }).success(function(response){
		           		$rootScope.loading = false;
		           		$scope.questionPreviewDet = {};
		            	if(response.status == 200)
		            	{
		            		$scope.questionPreviewDet = response;
		            		//console.log($scope.questionPreviewDet);
		            		$("#questionpreviewpopup").modal('show');
						}
						else{
							$("#getmessage").addClass('has-error');
							$scope.getmessage = response.message;
						}
		           }).error(function(error){
		           		$("#add_new_question").modal('hide');
		           		$("#getmessage").addClass('has-error');
						$scope.getmessage = "Some unknown error has occurred. Please try again.";
		           });
	}
	
	//delete single question
	$scope.deleteQuestion = function(id)
	{
		$("#getmessage").removeClass('has-success');
		$("#getmessage").removeClass('has-error');
		var deleteUser = $window.confirm('Are you sure to delete question?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'id' : id,
					'userid' : $scope.userid
				}
				var my_url = url_deletequestion;
				$rootScope.loading = false;
				$http({
				              method : 'DELETE',
				              url : my_url,
				              data : $.param($scope.param),
				              headers : {'authcode': $scope.authcode}
				           }).success(function(response){
				           		$rootScope.loading = true;
				            	if(response.status == 200)
				            	{
				            		getQunStatusFun();
				            		$scope.getQuestion();
				            		if($scope.paraaction == "edit" && $scope.paragraph_id > 0){
				            			$scope.editParaghaph($scope.paragraph_id);
				            		}
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Question deleted successfully.";
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
		     /// console.log($scope.selection);
		    }
	};
		  
	//delete multiple question
	$scope.delSelectQuestion = function()
	{
		$("#getmessage").removeClass('has-error');
		$("#getmessage").removeClass('has-success');
		$scope.getmessage = '';
		 var length = $scope.selection.length;
		 if(length > 10000)
		 {
		 	$("#getmessage").addClass('has-error');
		    $scope.getmessage = "Please select less than or equal to 10000 Students";
		 	return false;
		 }
		 if(length > 0)
		 {
		 	var deleteUser = $window.confirm('Are you sure to delete Students?');
		 	if(deleteUser)
		 	{
				$scope.getmessage = "delete...";
				$scope.param = {
					'userid' : $scope.userid,
					'ids' : $scope.selection
				}
				var my_url = url_deletemultiplequestion;
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
				            		var page = $scope.totalcount / $scope.numPerPage;
						   			if(Math.ceil(page) == $scope.currentPage)
						   			{
						   				$scope.currentPage = 1;
						   				$scope.begin = 0;
									}
				            		$scope.getQuestion();
				            		$("#getmessage").addClass('has-success');
				            		$scope.getmessage = "Question deleted successfully.";
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
		    $scope.getmessage = "Please select atleast one question";
		 }
	}	
	$("#select_questions").change(function(){
	    	$(".categories").prop('checked', $(this).prop("checked"));
	    	if($scope.allselect)
	    	{
				$scope.selection = [];
		    	angular.forEach($scope.questionArr, function(question) {
		    		$scope.selection.push(parseInt(question.id));
				});
				//console.log($scope.selection);
			}
			else{
				$scope.selection = [];
				//console.log($scope.selection);
			}	    	
	});	
	$scope.appendOption = function(no,response)

{						
	
		//alert((parseInt(no) - 1));
		var data='<div id="optionDiv'+no+'" class="margin">';
				data+='     		<div class="col-md-1"> ';
				data+='     		<input ';
				data+='     		type="radio" ';
				data+='            ng-model="questionDet.correctid" ';
				data+='            name="correctid" ';
				data+='            ng-value="'+no+'" ';
				data+='            checked="checked" ';
				data+='        />  ';
				data+='     		</div> ';
				data+='     		<div class="col-md-11"> ';
				data+=' 			Option '+no+'';
				data+=' 		<textarea ';
				data+=' 			type="text" ';
				data+=' 			name="option'+no+'" ';			
				data+=' 			id="option'+no+'" placeholder="option '+no+'"';					
				data+=' 			ng-model="optionDet['+(parseInt(no) - 1)+']" '; 
				data+='			class="dashboard_text mandatory" ';
				data+='			placeholder="Option '+no+'" ';
				data+=' 			autocomplete="off" ';
				data+=' 		/></textarea> ';
				data+=' 		</div> ';				
			    data+='	</div>';
				angular.element(document.getElementById('append_options')).append($compile(data)($scope));
				CKEDITOR.replace( 'option'+no, {height: 80});
	if (document.getElementById('option5')) {
		CKEDITOR.instances.option5.setData(response.options[4].optiontext);
	}
	if (document.getElementById('option6')) {
		CKEDITOR.instances.option6.setData(response.options[5].optiontext);
	}
	if (document.getElementById('option7')) {
		CKEDITOR.instances.option7.setData(response.options[6].optiontext);
	}
	if (document.getElementById('option8')) {
		CKEDITOR.instances.option8.setData(response.options[7].optiontext);
	}
	if (document.getElementById('option9')) {
		CKEDITOR.instances.option9.setData(response.options[8].optiontext);
	}
	if (document.getElementById('option10')) {
		CKEDITOR.instances.option10.setData(response.options[9].optiontext);
	}
	}	
	$scope.removeoption = function(no)
	{
		if($scope.optionDet.length >= parseInt(no))
		{
			$scope.optionDet.splice(parseInt(no)-1, 1);
		}
		
		var myEl = angular.element( document.querySelector( '#optionDiv'+no ) );
		myEl.remove();
		$scope.count--;
	}
})
//Directive for adding buttons on click that show an alert on click
.directive("addbuttons", function($compile){
	return function(scope, element, attrs){
		element.bind("click", function(){			
			if(scope.count < 10)
			{	
				scope.optionDet[scope.count] = "";
				scope.count++;
				var data='<div id="optionDiv'+scope.count+'" class="margin">';
				data+='     		<div class="col-md-1"> ';
				data+='     		<input ';
				data+='     		type="radio" ';
				data+='            ng-model="questionDet.correctid" ';
				data+='            name="correctid" ';
				data+='            ng-value="'+scope.count+'" ';
				data+='            checked="checked" ';
				data+='        />  ';
				data+='     		</div> ';
				data+='     		<div class="col-md-11"> ';
				data+=' 			Option '+scope.count+'';
				data+=' 		<textarea ';
				data+=' 			type="text" ';
				data+=' 			name="option'+scope.count+'" ';
				data+=' 			id="option'+scope.count+'" placeholder="option '+scope.count+'"';
				data+=' 			ng-model="optionDet['+(parseInt(scope.count) - 1)+']" ';
				data+='			class="dashboard_text mandatory" ';
				data+='			placeholder="Option '+scope.count+'" ';
				data+=' 			autocomplete="off" ';
				data+=' 		/> </textarea>';
				data+=' 		</div> ';
			    data+='	</div>';
			   
				angular.element(document.getElementById('append_options')).append($compile(data)(scope));
				CKEDITOR.replace( 'option'+scope.count, {height: 80});
				
				$('#append_options INPUT[type="file"]').change(function () {
		    		readImgOpt(this);
		        });		  
			}
			else{
				alert("limit is over");
			}
			
		});
	};
})
.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);
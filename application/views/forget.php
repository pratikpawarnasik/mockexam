<?php
	
	if(isset($usedone))
	{
?>
	<h2>This link is used only one time.</h2>	
<?php	}
	else
	if(isset($expire))
	{
?>	
	<h2>Time Out...Please resubmit the request to change your Password.</h2>	
<?php	}
	else
	if(isset($reset))
	{
?>
	
		<form method="POST" action="<?php echo base_url();?>index.php/forget/reset" id="resetpassword">
			<div class="col-md-12">
				<div class="col-md-6">
					<label>New Password : </label>
				</div>
				<div class="col-md-6">
					<input type="password" name="password" id="password1" class="from-control"></input>	
				</div>
			</div>
			<div class="col-md-12">
		 	<div class="col-md-6">
		 		<label>Confirm Password :</label>
		 	</div>
		 	<div class="col-md-6">
		 		<input type="password" name="cpassword" id="cpassword" class="form-control">
		 		<input type="hidden" name="chkpwd" id="chkpwd">
		 		<span id="perror"></span>
		 	</div>
	 	</div>
			<div class="col-md-12" style="margin-left: 170px;">
			<input type="hidden" name="userid" value="<?php echo $userid;?>">
			<input type="hidden" name="email" value="<?php echo $email;?>">
			<input type="hidden" name="type" value="<?php echo $type;?>">
			<input type="hidden" name="forgetid" value="<?php echo $forgetid;?>">
				<input type="button" name="passwordreset" id="passwordreset" class="btn btn-success" Value="Submit"></input>	
			</div>
		</form>
	
<script type="text/javascript" src="<?php echo base_url();?>js/jquery1.11.0.min.js"></script>
<script>
	$("#passwordreset").click(function()
	{
		$valid = true;
		$password = $("#password1").val();
		if($password == '')
		{
			alert("please enter password");
			$valid = false;
			return false;
		}
		$cpassword = $("#cpassword").val();
		if($cpassword == '')
		{
			alert("please enter Confirm password");
			$valid = false;
			return false;
		}
		if($password != $cpassword)
		{
			alert("password not match ...try again");
			$valid = false;
			return false;
		}
		
		if($valid)
		{
			$("#resetpassword").submit();
		}
	});
	$("#cpassword").keyup(function(){
		$password = $("#password1").val();
		$cpassword = $("#cpassword").val();
		if($password != $cpassword)
		{
			$("#chkpwd").val("Not_Match");
			$("#perror").html("<p style='color:red;'>Password Not Match</p>");
		}
		else
		{
			$("#chkpwd").val("Match");
			$("#perror").html("<p style='color:green;'>Password Match</p>");
		}
	});
</script>	
<?php	}
else{
?>
	<h2>Data Not Found...</h2>	
<?php }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="https://erp.remotecenter.com.bd/">

	<title>my creative code</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="login_resourse/image/png" href="login_resourse/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login_resourse/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login_resourse/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login_resourse/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="login_resourse/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login_resourse/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login_resourse/css/util.css">
	<link rel="stylesheet" type="text/css" href="login_resourse/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="img/logo.png" alt="IMG">
				</div>
				<form action="login.inc.php" class="login100-form validate-form" method="post">

				
					<span class="login100-form-title">
						Member Login
					</span>

					<div class="wrap-input100 validate-input" >
						<input class="input100" type="text" name="user_name" placeholder="User Name">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="pwd" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
				
                        <input type="submit" name="submit" class="login100-form-btn" value="Login">


					</div>

					<div class="text-center p-t-12">
					
						<a class="txt2" href="#">
						<?php
	if(isset($_GET['error'])){
		if($_GET['error'] == 'emptyinput'){
		  print '<b style="color:red">Need to fill all field</b>';
		}else if($_GET['error'] == 'stmfailed'){
			print '<b style="color:red">Somthing wrong</b>';

		}else if($_GET['error'] == 'wronglogin'){
			print '<b style="color:red">User Name not found</b>';

		}else if($_GET['error'] == 'wronglogin'){
			
			print '<b style="color:red">User Name not found</b>';
		}else if($_GET['error'] == 'logout'){
			print '<b style="color:red">You are Logout</b>';
		}else if($_GET['error'] == 'wronglink'){
			print '<b style="color:red">Please Login </b>';
		}else if($_GET['error'] == 'New'){
			print '<b style="color:green">Lets make a fresh start</b>';

		}else{                            

			print '<b style="color:red">####</b>';
		}

	}
	?>
						</a>
					</div>
                    <div class="text-center p-t-12">
						<span class="txt1">
						
						</span>
						<a class="txt2" href="#">
							
						</a>
					</div>
					<div class="text-center p-t-136">
						<a class="txt2" href="https://mycreativecode.com/">
							my creative code
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="login_resourse/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="login_resourse/vendor/bootstrap/js/popper.js"></script>
	<script src="login_resourse/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="login_resourse/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="login_resourse/vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->

</body>
</html>
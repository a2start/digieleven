<?php 
error_reporting(0);
$conn = mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");


if(isset($_POST['submit'])){

	$firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
	$lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
	$phone = mysqli_real_escape_string($conn, $_POST['phone']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);

	$sql = mysqli_query($conn, "INSERT INTO card_detail(first_name, last_name, phone, email) VALUES('$firstName', '$lastName','$phone','$email')");
	if($sql){
		session_start();
		$_SESSION['fullname'] = $firstName;
		$_SESSION['lastname'] = $lastName;
		$_SESSION['phone'] = $phone;
		$_SESSION['email'] = $email;
		echo "<script LANGUAGE='JAVASCRIPT'>
			window.location.href='qualification.php';
			</script>";
	}
}

?>
<!DOCTYPE html>
<html>
<head>  
	<meta charset="utf-8">
	<title>Construction Helps - Info</title>
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Font-->
	<link rel="shortcut icon" href="../images/construction/favicon.svg" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="css/opensans-font.css">
	<link rel="stylesheet" type="text/css" href="fonts/material-design-iconic-font/css/material-design-iconic-font.min.css">
	<!-- datepicker -->
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
	<!-- Main Style Css -->
    <link rel="stylesheet" href="css/style.css">
	<style>
		.actions ul li{
      visibility: hidden;
    }  
	.right-button{
		padding: 7px 24px;
		color: #fff;
		background: #104cba;
		font-size: 16px;
		font-weight: 500;
		border-radius: 5px;
		text-align: center;
		cursor: pointer;
		margin-top: 19px;
	}
	</style>
</head>
<body>
	<div class="page-content" style="background-image: url('../images/construction/london.webp');">
		<div class="wizard-heading">Construction Helps</div>
		<div class="wizard-v6-content">
			<div class="wizard-form">
		        <form class="form-register" id="form-register" method="post">
		        	<div id="form-total">						
		        		<!-- SECTION 1 -->
			            <h2>
			            	<p class="step-icon"><span>1</span></p>
			            	<span class="step-text">Personal Info</span>
			            </h2>
			            <section>							
			                <div class="inner">
			                	<div class="form-heading">
			                		<h3>Personal Info</h3>
			                		<span>1/3</span>
			                	</div>
								<div class="form-row">
									<div class="form-holder">
										<label class="form-row-inner">
											<input type="text" class="form-control" id="first_name" name="first_name" autocomplete="off" required>
											<span class="label">First Name</span>
										</label>
									</div>
									<div class="form-holder">
										<label class="form-row-inner">
											<input type="text" class="form-control" id="last_name" name="last_name" autocomplete="off" required>
											<span class="label">Last Name</span>
										</label>
									</div>
								</div>
								<div class="form-row">
									<div class="form-holder">
										<label class="form-row-inner">
											<input type="tel" class="form-control" id="phone" name="phone" autocomplete="off" required>
											<span class="label">Phone Number</span>
										</label>
									</div>
									<div class="form-holder">
										<label class="form-row-inner">
											<input type="email" name="email" id="email" class="form-control" autocomplete="off" required>
											<span class="label">E-Mail</span>
										</label>
									</div>
								</div>																
							</div>
			            </section>
						<!-- SECTION 2 -->						
			            
						<button type="submit" name="submit" class="btn right-button">Next</button>
						<!-- <a href="#next" class="actions"></a> -->
						
						<div>
						    <h4 style="color: #fff">Note : Test cost would be £43 including Admin Fee & VAT</h4>
						    <h4 style="color: #fff">For Card cost £59 including Admin Fee & VAT</h4>
						    <h4 style="color: #fff">For courses £150 including Admin Fee & VAT</h4>
						    <h4 style="color: #fff">NVQ COURSES £999 + VAT</h4>
						</div>
		        	</div>
		        </form>
			</div>
		</div>
	</div>
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/jquery.steps.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
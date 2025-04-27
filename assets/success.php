<?php 
session_start();
error_reporting(0);
$conn = mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");
if(empty($_SESSION['fullname'])){
    header("Location: info.php");
}else{
    
    $firstname = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    $phone = $_SESSION['phone'];
    $email = $_SESSION['email'];
    $qualify = $_SESSION['qualify'];
    
    if($qualify){
		    $to = "emailWazid@gmail.com";
            $subject = "You have received an Enquiry from construction Lead ";
            $message = "
                Name : $firstname $lastname <br>
                Email : $email  <br>
                Phone: $phone <br>
                Qualification: $qualify
            ";
            $headers  = "From: Construction Helps <constructionhelps@gmail.com>\n";
            $headers .= 'X-Mailer: PHP/' . phpversion();
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
        
            mail($to, $subject, $message, $headers);
    }
if(isset($_POST['submit'])){

    echo "<script LANGUAGE='JAVASCRIPT'>
            window.alert('Your Call Back Is arranged!! Please Wait For Sometime!');
            window.location.href='../index.html';
            </script>";
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Construction Helps - Success</title>
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="shortcut icon" href="../images/construction/favicon.svg" type="image/x-icon">
	<!-- Font-->
	<link rel="stylesheet" type="text/css" href="css/opensans-font.css">
	<link rel="stylesheet" type="text/css" href="fonts/material-design-iconic-font/css/material-design-iconic-font.min.css">
	<!-- datepicker -->
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
	<!-- Main Style Css -->
    <link rel="stylesheet" href="css/style.css"/>
	<style>
		.actions ul li{
      visibility: hidden;
    }  
	.right-button{
		padding: 15px 24px;
		color: #fff;
		background: #104cba;
		font-size: 16px;
		font-weight: 500;
		border-radius: 5px;
		text-align: center;
		cursor: pointer;
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
			            	<p class="step-icon"><span>3</span></p>
			            	<span class="step-text">Success</span>
			            </h2>
			            <section>							
			                <div class="inner">
			                	<div class="form-heading">
			                		<h3>Thank You For Submitting Your Details. Please Contact <a href="tel:03333 03 1186" style="color: #fff">03333 03 1186</a></h3>
			                		<span>3/3</span>
			                	</div>
																														
							</div>
			            </section>
						<!-- SECTION 2 -->						
			            
						<button type="submit" name="submit" class="btn right-button">Back To Home</button>
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
<?php }?>
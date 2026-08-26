<?php 
session_start();
error_reporting(0);
$conn = mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");
if(empty($_SESSION['fullname'])){
    header("Location: info.php");
    exit;
}else{
    
    $firstname = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    $phone = $_SESSION['phone'];
    $email = $_SESSION['email'];
    $dob = $_SESSION['dob'];
    $niNumber = $_SESSION['ni_number'];
    $address = $_SESSION['address'];
    $qualify = $_SESSION['qualify'];
    $serviceType = $_SESSION['service_type'];
    
    if($qualify){
        $to = "emailWazid@gmail.com";
        $subject = "Enquiry Received from digieleven.com - " . $serviceType;
        $message = "
            <strong>Service:</strong> $serviceType <br>
            <strong>Name:</strong> $firstname $lastname <br>
            <strong>Email:</strong> $email <br>
            <strong>Phone:</strong> $phone <br>
            <strong>DOB:</strong> $dob <br>
            <strong>NI Number:</strong> $niNumber <br>
            <strong>Address:</strong> $address <br>
            <strong>Qualification Status:</strong> $qualify <br>
        ";
        $headers  = "From: Construction Helps <info@constructionhelps.com>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
        @mail($to, $subject, $message, $headers);
    }

    if(isset($_POST['submit'])){
        echo "<script LANGUAGE='JAVASCRIPT'>
                window.location.href='../index.html';
                </script>";
        exit;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Construction Helps - Application Received</title>
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
			padding: 12px 28px;
			color: #fff;
			background: #104cba;
			font-size: 16px;
			font-weight: 600;
			border-radius: 5px;
			border: none;
			text-align: center;
			cursor: pointer;
			margin-top: 15px;
		}
		.price-card-box {
			background: rgba(255, 255, 255, 0.95);
			color: #333;
			padding: 30px 25px;
			border-radius: 8px;
			margin-bottom: 20px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.15);
			text-align: center;
		}
	</style>
</head>
<body>
	<div class="page-content" style="background-image: url('../images/construction/london.webp'); min-height: 100vh; padding: 40px 15px;">
		<div class="wizard-heading" style="text-align: center; color: #fff; font-size: 28px; font-weight: 700; margin-bottom: 20px;">Construction Helps</div>
		<div class="wizard-v6-content" style="max-width: 700px; margin: 0 auto;">
			<div class="wizard-form" style="background: rgba(16, 76, 186, 0.92); padding: 30px; border-radius: 10px;">
		        <form class="form-register" id="form-register" method="post">
		        	<div id="form-total">
						<div class="price-card-box">
							<div style="font-size: 48px; color: #28a745; margin-bottom: 15px;">&#10004;</div>
							<h3 style="color: #104cba; font-weight: 700; margin-bottom: 12px; font-size: 22px;">Thank You, <?php echo htmlspecialchars($firstname); ?>!</h3>
							<p style="font-size: 15px; color: #444; line-height: 1.6; margin-bottom: 20px;">Your details have been securely received by our booking team. A representative will contact you shortly to confirm your booking and process your request.</p>
							
							<div style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 6px; padding: 15px; margin-bottom: 20px; font-size: 14px; color: #333;">
								<strong>Need Immediate Assistance?</strong><br>
								Call Customer Support: <a href="tel:0800 002 5614" style="color: #104cba; font-weight: 700;">0800 002 5614</a> or <a href="tel:0333 303 1186" style="color: #104cba; font-weight: 700;">0333 303 1186</a>
							</div>

							<div>
								<button type="submit" name="submit" class="right-button" style="width: 100%; max-width: 250px;">Return to Home</button>
							</div>
						</div>
						
						<div style="background: rgba(255,255,255,0.15); border-radius: 6px; padding: 15px; color: #fff; font-size: 13px; line-height: 1.6; text-align: center;">
						    Construction Helps Ltd is an independent third-party service provider and is not affiliated with CITB or CSCS.
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
<?php } ?>
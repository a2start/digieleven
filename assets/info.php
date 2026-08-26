<?php 
error_reporting(0);
session_start();
$conn = mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");

if(isset($_POST['submit'])){
	$firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
	$lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
	$dob = mysqli_real_escape_string($conn, $_POST['dob']);
	$niNumber = mysqli_real_escape_string($conn, $_POST['ni_number']);
	$addressLine1 = mysqli_real_escape_string($conn, $_POST['address_line1']);
	$city = mysqli_real_escape_string($conn, $_POST['city']);
	$postcode = mysqli_real_escape_string($conn, $_POST['postcode']);
	$phone = mysqli_real_escape_string($conn, $_POST['phone']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$serviceType = mysqli_real_escape_string($conn, $_POST['service_type']);

	$_SESSION['fullname'] = $firstName . ' ' . $lastName;
	$_SESSION['firstname'] = $firstName;
	$_SESSION['lastname'] = $lastName;
	$_SESSION['dob'] = $dob;
	$_SESSION['ni_number'] = $niNumber;
	$_SESSION['address'] = $addressLine1 . ', ' . $city . ', ' . $postcode;
	$_SESSION['phone'] = $phone;
	$_SESSION['email'] = $email;
	$_SESSION['service_type'] = $serviceType;

	// Insert into database if connected
	if($conn){
		@mysqli_query($conn, "INSERT INTO card_detail(first_name, last_name, phone, email) VALUES('$firstName', '$lastName','$phone','$email')");
	}

	echo "<script LANGUAGE='JAVASCRIPT'>
		window.location.href='qualification.php';
		</script>";
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>  
	<meta charset="utf-8">
	<title>Construction Helps - Service &amp; Candidate Information</title>
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
			padding: 10px 28px;
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
			padding: 20px;
			border-radius: 8px;
			margin-bottom: 25px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.15);
		}
		.price-item {
			display: flex;
			justify-content: space-between;
			padding: 6px 0;
			border-bottom: 1px dashed #ccc;
			font-size: 14px;
		}
		.price-total {
			display: flex;
			justify-content: space-between;
			padding: 10px 0 0;
			font-size: 17px;
			font-weight: 700;
			color: #104cba;
		}
		.service-radio {
			margin-right: 8px;
		}
		.disclaimer-banner {
			background: #eaf3ff;
			border-left: 4px solid #104cba;
			padding: 12px 16px;
			border-radius: 4px;
			margin-bottom: 18px;
			font-size: 13px;
			line-height: 1.5;
			color: #222;
		}
	</style>
</head>
<body>
	<div class="page-content" style="background-image: url('../images/construction/london.webp'); min-height: 100vh; padding: 40px 15px;">
		<div class="wizard-heading" style="text-align: center; color: #fff; font-size: 28px; font-weight: 700; margin-bottom: 20px;">Construction Helps</div>
		<div class="wizard-v6-content" style="max-width: 850px; margin: 0 auto;">
			<div class="wizard-form" style="background: rgba(16, 76, 186, 0.92); padding: 30px; border-radius: 10px;">
		        
				<!-- Step 1: Pricing Breakdown (Visible BEFORE Personal Data Collection) -->
				<div class="disclaimer-banner">
					<strong>Independent Third-Party Booking Service:</strong> Construction Helps is an independent third-party administration service operated by Construction Helps Ltd. We are not part of, associated with, or endorsed by CITB or CSCS.
				</div>

				<div class="price-card-box">
					<h3 style="color: #104cba; margin-top: 0; margin-bottom: 15px; font-size: 20px; font-weight: 700;">Step 1: Transparent Pricing Breakdown</h3>
					<p style="font-size: 13px; color: #555; margin-bottom: 15px;">Please review the itemised breakdown of official fees and Construction Helps administration charges below:</p>
					
					<div class="row">
						<div class="col-md-6" style="margin-bottom: 15px;">
							<div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px;">
								<strong style="color: #104cba; font-size: 16px; display: block; margin-bottom: 8px;">CITB Test Booking</strong>
								<div class="price-item"><span>Official CITB Test Price:</span> <strong>£23.50</strong></div>
								<div class="price-item"><span>Booking &amp; Admin Service (inc. VAT):</span> <strong>£21.50</strong></div>
								<div class="price-total"><span>Total Payable:</span> <span>£45.00</span></div>
							</div>
						</div>

						<div class="col-md-6" style="margin-bottom: 15px;">
							<div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 15px;">
								<strong style="color: #104cba; font-size: 16px; display: block; margin-bottom: 8px;">CSCS Card Application</strong>
								<div class="price-item"><span>Official CSCS Card Fee:</span> <strong>£36.00</strong></div>
								<div class="price-item"><span>Application &amp; Admin Service (inc. VAT):</span> <strong>£29.00</strong></div>
								<div class="price-total"><span>Total Payable:</span> <span>£65.00</span></div>
							</div>
						</div>
					</div>
				</div>

				<!-- Step 2: Genuine Candidate Details Collection -->
				<form class="form-register" id="form-register" method="post">
					<div class="price-card-box" style="margin-bottom: 0;">
						<h3 style="color: #104cba; margin-top: 0; margin-bottom: 15px; font-size: 20px; font-weight: 700;">Step 2: Candidate Details</h3>
						<p style="font-size: 13px; color: #555; margin-bottom: 15px;">Enter the genuine candidate details below. The candidate's name must match their ID.</p>

						<div style="margin-bottom: 15px;">
							<label style="font-weight: 600; font-size: 14px; display: block; margin-bottom: 5px;">Select Required Service *</label>
							<select name="service_type" id="service_type" required style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 14px;">
								<option value="CITB Test Booking (£45.00)">CITB Test Booking — £45.00 Total (£23.50 test + £21.50 admin)</option>
								<option value="CSCS Card Application (£65.00)">CSCS Card Application — £65.00 Total (£36.00 card + £29.00 admin)</option>
								<option value="CITB Test + CSCS Card (£110.00)">CITB Test + CSCS Card Package — £110.00 Total</option>
								<option value="Health & Safety Awareness Course (£150.00)">Health &amp; Safety Awareness Course — £150.00 Total</option>
							</select>
						</div>

						<div class="row">
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Candidate First Name *</label>
								<input type="text" name="first_name" id="first_name" placeholder="First Name" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Candidate Last Name *</label>
								<input type="text" name="last_name" id="last_name" placeholder="Last Name" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
						</div>

						<div class="row">
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Candidate Date of Birth *</label>
								<input type="date" name="dob" id="dob" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">National Insurance Number *</label>
								<input type="text" name="ni_number" id="ni_number" placeholder="e.g. QQ 12 34 56 A" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Candidate Address Line 1 *</label>
								<input type="text" name="address_line1" id="address_line1" placeholder="House number and street" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
						</div>

						<div class="row">
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Town / City *</label>
								<input type="text" name="city" id="city" placeholder="Town/City" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Postcode *</label>
								<input type="text" name="postcode" id="postcode" placeholder="e.g. SW1A 1AA" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
						</div>

						<div class="row">
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Candidate Personal Phone *</label>
								<input type="tel" name="phone" id="phone" placeholder="07123456789" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
							<div class="col-md-6" style="margin-bottom: 12px;">
								<label style="font-weight: 600; font-size: 13px; margin-bottom: 4px; display: block;">Candidate Personal Email *</label>
								<input type="email" name="email" id="email" placeholder="candidate@example.com" required style="width: 100%; padding: 9px; border-radius: 4px; border: 1px solid #ccc;">
							</div>
						</div>

						<div style="margin-top: 15px; text-align: center;">
							<button type="submit" name="submit" class="right-button" style="width: 100%; max-width: 300px;">Continue to Step 3 &rarr;</button>
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
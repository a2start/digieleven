<?php 
session_start();
error_reporting(0);
$conn = mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");

if(empty($_SESSION['fullname'])){
    header("Location: info.php");
    exit;
}else{

if(isset($_POST['submit'])){
	$qualify = trim(strip_tags($_POST['qualify']));	
    
	if($conn){
		$safeQualify = mysqli_real_escape_string($conn, $qualify);
		$sql = @mysqli_query($conn, "UPDATE card_detail SET qualify='$safeQualify' WHERE qualify=''");
	}
	$_SESSION['qualify'] = $qualify;

	// Update qualification in submissions.json
	$dataFile = dirname(__DIR__) . '/data/submissions.json';
	if (file_exists($dataFile)) {
		$fileContent = file_get_contents($dataFile);
		if (!empty($fileContent)) {
			$submissions = json_decode($fileContent, true) ?: [];
			if (!empty($submissions)) {
				$submissions[0]['test_type'] = $qualify;
				$submissions[0]['status'] = 'Completed Form';
				file_put_contents($dataFile, json_encode($submissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
			}
		}
	}

	echo "<script LANGUAGE='JAVASCRIPT'>
		window.location.href='success.php';
		</script>";
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Construction Helps - Qualification</title>
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Font-->
	<link rel="shortcut icon" href="../images/construction/favicon.svg" type="image/x-icon">
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
			padding: 25px;
			border-radius: 8px;
			margin-bottom: 20px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.15);
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
							<h3 style="color: #104cba; font-weight: 700; margin-top: 0; margin-bottom: 15px; font-size: 20px;">Step 3 of 3: Construction Qualifications</h3>
							<p style="font-size: 14px; color: #555; margin-bottom: 20px;">Do you hold any current construction qualifications or NVQ certificates?</p>

							<div class="form-row">
								<div class="form-holder" style="width: 100%;">
									<label style="color: #333; font-weight: 600; font-size: 14px; display: block; margin-bottom: 8px;">Select Qualification Status:
										<select name="qualify" id="qualify" required style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 14px; margin-top: 5px;">
											<option value="">--- Choose Option ---</option>
											<option value="Yes - I have completed NVQ / equivalent">Yes — I hold NVQ / Construction Qualification</option>
											<option value="No - I need to take course / test">No — I do not currently hold a qualification</option>
											<option value="In Training - Currently Enrolled">Currently Enrolled in Training</option>
										</select>											
									</label>
								</div>									
							</div>

							<div style="margin-top: 20px; text-align: center;">
								<button type="submit" name="submit" class="right-button" style="width: 100%; max-width: 280px;">Complete Submission &rarr;</button>
							</div>
						</div>
						
						<div style="background: rgba(255,255,255,0.15); border-radius: 6px; padding: 15px; color: #fff; font-size: 13px; line-height: 1.6;">
						    <div><strong>CITB Test:</strong> £45.00 Total (£23.50 official test fee + £21.50 booking &amp; admin fee inc. VAT)</div>
						    <div><strong>CSCS Card Application:</strong> £65.00 Total (£36.00 official card fee + £29.00 application &amp; admin fee inc. VAT)</div>
						    <div><strong>Courses:</strong> From £150.00 &bull; <strong>NVQ Courses:</strong> From £999.00 + VAT</div>
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
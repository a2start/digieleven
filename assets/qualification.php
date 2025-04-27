<?php 
session_start();
error_reporting(0);
$conn = mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");

if(empty($_SESSION['fullname'])){
    header("Location: info.php");
}else{

if(isset($_POST['submit'])){

	$qualify = mysqli_real_escape_string($conn, $_POST['qualify']);	
    
	$sql = mysqli_query($conn, "UPDATE card_detail SET qualify='$qualify' WHERE qualify=''");
	if($sql){
	    $_SESSION['qualify'] = $qualify;
		echo "<script LANGUAGE='JAVASCRIPT'>
			window.location.href='success.php';
			</script>";
	}
}

?>
<!DOCTYPE html>
<html>
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
		padding: 7px 24px;
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
			            	<p class="step-icon"><span>2</span></p>
			            	<span class="step-text">Qualification</span>
			            </h2>
			            <section>							
			                <div class="inner">
			                	<div class="form-heading">
			                		<h3>Qualification</h3>
			                		<span>2/3</span>
			                	</div>
								<div class="form-row">
									<div class="form-holder">
										<label class="form-row-inner" style="color: #fff;">Do You Have Qualification?
											<select name="qualify" id="" autocomplete="off">
                                                <option value="">---Choose---</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>											
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
<?php }?>
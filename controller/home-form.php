<?php 
error_reporting(0);

if(isset($_POST['submit'])){
    $conn = @mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");

    // Check if full candidate booking submission (from citb-test.html) or contact form
    $firstName = isset($_POST['first_name']) ? trim(strip_tags($_POST['first_name'])) : '';
    $lastName = isset($_POST['last_name']) ? trim(strip_tags($_POST['last_name'])) : '';
    $username = isset($_POST['username']) ? trim(strip_tags($_POST['username'])) : '';
    
    if(!empty($firstName) || !empty($lastName)){
        $candidateName = $firstName . ' ' . $lastName;
    } else {
        $candidateName = $username;
    }

    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : 'General Enquiry';
    $dob = isset($_POST['dob']) ? trim(strip_tags($_POST['dob'])) : '';
    $niNumber = isset($_POST['ni_number']) ? trim(strip_tags($_POST['ni_number'])) : '';
    $addressLine1 = isset($_POST['address_line1']) ? trim(strip_tags($_POST['address_line1'])) : '';
    $city = isset($_POST['city']) ? trim(strip_tags($_POST['city'])) : '';
    $postcode = isset($_POST['postcode']) ? trim(strip_tags($_POST['postcode'])) : '';
    $retakePackage = isset($_POST['retake_package']) ? 'Yes (+£20.00)' : 'No';
    $preferredLocation = isset($_POST['preferred_location']) ? trim(strip_tags($_POST['preferred_location'])) : '';

    if($conn){
        $safeName = mysqli_real_escape_string($conn, $candidateName);
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $safePhone = mysqli_real_escape_string($conn, $phone);
        $safeSubject = mysqli_real_escape_string($conn, $subject);
        @mysqli_query($conn, "INSERT INTO homeform(username, email, phone, subject) VALUES('$safeName','$safeEmail','$safePhone','$safeSubject')");
    }

    $to = "emailWazid@gmail.com";
    $emailSubject = "Booking / Enquiry Submission: " . htmlspecialchars($subject);
    
    $message = "<h2>New Request from digieleven.com</h2>";
    $message .= "<p><strong>Service / Subject:</strong> " . htmlspecialchars($subject) . "</p>";
    $message .= "<p><strong>Candidate Name:</strong> " . htmlspecialchars($candidateName) . "</p>";
    $message .= "<p><strong>Candidate Email:</strong> " . htmlspecialchars($email) . "</p>";
    $message .= "<p><strong>Candidate Phone:</strong> " . htmlspecialchars($phone) . "</p>";

    if(!empty($dob)){
        $message .= "<p><strong>Date of Birth:</strong> " . htmlspecialchars($dob) . "</p>";
    }
    if(!empty($niNumber)){
        $message .= "<p><strong>National Insurance Number:</strong> " . htmlspecialchars($niNumber) . "</p>";
    }
    if(!empty($addressLine1)){
        $message .= "<p><strong>Address:</strong> " . htmlspecialchars($addressLine1) . ", " . htmlspecialchars($city) . ", " . htmlspecialchars($postcode) . "</p>";
    }
    if(!empty($preferredLocation)){
        $message .= "<p><strong>Preferred Location / Date:</strong> " . htmlspecialchars($preferredLocation) . "</p>";
    }
    $message .= "<p><strong>Retake Package Selected:</strong> " . htmlspecialchars($retakePackage) . "</p>";

    $headers  = "From: Construction Helps <info@constructionhelps.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    @mail($to, $emailSubject, $message, $headers);

    echo "<script language='JAVASCRIPT'>
        window.alert('Thank you for submitting your request. A representative will contact you shortly to confirm your booking.');
        window.location.href='../index.html';
        </script>";
    exit;
}
?>
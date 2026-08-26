<?php 
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

// Helper to determine if AJAX request
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

// Read JSON input if sent as payload
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $jsonDecoded = json_decode($raw, true);
        if (is_array($jsonDecoded)) {
            $input = $jsonDecoded;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($input['submit']) || !empty($input['first_name']) || !empty($input['username']) || !empty($input['email'])) {
    
    $firstName = isset($input['first_name']) ? trim(strip_tags($input['first_name'])) : '';
    $lastName = isset($input['last_name']) ? trim(strip_tags($input['last_name'])) : '';
    $username = isset($input['username']) ? trim(strip_tags($input['username'])) : '';
    
    if(!empty($firstName) || !empty($lastName)){
        $candidateName = trim($firstName . ' ' . $lastName);
    } else {
        $candidateName = $username;
    }

    $email = isset($input['email']) ? filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($input['phone']) ? trim(strip_tags($input['phone'])) : '';
    $subject = isset($input['subject']) ? trim(strip_tags($input['subject'])) : (isset($input['service_type']) ? trim(strip_tags($input['service_type'])) : 'CITB Test Booking');
    $dob = isset($input['dob']) ? trim(strip_tags($input['dob'])) : '';
    $niNumber = isset($input['ni_number']) ? trim(strip_tags($input['ni_number'])) : '';
    $addressLine1 = isset($input['address_line1']) ? trim(strip_tags($input['address_line1'])) : '';
    $city = isset($input['city']) ? trim(strip_tags($input['city'])) : '';
    $postcode = isset($input['postcode']) ? trim(strip_tags($input['postcode'])) : '';
    $retakePackage = (isset($input['retake_package']) && ($input['retake_package'] === 'Yes' || $input['retake_package'] === true || $input['retake_package'] === '1')) ? 'Yes (+£20.00)' : 'No';
    $preferredLocation = isset($input['preferred_location']) ? trim(strip_tags($input['preferred_location'])) : '';
    $testType = isset($input['test_type']) ? trim(strip_tags($input['test_type'])) : $subject;
    $sourcePage = isset($input['source_page']) ? trim(strip_tags($input['source_page'])) : (isset($_SERVER['HTTP_REFERER']) ? basename($_SERVER['HTTP_REFERER']) : 'Website Form');

    $leadId = 'CH-' . date('ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    $timestamp = date('Y-m-d H:i:s');

    $newSubmission = [
        'id' => $leadId,
        'created_at' => $timestamp,
        'candidate_name' => $candidateName,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'dob' => $dob,
        'ni_number' => $niNumber,
        'address_line1' => $addressLine1,
        'city' => $city,
        'postcode' => $postcode,
        'full_address' => trim($addressLine1 . ', ' . $city . ' ' . $postcode, ', '),
        'phone' => $phone,
        'email' => $email,
        'subject' => $subject,
        'test_type' => $testType,
        'retake_package' => $retakePackage,
        'preferred_location' => $preferredLocation,
        'source_page' => $sourcePage,
        'status' => 'New'
    ];

    // 1. Save to JSON File Storage (High Reliability)
    $dataFile = dirname(__DIR__) . '/data/submissions.json';
    $submissions = [];
    if (file_exists($dataFile)) {
        $fileContent = file_get_contents($dataFile);
        if (!empty($fileContent)) {
            $submissions = json_decode($fileContent, true) ?: [];
        }
    }
    array_unshift($submissions, $newSubmission);
    file_put_contents($dataFile, json_encode($submissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

    // 2. Save to MySQL database if available
    $conn = @mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");
    if ($conn) {
        $safeName = mysqli_real_escape_string($conn, $candidateName);
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $safePhone = mysqli_real_escape_string($conn, $phone);
        $safeSubject = mysqli_real_escape_string($conn, $subject);
        @mysqli_query($conn, "INSERT INTO homeform(username, email, phone, subject) VALUES('$safeName','$safeEmail','$safePhone','$safeSubject')");
        @mysqli_close($conn);
    }

    // 3. Send Notification Email
    $to = "info@constructionhelps.com, emailWazid@gmail.com";
    $emailSubject = "New Booking / Lead: " . $candidateName . " (" . $leadId . ")";
    
    $message = "<h2>New Request from digieleven.com</h2>";
    $message .= "<p><strong>Reference ID:</strong> " . htmlspecialchars($leadId) . "</p>";
    $message .= "<p><strong>Service / Subject:</strong> " . htmlspecialchars($subject) . "</p>";
    $message .= "<p><strong>Candidate Name:</strong> " . htmlspecialchars($candidateName) . "</p>";
    $message .= "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    $message .= "<p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>";

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
    $message .= "<p><strong>Retake Package:</strong> " . htmlspecialchars($retakePackage) . "</p>";

    $headers  = "From: Construction Helps <info@constructionhelps.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    @mail($to, $emailSubject, $message, $headers);

    // Return response based on request type
    if ($isAjax || !isset($_POST['submit'])) {
        echo json_encode([
            'success' => true,
            'lead_id' => $leadId,
            'message' => 'Thank you! Your booking details have been received successfully. Our team will contact you shortly.'
        ]);
        exit;
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Booking Received - Construction Helps</title>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <link rel='stylesheet' href='../css/bootstrap.css'>
            <link rel='stylesheet' href='../css/style.css'>
            <style>
                body { background: #f4f7fb; font-family: 'Open Sans', sans-serif; padding: 40px 15px; }
                .success-card { max-width: 580px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 35px; text-align: center; }
                .icon-circle { width: 70px; height: 70px; border-radius: 50%; background: #e8f5e9; color: #2e7d32; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='success-card'>
                <div class='icon-circle'>&#10004;</div>
                <h2 style='color: #104cba; margin-bottom: 10px;'>Booking Details Received!</h2>
                <p style='color: #555; font-size: 15px; line-height: 1.6;'>Thank you, <strong>" . htmlspecialchars($candidateName) . "</strong>. Your request (Ref: <strong>" . htmlspecialchars($leadId) . "</strong>) has been submitted successfully.</p>
                <div style='background: #f0f7ff; padding: 15px; border-radius: 6px; margin: 20px 0; text-align: left; font-size: 14px;'>
                    <div><strong>Service:</strong> " . htmlspecialchars($subject) . "</div>
                    <div><strong>Phone:</strong> " . htmlspecialchars($phone) . "</div>
                    <div><strong>Email:</strong> " . htmlspecialchars($email) . "</div>
                </div>
                <p style='font-size: 13px; color: #777;'>A representative from Construction Helps will call you shortly to confirm your booking and test arrangement.</p>
                <a href='../index.html' class='theme-btn btn-style-one' style='display: inline-block; padding: 10px 25px; margin-top: 15px; text-decoration: none;'>Return to Homepage</a>
            </div>
        </body>
        </html>";
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}
?>
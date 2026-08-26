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

    // 2. Save to MySQL database if configured in environment
    $dbHost = getenv('DB_HOST') ?: 'localhost';
    $dbUser = getenv('DB_USER') ?: 'pohoopmy_constructionhelps';
    $dbPass = getenv('DB_PASS') ?: '';
    $dbName = getenv('DB_NAME') ?: 'pohoopmy_constructionhelps';
    if (!empty($dbPass)) {
        $conn = @mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
        if ($conn) {
            $safeName = mysqli_real_escape_string($conn, $candidateName);
            $safeEmail = mysqli_real_escape_string($conn, $email);
            $safePhone = mysqli_real_escape_string($conn, $phone);
            $safeSubject = mysqli_real_escape_string($conn, $subject);
            @mysqli_query($conn, "INSERT INTO homeform(username, email, phone, subject) VALUES('$safeName','$safeEmail','$safePhone','$safeSubject')");
            @mysqli_close($conn);
        }
    }

    // 3. Email Configurations (Dynamic from Dashboard or defaults)
    $emailConfigFile = dirname(__DIR__) . '/data/email_config.json';
    $emailConfig = [];
    if (file_exists($emailConfigFile)) {
        $cfgRaw = @file_get_contents($emailConfigFile);
        if (!empty($cfgRaw)) $emailConfig = @json_decode($cfgRaw, true) ?: [];
    }

    $adminNotifyEmails = !empty($_POST['admin_notify_emails']) ? trim($_POST['admin_notify_emails']) : (!empty($emailConfig['admin_notify_emails']) ? $emailConfig['admin_notify_emails'] : 'info@constructionhelps.com, emailWazid@gmail.com');
    $fromEmail = !empty($_POST['from_email']) ? trim($_POST['from_email']) : (!empty($emailConfig['from_email']) ? $emailConfig['from_email'] : 'info@constructionhelps.com');
    $fromName = !empty($_POST['from_name']) ? trim($_POST['from_name']) : (!empty($emailConfig['from_name']) ? $emailConfig['from_name'] : 'Construction Helps');
    $sendCandidateAck = isset($_POST['send_candidate_ack']) ? ($_POST['send_candidate_ack'] === 'true' || $_POST['send_candidate_ack'] === '1') : (!isset($emailConfig['send_candidate_ack']) || $emailConfig['send_candidate_ack'] !== false);
    $candidateNotes = !empty($_POST['candidate_email_notes']) ? trim($_POST['candidate_email_notes']) : (!empty($emailConfig['candidate_email_notes']) ? $emailConfig['candidate_email_notes'] : 'An advisor from Construction Helps will call you shortly to confirm your booking slot and test centre details.');

    // --- A. Send Professional Admin Lead Notification ---
    $adminSubject = "🚨 New Lead #" . $leadId . " — " . $candidateName . " (" . $subject . ")";
    
    $adminHtml = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <style>
            body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
            .container { max-width: 620px; background: #ffffff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e1e8ed; }
            .header { background: #104cba; color: #ffffff; padding: 24px; text-align: center; }
            .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
            .header p { margin: 5px 0 0; font-size: 14px; opacity: 0.9; }
            .badge { display: inline-block; background: #ffb300; color: #000; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; margin-top: 8px; }
            .content { padding: 25px; }
            .table-details { width: 100%; border-collapse: collapse; margin-top: 15px; }
            .table-details th { text-align: left; padding: 10px; background: #f8fafc; color: #475569; font-size: 13px; border-bottom: 1px solid #e2e8f0; width: 38%; }
            .table-details td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #0f172a; }
            .btn-action { display: inline-block; background: #104cba; color: #ffffff !important; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; margin-right: 8px; }
            .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Candidate Booking Request</h1>
                <p>Construction Helps Booking System &bull; digieleven.com</p>
                <div class='badge'>Ref: #" . htmlspecialchars($leadId) . "</div>
            </div>
            <div class='content'>
                <h3 style='color: #104cba; margin-top: 0;'>Candidate Information</h3>
                <table class='table-details'>
                    <tr><th>Candidate Full Name</th><td><strong>" . htmlspecialchars($candidateName) . "</strong></td></tr>
                    <tr><th>Date of Birth</th><td>" . htmlspecialchars($dob ?: 'Not provided') . "</td></tr>
                    <tr><th>National Insurance</th><td><code>" . htmlspecialchars($niNumber ?: 'Not provided') . "</code></td></tr>
                    <tr><th>Phone Number</th><td><a href='tel:" . htmlspecialchars($phone) . "' style='color:#104cba; font-weight:700;'>" . htmlspecialchars($phone) . "</a></td></tr>
                    <tr><th>Email Address</th><td><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></td></tr>
                    <tr><th>Home Address</th><td>" . htmlspecialchars($addressLine1 . ($city ? ', ' . $city : '') . ($postcode ? ', ' . $postcode : '')) . "</td></tr>
                    <tr><th>Requested Service</th><td><strong style='color:#104cba;'>" . htmlspecialchars($subject) . "</strong></td></tr>
                    <tr><th>Test Type</th><td>" . htmlspecialchars($testType ?: 'Standard') . "</td></tr>
                    <tr><th>Retake Protection</th><td>" . htmlspecialchars($retakePackage) . "</td></tr>
                    <tr><th>Preferred Location / Date</th><td>" . htmlspecialchars($preferredLocation ?: 'Earliest available') . "</td></tr>
                    <tr><th>Received At</th><td>" . htmlspecialchars($timestamp) . "</td></tr>
                </table>
                <div style='margin-top: 25px; text-align: center;'>
                    <a href='tel:" . htmlspecialchars($phone) . "' class='btn-action' style='background: #16a34a;'>📞 Call Candidate</a>
                    <a href='mailto:" . htmlspecialchars($email) . "?subject=Your%20Construction%20Helps%20Booking%20" . urlencode($leadId) . "' class='btn-action'>✉️ Email Candidate</a>
                </div>
            </div>
            <div class='footer'>
                Construction Helps Ltd &bull; 71-75 Shelton Street, Covent Garden, London, WC2H 9JQ
            </div>
        </div>
    </body>
    </html>";

    $adminHeaders  = "From: " . $fromName . " <" . $fromEmail . ">\r\n";
    $adminHeaders .= "Reply-To: " . $candidateName . " <" . $email . ">\r\n";
    $adminHeaders .= "MIME-Version: 1.0\r\n";
    $adminHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";

    @mail($adminNotifyEmails, $adminSubject, $adminHtml, $adminHeaders);

    // --- B. Send Professional Candidate Acknowledgment Email ---
    if ($sendCandidateAck && !empty($email)) {
        $candSubject = "Booking Request Received — #" . $leadId . " | Construction Helps";
        
        $candHtml = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
                .container { max-width: 600px; background: #ffffff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e1e8ed; }
                .header { background: #104cba; color: #ffffff; padding: 25px; text-align: center; }
                .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
                .content { padding: 25px; }
                .ref-box { background: #eff6ff; border-left: 4px solid #104cba; padding: 15px 20px; border-radius: 6px; margin: 18px 0; font-size: 15px; }
                .summary-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                .summary-table td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
                .summary-table td.label { font-weight: 600; color: #64748b; width: 40%; }
                .notice-box { background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 6px; margin: 18px 0; font-size: 13.5px; color: #92400e; }
                .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Construction Helps</h1>
                    <p style='margin: 4px 0 0; opacity: 0.9; font-size: 14px;'>Booking Request Acknowledgment</p>
                </div>
                <div class='content'>
                    <p style='font-size: 15px;'>Dear <strong>" . htmlspecialchars($candidateName) . "</strong>,</p>
                    <p style='font-size: 14px; line-height: 1.6;'>Thank you for submitting your booking request with Construction Helps. We have successfully received your details.</p>
                    
                    <div class='ref-box'>
                        <strong>Your Booking Reference ID:</strong> <span style='color:#104cba; font-size:18px; font-weight:700;'>#" . htmlspecialchars($leadId) . "</span>
                    </div>

                    <h4 style='color:#104cba; margin-bottom: 8px;'>Summary of Your Request:</h4>
                    <table class='summary-table'>
                        <tr><td class='label'>Service:</td><td><strong>" . htmlspecialchars($subject) . "</strong></td></tr>
                        <tr><td class='label'>Candidate Name:</td><td>" . htmlspecialchars($candidateName) . "</td></tr>
                        <tr><td class='label'>Contact Phone:</td><td>" . htmlspecialchars($phone) . "</td></tr>
                        <tr><td class='label'>Retake Protection:</td><td>" . htmlspecialchars($retakePackage) . "</td></tr>
                        <tr><td class='label'>Preferred Venue / Date:</td><td>" . htmlspecialchars($preferredLocation ?: 'Earliest available') . "</td></tr>
                    </table>

                    <div class='notice-box'>
                        <strong>⚠️ Important ID Requirement for Test Day:</strong><br>
                        You must bring an acceptable, original Primary ID (a valid UK or international passport, a valid UK or European Photocard Driving Licence, or a verified UKVI eVisa) to the test centre.
                    </div>

                    <p style='font-size: 14px; line-height: 1.6; color: #334155;'>" . htmlspecialchars($candidateNotes) . "</p>

                    <p style='font-size: 14px; margin-top: 25px;'>If you have any questions, please contact our support team at <a href='mailto:" . htmlspecialchars($fromEmail) . "' style='color:#104cba; font-weight:600;'>" . htmlspecialchars($fromEmail) . "</a> or call <a href='tel:08000025614' style='color:#104cba; font-weight:600;'>0800 002 5614</a>.</p>
                </div>
                <div class='footer'>
                    <strong>Construction Helps Ltd</strong> (trading as Construction Helps)<br>
                    71-75 Shelton Street, Covent Garden, London, WC2H 9JQ<br>
                    Construction Helps is an independent third-party booking support service.
                </div>
            </div>
        </body>
        </html>";

        $candHeaders  = "From: " . $fromName . " <" . $fromEmail . ">\r\n";
        $candHeaders .= "Reply-To: " . $fromName . " <" . $fromEmail . ">\r\n";
        $candHeaders .= "MIME-Version: 1.0\r\n";
        $candHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";

        @mail($email, $candSubject, $candHtml, $candHeaders);
    }

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
<?php
// Test Email Endpoint for Construction Helps Admin Dashboard
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$type = isset($_POST['type']) ? trim($_POST['type']) : 'admin'; // 'admin' or 'candidate'
$targetEmail = isset($_POST['target_email']) ? trim($_POST['target_email']) : '';
$fromEmail = isset($_POST['from_email']) ? trim($_POST['from_email']) : 'info@constructionhelps.com';
$fromName = isset($_POST['from_name']) ? trim($_POST['from_name']) : 'Construction Helps';
$candidateNotes = isset($_POST['candidate_email_notes']) ? trim($_POST['candidate_email_notes']) : 'An advisor from Construction Helps will call you shortly to confirm your booking slot and test centre details.';

if (empty($targetEmail)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a target email address.']);
    exit;
}

$testLeadId = 'TEST-' . strtoupper(substr(uniqid(), -6));
$timestamp = date('d M Y, h:i A');

if ($type === 'admin') {
    // 1. Send Test Admin Lead Alert
    $subject = "🧪 [TEST] New Lead #" . $testLeadId . " — John Doe (CITB Operatives Test)";
    
    $html = "
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
            .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🧪 Test Admin Lead Notification</h1>
                <p>Construction Helps Booking System &bull; digieleven.com</p>
                <div class='badge'>Ref: #" . htmlspecialchars($testLeadId) . "</div>
            </div>
            <div class='content'>
                <p style='background: #ecfdf5; border-left: 4px solid #10b981; padding: 10px 14px; border-radius: 4px; color: #065f46; font-size: 13px;'>
                    <strong>Success:</strong> This is a test email sent from your Construction Helps Admin Dashboard to verify your email notifications.
                </p>
                <h3 style='color: #104cba; margin-top: 20px;'>Sample Candidate Information</h3>
                <table class='table-details'>
                    <tr><th>Candidate Full Name</th><td><strong>John Doe</strong></td></tr>
                    <tr><th>Date of Birth</th><td>15/08/1990</td></tr>
                    <tr><th>National Insurance</th><td><code>QQ 12 34 56 A</code></td></tr>
                    <tr><th>Phone Number</th><td><a href='tel:07700900123' style='color:#104cba; font-weight:700;'>07700 900123</a></td></tr>
                    <tr><th>Email Address</th><td><a href='mailto:johndoe.sample@example.com'>johndoe.sample@example.com</a></td></tr>
                    <tr><th>Home Address</th><td>10 Downing Street, London, SW1A 2AA</td></tr>
                    <tr><th>Requested Service</th><td><strong style='color:#104cba;'>CITB Operatives Test (£45.00)</strong></td></tr>
                    <tr><th>Test Type</th><td>Operatives (HS&E)</td></tr>
                    <tr><th>Retake Protection</th><td>Yes (+£20.00)</td></tr>
                    <tr><th>Preferred Location / Date</th><td>London Central &bull; Earliest Date</td></tr>
                    <tr><th>Received At</th><td>" . htmlspecialchars($timestamp) . "</td></tr>
                </table>
            </div>
            <div class='footer'>
                Construction Helps Ltd &bull; 71-75 Shelton Street, Covent Garden, London, WC2H 9JQ
            </div>
        </div>
    </body>
    </html>";

    $headers  = "From: " . $fromName . " <" . $fromEmail . ">\r\n";
    $headers .= "Reply-To: " . $fromEmail . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $sent = @mail($targetEmail, $subject, $html, $headers);
    echo json_encode([
        'success' => true,
        'message' => 'Test Admin Lead email sent successfully to: ' . $targetEmail
    ]);
} else {
    // 2. Send Test Candidate Acknowledgment Email
    $subject = "🧪 [TEST] Booking Request Received — #" . $testLeadId . " | Construction Helps";
    
    $html = "
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
                <p style='margin: 4px 0 0; opacity: 0.9; font-size: 14px;'>Booking Request Acknowledgment (Test Preview)</p>
            </div>
            <div class='content'>
                <p style='font-size: 15px;'>Dear <strong>John Doe</strong>,</p>
                <p style='font-size: 14px; line-height: 1.6;'>Thank you for submitting your booking request with Construction Helps. We have successfully received your details.</p>
                
                <div class='ref-box'>
                    <strong>Your Booking Reference ID:</strong> <span style='color:#104cba; font-size:18px; font-weight:700;'>#" . htmlspecialchars($testLeadId) . "</span>
                </div>

                <h4 style='color:#104cba; margin-bottom: 8px;'>Summary of Your Request:</h4>
                <table class='summary-table'>
                    <tr><td class='label'>Service:</td><td><strong>CITB Operatives Test (£45.00)</strong></td></tr>
                    <tr><td class='label'>Candidate Name:</td><td>John Doe</td></tr>
                    <tr><td class='label'>Contact Phone:</td><td>07700 900123</td></tr>
                    <tr><td class='label'>Retake Protection:</td><td>Yes (+£20.00)</td></tr>
                    <tr><td class='label'>Preferred Venue / Date:</td><td>London Central &bull; Earliest Date</td></tr>
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

    $headers  = "From: " . $fromName . " <" . $fromEmail . ">\r\n";
    $headers .= "Reply-To: " . $fromName . " <" . $fromEmail . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $sent = @mail($targetEmail, $subject, $html, $headers);
    echo json_encode([
        'success' => true,
        'message' => 'Test Candidate Acknowledgment email sent successfully to: ' . $targetEmail
    ]);
}

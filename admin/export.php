<?php
require_once __DIR__ . '/config.php';
check_admin_auth();

$submissions = get_all_submissions();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=digieleven_leads_' . date('Ymd_His') . '.csv');

$output = fopen('php://output', 'w');

// Output CSV header
fputcsv($output, [
    'Reference ID',
    'Date & Time',
    'Candidate Full Name',
    'First Name',
    'Last Name',
    'Date of Birth',
    'National Insurance Number',
    'Address Line 1',
    'City',
    'Postcode',
    'Phone',
    'Email',
    'Service Requested',
    'Test Type / Occupation',
    'Retake Package',
    'Preferred Location / Date',
    'Source Page',
    'Status'
]);

// Output data rows
foreach ($submissions as $row) {
    fputcsv($output, [
        $row['id'] ?? '',
        $row['created_at'] ?? '',
        $row['candidate_name'] ?? (($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
        $row['first_name'] ?? '',
        $row['last_name'] ?? '',
        $row['dob'] ?? '',
        $row['ni_number'] ?? '',
        $row['address_line1'] ?? '',
        $row['city'] ?? '',
        $row['postcode'] ?? '',
        $row['phone'] ?? '',
        $row['email'] ?? '',
        $row['subject'] ?? ($row['service_type'] ?? ''),
        $row['test_type'] ?? '',
        $row['retake_package'] ?? '',
        $row['preferred_location'] ?? '',
        $row['source_page'] ?? '',
        $row['status'] ?? 'New'
    ]);
}

fclose($output);
exit;
?>

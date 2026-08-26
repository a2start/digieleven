<?php
require_once __DIR__ . '/config.php';
check_admin_auth();

$submissions = get_all_submissions();
$message = '';

// Handle Status Update or Delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $leadId = isset($_POST['lead_id']) ? trim($_POST['lead_id']) : '';

    if ($action === 'update_status' && !empty($leadId)) {
        $newStatus = isset($_POST['status']) ? trim($_POST['status']) : 'New';
        foreach ($submissions as &$sub) {
            if ($sub['id'] === $leadId) {
                $sub['status'] = $newStatus;
                break;
            }
        }
        save_submissions($submissions);
        $message = "Status for lead #$leadId updated to $newStatus.";
    } elseif ($action === 'delete' && !empty($leadId)) {
        $submissions = array_values(array_filter($submissions, function($s) use ($leadId) {
            return $s['id'] !== $leadId;
        }));
        save_submissions($submissions);
        $message = "Submission #$leadId deleted successfully.";
    }
}

// Compute Statistics
$totalCount = count($submissions);
$newCount = 0;
$citbCount = 0;
$cscsCount = 0;

foreach ($submissions as $sub) {
    if (isset($sub['status']) && $sub['status'] === 'New') {
        $newCount++;
    }
    $subj = isset($sub['subject']) ? strtolower($sub['subject']) : '';
    if (strpos($subj, 'citb') !== false) {
        $citbCount++;
    }
    if (strpos($subj, 'cscs') !== false || strpos($subj, 'card') !== false) {
        $cscsCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions Dashboard - Construction Helps Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../images/construction/favicon.svg" type="image/x-icon">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }
        .navbar-admin {
            background: #104cba;
            color: #fff;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-nav {
            background: rgba(255,255,255,0.15);
            color: #fff;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-nav:hover {
            background: rgba(255,255,255,0.25);
            color: #fff;
        }
        .container-fluid {
            padding: 25px 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid #104cba;
        }
        .stat-card.new-leads { border-left-color: #f59e0b; }
        .stat-card.citb-leads { border-left-color: #10b981; }
        .stat-card.cscs-leads { border-left-color: #6366f1; }
        .stat-val { font-size: 28px; font-weight: 700; color: #0f172a; margin: 4px 0 0; }
        .stat-label { font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-icon { font-size: 32px; opacity: 0.2; }
        
        .main-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 22px;
        }
        .card-header-flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .search-box {
            position: relative;
            min-width: 280px;
        }
        .search-box input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13.5px;
        }
        .search-box i {
            position: absolute;
            left: 12px;
            top: 11px;
            color: #94a3b8;
        }
        .table-responsive {
            margin-top: 10px;
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .custom-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        .custom-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .custom-table tr:hover td {
            background: #f8fafc;
        }
        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-new { background: #fef3c7; color: #b45309; }
        .badge-contacted { background: #e0e7ff; color: #4338ca; }
        .badge-booked { background: #dcfce7; color: #15803d; }
        .badge-completed { background: #d1fae5; color: #047857; }
        .badge-cancelled { background: #fee2e2; color: #b91c1c; }
        
        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 13px;
            color: #475569;
            transition: all 0.2s;
        }
        .btn-action:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .btn-action.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }
        .btn-export {
            background: #059669;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-export:hover {
            background: #047857;
            color: #fff;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 650px;
            border-radius: 10px;
            padding: 25px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .modal-title { font-size: 18px; font-weight: 700; color: #104cba; margin: 0; }
        .close-modal { background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b; }
        .detail-row { display: flex; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
        .detail-label { width: 180px; font-weight: 600; color: #475569; }
        .detail-value { flex: 1; color: #0f172a; }
    </style>
</head>
<body>

<nav class="navbar-admin">
    <a href="dashboard.php" class="navbar-brand">
        <i class="fa-solid fa-helmet-safety"></i> Construction Helps Admin Panel
    </a>
    <div class="navbar-nav">
        <a href="../index.html" target="_blank" class="btn-nav"><i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Website</a>
        <a href="export.php" class="btn-nav"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        <a href="logout.php" class="btn-nav" style="background: #dc2626;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</nav>

<div class="container-fluid">
    <?php if (!empty($message)): ?>
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Submissions</div>
                <div class="stat-val"><?php echo $totalCount; ?></div>
            </div>
            <i class="fa-solid fa-users stat-icon"></i>
        </div>
        <div class="stat-card new-leads">
            <div>
                <div class="stat-label">New / Pending Leads</div>
                <div class="stat-val"><?php echo $newCount; ?></div>
            </div>
            <i class="fa-solid fa-clock stat-icon"></i>
        </div>
        <div class="stat-card citb-leads">
            <div>
                <div class="stat-label">CITB Test Bookings</div>
                <div class="stat-val"><?php echo $citbCount; ?></div>
            </div>
            <i class="fa-solid fa-file-signature stat-icon"></i>
        </div>
        <div class="stat-card cscs-leads">
            <div>
                <div class="stat-label">CSCS Card Applications</div>
                <div class="stat-val"><?php echo $cscsCount; ?></div>
            </div>
            <i class="fa-solid fa-id-card stat-icon"></i>
        </div>
    </div>

    <!-- Submissions Table Card -->
    <div class="main-card">
        <div class="card-header-flex">
            <div>
                <h2 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: #0f172a;">All Candidate Booking &amp; Form Submissions</h2>
                <p style="margin: 0; font-size: 13px; color: #64748b;">Showing all leads captured from the booking wizards, CITB page, and contact forms.</p>
            </div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" onkeyup="filterSubmissions()" placeholder="Search name, phone, email, reference...">
                </div>
                <a href="export.php" class="btn-export">
                    <i class="fa-solid fa-download"></i> Export All
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="custom-table" id="submissionsTable">
                <thead>
                    <tr>
                        <th>Ref ID</th>
                        <th>Date / Time</th>
                        <th>Candidate Name</th>
                        <th>Service Requested</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>City / Postcode</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #64748b;">
                                <i class="fa-solid fa-inbox" style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.4;"></i>
                                No form submissions recorded yet. Submissions will appear here in real-time as users fill out forms.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $index => $row): ?>
                            <?php 
                                $status = isset($row['status']) ? $row['status'] : 'New';
                                $statusClass = 'badge-' . strtolower($status);
                            ?>
                            <tr class="submission-row">
                                <td><strong>#<?php echo htmlspecialchars(isset($row['id']) ? $row['id'] : $index + 1); ?></strong></td>
                                <td><?php echo htmlspecialchars(isset($row['created_at']) ? $row['created_at'] : 'N/A'); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars(isset($row['candidate_name']) ? $row['candidate_name'] : ($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></strong>
                                    <?php if (!empty($row['dob'])): ?>
                                        <div style="font-size: 11.5px; color: #64748b;">DOB: <?php echo htmlspecialchars($row['dob']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: #104cba;"><?php echo htmlspecialchars(isset($row['subject']) ? $row['subject'] : ($row['service_type'] ?? 'CITB Test')); ?></span>
                                    <?php if (isset($row['retake_package']) && strpos($row['retake_package'], 'Yes') !== false): ?>
                                        <div style="font-size: 11px; color: #059669;"><i class="fa-solid fa-shield-halved"></i> Retake Protected</div>
                                    <?php endif; ?>
                                </td>
                                <td><a href="tel:<?php echo htmlspecialchars($row['phone'] ?? ''); ?>" style="color: #0f172a; text-decoration: none; font-weight: 600;"><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></a></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email'] ?? ''); ?>" style="color: #64748b; text-decoration: none;"><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></a></td>
                                <td><?php echo htmlspecialchars((!empty($row['city']) ? $row['city'] . ', ' : '') . ($row['postcode'] ?? 'N/A')); ?></td>
                                <td>
                                    <span class="badge-status <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <button type="button" class="btn-action" title="View Full Details" onclick='viewDetails(<?php echo json_encode($row); ?>)'>
                                        <i class="fa-solid fa-eye" style="color: #104cba;"></i>
                                    </button>

                                    <!-- Status changer -->
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="lead_id" value="<?php echo htmlspecialchars($row['id'] ?? ''); ?>">
                                        <select name="status" onchange="this.form.submit()" style="padding: 4px 6px; font-size: 12px; border-radius: 4px; border: 1px solid #cbd5e1; cursor: pointer;">
                                            <option value="New" <?php echo $status === 'New' ? 'selected' : ''; ?>>New</option>
                                            <option value="Contacted" <?php echo $status === 'Contacted' ? 'selected' : ''; ?>>Contacted</option>
                                            <option value="Booked" <?php echo $status === 'Booked' ? 'selected' : ''; ?>>Booked</option>
                                            <option value="Completed" <?php echo $status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="Cancelled" <?php echo $status === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>

                                    <!-- Delete lead -->
                                    <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="lead_id" value="<?php echo htmlspecialchars($row['id'] ?? ''); ?>">
                                        <button type="submit" class="btn-action delete" title="Delete Lead">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal" id="detailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-id-card"></i> Candidate Submission Details</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalBody">
            <!-- Populated via JavaScript -->
        </div>
        <div style="text-align: right; margin-top: 20px;">
            <button type="button" class="btn-nav" style="background: #64748b; border: none; cursor: pointer;" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<script>
function viewDetails(data) {
    var html = `
        <div class="detail-row"><div class="detail-label">Reference ID:</div><div class="detail-value"><strong>#${data.id || 'N/A'}</strong></div></div>
        <div class="detail-row"><div class="detail-label">Submission Date:</div><div class="detail-value">${data.created_at || 'N/A'}</div></div>
        <div class="detail-row"><div class="detail-label">Candidate Full Name:</div><div class="detail-value"><strong>${data.candidate_name || ((data.first_name || '') + ' ' + (data.last_name || ''))}</strong></div></div>
        <div class="detail-row"><div class="detail-label">Date of Birth:</div><div class="detail-value">${data.dob || 'Not provided'}</div></div>
        <div class="detail-row"><div class="detail-label">National Insurance:</div><div class="detail-value"><code>${data.ni_number || 'Not provided'}</code></div></div>
        <div class="detail-row"><div class="detail-label">Residential Address:</div><div class="detail-value">${data.address_line1 || data.full_address || data.address || 'Not provided'}</div></div>
        <div class="detail-row"><div class="detail-label">Town / City:</div><div class="detail-value">${data.city || 'Not provided'}</div></div>
        <div class="detail-row"><div class="detail-label">Postcode:</div><div class="detail-value">${data.postcode || 'Not provided'}</div></div>
        <div class="detail-row"><div class="detail-label">Phone Number:</div><div class="detail-value"><a href="tel:${data.phone}">${data.phone || 'N/A'}</a></div></div>
        <div class="detail-row"><div class="detail-label">Email Address:</div><div class="detail-value"><a href="mailto:${data.email}">${data.email || 'N/A'}</a></div></div>
        <div class="detail-row"><div class="detail-label">Requested Service:</div><div class="detail-value"><strong style="color: #104cba;">${data.subject || data.service_type || 'CITB Test'}</strong></div></div>
        <div class="detail-row"><div class="detail-label">Test Type / Occupation:</div><div class="detail-value">${data.test_type || 'Standard'}</div></div>
        <div class="detail-row"><div class="detail-label">Retake Package:</div><div class="detail-value">${data.retake_package || 'No'}</div></div>
        <div class="detail-row"><div class="detail-label">Preferred Location / Date:</div><div class="detail-value">${data.preferred_location || 'Earliest available'}</div></div>
        <div class="detail-row"><div class="detail-label">Submission Source:</div><div class="detail-value">${data.source_page || 'Website Booking Form'}</div></div>
        <div class="detail-row"><div class="detail-label">Status:</div><div class="detail-value"><span class="badge-status badge-${(data.status || 'new').toLowerCase()}">${data.status || 'New'}</span></div></div>
    `;
    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('detailsModal').classList.add('active');
}

function closeModal() {
    document.getElementById('detailsModal').classList.remove('active');
}

window.onclick = function(event) {
    var modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        closeModal();
    }
}

function filterSubmissions() {
    var input = document.getElementById('searchInput');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('submissionsTable');
    var tr = table.getElementsByClassName('submission-row');

    for (var i = 0; i < tr.length; i++) {
        var text = tr[i].textContent || tr[i].innerText;
        if (text.toLowerCase().indexOf(filter) > -1) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}
</script>

</body>
</html>

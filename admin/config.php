<?php
// Construction Helps Admin Configuration
session_start();

// Admin credentials (can be updated by the site administrator)
define('ADMIN_USER', 'ch-wazid-admin');
define('ADMIN_PASS', 'Ahad@8940');

// Data file path
define('DATA_FILE', dirname(__DIR__) . '/data/submissions.json');

// Check authentication helper
function check_admin_auth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

// Helper to get all submissions
function get_all_submissions() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $content = file_get_contents(DATA_FILE);
    if (empty($content)) {
        return [];
    }
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

// Helper to save submissions
function save_submissions($data) {
    return file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}
?>

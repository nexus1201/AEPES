<?php
session_start();
require_once "../config/database.php";
require_once "../includes/audit_helper.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Auditor') {
    die("Unauthorized");
}

if (!isset($_POST['ipcrf_id'], $_POST['action'])) {
    die("Invalid request");
}

$ipcrf_id = (int) $_POST['ipcrf_id'];
$action   = $_POST['action'];
$remarks  = trim($_POST['remarks'] ?? '');

if ($action === 'approve') {

    $stmt = $conn->prepare("
        UPDATE ipcrf
        SET status = 'For HR'
        WHERE ipcrf_id = ?
    ");
    $stmt->execute([$ipcrf_id]);

    logAudit($conn, $ipcrf_id, 'Auditor approved → Sent to HR', $remarks);

} else {
    die("Invalid action");
}

header("Location: dashboard.php");
exit;

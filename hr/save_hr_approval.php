<?php
session_start();
require_once "../config/database.php";
require_once "../includes/audit_helper.php";

/* =========================
   SECURITY CHECK (HR)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    die("Unauthorized");
}

if (!isset($_POST['ipcrf_id'])) {
    die("Missing IPCRF ID");
}

$ipcrf_id = (int) $_POST['ipcrf_id'];

/* =========================
   CERTIFY IPCRF (FINAL STEP)
========================= */
$stmt = $conn->prepare("
    UPDATE ipcrf
    SET status = 'Certified'
    WHERE ipcrf_id = ?
");
$stmt->execute([$ipcrf_id]);

if ($stmt->rowCount() === 0) {
    die("Certification failed: IPCRF not updated.");
}

/* =========================
   AUDIT TRAIL
========================= */
logAudit(
    $conn,
    $ipcrf_id,
    'HR certified IPCRF',
    null
);

/* =========================
   REDIRECT BACK
========================= */
header("Location: pending_hr.php");
exit;

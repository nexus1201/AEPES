<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (AUDITOR ONLY)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Auditor') {
    header("Location: ../login.php");
    exit;
}

/* =========================
   VALIDATE INPUT
========================= */
if (!isset($_POST['ipcrf_id'])) {
    die("Invalid request.");
}

$ipcrf_id = (int) $_POST['ipcrf_id'];
$auditor_id = (int) $_SESSION['user_id'];

/* =========================
   UPDATE IPCRF STATUS → HR
========================= */
$stmt = $conn->prepare("
    UPDATE ipcrf
    SET
        status = 'For HR Review',
        audited_by = ?,
        audited_at = NOW()
    WHERE ipcrf_id = ?
");

$stmt->execute([
    $auditor_id,
    $ipcrf_id
]);

/* =========================
   REDIRECT BACK
========================= */
header("Location: dashboard.php");
exit;

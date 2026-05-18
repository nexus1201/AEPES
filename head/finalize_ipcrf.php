<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    die("Unauthorized");
}

if (!isset($_POST['ipcrf_id'])) {
    die("Missing IPCRF ID");
}

$ipcrf_id = (int) $_POST['ipcrf_id'];

$stmt = $conn->prepare("
    UPDATE ipcrf
    SET status = 'For Audit'
    WHERE ipcrf_id = ?
");
$stmt->execute([$ipcrf_id]);

if ($stmt->rowCount() === 0) {
    die("Finalize failed: IPCRF not found or already For Audit");
}

header("Location: dashboard.php");
exit;

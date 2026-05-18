<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    die("Unauthorized");
}

$user_id = (int) ($_POST['user_id'] ?? 0);
if (!$user_id) die("Invalid user");

// Check latest IPCRF
$check = $conn->prepare("
    SELECT ipcrf_id
    FROM ipcrf
    WHERE user_id = ?
    ORDER BY ipcrf_id DESC
    LIMIT 1
");
$check->execute([$user_id]);
$existing = $check->fetch();

if ($existing) {
    // Just open existing IPCRF
    $conn->prepare("
        UPDATE ipcrf
        SET status = 'Open'
        WHERE ipcrf_id = ?
    ")->execute([$existing['ipcrf_id']]);
} else {
    // Create new IPCRF (year optional)
    $conn->prepare("
        INSERT INTO ipcrf (user_id, status)
        VALUES (?, 'Open')
    ")->execute([$user_id]);
}

header("Location: manage_ipcrf.php");
exit;

<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    die("Unauthorized");
}

$user_id = (int) ($_POST['user_id'] ?? 0);
if (!$user_id) die("Invalid user");

$conn->prepare("
    UPDATE ipcrf
    SET status = 'Closed'
    WHERE ipcrf_id = (
        SELECT ipcrf_id
        FROM (
            SELECT ipcrf_id
            FROM ipcrf
            WHERE user_id = ?
            ORDER BY ipcrf_id DESC
            LIMIT 1
        ) x
    )
")->execute([$user_id]);

header("Location: manage_ipcrf.php");
exit;

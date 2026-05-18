<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}
$title = 'Audit Trail';
$showBack = true;
$backLink = 'employees.php'; // or wherever your HR employee list is
require_once "../includes/header.php";


$user_id = (int)($_GET['user_id'] ?? 0);
if (!$user_id) die("Invalid employee");

$stmt = $conn->prepare("
    SELECT
        a.action,
        a.remarks,
        a.created_at,
        u.full_name AS actor_name,
        a.actor_role
    FROM audit_trail a
    JOIN ipcrf i ON a.ipcrf_id = i.ipcrf_id
    JOIN users u ON a.actor_id = u.user_id
    WHERE i.user_id = ?
    ORDER BY a.created_at ASC
");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Trail – Employee</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        .box {
            width:80%;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:8px;
        }
        .category {
            margin-top:20px;
            font-size:18px;
            font-weight:bold;
            color:#0b4dbb;
        }
        .log {
            border-left:4px solid #0b4dbb;
            padding:10px;
            margin:10px 0;
        }
    </style>
</head>
<body>

<div class="box">

    <?php
function detectCategory($action) {
    if (stripos($action, 'core') !== false) return 'Core';
    if (stripos($action, 'strategic') !== false) return 'Strategic';
    if (stripos($action, 'support') !== false) return 'Support';
    return 'General';
}
    $currentCategory = null;
    foreach ($logs as $log):
        $category = detectCategory($log['action']);

        if ($category !== $currentCategory):
            $currentCategory = $category;
    ?>
        <div class="category">
            <?= htmlspecialchars($currentCategory ?: 'General') ?>
        </div>
    <?php endif; ?>

        <div class="log">
            <b><?= htmlspecialchars($log['action']) ?></b><br>
            By: <?= htmlspecialchars($log['actor_name']) ?>
            (<?= htmlspecialchars($log['actor_role']) ?>)<br>
            Date: <?= date("F d, Y h:i A", strtotime($log['created_at'])) ?><br>

            <?php if ($log['remarks']): ?>
                <i>Remarks:</i> <?= nl2br(htmlspecialchars($log['remarks'])) ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if (empty($logs)): ?>
        <p>No audit actions recorded.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}
$title = 'Activity Log - Employees';
$showBack = true;
require_once "../includes/header.php";
/* Fetch employees with audit trail */
$stmt = $conn->prepare("
    SELECT DISTINCT
        u.user_id,
        u.full_name,
        u.department
    FROM audit_trail a
    JOIN ipcrf i ON a.ipcrf_id = i.ipcrf_id
    JOIN users u ON i.user_id = u.user_id
    ORDER BY u.full_name
");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activity Log – Employees</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        .box {
            width:70%;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:8px;
        }
        .item {
            padding:12px;
            border-left:5px solid #0b4dbb;
            margin-bottom:12px;
        }
        a { font-weight:bold; color:#0b4dbb; text-decoration:none; }
    </style>
</head>
<body>

<div class="box">

    <?php if (empty($employees)): ?>
        <p>No audit records found.</p>
    <?php else: ?>
        <?php foreach ($employees as $emp): ?>
            <div class="item">
                <strong><?= htmlspecialchars($emp['full_name']) ?></strong><br>
                Department: <?= htmlspecialchars($emp['department']) ?><br>
                <a href="audit_trail_view.php?user_id=<?= $emp['user_id'] ?>">
                    View Activity Log →
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>

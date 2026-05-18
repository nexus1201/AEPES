<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Auditor','HR'])) {
    header("Location: ../login.php");
    exit;
}

$ipcrf_id = (int) ($_GET['id'] ?? 0);
if ($ipcrf_id <= 0) {
    die("Invalid IPCRF reference.");
}


$stmt = $conn->prepare("
    SELECT
        a.action,
        a.remarks,
        a.created_at,
        u.full_name,
        a.actor_role
    FROM audit_trail a
    JOIN users u ON a.actor_id = u.user_id
    WHERE a.ipcrf_id = ?
    ORDER BY a.created_at ASC
");
$stmt->execute([$ipcrf_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Trail</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; }
        .box {
            width:80%;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:8px;
        }
        .log {
            border-left:4px solid #0b4dbb;
            padding:10px;
            margin-bottom:10px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Audit Trail</h2>
    <a href="dashboard.php" style="
        display:inline-block;
        margin-bottom:15px;
        text-decoration:none;
        font-weight:bold;
        color:#0b4dbb;
    ">
        ← Back to Dashboard
    </a>

    <?php if (empty($logs)): ?>
        <p>No audit actions recorded yet.</p>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
        <div class="log">
            <b><?= htmlspecialchars($log['action']) ?></b><br>
            By: <?= htmlspecialchars($log['full_name']) ?> (<?= $log['actor_role'] ?>)<br>
            Date: <?= date("F d, Y h:i A", strtotime($log['created_at'])) ?><br>

            <?php if ($log['remarks']): ?>
                <i>Remarks:</i> <?= nl2br(htmlspecialchars($log['remarks'])) ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>

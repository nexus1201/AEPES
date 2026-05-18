<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (HR ONLY)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}

/* =========================
   FETCH HR AUDIT LOGS ONLY
========================= */
$stmt = $conn->prepare("
    SELECT
        a.audit_id,
        a.ipcrf_id,
        a.action,
        a.remarks,
        a.created_at,
        u.full_name
    FROM audit_trail a
    JOIN users u ON a.actor_id = u.user_id
    WHERE a.actor_role = 'HR'
    ORDER BY a.created_at DESC
");
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>HR Audit Logs</title>
    <style>
        body {
            font-family: Arial;
            background:#f4f6f9;
        }
        .box {
            width:85%;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:8px;
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
        }
        .log {
            border-left:5px solid #006400;
            padding:12px 15px;
            margin-bottom:12px;
            background:#f9fff9;
        }
        .action {
            font-weight:bold;
            color:#006400;
        }
        .meta {
            font-size:13px;
            color:#555;
        }
        .back {
            display:inline-block;
            margin-bottom:15px;
            text-decoration:none;
            font-weight:bold;
            color:#0b4dbb;
        }
    </style>
</head>
<body>

<div class="box">
    <a class="back" href="dashboard.php">← Back to HR Dashboard</a>

    <h2>HR Audit Logs</h2>

    <?php if (empty($logs)): ?>
        <p>No HR audit actions recorded yet.</p>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
            <div class="log">
                <div class="action">
                    <?= htmlspecialchars($log['action']) ?>
                </div>

                <div class="meta">
                    IPCRF ID: <?= $log['ipcrf_id'] ?><br>
                    By: <?= htmlspecialchars($log['full_name']) ?> (HR)<br>
                    Date: <?= date("F d, Y h:i A", strtotime($log['created_at'])) ?>
                </div>

                <?php if (!empty($log['remarks'])): ?>
                    <div style="margin-top:6px;">
                        <i>Remarks:</i>
                        <?= nl2br(htmlspecialchars($log['remarks'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>

<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Auditor') {
    header("Location: ../login.php");
    exit;
}

/* =========================
   ROLE FILTER
========================= */
$roleFilter = $_GET['role'] ?? '';

$allowedRoles = ['Supervisor', 'Auditor', 'HR'];
$useFilter = in_array($roleFilter, $allowedRoles);

/* =========================
   FETCH AUDIT LOGS
========================= */
if ($useFilter) {
    $stmt = $conn->prepare("
        SELECT
            a.audit_id,
            a.ipcrf_id,
            a.action,
            a.remarks,
            a.created_at,
            a.actor_role,
            u.full_name
        FROM audit_trail a
        JOIN users u ON a.actor_id = u.user_id
        WHERE a.actor_role = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$roleFilter]);
} else {
    $stmt = $conn->prepare("
        SELECT
            a.audit_id,
            a.ipcrf_id,
            a.action,
            a.remarks,
            a.created_at,
            a.actor_role,
            u.full_name
        FROM audit_trail a
        JOIN users u ON a.actor_id = u.user_id
        ORDER BY a.created_at DESC
    ");
    $stmt->execute();
}

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs</title>
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
        .filter {
            margin-bottom:20px;
        }
        .log {
            border-left:5px solid #0b4dbb;
            padding:12px 15px;
            margin-bottom:12px;
            background:#fafafa;
        }
        .action {
            font-weight:bold;
            color:#0b4dbb;
        }
        .meta {
            font-size:13px;
            color:#555;
        }
        select, button {
            padding:6px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Audit Logs</h2>

    <!-- ROLE FILTER -->
    <form method="GET" class="filter">
        <label><b>Filter by Role:</b></label>

        <select name="role">
            <option value="">-- All Roles --</option>
            <option value="Supervisor" <?= $roleFilter==='Supervisor'?'selected':'' ?>>Supervisor</option>
            <option value="Auditor" <?= $roleFilter==='Auditor'?'selected':'' ?>>Auditor</option>
            <option value="HR" <?= $roleFilter==='HR'?'selected':'' ?>>HR</option>
        </select>

        <button type="submit">Filter</button>

        <a href="audit_logs.php"
           style="margin-left:10px; font-weight:bold; text-decoration:none;">
            Reset
        </a>
    </form>

    <?php if (empty($logs)): ?>
        <p>No audit logs found.</p>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
            <div class="log">
                <div class="action">
                    <?= htmlspecialchars($log['action']) ?>
                </div>

                <div class="meta">
                    IPCRF ID: <?= $log['ipcrf_id'] ?><br>
                    By: <?= htmlspecialchars($log['full_name']) ?>
                    (<?= htmlspecialchars($log['actor_role']) ?>)<br>
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

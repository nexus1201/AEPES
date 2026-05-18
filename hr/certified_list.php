<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (HR)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    die("Unauthorized");
}
$title = 'Certified IPCRFS';
$showBack = true;
require_once "../includes/header.php";

/* =========================
   FETCH CERTIFIED IPCRFs
========================= */
$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.evaluation_period,
        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.status = 'Certified'
    ORDER BY i.ipcrf_id DESC
");
$stmt->execute();
$ipcrfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Certified IPCRFs</title>
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
        .item {
            border-left:5px solid #006400;
            padding:12px;
            margin-bottom:15px;
        }
        a {
            font-weight:bold;
            text-decoration:none;
            color:#006400;
        }
    </style>
</head>
<body>

<div class="box">

    <?php if (empty($ipcrfs)): ?>
        <p>No certified IPCRFs yet.</p>
    <?php else: ?>
        <?php foreach ($ipcrfs as $row): ?>
            <div class="item">
                <b><?= htmlspecialchars($row['full_name']) ?></b><br>
                Department: <?= htmlspecialchars($row['department']) ?><br>
                Period: <?= htmlspecialchars($row['evaluation_period']) ?><br>

                <a href="certified_ipcrf.php?id=<?= $row['ipcrf_id'] ?>" target="_blank">
                    View Certified Output →
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>

<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (AUDITOR)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Auditor') {
    header("Location: ../login.php");
    exit;
}

$ipcrf_id = (int) ($_GET['id'] ?? 0);
if ($ipcrf_id <= 0) {
    die("Invalid IPCRF.");
}

$title = 'Audit IPCRF';
$showBack = true;
require_once "../includes/header.php";

/* =========================
   FETCH IPCRF (AUDIT ONLY)
========================= */
$stmt = $conn->prepare("
    SELECT
        i.evaluation_period,
        i.status,
        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.ipcrf_id = ?
");
$stmt->execute([$ipcrf_id]);
$ipcrf = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ipcrf) {
    die("IPCRF not found.");
if ($ipcrf['status'] === 'Approved') {
    echo "<p style='color:red;font-weight:bold;'>
        🔒 This IPCRF is already HR-approved and locked.
    </p>";
    exit;
}
}

/* =========================
   FETCH RATINGS
========================= */
$ratingsStmt = $conn->prepare("
    SELECT
        core_rating, core_remarks, core_status,
        strategic_rating, strategic_remarks, strategic_status,
        support_rating, support_remarks, support_status
    FROM ipcrf
    WHERE ipcrf_id = ?
");
$ratingsStmt->execute([$ipcrf_id]);
$ratings = $ratingsStmt->fetch(PDO::FETCH_ASSOC);

if (!$ratings) {
    die("Ratings not found.");
}

/* =========================
   CHECK COMPLETENESS
========================= */
$allReviewed =
    $ratings['core_status'] === 'Reviewed' &&
    $ratings['strategic_status'] === 'Reviewed' &&
    $ratings['support_status'] === 'Reviewed';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit IPCRF</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        .box {
            width:60%;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:8px;
        }
        textarea { width:100%; padding:10px; }
        h4 { margin-bottom:5px; color:#0b4dbb; }
        button {
            padding:10px 18px;
            border:none;
            font-weight:bold;
            cursor:pointer;
        }
        .warning {
            background:#fff3cd;
            padding:10px;
            border-left:4px solid #ff9800;
            margin-bottom:15px;
        }
    </style>
</head>
<body>

<div class="box">

    <p><b>Employee:</b> <?= htmlspecialchars($ipcrf['full_name']) ?></p>
    <p><b>Department:</b> <?= htmlspecialchars($ipcrf['department']) ?></p>
    <p><b>Evaluation Period:</b> <?= htmlspecialchars($ipcrf['evaluation_period']) ?></p>
    <p><b>Status:</b> <?= htmlspecialchars($ipcrf['status']) ?></p>

    <?php if (!$allReviewed): ?>
        <div class="warning">
            ⚠ One or more categories are not fully reviewed by the Supervisor.
        </div>
    <?php endif; ?>

    <hr>

    <h3>Supervisor Ratings Summary</h3>

    <h4>Core</h4>
    Rating: <?= $ratings['core_rating'] ?? '—' ?><br>
    Status: <?= $ratings['core_status'] ?><br>
    Remarks: <?= nl2br(htmlspecialchars($ratings['core_remarks'] ?? '—')) ?>

    <h4>Strategic</h4>
    Rating: <?= $ratings['strategic_rating'] ?? '—' ?><br>
    Status: <?= $ratings['strategic_status'] ?><br>
    Remarks: <?= nl2br(htmlspecialchars($ratings['strategic_remarks'] ?? '—')) ?>

    <h4>Support</h4>
    Rating: <?= $ratings['support_rating'] ?? '—' ?><br>
    Status: <?= $ratings['support_status'] ?><br>
    Remarks: <?= nl2br(htmlspecialchars($ratings['support_remarks'] ?? '—')) ?>

    <hr>

    <form method="POST" action="save_audit.php">
        <input type="hidden" name="ipcrf_id" value="<?= $ipcrf_id ?>">

        <label><b>Auditor Remarks</b></label>
        <textarea name="remarks" rows="4"
                  placeholder="Optional remarks"></textarea>

        <br><br>

        <button name="action" value="approve"
                style="background:#006400;color:white;">
            ✅ Approve & Send to HR
        </button>
    </form>
</div>

</body>
</html>

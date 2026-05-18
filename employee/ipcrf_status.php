<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: ../login.php");
    exit;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id === 0) {
    die("User session not found.");
}

$title = 'IPCRF Status';
$showBack = true;
require_once "../includes/header.php";

/* =========================
   FETCH LATEST IPCRF
========================= */
$stmt = $conn->prepare("
    SELECT
        status,
        evaluation_period,
        core_rating, core_remarks,
        strategic_rating, strategic_remarks,
        support_rating, support_remarks
    FROM ipcrf
    WHERE user_id = ?
    ORDER BY ipcrf_id DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$ipcrf = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ipcrf) {
    die("No IPCRF found.");
}

/* =========================
   CHECK SUBMISSIONS
========================= */
$submittedStmt = $conn->prepare("
    SELECT DISTINCT category
    FROM objectives o
    JOIN ipcrf i ON o.ipcrf_id = i.ipcrf_id
    WHERE i.user_id = ?
      AND i.evaluation_period = ?
");
$submittedStmt->execute([$user_id, $ipcrf['evaluation_period']]);
$submittedCategories = $submittedStmt->fetchAll(PDO::FETCH_COLUMN);

/* =========================
   TIMELINE LOGIC
========================= */
$hasAnySubmission = !empty($submittedCategories);

$allReviewed =
    !empty($ipcrf['core_rating']) &&
    !empty($ipcrf['strategic_rating']) &&
    !empty($ipcrf['support_rating']);

$timeline = [
    'HR Opened' => in_array($ipcrf['status'], ['Open','Submitted','Reviewed','Certified']),
    'Employee Submitted' => $hasAnySubmission,
    'Supervisor Reviewed' => $allReviewed,
    'HR Certified' => ($ipcrf['status'] === 'Certified')
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>IPCRF Status</title>
    <style>
        body { background:#f4f6f9; font-family:Arial; margin: 0}
        .container {
            max-width:800px;
            margin:40px auto;
            background:#fff;
            padding:30px;
            border-radius:10px;
            box-shadow:0 4px 12px rgba(0,0,0,0.1);
        }
        h2 { color:#0b4dbb; margin-bottom:20px; }
        .timeline {
            display:flex;
            justify-content:space-between;
        }
        .step {
            text-align:center;
            flex:1;
        }
        .dot {
            width:36px;
            height:36px;
            margin:0 auto 8px;
            border-radius:50%;
            line-height:36px;
            font-weight:bold;
            color:#fff;
        }
        .label {
            font-size:13px;
            font-weight:bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📊 IPCRF Evaluation Progress</h2>
    <p style="color:#555; margin-top:-10px;">
        Track the progress of your IPCRF from opening to certification.
    </p>


    <div class="timeline">
        <?php foreach ($timeline as $label => $active): ?>
            <div class="step">
                <div class="dot" style="background:<?= $active ? '#0b4dbb' : '#ccc' ?>">
                    <?= $active ? '✔' : '•' ?>
                </div>
                <div class="label" style="color:<?= $active ? '#0b4dbb' : '#888' ?>">
                    <?= $label ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="margin-top:25px; font-size:13px; color:#555;">
        <p><b>Legend:</b></p>
        <ul style="margin-left:18px;">
            <li><b>HR Opened</b> – HR has allowed IPCRF submission</li>
            <li><b>Employee Submitted</b> – You have submitted at least one category</li>
            <li><b>Supervisor Reviewed</b> – All categories have been evaluated</li>
            <li><b>HR Certified</b> – Final certification completed</li>
        </ul>
    </div>


    <hr style="margin:30px 0;">

    <?php if (!empty($submittedCategories)): ?>
        <p><b>Evaluation Period:</b> <?= htmlspecialchars($ipcrf['evaluation_period']) ?></p>
    <?php endif; ?>

    <p><b>Current Status:</b> <?= htmlspecialchars($ipcrf['status']) ?></p>
</div>


<?php require_once "../includes/footer.php"; ?>
</body>
</html>

<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    header("Location: ../login.php");
    exit;
}
$user_id = (int) $_SESSION['user_id'];

$title = 'Employee Dashboard';
$showBack = false;
include '../includes/header.php';

/* =========================
   FETCH LATEST IPCRF
========================= */
$stmt = $conn->prepare("
    SELECT
        i.evaluation_period,
        i.status,
        i.core_status,
        i.strategic_status,
        i.support_status,        
        i.evaluated_at,

        u.full_name AS evaluator_name,
        u.role AS evaluator_role,
        u.department AS evaluator_department,

        i.core_q1, i.core_qn2, i.core_t3, i.core_a4, i.core_rating, i.core_remarks,
        i.strategic_q1, i.strategic_qn2, i.strategic_t3, i.strategic_a4, i.strategic_rating, i.strategic_remarks,
        i.support_q1, i.support_qn2, i.support_t3, i.support_a4, i.support_rating, i.support_remarks,


        ROUND(
            (IFNULL(i.core_rating,0) + IFNULL(i.strategic_rating,0) + IFNULL(i.support_rating,0)) /
            NULLIF(
                (i.core_rating IS NOT NULL) +
                (i.strategic_rating IS NOT NULL) +
                (i.support_rating IS NOT NULL),
                0
            ), 2
        ) AS overall_rating
    FROM ipcrf i
    LEFT JOIN users u ON i.evaluated_by = u.user_id
    WHERE i.user_id = ?
    ORDER BY i.ipcrf_id DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$ipcrf = $stmt->fetch(PDO::FETCH_ASSOC);
$submittedCount = 0;

if ($ipcrf) {
    if (!empty($ipcrf['core_status'])) $submittedCount++;
    if (!empty($ipcrf['strategic_status'])) $submittedCount++;
    if (!empty($ipcrf['support_status'])) $submittedCount++;
}

$steps = [
    'Not Submitted'       => 'Not Submitted Yet',
    'Partially Submitted' => 'Employee Submitting IPCRF',
    'Submitted'           => 'Employee Submitted',
    'Reviewed'            => 'Supervisor Reviewed',
    'For Audit'           => 'For Audit',
    'For HR'              => 'For HR Approval',
    'Certified'           => 'Certified by HR'
];

if (!$ipcrf) {
    $currentStatus = 'Not Submitted';
} elseif (empty($ipcrf['status'])) {
    $currentStatus = 'Submitted';
} else {
    $currentStatus = $ipcrf['status'];
}


function categoryStatus($rating, $remarks) {

    if ($rating !== null) {
        return ['Approved', 'green'];
    }

    if ($rating === null && !empty($remarks)) {
        return ['Returned', 'red'];
    }

    return ['Pending', 'orange'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <style>
        body { margin:0; font-family:Arial; background:#f4f6f9; }
        .container { padding:25px; }
        .cards {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
            gap:20px;
        }
        .card {
            background:#fff;
            border-left:6px solid #0b4dbb;
            padding:20px;
            border-radius:8px;
            box-shadow:0 4px 10px rgba(0,0,0,.1);
        }
        h3,h4 { color:#0b4dbb; margin-top:0; }
        hr { margin:12px 0; }
        .label { font-weight:bold; }
    </style>
</head>
<body>
<div class="container">
    <p>Welcome, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p>

    <div class="cards">

        <!-- IPCRF -->
        <div class="card">
            <h3>My IPCRF</h3>
            <p>Submit or update your performance objectives.</p>
            <a href="ipcrf_form.php">Open IPCRF</a>
        </div>

        <!-- PERFORMANCE -->
        <div class="card">
            <h3>IPCRF Status</h3>
            <p>Track your IPCRF progress and approvals.</p>
            <a href="ipcrf_status.php">View Timeline</a>
        </div>


        <div class="card">
            <h3>My Performance Evaluation</h3>

            <?php if (!$ipcrf): ?>
                <p>No IPCRF submitted yet.</p>
            <?php else: ?>

                <p><span class="label">Evaluation Period:</span>
                    <?= htmlspecialchars($ipcrf['evaluation_period']) ?>
                </p>

                <?php if ($ipcrf['evaluated_at']): ?>
                    <p><span class="label">Evaluated by:</span> <?= htmlspecialchars($ipcrf['evaluator_name']) ?></p>
                    <p><span class="label">Role:</span> <?= htmlspecialchars($ipcrf['evaluator_role']) ?></p>
                    <p><span class="label">Department:</span> <?= htmlspecialchars($ipcrf['evaluator_department']) ?></p>
                    <p><span class="label">Date:</span>
                        <?= date("F d, Y", strtotime($ipcrf['evaluated_at'])) ?>
                    </p>
                <?php endif; ?>

                <hr>

                <?php [$coreStatus, $coreColor] = categoryStatus(
                    $ipcrf['core_rating'],
                    $ipcrf['core_remarks']
                ); ?>

                <h4>
                    Core Function
                    <span style="color:<?= $coreColor ?>; font-weight:bold;">
                        (<?= $coreStatus ?>)
                    </span>
                </h4>

                Q1: <?= $ipcrf['core_q1'] ?? '—' ?><br>
                Qn2: <?= $ipcrf['core_qn2'] ?? '—' ?><br>
                T3: <?= $ipcrf['core_t3'] ?? '—' ?><br>
                A4: <?= $ipcrf['core_a4'] ?? '—' ?><br>
                <b>Average:</b> <?= $ipcrf['core_rating'] ?? '—' ?>

                <?php if (!empty($ipcrf['core_remarks'])): ?>
                    <p>
                        <strong>Supervisor Remarks:</strong><br>
                        <?= nl2br(htmlspecialchars($ipcrf['core_remarks'])) ?>
                    </p>
                <?php endif; ?>

                <hr>

                <?php [$strategicStatus, $strategicColor] = categoryStatus(
                    $ipcrf['strategic_rating'],
                    $ipcrf['strategic_remarks']
                ); ?>

                <h4>
                    Strategic Function
                    <span style="color:<?= $strategicColor ?>; font-weight:bold;">
                        (<?= $strategicStatus ?>)
                    </span>
                </h4>

                Q1: <?= $ipcrf['strategic_q1'] ?? '—' ?><br>
                Qn2: <?= $ipcrf['strategic_qn2'] ?? '—' ?><br>
                T3: <?= $ipcrf['strategic_t3'] ?? '—' ?><br>
                A4: <?= $ipcrf['strategic_a4'] ?? '—' ?><br>
                <b>Average:</b> <?= $ipcrf['strategic_rating'] ?? '—' ?>

                <?php if (!empty($ipcrf['strategic_remarks'])): ?>
                    <p>
                        <strong>Supervisor Remarks:</strong><br>
                        <?= nl2br(htmlspecialchars($ipcrf['strategic_remarks'])) ?>
                    </p>
                <?php endif; ?>

                <hr>

                <?php [$supportStatus, $supportColor] = categoryStatus(
                    $ipcrf['support_rating'],
                    $ipcrf['support_remarks']
                ); ?>

                <h4>
                    Support Function
                    <span style="color:<?= $supportColor ?>; font-weight:bold;">
                        (<?= $supportStatus ?>)
                    </span>
                </h4>

                Q1: <?= $ipcrf['support_q1'] ?? '—' ?><br>
                Qn2: <?= $ipcrf['support_qn2'] ?? '—' ?><br>
                T3: <?= $ipcrf['support_t3'] ?? '—' ?><br>
                A4: <?= $ipcrf['support_a4'] ?? '—' ?><br>
                <b>Average:</b> <?= $ipcrf['support_rating'] ?? '—' ?>

                <?php if (!empty($ipcrf['support_remarks'])): ?>
                    <p>
                        <strong>Supervisor Remarks:</strong><br>
                        <?= nl2br(htmlspecialchars($ipcrf['support_remarks'])) ?>
                    </p>
                <?php endif; ?>

                <hr>

                <?php if ($ipcrf['status'] === 'Reviewed'): ?>
                    <b>Overall Rating:</b>
                    <?= number_format($ipcrf['overall_rating'], 2) ?>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <!-- HISTORY -->
        <div class="card">
            <h3>Performance History</h3>
            <p>View previous evaluation records.</p>
            <a href="performance_history.php">View Records</a>
        </div>

    </div>
</div>
<?php require_once "../includes/footer.php"; ?>
</body>
</html>

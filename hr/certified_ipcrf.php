<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (HR / EMPLOYEE)
========================= */
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['HR', 'Employee'])) {
    die("Unauthorized");
}

$title = 'Certified IPCRF';
$showBack = true;
require_once "../includes/header.php";

$ipcrf_id = (int) ($_GET['id'] ?? 0);
if (!$ipcrf_id) {
    die("Invalid IPCRF ID");
}
$sessionUserId = (int) ($_SESSION['user_id'] ?? 0);

/* =========================
   FETCH CERTIFIED IPCRF
========================= */
$stmt = $conn->prepare("
    SELECT
        i.evaluation_period,
        i.status,
        u.full_name,
        u.department,
        i.core_rating,
        i.core_remarks,
        i.strategic_rating,
        i.strategic_remarks,
        i.support_rating,
        i.support_remarks
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.ipcrf_id = ?
      AND i.status = 'Certified'
      AND (? = 'HR' OR i.user_id = ?)
");
$stmt->execute([$ipcrf_id, $_SESSION['role'], $sessionUserId]);
$ipcrf = $stmt->fetch(PDO::FETCH_ASSOC);
$actorsStmt = $conn->prepare("
    SELECT
        a.actor_role,
        u.full_name
    FROM audit_trail a
    JOIN users u ON a.actor_id = u.user_id
    WHERE a.ipcrf_id = ?
    ORDER BY a.created_at ASC
");
$actorsStmt->execute([$ipcrf_id]);
$actors = $actorsStmt->fetchAll(PDO::FETCH_ASSOC);

/* Initialize */
$supervisor = '-';
$auditor = '-';
$hr = '-';

foreach ($actors as $a) {
    if ($a['actor_role'] === 'Supervisor') {
        $supervisor = $a['full_name'];
    }
    if ($a['actor_role'] === 'Auditor') {
        $auditor = $a['full_name'];
    }
    if ($a['actor_role'] === 'HR') {
        $hr = $a['full_name'];
    }
}

if (!$ipcrf) {
    die("IPCRF not certified.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Certified IPCRF</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .box {
            width: 70%;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
        }
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .actions button {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            background: #0b4dbb;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="box">

    <p><b>Employee:</b> <?= htmlspecialchars($ipcrf['full_name']) ?></p>
    <p><b>Department:</b> <?= htmlspecialchars($ipcrf['department']) ?></p>
    <p><b>Evaluation Period:</b> <?= htmlspecialchars($ipcrf['evaluation_period']) ?></p>
    <p><b>Status:</b> <?= htmlspecialchars($ipcrf['status']) ?></p>
    <p><b>Supervisor:</b> <?= htmlspecialchars($supervisor) ?></p>
    <p><b>Auditor:</b> <?= htmlspecialchars($auditor) ?></p>
    <p><b>HR Certifier:</b> <?= htmlspecialchars($hr) ?></p>

    <hr>

    <p><b>Core Rating:</b> <?= $ipcrf['core_rating'] ?></p>
    <p><b>Strategic Rating:</b> <?= $ipcrf['strategic_rating'] ?></p>
    <p><b>Support Rating:</b> <?= $ipcrf['support_rating'] ?></p>

    <?php
    // Compute the average and resulting recommendation from the final ratings.
    $core = floatval($ipcrf['core_rating']);
    $strat = floatval($ipcrf['strategic_rating']);
    $supp = floatval($ipcrf['support_rating']);
    $average = round(($core + $strat + $supp) / 3, 2);

    function ipcrfDecision($avg) {
        if ($avg >= 4.8) {
            return 'Employee is recommended for promotion';
        }
        if ($avg >= 4.0) {
            return 'Employee is recommended for leadership training';
        }
        if ($avg >= 3.5) {
            return 'Employee will have a performance base salary increase';
        }
        if ($avg >= 3.0) {
            return 'Employee is good for contract renewal';
        }
        return 'Employee will undergo developmental training';
    }

    $decision = ipcrfDecision($average);
    ?>

    <p><b>Average Rating:</b> <?= $average ?></p>
    <p><b>Recommendation / Decision:</b> <?= htmlspecialchars($decision) ?></p>

    <hr>

    <div class="actions">
        <button type="button" onclick="window.open('generate_certified_ipcrf_pdf.php?id=<?= $ipcrf_id ?>', '_blank')">
            Generate PDF
        </button>
        <?php if ($_SESSION['role'] !== 'HR'): ?>
            <button type="button" onclick="window.print()">Print Certified IPCRF</button>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

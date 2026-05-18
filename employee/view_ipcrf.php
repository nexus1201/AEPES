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

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$user_id  = (int) $_SESSION['user_id'];
$ipcrf_id = (int) $_GET['id'];

/* =========================
   FETCH IPCRF WITH DETAILS
========================= */
$stmt = $conn->prepare("
    SELECT
        i.evaluation_period,
        i.status,
        i.evaluated_at,

        u.full_name AS evaluator_name,
        u.role AS evaluator_role,
        u.department AS evaluator_department,

        i.core_q1, i.core_qn2, i.core_t3, i.core_a4, i.core_rating,
        i.strategic_q1, i.strategic_qn2, i.strategic_t3, i.strategic_a4, i.strategic_rating,
        i.support_q1, i.support_qn2, i.support_t3, i.support_a4, i.support_rating,

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
    WHERE i.ipcrf_id = ?
    AND i.user_id = ?
    LIMIT 1
");
$stmt->execute([$ipcrf_id, $user_id]);
$ipcrf = $stmt->fetch();

if (!$ipcrf) {
    die("Record not found.");
}

$title = 'View IPCRF';
$showBack = true;
require_once "../includes/header.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>View IPCRF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }
        .container {
            width: 85%;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #0b4dbb;
            margin-top: 0;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
        }
        hr {
            margin: 15px 0;
        }
        .back {
            display: inline-block;
            margin-bottom: 15px;
            background: #ffd500;
            color: #000;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin: 15px 0 20px;
        }
        .pdf-btn {
            display: inline-block;
            background: #0b4dbb;
            color: #fff;
            padding: 9px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">

    <a class="back" href="performance_history.php">← Back to Performance History</a>

    <h2>Individual Performance Commitment and Review (IPCRF)</h2>

    <?php if ($ipcrf['status'] === 'Certified'): ?>
        <div class="actions">
            <a class="pdf-btn" href="../hr/generate_certified_ipcrf_pdf.php?id=<?= $ipcrf_id ?>" target="_blank">
                Generate PDF
            </a>
        </div>
    <?php endif; ?>

    <div class="section">
        <p><span class="label">Evaluation Period:</span>
            <?= htmlspecialchars($ipcrf['evaluation_period']) ?></p>

        <p><span class="label">Status:</span>
            <?= htmlspecialchars($ipcrf['status']) ?></p>
    </div>

    <?php if ($ipcrf['evaluated_at']): ?>
    <div class="section">
        <p><span class="label">Evaluated by:</span>
            <?= htmlspecialchars($ipcrf['evaluator_name']) ?></p>

        <p><span class="label">Evaluator Role:</span>
            <?= htmlspecialchars($ipcrf['evaluator_role']) ?></p>

        <p><span class="label">Evaluator Department:</span>
            <?= htmlspecialchars($ipcrf['evaluator_department']) ?></p>

        <p><span class="label">Date Evaluated:</span>
            <?= date("F d, Y", strtotime($ipcrf['evaluated_at'])) ?></p>
    </div>
    <?php endif; ?>

    <hr>

    <h3>Core Function</h3>
    Q1: <?= $ipcrf['core_q1'] ?? '—' ?><br>
    Qn2: <?= $ipcrf['core_qn2'] ?? '—' ?><br>
    T3: <?= $ipcrf['core_t3'] ?? '—' ?><br>
    A4: <?= $ipcrf['core_a4'] ?? '—' ?><br>
    <strong>Average:</strong> <?= $ipcrf['core_rating'] ?? '—' ?>

    <hr>

    <h3>Strategic Function</h3>
    Q1: <?= $ipcrf['strategic_q1'] ?? '—' ?><br>
    Qn2: <?= $ipcrf['strategic_qn2'] ?? '—' ?><br>
    T3: <?= $ipcrf['strategic_t3'] ?? '—' ?><br>
    A4: <?= $ipcrf['strategic_a4'] ?? '—' ?><br>
    <strong>Average:</strong> <?= $ipcrf['strategic_rating'] ?? '—' ?>

    <hr>

    <h3>Support Function</h3>
    Q1: <?= $ipcrf['support_q1'] ?? '—' ?><br>
    Qn2: <?= $ipcrf['support_qn2'] ?? '—' ?><br>
    T3: <?= $ipcrf['support_t3'] ?? '—' ?><br>
    A4: <?= $ipcrf['support_a4'] ?? '—' ?><br>
    <strong>Average:</strong> <?= $ipcrf['support_rating'] ?? '—' ?>

    <hr>

    <?php if ($ipcrf['status'] === 'Reviewed'): ?>
        <h3>Overall Rating</h3>
        <strong><?= number_format($ipcrf['overall_rating'], 2) ?></strong>
    <?php endif; ?>

</div>

<?php require_once "../includes/footer.php"; ?>
</body>
</html>

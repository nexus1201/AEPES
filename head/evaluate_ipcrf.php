<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    header("Location: ../login.php");
    exit;
}
$title = 'Evaluation Page';
$showBack = true;
require_once "../includes/header.php";

$ipcrf_id = (int) ($_GET['id'] ?? 0);
$category = $_GET['category'] ?? '';
$user_id = (int) ($_GET['user_id'] ?? 0);

if ($user_id === 0) {
    die("Employee not selected.");
}


if (!$ipcrf_id || !in_array($category, ['Core','Strategic','Support'])) {
    die("Invalid request.");
}

/* =========================
   FETCH OBJECTIVE
========================= */
$stmt = $conn->prepare("
    SELECT 
        objective_description,
        output,
        success_indicator,
        actual_accomplishment
    FROM objectives
    WHERE ipcrf_id = ? AND category = ?
");
$stmt->execute([$ipcrf_id, $category]);
$obj = $stmt->fetch();

if (!$obj) {
    die("Objective not found for this category.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Evaluate <?= htmlspecialchars($category) ?> Function</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; }
        .box {
            width:80%;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:6px;
        }
        label { font-weight:bold; display:block; margin-bottom:8px; }
        input, textarea {
            padding:10px;
            margin-top:6px;
            margin-bottom:15px;
        }
        .grade-container {
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:30px;
            margin-bottom:30px;
        }
        .grade-item {
            display:flex;
            flex-direction:column;
        }
        .grade-item label {
            margin-bottom:6px;
        }
        .grade-item input {
            width:100px;
            margin:0 0 15px 0;
        }
        textarea {
            width:100%;
        }
        button {
            background:#0b4dbb;
            color:#fff;
            padding:10px 20px;
            border:none;
            cursor:pointer;
        }
    </style>
</head>
<body>

<div class="box">
    <h2><?= htmlspecialchars($category) ?> Function Evaluation</h2>

    <p><strong>Objective:</strong><br><?= nl2br(htmlspecialchars($obj['objective_description'])) ?></p>
    <p><strong>Output:</strong><br><?= nl2br(htmlspecialchars($obj['output'])) ?></p>
    <p><strong>Success Indicator:</strong><br><?= nl2br(htmlspecialchars($obj['success_indicator'])) ?></p>
    <p><strong>Actual Accomplishment:</strong><br><?= nl2br(htmlspecialchars($obj['actual_accomplishment'])) ?></p>

    <form method="POST" action="save_evaluation.php">

        <input type="hidden" name="ipcrf_id" value="<?= $ipcrf_id ?>">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">

        <div class="grade-container">
            <div class="grade-item">
                <label>Q1 – Quality (1.00–5.00)</label>
                <input type="number" name="q1" step="0.01" min="1" max="5">
            </div>
            <div class="grade-item">
                <label>Qn2 – Quantity (1.00–5.00)</label>
                <input type="number" name="qn2" step="0.01" min="1" max="5">
            </div>
            <div class="grade-item">
                <label>T3 – Timeliness (1.00–5.00)</label>
                <input type="number" name="t3" step="0.01" min="1" max="5">
            </div>
            <div class="grade-item">
                <label>A4 – Average Performance (1.00–5.00)</label>
                <input type="number" name="a4" step="0.01" min="1" max="5">
            </div>
        </div>

        <label>Supervisor Remarks</label>
        <textarea name="remarks" rows="4" placeholder="Enter remarks for this category..." required></textarea>


        <button type="submit" name="action" value="approve">
            Approve <?= htmlspecialchars($category) ?>
        </button>

        <button type="submit"
                name="action"
                value="return"
                onclick="return validateReturn();"
                style="background:#b30000; margin-left:10px;">
            Return <?= htmlspecialchars($category) ?> for Revision
        </button>
    </form>
</div>
<script>
function validateReturn() {
    const remarks = document.querySelector('textarea[name="remarks"]').value.trim();
    if (!remarks) {
        alert("Remarks are required when returning a category.");
        return false;
    }
    return true;
}
</script>
</body>
</html>

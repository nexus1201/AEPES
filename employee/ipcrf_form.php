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

$title = 'IPCRF';
$showBack = true;
require_once "../includes/header.php";

// ===========================
// MASTER IPCRF LOCK (HR CONTROL)
// ===========================
$statusStmt = $conn->prepare("
    SELECT status
    FROM ipcrf
    WHERE user_id = ?
    ORDER BY ipcrf_id DESC
    LIMIT 1
");
$statusStmt->execute([$user_id]);
$ipcrfStatus = $statusStmt->fetchColumn();

// If no IPCRF yet OR not opened by HR → LOCK
if ($ipcrfStatus !== 'Open') {
    ?>
    <div style="
        max-width:800px;
        margin:60px auto;
        background:#ffffff;
        padding:35px;
        border-radius:12px;
        box-shadow:0 6px 18px rgba(0,0,0,0.1);
        border-left:8px solid #b30000;
        text-align:center;
    ">
        <h2 style="color:#b30000;">🔒 IPCRF Locked</h2>

        <p style="font-size:16px;">
            The IPCRF form is currently <strong>closed</strong>.
        </p>

        <p>
            Please wait for the <strong>HR Department</strong> to open
            the IPCRF for this evaluation period.
        </p>

        <a href="dashboard.php"
           style="
            display:inline-block;
            margin-top:20px;
            background:#0b4dbb;
            color:#fff;
            padding:12px 24px;
            text-decoration:none;
            border-radius:6px;
            font-weight:bold;
           ">
            ⬅ Back to Dashboard
        </a>
    </div>
    <?php require_once "../includes/footer.php"; ?>
    <?php
    exit;
}

/* =========================
   GET LATEST EVALUATION PERIOD
========================= */
$periodStmt = $conn->prepare("
    SELECT evaluation_period
    FROM ipcrf
    WHERE user_id = ?
    AND status IN ('Open','Submitted','Reviewed')
    ORDER BY ipcrf_id DESC
    LIMIT 1
");
$periodStmt->execute([$user_id]);
$period = $periodStmt->fetchColumn();

/* =========================
   FETCH SUBMITTED CATEGORIES (OBJECTIVES)
========================= */
$submittedCategories = [];

$objectiveStmt = $conn->prepare("
    SELECT DISTINCT o.category
    FROM objectives o
    JOIN ipcrf i ON o.ipcrf_id = i.ipcrf_id
    WHERE i.user_id = ?
      AND i.evaluation_period = ?
");
$objectiveStmt->execute([$user_id, $period]);

while ($row = $objectiveStmt->fetch(PDO::FETCH_ASSOC)) {
    $submittedCategories[] = $row['category'];
}
$submittedCount = count(array_unique($submittedCategories));

/* =========================
   FETCH CATEGORY STATUS (RATING / REMARKS)
========================= */
$statusStmt = $conn->prepare("
    SELECT
        core_rating, core_remarks,
        strategic_rating, strategic_remarks,
        support_rating, support_remarks
    FROM ipcrf
    WHERE user_id = ? AND evaluation_period = ?
    LIMIT 1
");
$statusStmt->execute([$user_id, $period]);
$categoryStatus = $statusStmt->fetch(PDO::FETCH_ASSOC) ?: [];
$hasReturned =
    (!empty($categoryStatus['core_remarks']) && empty($categoryStatus['core_rating'])) ||
    (!empty($categoryStatus['strategic_remarks']) && empty($categoryStatus['strategic_rating'])) ||
    (!empty($categoryStatus['support_remarks']) && empty($categoryStatus['support_rating']));


/* =========================
   LOCKING LOGIC (FINAL & CORRECT)
========================= */
function isLocked($category, $submittedCategories, $rating, $remarks) {

    // Not submitted yet → editable
    if (!in_array($category, $submittedCategories)) {
        return false;
    }

    // Rated → permanently locked
    if (!empty($rating)) {
        return true;
    }

    // Returned by supervisor → editable
    if (!empty($remarks)) {
        return false;
    }

    // Submitted but not returned → locked
    return true;
}

$coreLocked = isLocked(
    'Core',
    $submittedCategories,
    $categoryStatus['core_rating'] ?? null,
    $categoryStatus['core_remarks'] ?? null
);

$strategicLocked = isLocked(
    'Strategic',
    $submittedCategories,
    $categoryStatus['strategic_rating'] ?? null,
    $categoryStatus['strategic_remarks'] ?? null
);

$supportLocked = isLocked(
    'Support',
    $submittedCategories,
    $categoryStatus['support_rating'] ?? null,
    $categoryStatus['support_remarks'] ?? null
);

$formLocked =
    ($submittedCount === 3 && !$hasReturned);


/* =========================
   PROGRESS
========================= */
$completedCount = count(array_unique($submittedCategories));
$totalCategories = 3;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee IPCRF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #0b4dbb;
            margin-top: 0;
        }
        label {
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #0b4dbb;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #083a8c;
        }
        select option.locked-option {
            color: #999;
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Individual Performance Commitment and Review Form</h2>

    <div style="margin-bottom:15px;padding:10px;background:#eaf2ff;border-left:5px solid #0b4dbb;font-weight:bold;">
        Progress: <?= $completedCount ?> / <?= $totalCategories ?> Categories Submitted
    </div>

    <?php if (!empty($categoryStatus['core_remarks']) && empty($categoryStatus['core_rating'])): ?>
        <div style="background:#ffe6e6;padding:12px;margin-bottom:15px;border-left:5px solid #b30000;">
            <strong>Core Returned by Supervisor:</strong><br>
            <?= nl2br(htmlspecialchars($categoryStatus['core_remarks'])) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($categoryStatus['strategic_remarks']) && empty($categoryStatus['strategic_rating'])): ?>
        <div style="background:#ffe6e6;padding:12px;margin-bottom:15px;border-left:5px solid #b30000;">
            <strong>Strategic Returned by Supervisor:</strong><br>
            <?= nl2br(htmlspecialchars($categoryStatus['strategic_remarks'])) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($categoryStatus['support_remarks']) && empty($categoryStatus['support_rating'])): ?>
        <div style="background:#ffe6e6;padding:12px;margin-bottom:15px;border-left:5px solid #b30000;">
            <strong>Support Returned by Supervisor:</strong><br>
            <?= nl2br(htmlspecialchars($categoryStatus['support_remarks'])) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="submit_ipcrf.php" enctype="multipart/form-data">

        <label>Evaluation Period</label>
        <select name="period" required>
            <option value="">-- Select Evaluation Year --</option>
            <?php
            $currentYear = date('Y');
            for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
                $selected = ($period == $year) ? 'selected' : '';
                echo "<option value=\"$year\" $selected>$year</option>";
            }
            ?>
        </select>

        <label>Performance Objective</label>
        <textarea name="objective" required></textarea>

        <label>Category</label>
        <select name="category" required>
            <option value="">-- Select Category --</option>
        <option value="Core"
            <?= $coreLocked ? 'disabled class="locked-option"' : '' ?>>
            Core <?= $coreLocked ? '🔒 Locked' : '' ?>
        </option>

        <option value="Strategic"
            <?= $strategicLocked ? 'disabled class="locked-option"' : '' ?>>
            Strategic <?= $strategicLocked ? '🔒 Locked' : '' ?>
        </option>

        <option value="Support"
            <?= $supportLocked ? 'disabled class="locked-option"' : '' ?>>
            Support <?= $supportLocked ? '🔒 Locked' : '' ?>
        </option>

        </select>

        <label>Output</label>
        <textarea name="output" required></textarea>

        <label>Success Indicator</label>
        <textarea name="success_indicator" required></textarea>

        <label>Actual Accomplishment</label>
        <textarea name="actual_accomplishment" required></textarea>

        <label>Attach Proof</label>
        <input type="file" name="proof" required>

        <?php if ($formLocked): ?>
            <button type="button" disabled style="background:#999;">IPCRF Locked 🔒</button>
        <?php else: ?>
            <button type="submit">Submit IPCRF</button>
        <?php endif; ?>

    </form>
</div>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>

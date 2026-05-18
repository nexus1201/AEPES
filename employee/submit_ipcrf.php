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
// ===========================
// BLOCK SUBMISSION IF IPCRF CLOSED
// ===========================
$check = $conn->prepare("
    SELECT status
    FROM ipcrf
    WHERE user_id = ?
    ORDER BY ipcrf_id DESC
    LIMIT 1
");
$check->execute([$user_id]);
$status = $check->fetchColumn();

if ($status !== 'Open') {
    die("IPCRF is currently closed by HR.");
}


/* =========================
   GET FORM DATA
========================= */
$period  = $_POST['period'] ?? '';
$objective = $_POST['objective'] ?? '';
$category  = $_POST['category'] ?? '';
$category = ucfirst(strtolower(trim($category)));

if (!in_array($category, ['Core','Strategic','Support'])) {
    die("Invalid category selected.");
}
$output = $_POST['output'] ?? '';
$success_indicator = $_POST['success_indicator'] ?? '';
$actual_accomplishment = $_POST['actual_accomplishment'] ?? '';

if (
    $period === '' ||
    $objective === '' ||
    $category === '' ||
    $output === '' ||
    $success_indicator === '' ||
    $actual_accomplishment === ''
) {
    die("All fields are required.");
}

/* =========================
   FILE UPLOAD
========================= */
$uploadDir = "../uploads/ipcrf_proofs/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== 0) {
    die("Proof attachment is required.");
}

$ext = strtolower(pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION));
$allowed = ['pdf','jpg','jpeg','png','doc','docx'];

if (!in_array($ext, $allowed)) {
    die("Invalid file type.");
}

$fileName = time() . "_" . uniqid() . "." . $ext;
move_uploaded_file($_FILES['proof']['tmp_name'], $uploadDir . $fileName);

/* =========================
   GET OR CREATE IPCRF
========================= */
$check = $conn->prepare("
    SELECT ipcrf_id
    FROM ipcrf
    WHERE user_id = ?
    ORDER BY ipcrf_id DESC
    LIMIT 1
");
$check->execute([$user_id]);
$existing = $check->fetch();

if ($existing) {
    $ipcrf_id = $existing['ipcrf_id'];

    // ✅ IMPORTANT: update evaluation period to employee-selected year
    $conn->prepare("
        UPDATE ipcrf
        SET evaluation_period = ?
        WHERE ipcrf_id = ?
    ")->execute([$period, $ipcrf_id]);

} else {
    $insert = $conn->prepare("
        INSERT INTO ipcrf (user_id, evaluation_period, status)
        VALUES (?, ?, 'Open')
    ");
    $insert->execute([$user_id, $period]);
    $ipcrf_id = $conn->lastInsertId();
}
// =======================
// BLOCK EDITING IF CATEGORY IS ALREADY RATED
// =======================
$check = $conn->prepare("
    SELECT core_rating, strategic_rating, support_rating
    FROM ipcrf
    WHERE ipcrf_id = ?
");
$check->execute([$ipcrf_id]);
$ratings = $check->fetch(PDO::FETCH_ASSOC);

if (
    ($category === 'Core' && !empty($ratings['core_rating'])) ||
    ($category === 'Strategic' && !empty($ratings['strategic_rating'])) ||
    ($category === 'Support' && !empty($ratings['support_rating']))
) {
    die("This category has already been rated and is locked.");
}

/* =========================
   REMOVE OLD OBJECTIVE (SAME CATEGORY ONLY)
========================= */
$delete = $conn->prepare("
    DELETE FROM objectives
    WHERE ipcrf_id = ? AND category = ?
");
$delete->execute([$ipcrf_id, $category]);

/* =========================
   INSERT NEW OBJECTIVE
========================= */
$insertObj = $conn->prepare("
    INSERT INTO objectives
    (ipcrf_id, objective_description, category, output, success_indicator, actual_accomplishment, proof_attachment)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$insertObj->execute([
    $ipcrf_id,
    $objective,
    $category,
    $output,
    $success_indicator,
    $actual_accomplishment,
    $fileName
]);
/* ===========================
   FIX IPCRF STATUS (FINAL LOGIC)
=========================== */

// Count submitted categories
$countStmt = $conn->prepare("
    SELECT COUNT(DISTINCT category)
    FROM objectives
    WHERE ipcrf_id = ?
");
$countStmt->execute([$ipcrf_id]);
$submittedCount = (int)$countStmt->fetchColumn();

// Check returned categories
$returnStmt = $conn->prepare("
    SELECT
        core_remarks,
        strategic_remarks,
        support_remarks
    FROM ipcrf
    WHERE ipcrf_id = ?
");
$returnStmt->execute([$ipcrf_id]);
$remarks = $returnStmt->fetch(PDO::FETCH_ASSOC);

$hasReturned =
    (!empty($remarks['core_remarks'])) ||
    (!empty($remarks['strategic_remarks'])) ||
    (!empty($remarks['support_remarks']));

// Decide correct status
if ($submittedCount < 3 || $hasReturned) {
    // KEEP FORM OPEN
    $conn->prepare("
        UPDATE ipcrf
        SET status = 'Open'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);
} else {
    // ALL DONE → CLOSE FOR REVIEW
    $conn->prepare("
        UPDATE ipcrf
        SET status = 'Submitted'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);
}

// ===========================
// CHECK IF ALL CATEGORIES ARE SUBMITTED
// ===========================
$countStmt = $conn->prepare("
    SELECT COUNT(DISTINCT category)
    FROM objectives
    WHERE ipcrf_id = ?
");
$countStmt->execute([$ipcrf_id]);
$submittedCount = (int) $countStmt->fetchColumn();

/* =========================
   RESET CATEGORY STATUS ON RESUBMIT
========================= */
if ($category === 'Core') {
    $conn->prepare("
        UPDATE ipcrf
        SET
            core_remarks = NULL,
            core_status = 'Pending'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);
}

if ($category === 'Strategic') {
    $conn->prepare("
        UPDATE ipcrf
        SET
            strategic_remarks = NULL,
            strategic_status = 'Pending'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);
}

if ($category === 'Support') {
    $conn->prepare("
        UPDATE ipcrf
        SET
            support_remarks = NULL,
            support_status = 'Pending'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);
}

/* =========================
   REDIRECT
========================= */
$title = 'IPCRF Submitted';
require_once "../includes/header.php";
?>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #f4f6f9;
    }

    .result-box {
        max-width: 700px;
        margin: 60px auto;
        background: #ffffff;
        padding: 35px;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        border-left: 8px solid #0b4dbb;
        text-align: center;
    }

    .result-box h2 {
        color: #006400;
        margin-bottom: 10px;
    }

    .result-box p {
        font-size: 16px;
        color: #333;
        margin-bottom: 20px;
    }

    .badge {
        display: inline-block;
        background: #0b4dbb;
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .actions a {
        display: inline-block;
        margin: 10px;
        padding: 12px 22px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
    }

    .back {
        background: #0b4dbb;
        color: #fff;
    }

    .dashboard {
        background: #ffd500;
        color: #000;
    }

    .actions a:hover {
        opacity: 0.9;
    }
</style>

<div class="result-box">
    <h2>✅ IPCRF Submitted Successfully</h2>

    <span class="badge">
        <?= htmlspecialchars($category) ?> Function
    </span>

    <p>
        Your IPCRF entry has been saved and submitted for review.<br>
        Please wait for your Department Head to evaluate your submission.
    </p>

    <div class="actions">
        <a class="back" href="ipcrf_form.php">
            ⬅ Submit Another Category
        </a>

        <a class="dashboard" href="dashboard.php">
            🏠 Back to Dashboard
        </a>
    </div>
</div>

<?php
exit;


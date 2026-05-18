<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['user_id'])) {
    die("Employee not selected.");
}

$user_id = (int) $_GET['user_id'];

/* =========================
   FETCH IPCRF + OBJECTIVES
========================= */
$stmt = $conn->prepare("
    SELECT 
        i.ipcrf_id,
        i.evaluation_period,
        i.status,

        i.core_rating,
        i.core_remarks,

        i.strategic_rating,
        i.strategic_remarks,

        i.support_rating,
        i.support_remarks,

        o.category,
        o.objective_description,
        o.output,
        o.success_indicator,
        o.actual_accomplishment,
        o.proof_attachment,

        u.full_name
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    JOIN objectives o ON o.ipcrf_id = i.ipcrf_id
    WHERE i.ipcrf_id = (
        SELECT ipcrf_id
        FROM ipcrf
        WHERE user_id = ?
        ORDER BY ipcrf_id DESC
        LIMIT 1
    )
    ORDER BY FIELD(o.category,'Core','Strategic','Support')
");

$stmt->execute([$user_id]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$records) {
    die("No IPCRF found.");
}

/* =========================
   HEADER + FINALIZE CHECK
========================= */
$header = $records[0];
$ipcrf_id = $header['ipcrf_id'];

$canFinalize =
    $header['core_rating'] !== null &&
    $header['strategic_rating'] !== null &&
    $header['support_rating'] !== null;
$isFinalApproved = ($header['status'] === 'Approved');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Department Head | IPCRF Evaluation</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; }
        .card {
            width:90%;
            margin:20px auto;
            background:#fff;
            padding:20px;
            border-radius:8px;
        }
        .category {
            background:#ffd500;
            padding:4px 10px;
            border-radius:4px;
            font-weight:bold;
        }
        .btn {
            background:#0b4dbb;
            color:#fff;
            padding:8px 16px;
            text-decoration:none;
            border-radius:5px;
            font-weight:bold;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">IPCRF Evaluation</h2>
<div style="width:90%; margin:15px auto;">
    <a href="employees.php"
       style="
           display:inline-block;
           background:#0b4dbb;
           color:#fff;
           padding:8px 16px;
           text-decoration:none;
           border-radius:5px;
           font-weight:bold;
       ">
        ⬅ Back to Employees
    </a>
</div>

<?php foreach ($records as $row): ?>
<div class="card">
    <h3><?= htmlspecialchars($row['full_name']) ?></h3>

    <p><b>Period:</b> <?= $row['evaluation_period'] ?></p>
    <p>
    <b>Status:</b>
    <span style="
        padding:4px 10px;
        border-radius:4px;
        background:<?= $header['status']==='Approved' ? '#2e7d32' : '#ffd500' ?>;
        color:white;
        font-weight:bold;
    ">
        <?= htmlspecialchars($header['status']) ?>
    </span>
    </p>
    <p><b>Category:</b> <span class="category"><?= $row['category'] ?></span></p>

    <p><b>Objective:</b><br><?= nl2br(htmlspecialchars($row['objective_description'])) ?></p>
    <p><b>Output:</b><br><?= nl2br(htmlspecialchars($row['output'])) ?></p>
    <p><b>Success Indicator:</b><br><?= nl2br(htmlspecialchars($row['success_indicator'])) ?></p>
    <p><b>Actual Accomplishment:</b><br><?= nl2br(htmlspecialchars($row['actual_accomplishment'])) ?></p>
    <?php if (!empty($row['proof_attachment'])): ?>
        <p>
            <b>Attached Proof:</b><br>
            <a href="../uploads/ipcrf_proofs/<?= urlencode($row['proof_attachment']) ?>"
            target="_blank"
            style="
                display:inline-block;
                margin-top:6px;
                background:#0b4dbb;
                color:white;
                padding:6px 12px;
                text-decoration:none;
                border-radius:4px;
                font-weight:bold;
            ">
                📎 View / Download Attachment
            </a>
        </p>
    <?php else: ?>
        <p><b>Attached Proof:</b> <i>No file uploaded</i></p>
    <?php endif; ?>
    <?php
    $category = $row['category'];

    $isApproved = false;
    $isReturned = false;

    if ($category === 'Core') {
        $isApproved = $row['core_rating'] !== null;
        $isReturned = $row['core_rating'] === null && $row['core_remarks'] !== null;
    }

    if ($category === 'Strategic') {
        $isApproved = $row['strategic_rating'] !== null;
        $isReturned = $row['strategic_rating'] === null && $row['strategic_remarks'] !== null;
    }

    if ($category === 'Support') {
        $isApproved = $row['support_rating'] !== null;
        $isReturned = $row['support_rating'] === null && $row['support_remarks'] !== null;
    }
    ?>


<?php if ($isApproved): ?>
    <span style="color:green; font-weight:bold;">✔ Approved</span>

<?php elseif ($isReturned): ?>
    <span style="color:#b30000; font-weight:bold;">
        🔄 Returned for Revision
    </span>

<?php else: ?>
    <?php if ($isFinalApproved): ?>
        <span style="color:gray;font-weight:bold;">
            🔒 Locked (HR Approved)
        </span>
    <?php else: ?>
        <a class="btn"
           href="evaluate_ipcrf.php?id=<?= $row['ipcrf_id'] ?>&category=<?= $row['category'] ?>&user_id=<?= $user_id ?>">
           Evaluate
        </a>
    <?php endif; ?>
<?php endif; ?>


</div>
<?php endforeach; ?>

<?php if ($canFinalize && !$isFinalApproved): ?>
<div class="card" style="
    text-align:center;
    background:#eaf7ea;
    border-left:6px solid #006400;
">
    <form method="POST" action="finalize_ipcrf.php">
        <input type="hidden" name="ipcrf_id" value="<?= (int)$ipcrf_id ?>">
        <button style="
            background:#006400;
            color:white;
            padding:14px 28px;
            border:none;
            font-size:16px;
            font-weight:bold;
            cursor:pointer;
        ">
            ✅ Finalize & Send to Auditor
        </button>
    </form>
</div>
<?php endif; ?>


</body>
</html>

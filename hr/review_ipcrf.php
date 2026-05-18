<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (HR)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    die("Unauthorized");
}

$ipcrf_id = (int) ($_GET['id'] ?? 0);
if (!$ipcrf_id) {
    die("Invalid IPCRF ID");
}

/* =========================
   FETCH IPCRF SUMMARY
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
    die("IPCRF not found");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HR Review & Certification</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        /* HEADER */
        .header {
            background: #0b4dbb;
            color: #fff;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .back-btn {
            background: #ffd500;
            color: #000;
            padding: 8px 14px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
        }

        /* CARD */
        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        h2 {
            color: #0b4dbb;
            margin-top: 0;
        }

        .info {
            margin-bottom: 18px;
            padding: 12px;
            background: #f0f4ff;
            border-left: 5px solid #0b4dbb;
            border-radius: 4px;
        }

        .info b {
            display: inline-block;
            width: 160px;
        }

        .status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
            background: #ffd500;
            color: #000;
        }

        /* ACTIONS */
        .actions {
            margin-top: 30px;
            text-align: center;
        }

        .certify-btn {
            background: #006400;
            color: #fff;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .certify-btn:hover {
            background: #004d00;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #0b4dbb;
            font-weight: bold;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>AEPES – HR Certification</h1>
    <a class="back-btn" href="pending_hr.php">Back</a>
</div>

<div class="container">
    <h2>Review IPCRF</h2>

    <div class="info">
        <b>Employee:</b> <?= htmlspecialchars($ipcrf['full_name']) ?>
    </div>

    <div class="info">
        <b>Department:</b> <?= htmlspecialchars($ipcrf['department']) ?>
    </div>

    <div class="info">
        <b>Evaluation Period:</b> <?= htmlspecialchars($ipcrf['evaluation_period']) ?>
    </div>

    <div class="info">
        <b>Status:</b>
        <span class="status">
            <?= htmlspecialchars($ipcrf['status']) ?>
        </span>
    </div>

    <div class="actions">
        <form method="POST" action="save_hr_approval.php">
            <input type="hidden" name="ipcrf_id" value="<?= $ipcrf_id ?>">
            <button type="submit" class="certify-btn">
                ✅ Certify IPCRF
            </button>
        </form>

        <a class="back-link" href="pending_hr.php">
            ← Back to Pending HR Approvals
        </a>
    </div>
</div>

</body>
</html>


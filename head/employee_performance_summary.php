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
$title = 'Employee Performance Summary';
$showBack = true;
require_once "../includes/header.php";

/* =========================
   FETCH EMPLOYEE PERFORMANCE
========================= */
$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.user_id,
        i.evaluation_period,

        u.full_name,
        u.department,

        i.core_status,
        i.strategic_status,
        i.support_status,

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
    JOIN users u ON i.user_id = u.user_id
    ORDER BY u.full_name ASC
");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   STATUS HELPER
========================= */
function badge($status) {
    return match ($status) {
        'Reviewed' => '<span style="color:green;font-weight:bold;">Reviewed</span>',
        'Returned' => '<span style="color:red;font-weight:bold;">Returned</span>',
        default    => '<span style="color:orange;font-weight:bold;">Pending</span>',
    };
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Performance Summary</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        .container {
            width: 95%;
            margin: 30px auto;
            background:#fff;
            padding:20px;
            border-radius:8px;
            box-shadow:0 4px 10px rgba(0,0,0,.1);
        }
        h2 { color:#0b4dbb; }
        table {
            width:100%;
            border-collapse:collapse;
        }
        th, td {
            padding:10px;
            border:1px solid #ddd;
            text-align:center;
        }
        th {
            background:#0b4dbb;
            color:#fff;
        }
        a {
            text-decoration:none;
            font-weight:bold;
            color:#0b4dbb;
        }
    </style>
</head>
<body>

<div class="container">

    <table>
        <tr>
            <th>Employee</th>
            <th>Department</th>
            <th>Period</th>
            <th>Core</th>
            <th>Strategic</th>
            <th>Support</th>
            <th>Overall</th>
            <th>Action</th>
        </tr>

        <?php if (!$records): ?>
            <tr>
                <td colspan="8">No records found.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($records as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['evaluation_period']) ?></td>

                <td><?= badge($row['core_status']) ?></td>
                <td><?= badge($row['strategic_status']) ?></td>
                <td><?= badge($row['support_status']) ?></td>

                <td>
                    <?= $row['overall_rating'] !== null
                        ? number_format($row['overall_rating'], 2)
                        : '—' ?>
                </td>

                <td>
                    <a href="evaluations.php?user_id=<?= $row['user_id'] ?>">
                        View
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
</div>

</body>
</html>

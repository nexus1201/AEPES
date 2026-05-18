<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (HR)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}

/* =========================
   FETCH CERTIFIED IPCRFs
========================= */
$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.evaluation_period,
        i.status,
        u.full_name,
        u.department,

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
    WHERE i.status = 'Certified'
    ORDER BY i.evaluation_period DESC
");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>HR Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:#f4f6f9;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 14px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #0b4dbb;
            color: #fff;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #0b4dbb;
        }
        .back {
            display: inline-block;
            margin: 20px;
            background: #ffd500;
            color: #000;
            padding: 8px 14px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
        }
        .status-certified {
            color: green;
            font-weight: bold;
        }
        a {
            color: #0b4dbb;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>

<a class="back" href="dashboard.php">← Back to HR Dashboard</a>

<h2>Certified IPCRF Reports</h2>

<?php if (empty($records)): ?>
    <p style="text-align:center;">No certified IPCRFs found.</p>
<?php else: ?>

<table>
    <tr>
        <th>Employee</th>
        <th>Department</th>
        <th>Evaluation Period</th>
        <th>Overall Rating</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach ($records as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['department']) ?></td>
        <td><?= htmlspecialchars($row['evaluation_period']) ?></td>
        <td><?= number_format($row['overall_rating'], 2) ?></td>
        <td class="status-certified">Certified</td>
        <td>
            <a href="view_ipcrf.php?id=<?= $row['ipcrf_id'] ?>">
                View
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php endif; ?>

</body>
</html>

<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    header("Location: ../login.php");
    exit;
}
$title = 'Rate Employee';
$showBack = true;
require_once "../includes/header.php";

$stmt = $conn->query("
    SELECT DISTINCT
        u.user_id,
        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE
        i.core_status IN ('Pending','Returned')
        OR i.strategic_status IN ('Pending','Returned')
        OR i.support_status IN ('Pending','Returned')
    ORDER BY u.full_name
");


$employees = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Employee for Evaluation</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        table {
            width: 70%;
            margin: 40px auto;
            background: #fff;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            border: 1px solid #ddd;
        }
        th {
            background: #0b4dbb;
            color: #fff;
        }
        a {
            color: #0b4dbb;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Select Employee for Evaluation</h2>

<table>
    <tr>
        <th>Employee Name</th>
        <th>Department</th>
        <th>Action</th>
    </tr>

    <?php if (count($employees) === 0): ?>
        <tr>
            <td colspan="3" style="text-align:center;">No submitted IPCRFs found.</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($employees as $emp): ?>
        <tr>
            <td><?= htmlspecialchars($emp['full_name']) ?></td>
            <td><?= htmlspecialchars($emp['department']) ?></td>
                <td>
                    <a href="evaluations.php?user_id=<?= $emp['user_id'] ?>">
                        Review / Rate
                    </a>
                </td>
        </tr>
    <?php endforeach; ?>
</table>


</body>
</html>

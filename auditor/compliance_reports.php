<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Auditor') {
    header("Location: ../login.php");
    exit;
}

/* =========================
   FETCH CERTIFIED & HR-APPROVED IPCRFs
========================= */
$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.evaluation_period,
        i.status,
        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.status IN ('For HR', 'Certified')
    ORDER BY i.evaluation_period DESC
");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Compliance Reports</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; }
        .box {
            width:90%;
            margin:30px auto;
            background:#fff;
            padding:25px;
            border-radius:8px;
        }
        table {
            width:100%;
            border-collapse:collapse;
        }
        th, td {
            padding:12px;
            border:1px solid #ddd;
            text-align:center;
        }
        th {
            background:#0b4dbb;
            color:white;
        }
        .certified { color:green; font-weight:bold; }
        .forhr { color:blue; font-weight:bold; }
        a {
            font-weight:bold;
            color:#0b4dbb;
            text-decoration:none;
        }
    </style>
</head>
<body>

<div class="box">
    <a href="dashboard.php">← Back to Auditor Dashboard</a>
    <h2>Compliance Reports</h2>

    <?php if (empty($records)): ?>
        <p>No records available for reporting.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Period</th>
                <th>Status</th>
                <th>Audit Trail</th>
            </tr>

            <?php foreach ($records as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['evaluation_period']) ?></td>
                <td>
                    <?php if ($row['status'] === 'Certified'): ?>
                        <span class="certified">Certified</span>
                    <?php else: ?>
                        <span class="forhr">For HR Approval</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="audit_trail.php?id=<?= $row['ipcrf_id'] ?>">
                        View Audit Trail
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

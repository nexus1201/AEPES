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

$title = 'Pending Submissions';
$showBack = true;
require_once "../includes/header.php";

/* =========================
   FETCH IPCRFs WITH PENDING OR RETURNED CATEGORIES
========================= */
$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.user_id,
        i.evaluation_period,

        i.core_status,
        i.strategic_status,
        i.support_status,

        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE
        i.core_status IN ('Pending','Returned')
        OR i.strategic_status IN ('Pending','Returned')
        OR i.support_status IN ('Pending','Returned')
    ORDER BY i.ipcrf_id DESC
");
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   STATUS BADGE HELPER
========================= */
function badge($status) {
    if ($status === 'Reviewed') {
        return '<span style="color:green;font-weight:bold;">Reviewed</span>';
    }
    if ($status === 'Returned') {
        return '<span style="color:red;font-weight:bold;">Returned</span>';
    }
    return '<span style="color:orange;font-weight:bold;">Pending</span>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending IPCRF Submissions</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            background:#fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background:#0b4dbb;
            color:#fff;
        }
        a.btn {
            display:inline-block;
            padding:6px 10px;
            background:#0b4dbb;
            color:#fff;
            text-decoration:none;
            border-radius:4px;
            font-size:13px;
        }
        a.disabled {
            color:#888;
            pointer-events:none;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Pending IPCRF Submissions</h2>

<?php if (!$records): ?>
    <p style="text-align:center;">No pending or returned IPCRFs.</p>
<?php else: ?>

<table>
    <tr>
        <th>Employee</th>
        <th>Department</th>
        <th>Evaluation Period</th>

        <th>Core</th>
        <th>Strategic</th>
        <th>Support</th>
    </tr>

    <?php foreach ($records as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['evaluation_period']) ?></td>

            <!-- CORE -->
            <td>
                <?= badge($row['core_status']) ?><br>
            <?php if ($row['core_status'] === 'Pending'): ?>
                <a class="btn"
                href="evaluate_ipcrf.php?id=<?= $row['ipcrf_id'] ?>&category=Core&user_id=<?= $row['user_id'] ?>">
                    Evaluate
                </a>
            <?php endif; ?>
            </td>
            <!-- STRATEGIC -->
            <td>
                <?= badge($row['strategic_status']) ?><br>
            <?php if ($row['strategic_status'] === 'Pending'): ?>
                <a class="btn"
                href="evaluate_ipcrf.php?id=<?= $row['ipcrf_id'] ?>&category=Strategic&user_id=<?= $row['user_id'] ?>">
                    Evaluate
                </a>
            <?php endif; ?>
            </td>
            <!-- SUPPORT -->
            <td>
                <?= badge($row['support_status']) ?><br>
            <?php if ($row['support_status'] === 'Pending'): ?>
                <a class="btn"
                href="evaluate_ipcrf.php?id=<?= $row['ipcrf_id'] ?>&category=Support&user_id=<?= $row['user_id'] ?>">
                    Evaluate
                </a>
            <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php endif; ?>

</body>
</html>

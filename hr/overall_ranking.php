<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}

$title = 'Overall Ranking';
$showBack = true;
require_once "../includes/header.php";

$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.evaluation_period,
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
    ORDER BY overall_rating DESC, u.full_name ASC, i.ipcrf_id DESC
");
$stmt->execute();
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Certified IPCRF Overall Ranking</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }
        .container {
            padding: 25px;
        }
        .panel {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .panel-header {
            padding: 22px 24px 12px;
        }
        .panel-header h2 {
            margin: 0 0 8px;
            color: #0b4dbb;
        }
        .panel-header p {
            margin: 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 16px;
            border-top: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background: #0b4dbb;
            color: #fff;
            border-top: 0;
        }
        td.rank,
        td.score {
            text-align: center;
            white-space: nowrap;
        }
        td.score strong {
            color: #0b4dbb;
        }
        .view-link {
            color: #0b4dbb;
            font-weight: bold;
            text-decoration: none;
        }
        .empty-state {
            padding: 24px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="panel">
        <div class="panel-header">
            <h2>Certified IPCRF Overall Ranking</h2>
            <p>All certified IPCRFs ranked by overall average.</p>
        </div>

        <?php if (empty($rankings)): ?>
            <p class="empty-state">No certified IPCRFs available for ranking yet.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Rank</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Evaluation Period</th>
                    <th>Overall Average</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($rankings as $index => $row): ?>
                    <tr>
                        <td class="rank"><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['evaluation_period']) ?></td>
                        <td class="score"><strong><?= number_format($row['overall_rating'], 2) ?></strong></td>
                        <td>
                            <a class="view-link" href="certified_ipcrf.php?id=<?= $row['ipcrf_id'] ?>" target="_blank">
                                View Certified Output
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
</body>
</html>

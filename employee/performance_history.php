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
$title = 'Performance History';
$showBack = true;
require_once "../includes/header.php";
/* =========================
   FETCH ALL IPCRF RECORDS
========================= */
$stmt = $conn->prepare("
    SELECT
        ipcrf_id,
        evaluation_period,
        status,
        core_rating,
        strategic_rating,
        support_rating,

        ROUND(
            (IFNULL(core_rating,0) + IFNULL(strategic_rating,0) + IFNULL(support_rating,0)) /
            NULLIF(
                (core_rating IS NOT NULL) +
                (strategic_rating IS NOT NULL) +
                (support_rating IS NOT NULL),
                0
            ), 2
        ) AS overall_rating
    FROM ipcrf
    WHERE user_id = ?
    ORDER BY ipcrf_id DESC
");
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Performance History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 14px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #0b4dbb;
            color: #ffffff;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #0b4dbb;
        }
        a {
            color: #0b4dbb;
            font-weight: bold;
            text-decoration: none;
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
        .status-reviewed {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>


<h2>My Performance History</h2>

<?php if (count($records) === 0): ?>
    <p style="text-align:center;">No performance records found.</p>
<?php else: ?>

<table>
    <tr>
        <th>Evaluation Period</th>
        <th>Core</th>
        <th>Strategic</th>
        <th>Support</th>
        <th>Overall</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach ($records as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['evaluation_period']) ?></td>

        <td><?= $row['core_rating'] !== null ? number_format($row['core_rating'], 2) : '—' ?></td>
        <td><?= $row['strategic_rating'] !== null ? number_format($row['strategic_rating'], 2) : '—' ?></td>
        <td><?= $row['support_rating'] !== null ? number_format($row['support_rating'], 2) : '—' ?></td>
        <td>
            <?= in_array($row['status'], ['Reviewed','For Audit','For HR','Certified'])
                ? number_format($row['overall_rating'], 2)
                : '—'
            ?>
        </td>

        <td>
            <?php
            switch ($row['status']) {
                case 'Certified':
                    echo '<span style="color:green;font-weight:bold;">Certified</span>';
                    break;

                case 'For HR':
                    echo '<span style="color:blue;font-weight:bold;">For HR Approval</span>';
                    break;

                case 'For Audit':
                    echo '<span style="color:orange;font-weight:bold;">For Audit</span>';
                    break;

                case 'Reviewed':
                    echo '<span class="status-reviewed">Reviewed</span>';
                    break;

                case 'Submitted':
                    echo '<span class="status-pending">Submitted</span>';
                    break;

                default:
                    echo '<span class="status-pending">Pending</span>';
            }
            ?>
        </td>

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

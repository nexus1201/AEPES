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
   FETCH AUDIT STATUS COUNTS
========================= */
$statusStmt = $conn->prepare("
    SELECT
        SUM(CASE WHEN status = 'For Audit' THEN 1 ELSE 0 END) AS pending_review,
        SUM(CASE WHEN status IN ('For HR', 'For HR Review', 'Certified') THEN 1 ELSE 0 END) AS approved_ipcrf
    FROM ipcrf
");
$statusStmt->execute();
$statusCounts = $statusStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$chartLabels = ['Pending Review', 'Approved IPCRF'];
$chartCounts = [
    (int) ($statusCounts['pending_review'] ?? 0),
    (int) ($statusCounts['approved_ipcrf'] ?? 0),
];
$chartColors = ['#FFC300', '#27AE60'];

$title = 'Auditor Dashboard';
$showBack = false;
include '../includes/header.php';

/* =========================
   FETCH IPCRFs FOR AUDIT
========================= */
$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.evaluation_period,
        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.status = 'For Audit'
    ORDER BY i.ipcrf_id DESC
");
$stmt->execute();
$ipcrfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Internal Auditor Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }
        .container {
            padding: 25px;
        }
        .chart-container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin: 0 auto 30px;
            max-width: 500px;
        }
        .chart-container h3 {
            margin-top: 0;
            color: #0b4dbb;
        }
        .chart-wrap {
            position: relative;
            height: 320px;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .card {
            background: #fff;
            border-left: 6px solid #0b4dbb;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h3 {
            margin-top: 0;
            color: #0b4dbb;
        }
        .ipcrf-item {
            margin-bottom: 12px;
        }
        .review-link {
            display: inline-block;
            margin-top: 5px;
            text-decoration: none;
            font-weight: bold;
            color: #0b4dbb;
        }
    </style>
</head>
<body>

<div class="container">

    <p>Welcome, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p>

    <div class="cards">

        <!-- PENDING AUDIT -->
        <div class="card">
            <h3>Pending Evaluations for Audit (<?= count($ipcrfs) ?>)</h3>

            <?php if (empty($ipcrfs)): ?>
                <p>No IPCRFs pending audit.</p>
            <?php else: ?>
                <?php foreach ($ipcrfs as $row): ?>
                    <div class="ipcrf-item">
                        <strong><?= htmlspecialchars($row['full_name']) ?></strong><br>
                        Department: <?= htmlspecialchars($row['department']) ?><br>
                        Period: <?= htmlspecialchars($row['evaluation_period']) ?><br>

                        <a class="review-link"
                           href="review_ipcrf.php?id=<?= $row['ipcrf_id'] ?>">
                            Review IPCRF →
                        </a>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="chart-container">
        <h3>Audit Review Overview</h3>
        <div class="chart-wrap">
            <canvas id="auditStatusChart"></canvas>
        </div>
    </div>
</div>
<?php require_once "../includes/footer.php"; ?>
<script>
    const auditStatusCtx = document.getElementById('auditStatusChart').getContext('2d');
    new Chart(auditStatusCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                data: <?= json_encode($chartCounts) ?>,
                backgroundColor: <?= json_encode($chartColors) ?>,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 14 },
                        padding: 15
                    }
                }
            }
        }
    });
</script>
</body>
</html>

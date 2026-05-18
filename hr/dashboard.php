<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}
/* =========================
   FETCH IPCRF STATUS COUNTS
========================= */
$statusQuery = "
    SELECT status, COUNT(*) as count
    FROM ipcrf
    GROUP BY status
    ORDER BY status ASC
";
$statusStmt = $conn->prepare($statusQuery);
$statusStmt->execute();
$statusData = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for chart
$statuses = [];
$counts = [];
$bgColors = [];
$colors = [
    'Open' => '#FFC300',
    'Submitted' => '#3498DB',
    'For HR' => '#E74C3C',
    'Certified' => '#27AE60',
    'Closed' => '#95A5A6'
];

foreach ($statusData as $row) {
    $statuses[] = $row['status'];
    $counts[] = $row['count'];
    $bgColors[] = $colors[$row['status']] ?? '#999999';
}

$topPerformersStmt = $conn->prepare("
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
    LIMIT 5
");
$topPerformersStmt->execute();
$topPerformers = $topPerformersStmt->fetchAll(PDO::FETCH_ASSOC);

$title = 'HR Dashboard';
$showBack = false;
include '../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>HR Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .container {
            padding: 25px;
        }
        .welcome {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .chart-container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .chart-section h2 {
            margin-top: 0;
            color: #0b4dbb;
        }
        .overview-grid {
            display: grid;
            grid-template-columns: minmax(320px, 500px) minmax(320px, 1fr);
            gap: 24px;
            align-items: start;
            margin-top: 30px;
        }
        .ranking-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .ranking-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 10px;
            background: #f6f9ff;
            border: 1px solid #dbe6ff;
        }
        .ranking-position {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0b4dbb;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        .ranking-info {
            flex: 1;
            min-width: 0;
        }
        .ranking-name {
            margin: 0 0 4px;
            color: #0b4dbb;
            font-weight: bold;
        }
        .ranking-meta {
            margin: 0;
            color: #555;
            font-size: 14px;
            line-height: 1.45;
        }
        .ranking-score {
            text-align: right;
            flex-shrink: 0;
        }
        .ranking-score strong {
            display: block;
            font-size: 22px;
            color: #0b4dbb;
        }
        .ranking-score span {
            color: #666;
            font-size: 13px;
        }
        .ranking-footer {
            margin-top: 18px;
        }
        .ranking-btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            background: #0b4dbb;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        .ranking-btn:hover {
            background: #083a8c;
        }
        .empty-state {
            margin: 0;
            color: #555;
        }
        @media (max-width: 900px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            background: #fff;
            border-left: 6px solid #0b4dbb;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin-top: 0;
            color: #0b4dbb;
        }
        .card p {
            margin: 10px 0;
            color: #555;
        }
        .card a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #0b4dbb;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="welcome">
        Welcome, <strong><?php echo $_SESSION['name']; ?></strong>
    </div>

    <div class="cards">
    <div class="card">
        <h3>Create User</h3>
        <p>Create employee, supervisor, auditor, or HR accounts.</p>
        <a href="create_employee.php">Add User</a>
    </div>
    <div class="card">
        <h3>IPCRF Access Control</h3>
        <p>Open or close IPCRF form for employees.</p>
        <a href="manage_ipcrf.php">Manage IPCRF</a>
    </div>
    <div class="card">
        <h3>Pending HR Approvals</h3>
        <p>Review and approve evaluations forwarded by Auditor.</p>
        <a href="pending_hr.php">Review IPCRFs</a>
    </div>
    <div class="card">
        <h3>Certified IPCRFs</h3>
        <p>View and print certified evaluations.</p>
        <a href="certified_list.php">View Certified IPCRFs</a>
    </div>
    <div class="card">
        <h3>Activity Log</h3>
        <p>View full approval history per employee.</p>
        <a href="audit_employees.php">View Activity Log</a>
    </div>
</div>

    <div class="chart-section overview-grid">
        <div class="chart-container">
            <h2>IPCRF Status Overview</h2>
            <canvas id="statusChart"></canvas>
        </div>

        <div class="chart-container">
            <h2>Certified Top Performers</h2>

            <?php if (empty($topPerformers)): ?>
                <p class="empty-state">No certified IPCRFs available for ranking yet.</p>
            <?php else: ?>
                <div class="ranking-list">
                    <?php foreach ($topPerformers as $index => $row): ?>
                        <div class="ranking-item">
                            <span class="ranking-position"><?= $index + 1 ?></span>

                            <div class="ranking-info">
                                <p class="ranking-name"><?= htmlspecialchars($row['full_name']) ?></p>
                                <p class="ranking-meta">
                                    <?= htmlspecialchars($row['department']) ?><br>
                                    Period: <?= htmlspecialchars($row['evaluation_period']) ?>
                                </p>
                            </div>

                            <div class="ranking-score">
                                <strong><?= number_format($row['overall_rating'], 2) ?></strong>
                                <span>Overall Average</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="ranking-footer">
                    <a class="ranking-btn" href="overall_ranking.php">View Full Ranking</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php require_once "../includes/footer.php"; ?>

<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($statuses); ?>,
            datasets: [{
                data: <?php echo json_encode($counts); ?>,
                backgroundColor: <?php echo json_encode($bgColors); ?>,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
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

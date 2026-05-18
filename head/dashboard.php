<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    header("Location: ../login.php");
    exit;
}
/* =========================
   FETCH IPCRF STATUS COUNTS
========================= */
$statusQuery = "
    SELECT 
        'Pending' as status, 
        (SELECT COUNT(*) FROM ipcrf WHERE (core_status = 'Pending' OR strategic_status = 'Pending' OR support_status = 'Pending') AND status != 'Closed') as count
    UNION ALL
    SELECT 
        'Reviewed' as status,
        (SELECT COUNT(*) FROM ipcrf WHERE (core_status = 'Reviewed' OR strategic_status = 'Reviewed' OR support_status = 'Reviewed') AND status != 'Closed') as count
    UNION ALL
    SELECT 
        'Returned' as status,
        (SELECT COUNT(*) FROM ipcrf WHERE (core_status = 'Returned' OR strategic_status = 'Returned' OR support_status = 'Returned') AND status != 'Closed') as count
";
$statusStmt = $conn->prepare($statusQuery);
$statusStmt->execute();
$statusData = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for chart
$statuses = [];
$counts = [];
$bgColors = [];
$colors = [
    'Pending' => '#FFC300',
    'Reviewed' => '#27AE60',
    'Returned' => '#E74C3C'
];

foreach ($statusData as $row) {
    $statuses[] = $row['status'];
    $counts[] = $row['count'];
    $bgColors[] = $colors[$row['status']] ?? '#999999';
}

$title = 'Department Head Dashboard';
$showBack = false;
include '../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Department Head Dashboard</title>
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
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .chart-section h2 {
            margin-top: 0;
            color: #0b4dbb;
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
            <h3>Rate Employee</h3>
            <p>Evaluate employee performance and assign ratings.</p>
            <a href="employees.php">Rate Employee</a>

        </div>

        <div class="card">
            <h3>Pending Submissions</h3>
            <p>View IPCRF submissions awaiting your review.</p>
            <a href="pending_submissions.php">View Pending</a>
            
        </div>

        <div class="card">
            <h3>Performance Summary</h3>
            <p>View performance overview of your department.</p>
            <a href="performance_summary.php">View Summary</a>
        </div>
        <div class="card">
            <h3>Employee Performance</h3>
            <p>View evaluation status of all employees.</p>
            <a href="employee_performance_summary.php">View Employees</a>
        </div>

    </div>

    <div class="chart-section">
        <div class="chart-container">
            <h2>IPCRF Form Status Overview</h2>
            <canvas id="statusChart"></canvas>
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

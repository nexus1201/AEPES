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
$title = 'Performance Summary';
$showBack = true;
require_once "../includes/header.php";

/* =========================
   SUMMARY COUNTS
========================= */

// Total IPCRFs
$totalStmt = $conn->query("SELECT COUNT(*) FROM ipcrf");
$totalIPCRF = $totalStmt->fetchColumn();

// Fully reviewed (all categories reviewed)
$reviewedStmt = $conn->query("
    SELECT COUNT(*) FROM ipcrf
    WHERE core_status = 'Reviewed'
      AND strategic_status = 'Reviewed'
      AND support_status = 'Reviewed'
");
$reviewedIPCRF = $reviewedStmt->fetchColumn();

// Returned (any category returned)
$returnedStmt = $conn->query("
    SELECT COUNT(*) FROM ipcrf
    WHERE core_status = 'Returned'
       OR strategic_status = 'Returned'
       OR support_status = 'Returned'
");
$returnedIPCRF = $returnedStmt->fetchColumn();

// Pending (any category pending)
$pendingStmt = $conn->query("
    SELECT COUNT(*) FROM ipcrf
    WHERE core_status = 'Pending'
       OR strategic_status = 'Pending'
       OR support_status = 'Pending'
");
$pendingIPCRF = $pendingStmt->fetchColumn();

/* =========================
   AVERAGE RATINGS
========================= */
$avgStmt = $conn->query("
    SELECT
        ROUND(AVG(core_rating), 2) AS avg_core,
        ROUND(AVG(strategic_rating), 2) AS avg_strategic,
        ROUND(AVG(support_rating), 2) AS avg_support
    FROM ipcrf
");
$avg = $avgStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Performance Summary</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        .container {
            width: 90%;
            margin: 30px auto;
        }
        h2 {
            color:#0b4dbb;
        }
        .cards {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:20px;
            margin-bottom:30px;
        }
        .card {
            background:#fff;
            padding:20px;
            border-radius:8px;
            box-shadow:0 4px 10px rgba(0,0,0,.1);
            border-left:6px solid #0b4dbb;
        }
        .value {
            font-size:28px;
            font-weight:bold;
            margin-top:10px;
        }
        table {
            width:100%;
            border-collapse:collapse;
            background:#fff;
        }
        th, td {
            padding:12px;
            border:1px solid #ddd;
            text-align:center;
        }
        th {
            background:#0b4dbb;
            color:#fff;
        }
        a {
            display:inline-block;
            margin-top:20px;
            text-decoration:none;
            font-weight:bold;
            color:#0b4dbb;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- SUMMARY CARDS -->
    <div class="cards">
        <div class="card">
            <h3>Total IPCRFs</h3>
            <div class="value"><?= $totalIPCRF ?></div>
        </div>

        <div class="card">
            <h3>Fully Reviewed</h3>
            <div class="value" style="color:green;">
                <?= $reviewedIPCRF ?>
            </div>
        </div>

        <div class="card">
            <h3>Returned</h3>
            <div class="value" style="color:red;">
                <?= $returnedIPCRF ?>
            </div>
        </div>

        <div class="card">
            <h3>Pending</h3>
            <div class="value" style="color:orange;">
                <?= $pendingIPCRF ?>
            </div>
        </div>
    </div>

    <!-- AVERAGE RATINGS -->
    <h3>Average Ratings</h3>

    <table>
        <tr>
            <th>Category</th>
            <th>Average Rating</th>
        </tr>
        <tr>
            <td>Core</td>
            <td><?= $avg['avg_core'] ?? '—' ?></td>
        </tr>
        <tr>
            <td>Strategic</td>
            <td><?= $avg['avg_strategic'] ?? '—' ?></td>
        </tr>
        <tr>
            <td>Support</td>
            <td><?= $avg['avg_support'] ?? '—' ?></td>
        </tr>
    </table>

</div>

</body>
</html>

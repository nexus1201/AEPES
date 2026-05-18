<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    die("Unauthorized");
}

$ipcrf_id = (int)($_GET['id'] ?? 0);
if (!$ipcrf_id) die("Invalid IPCRF.");

$stmt = $conn->prepare("
    SELECT
        i.*,
        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.ipcrf_id = ?
");
$stmt->execute([$ipcrf_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) die("Record not found.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Certified IPCRF</title>
    <style>
        body {
            font-family: Arial;
            padding: 40px;
        }
        h2, h3 {
            text-align: center;
        }
        table {
            width:100%;
            border-collapse: collapse;
            margin-top:20px;
        }
        td, th {
            border:1px solid #000;
            padding:10px;
        }
        .sign {
            margin-top:60px;
            text-align:right;
        }
    </style>
</head>
<body onload="window.print()">

<h2>Automated Employee Performance Evaluation System</h2>
<h3>OFFICIAL PERFORMANCE CERTIFICATION</h3>

<p><b>Employee:</b> <?= htmlspecialchars($data['full_name']) ?></p>
<p><b>Department:</b> <?= htmlspecialchars($data['department']) ?></p>
<p><b>Evaluation Period:</b> <?= htmlspecialchars($data['evaluation_period']) ?></p>

<table>
<tr><th>Category</th><th>Rating</th></tr>
<tr><td>Core</td><td><?= $data['core_rating'] ?></td></tr>
<tr><td>Strategic</td><td><?= $data['strategic_rating'] ?></td></tr>
<tr><td>Support</td><td><?= $data['support_rating'] ?></td></tr>
</table>

<p><b>Status:</b> CERTIFIED BY HR</p>

<div class="sign">
    ___________________________<br>
    HR Officer Signature<br>
    Date: <?= date("F d, Y") ?>
</div>

</body>
</html>

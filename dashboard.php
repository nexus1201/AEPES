<?php
// AEPES Starter Backend (index.php)
// Thesis: Developing an Automated Employee Performance Evaluation System (AEPES)
// Stack: PHP 8+, MySQL, HTML/CSS

session_start();

// Database connection (configurable)
$host = 'localhost';
$db   = 'aepes_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed");
}

// -------- WEIGHTED SCORING MODULE --------
function calculateFinalScore(array $objectives): float {
    // $objectives = [ ['rating'=>5, 'weight'=>0.5], ... ]
    $total = 0.0;
    foreach ($objectives as $obj) {
        $total += ($obj['rating'] * $obj['weight']);
    }
    return round($total, 3); // CSC requirement: 3 decimal places
}

// Example usage (test case)
$sampleObjectives = [
    ['rating' => 5, 'weight' => 0.50], // Core
    ['rating' => 4, 'weight' => 0.30], // Strategic
    ['rating' => 4, 'weight' => 0.20]  // Support
];

$finalScore = calculateFinalScore($sampleObjectives);

// -------- SIMPLE ROLE CHECK --------
function requireRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('Access denied');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AEPES – Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Automated Employee Performance Evaluation System</h1>
    <p><strong>Sample Final Score:</strong> <?php echo $finalScore; ?></p>
</body>
</html>
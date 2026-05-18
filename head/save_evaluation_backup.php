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

$evaluator_id = (int) $_SESSION['user_id'];

/* =========================
   BASIC VALIDATION
========================= */
if (
    !isset(
        $_POST['ipcrf_id'],
        $_POST['user_id'],
        $_POST['category'],
        $_POST['q1'],
        $_POST['qn2'],
        $_POST['t3'],
        $_POST['a4']
    )
) {
    die("Invalid request.");
}

/* =========================
   FETCH POST DATA
========================= */
$ipcrf_id = (int) $_POST['ipcrf_id'];
$user_id  = (int) $_POST['user_id'];
$category = $_POST['category'];

$q1  = (float) $_POST['q1'];
$qn2 = (float) $_POST['qn2'];
$t3  = (float) $_POST['t3'];
$a4  = (float) $_POST['a4'];

/* =========================
   COMPUTE AVERAGE (2 DECIMALS)
========================= */
$avg = round(($q1 + $qn2 + $t3 + $a4) / 4, 2);

if ($category === 'Core') {

    $sql = "
        UPDATE ipcrf SET
            core_q1 = ?, core_qn2 = ?, core_t3 = ?, core_a4 = ?,
            core_rating = ?, evaluated_by = ?, evaluated_at = NOW(), status = 'Reviewed'
        WHERE ipcrf_id = ?
    ";
    $params = [$q1, $qn2, $t3, $a4, $avg, $ipcrf_id];

} elseif ($category === 'Strategic') {

    $sql = "
        UPDATE ipcrf SET
            strategic_q1 = ?, strategic_qn2 = ?, strategic_t3 = ?, strategic_a4 = ?,
            strategic_rating = ?, evaluated_by = ?, evaluated_at = NOW(), status = 'Reviewed', 
        WHERE ipcrf_id = ?
    ";
    $params = [$q1, $qn2, $t3, $a4, $avg, $ipcrf_id];

} else {

    $sql = "
        UPDATE ipcrf SET
            support_q1 = ?, support_qn2 = ?, support_t3 = ?, support_a4 = ?,
            support_rating = ?, evaluated_by = ?, evaluated_at = NOW(), status = 'Reviewed'
        WHERE ipcrf_id = ?
    ";
    $params = [$q1, $qn2, $t3, $a4, $avg, $ipcrf_id];
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);


/* =========================
   CHECK IF ALL CATEGORIES ARE RATED
========================= */
$check = $conn->prepare("
    SELECT core_rating, strategic_rating, support_rating
    FROM ipcrf
    WHERE ipcrf_id = ?
");
$check->execute([$ipcrf_id]);
$row = $check->fetch();

/* =========================
   MARK AS REVIEWED ONLY IF COMPLETE
========================= */
if (
    $row &&
    $row['core_rating'] !== null &&
    $row['strategic_rating'] !== null &&
    $row['support_rating'] !== null
) {
    $overall = round(
        ($row['core_rating'] + $row['strategic_rating'] + $row['support_rating']) / 3,
        2
    );

    $update = $conn->prepare("
        UPDATE ipcrf
        SET overall_rating = ?, status = 'Reviewed'
        WHERE ipcrf_id = ?
    ");
    $update->execute([$overall, $ipcrf_id]);
}


/* =========================
   REDIRECT BACK TO EMPLOYEE CONTEXT
========================= */
header("Location: evaluations.php?user_id=" . $user_id);
exit;

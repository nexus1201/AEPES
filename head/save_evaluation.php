<?php
session_start();
require_once "../config/database.php";
require_once "../includes/audit_helper.php";


/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Supervisor') {
    header("Location: ../login.php");
    exit;
}

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
        $_POST['a4'],
        $_POST['action']
    )
) {
    die("Invalid request.");
}

/* =========================
   FETCH DATA
========================= */
$ipcrf_id     = (int) $_POST['ipcrf_id'];
$statusStmt = $conn->prepare("
    SELECT status FROM ipcrf WHERE ipcrf_id = ?
");
$statusStmt->execute([$ipcrf_id]);
$currentStatus = $statusStmt->fetchColumn();

if ($currentStatus === 'Approved') {
    die("This IPCRF is already HR-approved and locked.");
}
$user_id      = (int) $_POST['user_id'];
$category     = $_POST['category'];
$evaluator_id = (int) $_SESSION['user_id'];

$q1  = (float) $_POST['q1'];
$qn2 = (float) $_POST['qn2'];
$t3  = (float) $_POST['t3'];
$a4  = (float) $_POST['a4'];

$remarks = trim($_POST['remarks'] ?? '');
$action  = $_POST['action'];

/* =========================
   COMPUTE AVERAGE
========================= */
$avg = round(($q1 + $qn2 + $t3 + $a4) / 4, 2);

/* =========================
   SWITCH CATEGORY
========================= */
switch ($category) {

    case 'Core':

        if ($action === 'approve') {
            $sql = "
                UPDATE ipcrf SET
                    core_q1 = ?,
                    core_qn2 = ?,
                    core_t3 = ?,
                    core_a4 = ?,
                    core_rating = ?,
                    core_remarks = NULL,
                    core_status = 'Reviewed',
                    evaluated_by = ?,
                    evaluated_at = NOW()
                WHERE ipcrf_id = ?
            ";
            $params = [$q1, $qn2, $t3, $a4, $avg, $evaluator_id, $ipcrf_id];
            logAudit(
        $conn,
        $ipcrf_id,
        "Supervisor approved $category",
        $remarks
);


        } else {
            $sql = "
                UPDATE ipcrf SET
                    core_remarks = ?,
                    core_status = 'Returned'
                WHERE ipcrf_id = ?
            ";
            $params = [$remarks, $ipcrf_id];
            logAudit(
        $conn,
        $ipcrf_id,
        "Supervisor returned $category",
        $remarks
);

        }
        break;

    case 'Strategic':

        if ($action === 'approve') {
            $sql = "
                UPDATE ipcrf SET
                    strategic_q1 = ?,
                    strategic_qn2 = ?,
                    strategic_t3 = ?,
                    strategic_a4 = ?,
                    strategic_rating = ?,
                    strategic_remarks = NULL,
                    strategic_status = 'Reviewed',
                    evaluated_by = ?,
                    evaluated_at = NOW()
                WHERE ipcrf_id = ?
            ";
            $params = [$q1, $qn2, $t3, $a4, $avg, $evaluator_id, $ipcrf_id];
            logAudit(
        $conn,
        $ipcrf_id,
        "Supervisor approved $category",
        $remarks
);


        } else {
            $sql = "
                UPDATE ipcrf SET
                    strategic_remarks = ?,
                    strategic_status = 'Returned'
                WHERE ipcrf_id = ?
            ";
            $params = [$remarks, $ipcrf_id];
            logAudit(
        $conn,
        $ipcrf_id,
        "Supervisor returned $category",
        $remarks
    );

        }
        break;

    case 'Support':

        if ($action === 'approve') {
            $sql = "
                UPDATE ipcrf SET
                    support_q1 = ?,
                    support_qn2 = ?,
                    support_t3 = ?,
                    support_a4 = ?,
                    support_rating = ?,
                    support_remarks = NULL,
                    support_status = 'Reviewed',
                    evaluated_by = ?,
                    evaluated_at = NOW()
                WHERE ipcrf_id = ?
            ";
            $params = [$q1, $qn2, $t3, $a4, $avg, $evaluator_id, $ipcrf_id];
            logAudit(
        $conn,
        $ipcrf_id,
        "Supervisor approved $category",
        $remarks
    );


        } else {
            $sql = "
                UPDATE ipcrf SET
                    support_remarks = ?,
                    support_status = 'Returned'
                WHERE ipcrf_id = ?
            ";
            $params = [$remarks, $ipcrf_id];
            logAudit(
        $conn,
        $ipcrf_id,
        "Supervisor returned $category",
        $remarks
    );

        }
        break;

    default:
        die("Invalid category.");
}

/* =========================
   EXECUTE
========================= */
$stmt = $conn->prepare($sql);
$stmt->execute($params);
/* =========================
   UPDATE OVERALL IPCRF STATUS
========================= */
$check = $conn->prepare("
    SELECT
        core_status,
        strategic_status,
        support_status
    FROM ipcrf
    WHERE ipcrf_id = ?
");
$check->execute([$ipcrf_id]);
$statusRow = $check->fetch(PDO::FETCH_ASSOC);

if (
    $statusRow['core_status'] === 'Reviewed' &&
    $statusRow['strategic_status'] === 'Reviewed' &&
    $statusRow['support_status'] === 'Reviewed'
) {
    // All categories approved by Supervisor
    $conn->prepare("
        UPDATE ipcrf
        SET status = 'Reviewed'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);

} elseif (
    $statusRow['core_status'] === 'Returned' ||
    $statusRow['strategic_status'] === 'Returned' ||
    $statusRow['support_status'] === 'Returned'
) {
    // At least one category returned → reopen for employee
    $conn->prepare("
        UPDATE ipcrf
        SET status = 'Open'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);

} else {
    // Still under review
    $conn->prepare("
        UPDATE ipcrf
        SET status = 'Submitted'
        WHERE ipcrf_id = ?
    ")->execute([$ipcrf_id]);
}



/* =========================
   REDIRECT
========================= */
header("Location: evaluations.php?user_id=" . $user_id);
exit;

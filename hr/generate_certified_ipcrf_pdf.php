<?php
session_start();
require_once "../config/database.php";
require_once __DIR__ . '/../vendor/autoload.php';

use Mpdf\Mpdf;

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['HR', 'Employee'])) {
    die("Unauthorized");
}

$ipcrf_id = (int) ($_GET['id'] ?? 0);
if (!$ipcrf_id) {
    die("Invalid IPCRF ID");
}
$sessionUserId = (int) ($_SESSION['user_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT
        i.ipcrf_id,
        i.evaluation_period,
        i.status,
        i.core_rating,
        i.core_remarks,
        i.strategic_rating,
        i.strategic_remarks,
        i.support_rating,
        i.support_remarks,
        u.full_name,
        u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.ipcrf_id = ?
      AND i.status = 'Certified'
      AND (? = 'HR' OR i.user_id = ?)
");
$stmt->execute([$ipcrf_id, $_SESSION['role'], $sessionUserId]);
$ipcrf = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ipcrf) {
    die("IPCRF not certified.");
}

$actorsStmt = $conn->prepare("
    SELECT
        a.actor_role,
        u.full_name
    FROM audit_trail a
    JOIN users u ON a.actor_id = u.user_id
    WHERE a.ipcrf_id = ?
    ORDER BY a.created_at ASC
");
$actorsStmt->execute([$ipcrf_id]);
$actors = $actorsStmt->fetchAll(PDO::FETCH_ASSOC);

$supervisor = '-';
$hr = '-';
$ratingRemarks = [
    'Core' => $ipcrf['core_remarks'] ?? '',
    'Strategic' => $ipcrf['strategic_remarks'] ?? '',
    'Support' => $ipcrf['support_remarks'] ?? '',
];

foreach ($actors as $actor) {
    if ($actor['actor_role'] === 'Supervisor') {
        $supervisor = $actor['full_name'];
    }

    if ($actor['actor_role'] === 'HR') {
        $hr = $actor['full_name'];
    }
}

$remarksStmt = $conn->prepare("
    SELECT action, remarks
    FROM audit_trail
    WHERE ipcrf_id = ?
      AND actor_role = 'Supervisor'
      AND action LIKE 'Supervisor approved %'
      AND remarks IS NOT NULL
      AND remarks <> ''
    ORDER BY created_at ASC
");
$remarksStmt->execute([$ipcrf_id]);

foreach ($remarksStmt->fetchAll(PDO::FETCH_ASSOC) as $log) {
    foreach (array_keys($ratingRemarks) as $category) {
        if (stripos($log['action'], $category) !== false && trim((string) $ratingRemarks[$category]) === '') {
            $ratingRemarks[$category] = $log['remarks'];
        }
    }
}

function h($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function displayValue($value) {
    $value = trim((string) ($value ?? ''));
    return $value !== '' ? h($value) : '-';
}

function displayRemarks($value) {
    $value = trim((string) ($value ?? ''));
    return $value !== '' ? nl2br(h($value)) : 'No remarks provided.';
}

function ipcrfDecision($avg) {
    if ($avg >= 4.8) {
        return 'Employee is recommended for promotion';
    }
    if ($avg >= 4.0) {
        return 'Employee is recommended for leadership training';
    }
    if ($avg >= 3.5) {
        return 'Employee will have a performance base salary increase';
    }
    if ($avg >= 3.0) {
        return 'Employee is good for contract renewal';
    }
    return 'Employee will undergo developmental training';
}

$core = (float) $ipcrf['core_rating'];
$strategic = (float) $ipcrf['strategic_rating'];
$support = (float) $ipcrf['support_rating'];
$average = round(($core + $strategic + $support) / 3, 2);
$decision = ipcrfDecision($average);

$certifiedDate = date('F d, Y');
$safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $ipcrf['full_name']);
$safePeriod = preg_replace('/[^A-Za-z0-9_-]+/', '_', $ipcrf['evaluation_period']);

$html = '
<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #111;
}
.header {
    text-align: center;
    margin-bottom: 18px;
}
.header h2 {
    font-size: 16px;
    margin: 0 0 4px;
    text-transform: uppercase;
}
.header p {
    margin: 0;
    font-size: 11px;
}
.section-title {
    background: #e9eef7;
    border: 1px solid #444;
    font-weight: bold;
    padding: 6px 8px;
    margin-top: 14px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
td, th {
    border: 1px solid #444;
    padding: 7px 8px;
    vertical-align: top;
}
th {
    background: #f2f2f2;
    font-weight: bold;
    text-align: left;
}
.label {
    width: 28%;
    font-weight: bold;
}
.rating {
    width: 16%;
    text-align: center;
    font-weight: bold;
}
.certification {
    margin-top: 14px;
    text-align: justify;
    line-height: 1.5;
}
.signature-table td {
    height: 58px;
    text-align: center;
    vertical-align: bottom;
}
.small {
    font-size: 10px;
}
</style>

<div class="header">
    <h2>Certification for Evaluation</h2>
    <p>Individual Performance Commitment and Review Form (IPCRF)</p>
</div>

<div class="section-title">Employee Information</div>
<table>
    <tr>
        <td class="label">Employee Name</td>
        <td>'.displayValue($ipcrf['full_name']).'</td>
    </tr>
    <tr>
        <td class="label">Department</td>
        <td>'.displayValue($ipcrf['department']).'</td>
    </tr>
    <tr>
        <td class="label">Evaluation Period</td>
        <td>'.displayValue($ipcrf['evaluation_period']).'</td>
    </tr>
    <tr>
        <td class="label">Supervisor Name</td>
        <td>'.displayValue($supervisor).'</td>
    </tr>
    <tr>
        <td class="label">HR Certifier Name</td>
        <td>'.displayValue($hr).'</td>
    </tr>
</table>

<div class="section-title">Final Evaluation Ratings</div>
<table>
    <tr>
        <th>Rating Area</th>
        <th class="rating">Rating</th>
        <th>Remarks</th>
    </tr>
    <tr>
        <td>Core Rating</td>
        <td class="rating">'.displayValue($ipcrf['core_rating']).'</td>
        <td>'.displayRemarks($ratingRemarks['Core']).'</td>
    </tr>
    <tr>
        <td>Strategic Rating</td>
        <td class="rating">'.displayValue($ipcrf['strategic_rating']).'</td>
        <td>'.displayRemarks($ratingRemarks['Strategic']).'</td>
    </tr>
    <tr>
        <td>Support Rating</td>
        <td class="rating">'.displayValue($ipcrf['support_rating']).'</td>
        <td>'.displayRemarks($ratingRemarks['Support']).'</td>
    </tr>
    <tr>
        <td><b>Average Rating</b></td>
        <td class="rating">'.h(number_format($average, 2)).'</td>
        <td><b>Recommendation:</b> '.h($decision).'</td>
    </tr>
</table>

<div class="section-title">Certification</div>
<p class="certification">
    This certifies that <b>'.displayValue($ipcrf['full_name']).'</b> of the
    <b>'.displayValue($ipcrf['department']).'</b> has completed the certified IPCRF
    evaluation for the period <b>'.displayValue($ipcrf['evaluation_period']).'</b>.
    The final average rating is <b>'.h(number_format($average, 2)).'</b>, with the
    recommendation: <b>'.h($decision).'</b>.
</p>

<br>
<table class="signature-table">
    <tr>
        <td>
            <b>'.displayValue($supervisor).'</b><br>
            <span class="small">Supervisor</span>
        </td>
        <td>
            <b>'.displayValue($hr).'</b><br>
            <span class="small">HR Certifier</span>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="small">Generated on '.h($certifiedDate).'</td>
    </tr>
</table>
';

$mpdf = new Mpdf([
    'format' => 'A4',
    'margin_left' => 14,
    'margin_right' => 14,
    'margin_top' => 12,
    'margin_bottom' => 12,
]);

$mpdf->WriteHTML($html);
$mpdf->Output("Certified_IPCRF_{$safeName}_{$safePeriod}.pdf", "I");
exit;

<?php
ini_set('zlib.output_compression', 'Off');
ini_set('output_buffering', 'Off');
while (ob_get_level()) {
    ob_end_clean();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../config/database.php";

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../vendor/autoload.php';
$phpWord = new PhpWord();


function clean($text) {
    return trim((string)$text);
}





if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Employee') {
    die("Unauthorized");
}

$user_id = (int) $_SESSION['user_id'];

/* =========================
   FETCH IPCRF (CERTIFIED ONLY)
========================= */
$stmt = $conn->prepare("
    SELECT i.*, u.full_name, u.department
    FROM ipcrf i
    JOIN users u ON i.user_id = u.user_id
    WHERE i.user_id = ?
    ORDER BY i.ipcrf_id DESC
    LIMIT 1
");
$stmt->execute([$user_id]);
$ipcrf = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ipcrf || $ipcrf['status'] !== 'Certified') {
    die("IPCRF not certified.");
}

/* =========================
   FETCH OBJECTIVES
========================= */
$objStmt = $conn->prepare("
    SELECT *
    FROM objectives
    WHERE ipcrf_id = ?
    ORDER BY FIELD(category,'Strategic','Core','Support')
");
$objStmt->execute([$ipcrf['ipcrf_id']]);
$objectives = $objStmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   INIT WORD
========================= */
$phpWord = new \PhpOffice\PhpWord\PhpWord();
$section = $phpWord->addSection([
    'orientation' => 'landscape',
    'paperSize'   => 'A4',
    'marginLeft'  => 800,
    'marginRight' => 800,
    'marginTop'   => 800,
    'marginBottom'=> 800,
]);

/* =========================
   TITLE
========================= */
$section->addText(
    'Individual Performance Commitment and Review (IPCRF)',
    ['bold' => true, 'size' => 14],
    ['alignment' => 'center']
);

$section->addText(
    '(For Employees)',
    ['italic' => true, 'size' => 10],
    ['alignment' => 'center']
);

$section->addTextBreak(1);

/* =========================
   INTRO PARAGRAPH
========================= */
$section->addText(
    "I, " . clean($ipcrf['full_name']) .
    ", of the " . clean($ipcrf['department']) .
    ", commit to deliver and agree to be rated on the attainment of the following targets in accordance with the indicated measures for the period of (" .
    clean($ipcrf['evaluation_period']) . ").",
    ['size' => 10]
);

$section->addTextBreak(1);

/* =========================
   MAIN TABLE HEADER
========================= */
$phpWord->addTableStyle(
    'InfoTable',
    ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]
);

$phpWord->addTableStyle(
    'MainTable',
    ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]
);
/* =========================
   HEADER INFO TABLE
========================= */
$info = $section->addTable('InfoTable');
$info->addRow();
$info->addCell(4000)->addText("Name: {$ipcrf['full_name']}");
$info->addCell(4000)->addText("Position: {$ipcrf['department']}");
$evalDate = !empty($ipcrf['evaluated_at'])
    ? date('m/d/Y', strtotime($ipcrf['evaluated_at']))
    : '';
$info->addCell(4000)->addText("Date: $evalDate");





$table = $section->addTable('MainTable');

$table->addRow();
$table->addCell(3000)->addText('Output', ['bold'=>true]);
$table->addCell(3000)->addText('Success Indicator', ['bold'=>true]);
$table->addCell(3000)->addText('Actual Accomplishment', ['bold'=>true]);
$table->addCell(800)->addText('Q1', ['bold'=>true]);
$table->addCell(800)->addText('Qn2', ['bold'=>true]);
$table->addCell(800)->addText('T3', ['bold'=>true]);
$table->addCell(800)->addText('A4', ['bold'=>true]);
$table->addCell(2500)->addText('Remarks', ['bold'=>true]);

/* =========================
   FILL DATA
========================= */
foreach ($objectives as $o) {

    $prefix = strtolower($o['category']);

    $table->addRow();
    $table->addCell()->addText(clean($o['output']));
    $table->addCell()->addText(clean($o['success_indicator']));
    $table->addCell()->addText(clean($o['actual_accomplishment']));


    $table->addCell()->addText(clean($ipcrf["{$prefix}_q1"] ?? ''));
    $table->addCell()->addText(clean($ipcrf["{$prefix}_qn2"] ?? ''));
    $table->addCell()->addText(clean($ipcrf["{$prefix}_t3"] ?? ''));
    $table->addCell()->addText(clean($ipcrf["{$prefix}_a4"] ?? ''));
    $table->addCell()->addText(clean($ipcrf["{$prefix}_remarks"] ?? ''));

}

/* =========================
   NEW PAGE (PAGE 2)
========================= */
$section->addPageBreak();
$section->addText('— End of IPCRF —', ['italic'=>true], ['alignment'=>'center']);

if (ob_get_length()) {
    ob_end_clean();
}

header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Cache-Control: max-age=0");

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save("php://output认为");
exit;


<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}

$title = 'Manage IPCRF';
$showBack = true;
require_once "../includes/header.php";

// Fetch employees
$stmt = $conn->prepare("
    SELECT 
        u.user_id,
        u.full_name,
        COALESCE(i.status, 'Closed') AS ipcrf_status
    FROM users u
    LEFT JOIN ipcrf i
        ON i.user_id = u.user_id
        AND i.ipcrf_id = (
            SELECT ipcrf_id
            FROM ipcrf
            WHERE user_id = u.user_id
            ORDER BY ipcrf_id DESC
            LIMIT 1
        )
    WHERE u.role = 'Employee'
    ORDER BY u.full_name
");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.container {
    max-width: 900px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 8px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}
th {
    background: #0b4dbb;
    color: #fff;
}
.btn-open {
    background: #006400;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
}
.btn-close {
    background: #b30000;
    color: #fff;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
}
.status-open { color: green; font-weight: bold; }
.status-closed { color: red; font-weight: bold; }
</style>

<div class="container">
    <h2>IPCRF Access Control</h2>

    <table>
        <tr>
            <th>Employee</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($employees as $emp): ?>
        <tr>
            <td><?= htmlspecialchars($emp['full_name']) ?></td>
            <td class="<?= $emp['ipcrf_status'] === 'Open' ? 'status-open' : 'status-closed' ?>">
                <?= htmlspecialchars($emp['ipcrf_status']) ?>
            </td>
            <td>
                <?php if ($emp['ipcrf_status'] !== 'Open'): ?>
                    <form method="POST" action="open_ipcrf.php" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $emp['user_id'] ?>">
                        <button class="btn-open">🔓 Open</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="close_ipcrf.php" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $emp['user_id'] ?>">
                        <button class="btn-close">🔒 Close</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once "../includes/footer.php"; ?>

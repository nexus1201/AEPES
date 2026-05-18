<?php
session_start();
require_once "../config/database.php";

/* =========================
   SECURITY CHECK (HR ONLY)
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../login.php");
    exit;
}
$title = 'Create Employee';
$showBack = true;
require_once "../includes/header.php";

$message = "";
$isSuccess = false;

/* =========================
   HANDLE FORM SUBMISSION
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $employee_no = trim($_POST['employee_no'] ?? '');
    $full_name   = trim($_POST['full_name'] ?? '');
    $department  = trim($_POST['department'] ?? '');
    $username    = trim($_POST['username'] ?? '');
    $password    = $_POST['password'] ?? '';
    $role        = $_POST['role'] ?? '';

    $allowed_roles = ['Employee', 'Supervisor', 'Auditor', 'HR'];

    if (
        $employee_no === '' ||
        $full_name === '' ||
        $username === '' ||
        $password === '' ||
        $role === ''
    ) {
        $message = "All required fields must be filled out.";
    } elseif (!in_array($role, $allowed_roles)) {
        $message = "Invalid role selected.";
    } else {

        // Check username uniqueness
        $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check->execute([$username]);

        if ($check->fetch()) {
            $message = "Username already exists.";
        } else {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users
                (employee_no, full_name, role, department, username, password_hash, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Active')
            ");

            $stmt->execute([
                $employee_no,
                $full_name,
                $role,
                $department,
                $username,
                $password_hash
            ]);

            $message = "User account created successfully!";
            $isSuccess = true;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin: 0;
            padding: 0;}
        .box {
            width: 440px;
            margin: 40px auto;
            background:#fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 { color:#0b4dbb; margin-top:0; }
        label { font-weight:bold; display:block; margin-top:10px; }
        input, select {
            width:100%;
            padding:8px;
            margin-top:5px;
        }
        button {
            margin-top:15px;
            width:100%;
            padding:10px;
            background:#0b4dbb;
            color:#fff;
            border:none;
            font-weight:bold;
            cursor:pointer;
        }
        .msg {
            margin-bottom:10px;
            font-weight:bold;
            color:green;
        }
        .error {
            margin-bottom:10px;
            font-weight:bold;
            color:red;
        }
        a {
            display:block;
            margin-top:15px;
            text-align:center;
            text-decoration:none;
            font-weight:bold;
            color:#0b4dbb;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Create User Account</h2>

    <?php if ($message): ?>
        <div class="<?= $isSuccess ? 'msg' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Employee No</label>
        <input type="text" name="employee_no" required>

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Role</label>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="Employee">Employee</option>
            <option value="Supervisor">Department Head</option>
            <option value="Auditor">Internal Auditor</option>
            <option value="HR">HR</option>
        </select>

        <label>Department</label>
        <input type="text" name="department">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Temporary Password</label>
        <input type="password" name="password" required>

        <button type="submit">Create User</button>
    </form>

</div>

</body>
</html>

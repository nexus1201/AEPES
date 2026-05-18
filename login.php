<?php
require_once "config/database.php";
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'Active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['full_name'];
        // ROLE-BASED REDIRECT
switch ($user['role']) {
    case 'HR':
        header("Location: hr/dashboard.php");
        break;

    case 'Auditor':
        header("Location: auditor/dashboard.php");
        break;

    case 'Supervisor': // Department Head
        header("Location: head/dashboard.php");
        break;

    case 'Employee':
        header("Location: employee/dashboard.php");
        break;

    default:
        header("Location: login.php");
        break;
}
exit;

    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AEPES Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0b4dbb, #ffd500);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #ffffff;
            width: 360px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .login-box img {
            width: 120px;
            margin-bottom: 15px;
        }
        .login-box h2 {
            margin-bottom: 20px;
            color: #0b4dbb;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .login-box button {
            width: 100%;
            padding: 10px;
            background: #0b4dbb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            box-sizing: border-box;
            display: block;
        }
        .login-box button:hover {
            background: #083a8c;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="assets/logo.png" alt="MMTVTC Logo">
        <h2>AEPES Login</h2>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="footer">
            Mandaluyong Manpower & Technical-Vocational Training Center<br>
            © 1972 – AEPES
        </div>
    </div>
</body>
</html>

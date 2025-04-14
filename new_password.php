<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_SESSION['reset_email'];

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_pass, $email);
    $stmt->execute();

    session_unset();
    session_destroy();

    echo "<script>alert('Password changed successfully.');window.location.href='login.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Ownix</title>
    <style>
        body {
            background: linear-gradient(to right, #1f1c2c, #928dab);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #2e2e3a;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            color: white;
            width: 350px;
            text-align: center;
        }

        .form-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            margin-top: 20px;
            background: #444;
            color: white;
            font-size: 16px;
        }

        .form-container button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, #00c9ff, #92fe9d);
            color: #000;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
        }

        .form-container h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Set New Password</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="Enter new password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>

<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $code = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $code;

        $subject = "Ownix Password Reset Code";
        $message = "Your password reset code is: <b>$code</b>";
        $to = $email;

        include 'send_mail.php';

        echo "<script>alert('Code sent to your email.');window.location.href='reset_code.php';</script>";
    } else {
        echo "<script>alert('Email not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ownix - Forgot Password</title>
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

        .form-container input[type="email"] {
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
        <h2>Forgot Password</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Code</button>
        </form>
    </div>
</body>
</html>

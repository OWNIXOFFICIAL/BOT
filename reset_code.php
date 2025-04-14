<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['code'];

    if ($entered_code == $_SESSION['reset_code']) {
        echo "<script>window.location.href='new_password.php';</script>";
    } else {
        echo "<script>alert('Invalid code');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Code - Ownix</title>
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

        .form-container input[type="text"] {
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
        <h2>Enter Verification Code</h2>
        <form method="POST">
            <input type="text" name="code" placeholder="Enter 6-digit code" required>
            <button type="submit">Verify Code</button>
        </form>
    </div>
</body>
</html>

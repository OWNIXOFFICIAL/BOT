<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login - Ownix</title>
  <style>
    body {
      margin: 0;
      background: linear-gradient(120deg, #0f0c29, #302b63, #24243e);
      font-family: 'Segoe UI', sans-serif;
      color: white;
    }
    .form-container {
      width: 400px;
      margin: 100px auto;
      padding: 30px;
      background: rgba(255,255,255,0.05);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 0 20px rgba(0,0,0,0.4);
    }
    h2 {
      text-align: center;
      color: #00f3ff;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      border: none;
      border-radius: 10px;
      background: rgba(255,255,255,0.1);
      color: #fff;
    }
    input::placeholder {
      color: #ccc;
    }
    button {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(90deg, #00c6ff, #0072ff);
      color: white;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      transform: scale(1.03);
    }
    .error {
      color: #ff4d4d;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>🔓 Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
<p style="text-align:center; margin-top:10px;">
  <a href="forgot_password.php" style="color:#00f3ff;">Forgot Password?</a>
</p>

    </form>
  </div>
</body>
</html>

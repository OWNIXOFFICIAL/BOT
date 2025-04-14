<?php
include 'db.php';

$ref_code = isset($_GET['ref']) ? $_GET['ref'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $referral_code = bin2hex(random_bytes(4));
    $referred_by = $_POST['referral'] ?? null;

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, password, referral_code, referred_by, tokens) VALUES (?, ?, ?, ?, 1000)");
    $stmt->bind_param("ssss", $username, $password, $referral_code, $referred_by);
    $stmt->execute();
    $user_id = $stmt->insert_id;

    // Airdrop bonus
    $conn->query("INSERT INTO bonuses (user_id, type, amount) VALUES ($user_id, 'Airdrop Bonus', 1000)");

    // Referral bonus
    if ($referred_by) {
        $refStmt = $conn->prepare("SELECT id FROM users WHERE referral_code = ?");
        $refStmt->bind_param("s", $referred_by);
        $refStmt->execute();
        $refResult = $refStmt->get_result();
        if ($refUser = $refResult->fetch_assoc()) {
            $ref_id = $refUser['id'];
            $conn->query("INSERT INTO bonuses (user_id, type, amount) VALUES ($ref_id, 'Referral Bonus', 100)");
            $conn->query("UPDATE users SET tokens = tokens + 100 WHERE id = $ref_id");
        }
    }

    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register - Ownix</title>
  <style>
    body {
      margin: 0;
      background: linear-gradient(120deg, #141e30, #243b55);
      font-family: 'Segoe UI', sans-serif;
      color: white;
    }
    .form-container {
      width: 400px;
      margin: 80px auto;
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
  </style>
</head>
<body>
  <div class="form-container">
    <h2>🔐 Create Account</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="text" name="referral" placeholder="Referral Code (Optional)" value="<?php echo htmlspecialchars($ref_code); ?>" />
      <button type="submit">Register</button>
    </form>
  </div>
</body>
</html>

<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT username, tokens, referral_code FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$username = $user['username'];
$tokens = $user['tokens'];
$referral_code = $user['referral_code'];

// Get referrals count
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE referred_by = ?");
$stmt->bind_param("s", $referral_code);
$stmt->execute();
$referralData = $stmt->get_result()->fetch_assoc();
$referral_count = $referralData['total'];
$referral_bonus = $referral_count * 100;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Ownix Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(120deg, #0f2027, #203a43, #2c5364);
      color: #fff;
    }
    .container {
      max-width: 900px;
      margin: 40px auto;
      padding: 30px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 0 30px rgba(0,0,0,0.3);
    }
    h2, h3 {
      text-align: center;
      color: #00f3ff;
    }
    .balance {
      font-size: 24px;
      margin: 20px 0;
      text-align: center;
    }
    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 40px;
      background: linear-gradient(90deg, #00c6ff, #0072ff);
      color: white;
      font-size: 16px;
      cursor: pointer;
      margin: 10px;
      box-shadow: 0 4px 20px rgba(0,198,255,0.4);
      transition: transform 0.2s ease;
    }
    .btn:hover {
      transform: scale(1.05);
    }
    .actions {
      text-align: center;
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
      background: rgba(255, 255, 255, 0.03);
    }
    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    th {
      color: #00f3ff;
      font-weight: 600;
    }
    td {
      color: #e0f7fa;
    }
    .info-box {
      background: rgba(0, 198, 255, 0.08);
      padding: 15px 20px;
      border-radius: 12px;
      margin: 10px 0;
      box-shadow: 0 2px 10px rgba(0, 198, 255, 0.2);
    }
    code {
      background: #111;
      padding: 6px 10px;
      border-radius: 6px;
      color: #0ff;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>👋 Welcome, <?php echo htmlspecialchars($username); ?></h2>
    <div class="balance">💰 Your Balance: <strong><?php echo $tokens; ?> ONX</strong></div>

    <div class="actions">
      <a href="send.php"><button class="btn">🔁 Send</button></a>
      <a href="receive.php"><button class="btn">📥 Receive</button></a>
    </div>

    <div class="info-box">
      👥 Total Referrals: <strong><?php echo $referral_count; ?></strong><br>
      🎁 Referral Bonus Earned: <strong><?php echo $referral_bonus; ?> ONX</strong>
    </div>

    <h3>🎁 Bonus History</h3>
    <table>
      <tr>
        <th>Bonus Type</th>
        <th>Amount</th>
        <th>Date</th>
      </tr>
      <?php
      $stmt = $conn->prepare("SELECT * FROM bonuses WHERE user_id = ? ORDER BY created_at DESC");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $bonuses = $stmt->get_result();
      while ($row = $bonuses->fetch_assoc()) {
        echo "<tr>
          <td>{$row['type']}</td>
          <td>{$row['amount']} ONX</td>
          <td>{$row['created_at']}</td>
        </tr>";
      }
      ?>
    </table>

    <h2 style="margin-top: 40px;">📢 Referral Panel</h2>
    <div class="info-box">
      <p>🔗 Your Referral Link:</p>
      <code>http://localhost/ownix/register.php?ref=<?php echo $referral_code; ?></code><br><br>
      <p>🆔 Your Referral Code: <strong><?php echo $referral_code; ?></strong></p>
    </div>

    <h3>🤝 Referred Users</h3>
    <table>
      <tr>
        <th>Username</th>
        <th>Join Date</th>
      </tr>
      <?php
      $stmt = $conn->prepare("SELECT username, created_at FROM users WHERE referred_by = ?");
      $stmt->bind_param("s", $referral_code);
      $stmt->execute();
      $referrals = $stmt->get_result();
      while ($row = $referrals->fetch_assoc()) {
        echo "<tr>
          <td>" . htmlspecialchars($row['username']) . "</td>
          <td>" . $row['created_at'] . "</td>
        </tr>";
      }

      if ($referrals->num_rows == 0) {
        echo "<tr><td colspan='2'>No users referred yet.</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>

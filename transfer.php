<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("⛔ Unauthorized");
}

$sender_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_email = trim($_POST['recipient_email']);
    $amount = (int)$_POST['amount'];

    if ($amount <= 0) {
        die("❌ Invalid token amount.");
    }

    // Check if recipient exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $recipient_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        die("❌ Recipient not found.");
    }

    $recipient = $result->fetch_assoc();
    $recipient_id = $recipient['id'];

    if ($recipient_id == $sender_id) {
        die("❌ You can't send tokens to yourself.");
    }

    // Get sender's token balance
    $stmt = $conn->prepare("SELECT tokens FROM users WHERE id = ?");
    $stmt->bind_param("i", $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sender = $result->fetch_assoc();

    if ($sender['tokens'] < $amount) {
        die("❌ You don't have enough tokens.");
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Deduct from sender
        $stmt = $conn->prepare("UPDATE users SET tokens = tokens - ? WHERE id = ?");
        $stmt->bind_param("ii", $amount, $sender_id);
        $stmt->execute();

        // Add to recipient
        $stmt = $conn->prepare("UPDATE users SET tokens = tokens + ? WHERE id = ?");
        $stmt->bind_param("ii", $amount, $recipient_id);
        $stmt->execute();

        $conn->commit();
        echo "✅ Tokens sent successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        echo "❌ Transfer failed. Try again.";
    }
} else {
    echo "⛔ Invalid request.";
}
?>
<br><br>
<a href="dashboard.php">🔙 Back to Dashboard</a>

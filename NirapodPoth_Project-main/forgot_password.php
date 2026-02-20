<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'nirapodpoth_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    // Check if email exists in users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        // TODO: Generate a reset token, save it, and email the user the reset link
        $message = "If this email exists in our system, a password reset link has been sent.";
    } else {
        $message = "If this email exists in our system, a password reset link has been sent.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Forgot Password | NirapodPoth</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #202F6A, #ffffff);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
  }
  .container {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0,0,0,0.2);
    width: 380px;
    text-align: center;
  }
  input[type="email"] {
    width: 100%;
    padding: 14px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    margin-bottom: 20px;
  }
  button {
    background-color: #202F6A;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    transition: background 0.3s ease;
  }
  button:hover {
    background-color: #172352;
  }
  .message {
    color: green;
    margin-bottom: 20px;
  }
  a {
    color: #202F6A;
    text-decoration: none;
  }
</style>
</head>
<body>

<div class="container">
  <h2>Forgot Password</h2>
  
  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  
  <form method="POST" action="forgot_password.php">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
  </form>
  
  <p><a href="login.php">Back to Login</a></p>
</div>

</body>
</html>

<?php
// register.php

$conn = new mysqli('localhost', 'root', '', 'nirapodpoth_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fullname = $email = $nid = $password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $nid = trim($_POST['nid']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($fullname)) { $errors[] = "Full name is required."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Valid email is required."; }
    if (empty($nid)) { $errors[] = "NID is required."; }
    if (strlen($password) < 6) { $errors[] = "Password must be at least 6 characters."; }
    if ($password !== $confirm_password) { $errors[] = "Passwords do not match."; }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR nid=?");
    $stmt->bind_param("ss", $email, $nid);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email or NID already registered.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, nid) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $nid);
        if ($stmt->execute()) {
            echo "✅ Registration successful. <a href='login.php'>Login here</a>";
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "❌ Could not register. Try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register | NirapodPoth</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      margin: 0;
      height: 100vh;
      background: linear-gradient(135deg, #202F6A, #ffffff);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(0,0,0,0.2);
      width: 400px;
      text-align: center;
      animation: slideFade 1s ease;
    }
    .header-title {
      font-size: 26px;
      font-weight: bold;
      color: #202F6A;
      margin-bottom: 5px;
    }
    .subtitle {
      font-size: 14px;
      color: #555;
      margin-bottom: 25px;
    }
    .error-list {
      text-align: left;
      color: red;
      margin-bottom: 20px;
      padding-left: 20px;
    }
    .form-group {
      position: relative;
      margin-bottom: 30px;
      text-align: left;
    }
    .form-group input {
      width: 100%;
      padding: 14px 12px 14px 40px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      background: transparent;
      outline: none;
    }
    .form-group label {
      position: absolute;
      left: 40px;
      top: 14px;
      font-size: 14px;
      color: #888;
      background-color: white;
      padding: 0 4px;
      transition: 0.2s ease;
      pointer-events: none;
    }
    .form-group input:focus + label,
    .form-group input:not(:placeholder-shown) + label {
      top: -8px;
      left: 32px;
      font-size: 11px;
      color: #202F6A;
    }
    .form-group .icon {
      position: absolute;
      left: 12px;
      top: 14px;
      width: 20px;
      height: 20px;
      opacity: 0.6;
    }
    button {
      background-color: #202F6A;
      color: white;
      border: none;
      width: 100%;
      padding: 12px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button:hover {
      background-color: #F39C12;
    }
    .link-text {
      margin-top: 20px;
      font-size: 14px;
    }
    .link-text a {
      color: #202F6A;
      text-decoration: none;
      border-bottom: 1px solid transparent;
      transition: border 0.3s ease;
    }
    .link-text a:hover {
      border-bottom: 1px solid #202F6A;
    }
    @keyframes slideFade {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-title">Register for NirapodPoth</div>
    <div class="subtitle">Create your secure account</div>

    <?php if (!empty($errors)): ?>
      <ul class="error-list">
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
      <div class="form-group">
        <input type="text" name="fullname" placeholder=" " value="<?php echo htmlspecialchars($fullname); ?>" required>
        <label>Full Name</label>
        <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" class="icon" alt="User Icon" />
      </div>

      <div class="form-group">
        <input type="email" name="email" placeholder=" " value="<?php echo htmlspecialchars($email); ?>" required>
        <label>Email Address</label>
        <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" class="icon" alt="Email Icon" />
      </div>

      <div class="form-group">
        <input type="text" name="nid" placeholder=" " value="<?php echo htmlspecialchars($nid); ?>" required>
        <label>NID Number</label>
        <img src="https://cdn-icons-png.flaticon.com/512/2919/2919592.png" class="icon" alt="ID Icon" />
      </div>

      <div class="form-group">
        <input type="password" name="password" placeholder=" " required>
        <label>Password</label>
        <img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" class="icon" alt="Lock Icon" />
      </div>

      <div class="form-group">
        <input type="password" name="confirm_password" placeholder=" " required>
        <label>Confirm Password</label>
        <img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" class="icon" alt="Lock Icon" />
      </div>

      <button type="submit">Register</button>
    </form>

    <div class="link-text">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>
</body>
</html>

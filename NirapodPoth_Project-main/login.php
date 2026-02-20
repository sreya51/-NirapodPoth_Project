<?php
session_start();

// Auto-login if session exists
if (isset($_SESSION["user_id"])) {
    if ($_SESSION['is_admin']) {
        header("Location: choose_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
}

// DB Connection
$conn = new mysqli('localhost', 'root', '', 'nirapodpoth_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $password = "";
$error = "";

// Login logic on POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $fullname, $hashed_password, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["fullname"] = $fullname;
            $_SESSION["is_admin"] = $is_admin;

            // Redirect based on admin or not
            if ($is_admin) {
                header("Location: choose_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "❌ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login | NirapodPoth</title>
  <style>
    
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
 body {
  margin: 0;
  height: 100vh;
  background: linear-gradient(to right, #f7f9fc, #dbefff);
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Segoe UI', sans-serif;
}

    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(0,0,0,0.2);
      width: 380px;
      text-align: center;
      animation: slideFade 1s ease;
    }
    .header-title {
      margin-bottom: 10px;
    }
    .header-title .welcome {
      font-weight: 600;
      font-size: 23px;
      color: #202F6A;
      display: block;
      margin-bottom: -5px;
    }
    .header-title .brand {
      font-weight: 700;
      font-size: 28px;
      color: #202F6A;
    }
    .subtitle {
      font-size: 12px;
      color: #555;
      margin-bottom: 25px;
      margin-top: -5px;
    }
    .form-group {
      position: relative;
      margin-bottom: 30px;
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
      top: 14px;
      left: 40px;
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
    .extras {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 20px;
    }
    .forgot-link a {
      font-size: 14px;
      color: #202F6A;
      text-decoration: none;
      border-bottom: 1px solid transparent;
      transition: 0.3s ease;
    }
    .forgot-link a:hover {
      border-bottom: 1px solid #202F6A;
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
    .error {
      color: red;
      margin-bottom: 10px;
    }
    @keyframes slideFade {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    
  </style>
</head>
<body>
  <div class="container">
    <div class="header-title">
      <span class="welcome">Welcome to</span>
      <span class="brand">NirapodPoth</span>
    </div>

    <div class="subtitle">Your Safety, Our Priority</div>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
      <div class="form-group">
        <input type="email" name="email" placeholder=" " required />
        <label>Email Address</label>
        <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" class="icon" alt="Email Icon" />
      </div>

      <div class="form-group">
        <input type="password" name="password" placeholder=" " required />
        <label>Password</label>
        <img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" class="icon" alt="Lock Icon" />
      </div>

      <div class="extras">
        <div class="forgot-link"><a href="forgot_password.php">Forgot Password?</a></div>
      </div>

      <button type="submit">Login</button>
    </form>

    <div class="link-text">
      Don't have an account? <a href="register.php">Register here</a>
    </div>
  </div>
</body>
</html>

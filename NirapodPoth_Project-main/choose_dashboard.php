<?php
session_start();

// Security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Choose Dashboard | NirapodPoth</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #202F6A, #ffffff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
        }
        .box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0,0,0,0.2);
            text-align: center;
            width: 320px;
        }
        h2 {
            margin-bottom: 25px;
            color: #202F6A;
        }

        /* Buttons with white bg and blue text */
        .btn-white {
            display: block;
            margin: 12px auto;
            padding: 12px 25px;
            background-color: #ffffff;
            color: #202F6A;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            width: fit-content;
            transition: all 0.3s ease;
            border: 1px solid #202F6A;
        }
        .btn-white:hover {
            background-color: #202F6A;
            color: #ffffff;
        }

        /* The "Go to User Dashboard" button style with reversed hover */
        .btn-blue-reverse {
            display: block;
            margin: 12px auto;
            padding: 12px 25px;
            background-color: #202F6A;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            width: fit-content;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        .btn-blue-reverse:hover {
            background-color: #F39C12;
            color: #ffffffff;
            border: 1px solid #F39C12;
        }

        /* Back button at top-left */
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 8px 14px;
            background-color: #202F6A;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: #F39C12;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-btn">← Back to Home</a>

    <div class="box">
        <h2>Welcome, Admin <?= htmlspecialchars($_SESSION['fullname']) ?>!</h2>
        <a class="btn-white" href="admin_dashboard.php">Go to Admin Dashboard</a>
        <a class="btn-blue-reverse" href="dashboard.php">Go to User Dashboard</a>
        <a class="btn-white" href="logout.php">Logout</a>
    </div>
</body>
</html>

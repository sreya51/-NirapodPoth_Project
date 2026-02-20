<?php
session_start();

// Ensure the user is an admin and logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'nirapodpoth_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get report ID from GET parameter and validate it
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$report_id = intval($_GET['id']);
$admin_id = $_SESSION['user_id'];

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason'] ?? '');

    if (empty($reason)) {
        $error = "Please enter a reason for flagging the report.";
    } else {
        // Use a transaction to ensure both queries succeed or fail together
        $conn->begin_transaction();
        
        try {
            // Step 1: Insert into flagged_reports table
            $stmt = $conn->prepare("INSERT INTO flagged_reports (report_id, admin_id, reason, flagged_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $report_id, $admin_id, $reason);
            $stmt->execute();
            $stmt->close();

            // Step 2: Update the 'flagged' column in the incidents table
            // This is the missing piece of code
            $stmt_update = $conn->prepare("UPDATE incidents SET flagged = 0 WHERE id = ?");
            $stmt_update->bind_param("i", $report_id);
            $stmt_update->execute();
            $stmt_update->close();

            // If both queries are successful, commit the transaction
            $conn->commit();
            
            // Redirect with success message
            header("Location: admin_dashboard.php?flag_success=1");
            exit;

        } catch (mysqli_sql_exception $exception) {
            // If any query fails, roll back the transaction and show an error
            $conn->rollback();
            $error = "Database error: Could not flag the report.";
            // You can also log the full error for debugging: error_log($exception->getMessage());
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Flag Report - NirapodPoth</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f5f7fa;
        padding: 40px;
    }
    .container {
        max-width: 480px;
        margin: auto;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
    h2 {
        color: #202F6A;
        margin-bottom: 20px;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }
    textarea {
        width: 100%;
        height: 120px;
        padding: 10px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
        resize: vertical;
        margin-bottom: 20px;
    }
    .btn {
        background: #202F6A;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
    }
    .btn:hover {
        background: #172352;
    }
    .error {
        color: red;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .back-link {
        display: inline-block;
        margin-top: 15px;
        text-decoration: none;
        color: #202F6A;
        font-weight: 600;
    }
    .back-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Flag Report</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
    <label for="reason">Reason for Flagging</label>
    <textarea id="reason" name="reason" placeholder="Write the reason here..." required><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>

    <button type="submit" class="btn">Submit Flag</button>
</form>


    <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>

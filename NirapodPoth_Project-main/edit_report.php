<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "nirapodpoth_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
$report_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $success = "";

// Fetch report to edit
$stmt = $conn->prepare("SELECT type, location_id, incident_time, description FROM incidents WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

$stmt->bind_result($type, $location_id, $incident_time, $description);
$stmt->fetch();
$stmt->close();

// Handle form submission to update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type"];
    $location_id = intval($_POST["location"]);  // Important: get ID, convert to int
    $incident_time = $_POST["incident_time"];
    $description = $_POST["description"];

    $update_stmt = $conn->prepare("UPDATE incidents SET type = ?, location_id = ?, incident_time = ?, description = ? WHERE id = ? AND user_id = ?");
    $update_stmt->bind_param("sissii", $type, $location_id, $incident_time, $description, $report_id, $user_id);

    if ($update_stmt->execute()) {
        $success = "✅ Report updated successfully.";
    } else {
        $error = "❌ Failed to update report.";
    }
    $update_stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Report | NirapodPoth</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #202F6A, #ffffff);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }
  .container {
    background: rgba(255,255,255,0.95);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
    width: 450px;
  }
  h2 {
    text-align: center;
    color: #202F6A;
    margin-bottom: 20px;
  }
  label {
    font-weight: bold;
    display: block;
    margin-bottom: 6px;
  }
  input, select, textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
  }
  textarea {
    height: 80px;
    resize: vertical;
  }
  button {
    width: 100%;
    background-color: #202F6A;
    color: white;
    border: none;
    padding: 12px;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
  }
  button:hover {
    background-color: #172352;
  }
  .success {
    color: green;
    margin-bottom: 10px;
    text-align: center;
  }
  .error {
    color: red;
    margin-bottom: 10px;
    text-align: center;
  }
  a.back-link {
    display: inline-block;
    margin-top: 10px;
    color: #202F6A;
    text-decoration: none;
    font-size: 14px;
  }
  a.back-link:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Edit Incident Report</h2>

    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="type">Incident Type:</label>
    <select name="type" required>
  <option value="">-- Select --</option>
    <option value="Eve Teasing" <?= ($type == 'Eve Teasing') ? 'selected' : '' ?>>Eve Teasing</option>
<option value="Harassment" <?= ($type == 'Harassment') ? 'selected' : '' ?>>Harassment</option>
  <option value="Kidnapping" <?= ($type == 'Kidnapping') ? 'selected' : '' ?>>Kidnapping</option>
  <option value="Road Accident" <?= ($type == 'Road Accident') ? 'selected' : '' ?>>Road Accident</option>
  <option value="Stalking" <?= ($type == 'Stalking') ? 'selected' : '' ?>>Stalking</option>
  <option value="Suspicious Activity" <?= ($type == 'Suspicious Activity') ? 'selected' : '' ?>>Suspicious Activity</option>
  <option value="Theft" <?= ($type == 'Theft') ? 'selected' : '' ?>>Theft</option>

</select>

      <label for="location">Location:</label>
      <select name="location" required>
  <option value="">-- Select Location --</option>
  <option value="1" <?= ($location_id == 1) ? 'selected' : '' ?>>Bondor Bazar</option>
  <option value="2" <?= ($location_id == 2) ? 'selected' : '' ?>>Sylhet Sadar</option>
  <option value="3" <?= ($location_id == 3) ? 'selected' : '' ?>>Ambarkhana</option>
  <option value="4" <?= ($location_id == 4) ? 'selected' : '' ?>>Mirabazar</option>
  <option value="5" <?= ($location_id == 5) ? 'selected' : '' ?>>Zindabazar</option>
  <option value="6" <?= ($location_id == 6) ? 'selected' : '' ?>>Chowhatta</option>
  <option value="7" <?= ($location_id == 7) ? 'selected' : '' ?>>Nayasarak</option>
  <option value="8" <?= ($location_id == 8) ? 'selected' : '' ?>>Subidbazar</option>
  <option value="9" <?= ($location_id == 9) ? 'selected' : '' ?>>Kodomtoli</option>
  <option value="10" <?= ($location_id == 10) ? 'selected' : '' ?>>Uposhohor</option>
  <option value="11" <?= ($location_id == 11) ? 'selected' : '' ?>>Pathantula</option>
  <option value="12" <?= ($location_id == 12) ? 'selected' : '' ?>>Majortila</option>
  <option value="13" <?= ($location_id == 13) ? 'selected' : '' ?>>Shibganj</option>
  <option value="14" <?= ($location_id == 14) ? 'selected' : '' ?>>Kumarpara</option>
  <option value="15" <?= ($location_id == 15) ? 'selected' : '' ?>>Modina Market</option>
</select>

      </select>

      <label for="incident_time">Time of Incident:</label>
      <input type="datetime-local" id="incident_time" name="incident_time" value="<?= date('Y-m-d\TH:i', strtotime($incident_time)) ?>" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" required><?= htmlspecialchars($description) ?></textarea>

      <button type="submit">Update Report</button>
    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
  </div>
</body>
</html>

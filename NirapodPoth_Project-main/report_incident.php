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

$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $anonymous   = isset($_POST["anonymous"]) && $_POST["anonymous"] === 'on';
    $user_id     = $anonymous ? null : $_SESSION["user_id"];
    $type_id     = isset($_POST["type_id"]) ? (int)$_POST["type_id"] : null;
    $location_id = isset($_POST["location_id"]) ? (int)$_POST["location_id"] : null;

    $incident_time     = $_POST["incident_time"];
    $description       = $_POST["description"];
    $safety_status     = isset($_POST['safety_status']) ? trim($_POST['safety_status']) : '';
    $feedback_response = $safety_status;

    
$flagged = 0;
    if ($safety_status === '') {
        $error = "❌ Please select your safety status response.";
    }

    $contact_number = null;
    if ($anonymous && $safety_status === "No, still in danger") {
        $contact_number = trim($_POST["contact_number"] ?? '');
    }

    // Validate location and type_id
    if (empty($error)) {
        if (empty($location_id)) {
            $error = "❌ Please select a valid location.";
        } elseif (empty($type_id)) {
            $error = "❌ Please select a valid incident type.";
        }
    }

    // Validate contact number if provided
    if (empty($error) && $contact_number && !preg_match('/^01[0-9]{9}$/', $contact_number)) {
        $error = "❌ Invalid Bangladeshi phone number format.";
    }

    if (empty($error)) {
        if ($anonymous) {
            $stmt = $conn->prepare("
                INSERT INTO incidents 
                (user_id, type_id, location_id, incident_time, description, safety_status, contact_number, flagged) 
                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "iissssi",
                $type_id, $location_id, $incident_time, $description, $safety_status, $contact_number, $flagged
            );
        } else {
            $stmt = $conn->prepare("
                INSERT INTO incidents 
                (user_id, type_id, location_id, incident_time, description, safety_status, contact_number, flagged) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "iiissssi",
                $user_id, $type_id, $location_id, $incident_time, $description, $safety_status, $contact_number, $flagged
            );
        }

        if ($stmt->execute()) {
            $incident_id = $stmt->insert_id;

            if ($incident_id > 0) {
                // Save feedback
                $stmt_feedback = $conn->prepare("INSERT INTO feedback (report_id, response) VALUES (?, ?)");
                $stmt_feedback->bind_param("is", $incident_id, $feedback_response);
                if (!$stmt_feedback->execute()) {
                    $error = "Failed to save feedback: " . $stmt_feedback->error;
                }
                $stmt_feedback->close();

                // Save flagged report if necessary
                if ($flagged) {
                    $flagged_by = $user_id ?? NULL;
                    $stmt_flag = $conn->prepare("
                        INSERT INTO flagged_reports (report_id, admin_id, flagged_at, reason)
                        VALUES (?, ?, NOW(), ?)
                    ");
                    $reason = "Reporter indicated they are still in danger";
                    $stmt_flag->bind_param("iis", $incident_id, $flagged_by, $reason);
                    if (!$stmt_flag->execute()) {
                        $error = "Failed to save flagged report: " . $stmt_flag->error;
                    }
                    $stmt_flag->close();
                }

                if (empty($error)) {
                    header("Location: dashboard.php?success=1");
                    exit;
                }
            } else {
                $error = "Failed to insert incident.";
            }
        } else {
            $error = "Failed to insert incident: " . $stmt->error;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Report Incident | NirapodPoth</title>
  <style>
    body {
      margin: 0;
      background: linear-gradient(135deg, #202F6A, #ffffff);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(0,0,0,0.2);
      width: 470px;
    }
    h2 {
      color: #202F6A;
      text-align: center;
      margin-bottom: 20px;
    }
    .form-group {
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
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    textarea {
      resize: vertical;
      height: 80px;
    }
    button {
      width: 100%;
      background-color: #202F6A;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button:hover {
      background-color: #172352;
    }
    .success {
      color: green;
      margin-bottom: 10px;
      text-align: center;
      font-weight: 600;
    }
    .error {
      color: red;
      margin-bottom: 10px;
      text-align: center;
      font-weight: 600;
    }

    .anonymous-toggle {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      user-select: none;
    }
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
      margin-right: 12px;
    }
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: #ccc;
      border-radius: 24px;
      transition: 0.4s;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      border-radius: 50%;
      transition: 0.4s;
    }
    .switch input:checked + .slider {
      background-color: #202F6A;
    }
    .switch input:checked + .slider:before {
      transform: translateX(26px);
    }
    .toggle-label {
      font-weight: 600;
      color: #202F6A;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Report an Incident</h2>

    <?php if (!empty($success)): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label>Incident Type:</label>
        <select name="type_id" required>
          <option value="">-- Select --</option>
          <?php
          $typesResult = $conn->query("SELECT type_id, type_name FROM incident_types ORDER BY type_name");
          while ($typeRow = $typesResult->fetch_assoc()) {
              $selected = (isset($_POST['type_id']) && $_POST['type_id'] == $typeRow['type_id']) ? 'selected' : '';
              echo "<option value='{$typeRow['type_id']}' $selected>" . htmlspecialchars($typeRow['type_name']) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label>Location:</label>
        <select name="location_id" required>
          <option value="">-- Select Location --</option>
          <?php
          $result = $conn->query("SELECT location_id, name FROM locations");
          while ($row = $result->fetch_assoc()) {
              $selected = (isset($_POST['location_id']) && $_POST['location_id'] == $row['location_id']) ? 'selected' : '';
              echo "<option value='{$row['location_id']}' $selected>" . htmlspecialchars($row['name']) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label>Time of Incident:</label>
        <input type="datetime-local" name="incident_time" required value="<?= htmlspecialchars($_POST['incident_time'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Description:</label>
        <textarea name="description" placeholder="Short description..." required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </div>

      <div class="form-group anonymous-toggle">
        <label class="switch">
          <input type="checkbox" name="anonymous" id="anonymous" <?= isset($_POST['anonymous']) ? 'checked' : '' ?>>
          <span class="slider"></span>
        </label>
        <label for="anonymous" class="toggle-label">Report Anonymously</label>
      </div>

      <div class="form-group">
        <label>Are you safe now?</label>
        <select name="safety_status" required>
          <option value="">-- Select --</option>
          <option value="Yes, I'm safe now" <?= (isset($_POST['safety_status']) && $_POST['safety_status'] === "Yes, I'm safe now") ? 'selected' : '' ?>>Yes, I'm safe now</option>
          <option value="No, still in danger" <?= (isset($_POST['safety_status']) && $_POST['safety_status'] === "No, still in danger") ? 'selected' : '' ?>>No, still in danger</option>
          <option value="Prefer not to say" <?= (isset($_POST['safety_status']) && $_POST['safety_status'] === "Prefer not to say") ? 'selected' : '' ?>>Prefer not to say</option>
        </select>
      </div>

      <a href="dashboard.php" style="
        display: inline-block;
        padding: 10px 16px;
        background-color: #ffffff;
        color: #202F6A;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s ease;
      " 
      onmouseover="this.style.backgroundColor='#202F6A'; this.style.color='#ffffff';" 
      onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#202F6A';">
        ← Back to Dashboard
      </a>

      <div class="form-group" id="contact_field" style="display: none;">
        <label>Phone Number (Optional for anonymous, required if unsafe):</label>
        <input type="text" name="contact_number" placeholder="01XXXXXXXXX" value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>">
      </div>

      <button type="submit">Submit Report</button>
    </form>
  </div>

  <script>
    const anon = document.getElementById("anonymous");
    const status = document.getElementsByName("safety_status")[0];
    const phone = document.getElementById("contact_field");

    function togglePhoneField() {
      if (anon.checked && status.value === "No, still in danger") {
        phone.style.display = "block";
      } else {
        phone.style.display = "none";
      }
    }

    anon.addEventListener("change", togglePhoneField);
    status.addEventListener("change", togglePhoneField);
    window.addEventListener("load", togglePhoneField);
  </script>
</body>
</html>
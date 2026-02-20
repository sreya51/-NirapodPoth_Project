<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'nirapodpoth_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle verify/unverify/delete/mark fake actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verify_id'])) {
        $id = intval($_POST['verify_id']);
        $new_status = ($_POST['current_status'] === 'Verified') ? 'Unverified' : 'Verified';
        $stmt = $conn->prepare("UPDATE incidents SET status=? WHERE id=?");
        $stmt->bind_param("si", $new_status, $id);
        $stmt->execute();
        $stmt->close();
    }
  if (isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    
    // First delete flagged reports for this incident
    $stmt = $conn->prepare("DELETE FROM flagged_reports WHERE report_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Then delete the incident
    $stmt = $conn->prepare("DELETE FROM incidents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}


    if (isset($_POST['mark_fake_id'])) {
        $id = intval($_POST['mark_fake_id']);
        $stmt = $conn->prepare("UPDATE incidents SET is_fake=1 WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_dashboard.php");
    exit;
}

// Handle filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$whereClause = '';
if ($filter === 'verified') {
    $whereClause = "WHERE i.status = 'Verified'";
} elseif ($filter === 'pending') {
    $whereClause = "WHERE i.status IS NULL OR i.status = 'Unverified'";
} elseif ($filter === 'fake') {
    $whereClause = "WHERE i.is_fake = 1";
}

// Fetch filtered incidents with incident type name
$incidents = [];
$sql = "SELECT 
  i.id, 
  u.fullname, 
  t.type_name AS type, 
  l.name AS location_name, 
  i.incident_time, 
  i.description, 
  i.safety_status, 
  i.contact_number, 
  i.status, 
  i.is_fake,
  CASE WHEN COUNT(fr.report_id) > 0 THEN 1 ELSE 0 END AS flagged
FROM incidents i 
LEFT JOIN users u ON i.user_id = u.id 
LEFT JOIN locations l ON i.location_id = l.location_id
LEFT JOIN incident_types t ON i.type_id = t.type_id
LEFT JOIN flagged_reports fr ON fr.report_id = i.id
$whereClause 
GROUP BY i.id, u.fullname, t.type_name, l.name, i.incident_time, i.description, i.safety_status, i.contact_number, i.status, i.is_fake
ORDER BY i.incident_time DESC

";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $incidents[] = $row;
    }
}

// Count total users
$user_count = 0;
$res_users = $conn->query("SELECT COUNT(*) as total FROM users");
if ($res_users) {
    $row = $res_users->fetch_assoc();
    $user_count = $row['total'];
}

// Fetch flagged reports
$flagged_reports = [];
$sql_flagged = "SELECT fr.flag_id, i.description, u.fullname AS admin_name, fr.reason, fr.flagged_at
FROM flagged_reports fr
JOIN incidents i ON fr.report_id = i.id
JOIN users u ON fr.admin_id = u.id
ORDER BY fr.flagged_at DESC";
$result_flagged = $conn->query($sql_flagged);
if ($result_flagged) {
    while ($row = $result_flagged->fetch_assoc()) {
        $flagged_reports[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard | NirapodPoth</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f7fa;
    margin: 0;
    padding: 0;
  }
  header {
    background: #202F6A;
    color: white;
    padding: 15px 30px;
    text-align: center;
    font-size: 24px;
    font-weight: 700;
  }
  .container {
    max-width: 1200px;
    margin: 25px auto;
    padding: 0 20px;
  }
  .stats {
    background: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .stats div {
    font-size: 18px;
    color: #202F6A;
    font-weight: 600;
  }
  .filter-bar {
    margin-bottom: 20px;
  }
  select {
    padding: 8px;
    font-size: 14px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
    margin-bottom: 40px;
  }
  th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    color: #333;
    vertical-align: middle;
    position: relative; /* needed for dropdown positioning */
  }
  th {
    background: #202F6A;
    color: white;
    font-weight: 600;
  }
  tr:hover {
    background: #f0f8ff;
  }
  .action-btn {
    background: #202F6A;
    color: white;
    border: none;
    padding: 7px 14px;
    margin-right: 8px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    transition: background 0.3s ease;
  }
  .action-btn:hover {
    background: #172352;
  }
  .delete-btn {
    background: #c0392b;
  }
  .delete-btn:hover {
    background: #922b21;
  }
  form.inline-form {
    display: inline-block;
    margin: 0;
  }
  .fake-flag {
    color: red;
    font-weight: bold;
  }

  /* Dropdown container */
  .dropdown {
    position: relative;
    display: inline-block;
  }

  /* Three dots button */
  .dropbtn {
    background: transparent;
    border: none;
    font-size: 22px;
    cursor: pointer;
    user-select: none;
    padding: 0 5px;
    color: #202F6A;
    font-weight: bold;
    line-height: 1;
    border-radius: 3px;
  }

  /* Dropdown menu */
  .dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 180px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    border-radius: 5px;
    top: 130%; /* a bit below the button */
    right: 0;
    z-index: 1000;
  }

  /* Show dropdown when active */
  .dropdown.show .dropdown-content {
    display: block;
  }

  /* Dropdown links and buttons */
  .dropdown-content a,
  .dropdown-content form button {
    padding: 10px 15px;
    display: block;
    text-decoration: none;
    color: black;
    background: none;
    border: none;
    text-align: left;
    width: 100%;
    cursor: pointer;
    font-size: 14px;
    box-sizing: border-box;
  }

  /* Hover effect */
  .dropdown-content a:hover,
  .dropdown-content form button:hover {
    background-color: #f1f1f1;
  }

  /* Prevent dropdown clipping */
  table, tbody, tr, td {
    overflow: visible !important;
  }
</style>
</head>
<body>

<header>Admin Dashboard - NirapodPoth</header>

<div style="max-width: 1200px; margin: 15px auto; padding: 0 20px;">
 <a href="choose_dashboard.php" style="
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
onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#202F6A';"
>
  ← Back to Choose Dashboard
</a>

</div>

<div class="container">

  <div class="stats">
    <div>Total Users: <?= htmlspecialchars($user_count) ?></div>
    <div>Total Reports: <?= count($incidents) ?></div>
  </div>

  <div class="filter-bar">
    <form method="GET" action="">
      <label for="filter">Filter Reports: </label>
      <select name="filter" id="filter" onchange="this.form.submit()">
        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
        <option value="verified" <?= $filter === 'verified' ? 'selected' : '' ?>>Verified</option>
        <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending/Unverified</option>
        <option value="fake" <?= $filter === 'fake' ? 'selected' : '' ?>>Fake</option>
      </select>
    </form>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Reported By</th>
        <th>Type</th>
        <th>Location</th>
        <th>Time</th>
        <th>Description</th>
        <th>Safety Status</th>
        <th>Contact Number</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($incidents)): ?>
        <tr><td colspan="10" style="text-align:center;">No incidents reported yet.</td></tr>
      <?php else: ?>
        <?php foreach ($incidents as $incident): ?>
          <tr>
            <td><?= htmlspecialchars($incident['id']) ?></td>
            <td><?= htmlspecialchars($incident['fullname'] ?? 'Anonymous') ?></td>
            <td><?= htmlspecialchars($incident['type']) ?></td>
            <td><?= htmlspecialchars($incident['location_name'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($incident['incident_time']))) ?></td>
            <td><?= htmlspecialchars($incident['description']) ?></td>
            <td><?= htmlspecialchars($incident['safety_status']) ?></td>
            <td>
              <?php 
                if (!empty($incident['contact_number']) && $_SESSION['is_admin'] == 1) {
                    echo htmlspecialchars($incident['contact_number']);
                } else {
                    echo '-';
                }
              ?>
            </td>
            <td><?= htmlspecialchars($incident['status'] ?? 'Unverified') ?></td>
            <td>
              <div class="dropdown" tabindex="0" onclick="toggleDropdown(this)">
                <button class="dropbtn" aria-label="More actions">⋮</button>
                <div class="dropdown-content" role="menu">
                  <!-- Verify form -->
                  <form method="POST" action="" onsubmit="return confirm('Are you sure you want to toggle verify status?');">
                    <input type="hidden" name="verify_id" value="<?= $incident['id'] ?>">
                    <input type="hidden" name="current_status" value="<?= htmlspecialchars($incident['status'] ?? 'Unverified') ?>">
                    <button type="submit"><?= ($incident['status'] ?? 'Unverified') === 'Verified' ? 'Unverify' : 'Verify' ?></button>
                  </form>

                  <!-- Mark as fake form -->
                  <?php if (!$incident['is_fake']): ?>
                    <form method="POST" action="" onsubmit="return confirm('Mark this report as fake?');">
                      <input type="hidden" name="mark_fake_id" value="<?= $incident['id'] ?>">
                      <button type="submit">🚫 Mark as Fake</button>
                    </form>
                  <?php else: ?>
                    <span style="color: red; font-weight: 600; padding: 8px 12px; display: block;">🚫 Marked as Fake</span>
                  <?php endif; ?>

                  <!-- Flag Report Link -->
                  <a href="flag.php?id=<?= $incident['id'] ?>">🚩 Flag Report</a>

                  <!-- Delete form -->
                  <form method="POST" action="" onsubmit="return confirm('Delete this report?');">
                    <input type="hidden" name="delete_id" value="<?= $incident['id'] ?>">
                    <button type="submit" class="delete-btn">🗑 Delete</button>
                  </form>
                </div>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- New Flagged Reports Table -->
  <h2>Flagged Reports</h2>
  <table>
    <thead>
      <tr>
        <th>Flag ID</th>
        <th>Incident Description</th>
        <th>Flagged By (Admin)</th>
        <th>Reason</th>
        <th>Flagged At</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($flagged_reports)): ?>
        <tr><td colspan="5" style="text-align:center;">No flagged reports.</td></tr>
      <?php else: ?>
        <?php foreach ($flagged_reports as $flag): ?>
          <tr>
            <td><?= htmlspecialchars($flag['flag_id']) ?></td>
            <td><?= htmlspecialchars($flag['description']) ?></td>
            <td><?= htmlspecialchars($flag['admin_name']) ?></td>
            <td><?= htmlspecialchars($flag['reason']) ?></td>
            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($flag['flagged_at']))) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

</div>

<script>
  function toggleDropdown(element) {
    // Close other open dropdowns first
    document.querySelectorAll('.dropdown').forEach(drop => {
      if (drop !== element) {
        drop.classList.remove('show');
      }
    });
    // Toggle this dropdown
    element.classList.toggle('show');
  }

  // Close dropdowns if clicked outside
  document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown')) {
      document.querySelectorAll('.dropdown').forEach(drop => drop.classList.remove('show'));
    }
  });
</script>

</body>
</html>

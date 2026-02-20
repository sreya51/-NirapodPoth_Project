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
$fullname = $_SESSION["fullname"] ?? 'User';

// Handle search filters
$locationFilter = $_GET['location'] ?? '';

$sql = "SELECT 
    incidents.id,
    incidents.user_id,
    users.fullname AS reporter_name,
    incident_types.type_name AS incident_type,
    incidents.incident_time,
    incidents.description,
    incidents.safety_status,
    incidents.created_at,
    incidents.contact_number,
    locations.name AS location_name,
    feedback.response AS feedback_response
FROM incidents
LEFT JOIN users ON incidents.user_id = users.id
LEFT JOIN locations ON incidents.location_id = locations.location_id
LEFT JOIN incident_types ON incidents.type_id = incident_types.type_id
LEFT JOIN feedback ON incidents.id = feedback.report_id
WHERE incidents.status = 'Verified' 
  AND incidents.is_fake = 0";

if (!empty($locationFilter)) {
    $locationFilterEscaped = $conn->real_escape_string($locationFilter);
    $sql .= " AND locations.name LIKE '%$locationFilterEscaped%'";
}

$sql .= " ORDER BY incidents.created_at DESC";
$result = $conn->query($sql);

// Top risky locations
$topLocationsSql = "
    SELECT l.name AS location_name, COUNT(i.id) AS total_reports
    FROM incidents i
    LEFT JOIN locations l ON i.location_id = l.location_id
    WHERE i.status = 'Verified' AND i.is_fake = 0
    GROUP BY l.location_id
    ORDER BY total_reports DESC
    LIMIT 3
";
$topLocationsResult = $conn->query($topLocationsSql);

// Flagged reports
$flaggedSql = "SELECT 
    incidents.id,
    incidents.user_id,
    users.fullname AS reporter_name,
    incident_types.type_name AS incident_type,
    incidents.incident_time,
    incidents.description,
    incidents.safety_status,
    incidents.created_at,
    incidents.contact_number,
    locations.name AS location_name
FROM incidents
LEFT JOIN users ON incidents.user_id = users.id
LEFT JOIN locations ON incidents.location_id = locations.location_id
LEFT JOIN incident_types ON incidents.type_id = incident_types.type_id
WHERE incidents.is_fake = 1
ORDER BY incidents.created_at DESC";
$flaggedResult = $conn->query($flaggedSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard | NirapodPoth</title>
  <style>
    body {
      
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #f7f9fc, #dbefff);
      padding: 40px 20px;
    }
    
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    .welcome {
      font-size: 18px;
      font-weight: bold;
      color: #202F6A;
    }
    .actions {
      display: flex;
      gap: 10px;
    }
    .btn {
      background-color: #202F6A;
      color: white;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
    }
    .btn:hover {
      background-color: #F39C12;
    }
    h1 {
      text-align: center;
      color: #202F6A;
      margin-bottom: 20px;
    }
    .floating-search-wrapper {
      position: relative;
      text-align: center;
      margin-bottom: 30px;
    }
    .floating-search-icon {
      font-size: 16px;
      font-weight: medium;
      color: #007BFF;
      background: white;
      border-radius: 20px;
      padding: 8px 10px;
      width: 30px;
      height: 30px;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      display: inline-flex;
      justify-content: center;
      align-items: center;
      transition: transform 0.5s;
    }
    .floating-search-bar {
      display: none;
      justify-content: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 20px;
      animation: fadeIn 0.3s ease;
    }
    .floating-search-bar input {
      padding: 10px 14px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 8px;
      outline: none;
      min-width: 220px;
    }
    .floating-search-bar button {
      background-color: #202F6A;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
    }
    .close-btn {
      font-size: 20px;
      font-weight: bold;
      color: #0068d7ff;
      background: none;
      border: none;
      cursor: pointer;
      margin-left: 10px;
      padding: 5px 10px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    .top-locations {
      margin-bottom: 30px;
      background: #fff3cd;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    .top-locations h2 {
      color: #856404;
      margin-top: 0;
    }
    .top-locations ul {
      list-style: none;
      padding-left: 0;
      font-size: 16px;
      color: #5a4d00;
    }
    .top-locations li {
      margin-bottom: 10px;
    }
    .incident-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 20px;
    }
    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 20px;
      transition: 0.3s;
      position: relative;
    }
    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    .card h3 {
      margin: 0;
      color: #202F6A;
      font-size: 18px;
    }
    .card .meta {
      font-size: 14px;
      margin: 10px 0;
      color: #555;
    }
    .card .description {
      font-size: 15px;
      color: #333;
      margin-top: 10px;
       overflow: visible !important;
    }
    .meta span {
  display: block;
  margin-bottom: 5px;
  white-space: normal;    /* Allow wrapping */
  word-wrap: break-word;  /* Break long words */
  overflow-wrap: break-word; /* Support for newer browsers */
}

    .meta i {
      margin-right: 6px;
      color: #202F6A;
    }
    .edit-delete {
      margin-top: 15px;
      display: flex;
      gap: 10px;
    }
    .edit-delete .btn {
      font-size: 13px;
      padding: 8px 12px;
    }
    
  </style>
</head>
<body>
<?php
  $backUrl = ($_SESSION['is_admin'] ?? false) ? 'choose_dashboard.php' : 'index.php';
?>
<a href="<?= htmlspecialchars($backUrl) ?>" style="
  display: inline-block;
  margin-bottom: 10px;
  padding: 6px 12px;
  background-color: #202F6A;
  color: white;
  text-decoration: none;
  font-size: 14px;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  transition: background-color 0.3s ease;
">
  ← Back
</a>

  <div class="top-bar">
    <div class="welcome">👋 Welcome, <?= htmlspecialchars($fullname) ?>!</div>
    <div class="actions">
      <a href="report_incident.php" class="btn">➕ Report New Incident</a>
      <a href="logout.php" class="btn">🚪 Logout</a>
    </div>
  </div>

  <h1>📋 Reported Incidents</h1>

  <div class="floating-search-wrapper">
    <div class="floating-search-icon" onclick="toggleSearchBar(true)">🔍</div>
    <form method="GET" class="floating-search-bar" id="searchForm">
      <input type="text" name="location" placeholder="Search by location" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
      <button type="submit">Search</button>
      <div class="close-btn" onclick="toggleSearchBar(false)">✖</div>
    </form>
  </div>

  <?php if ($topLocationsResult && $topLocationsResult->num_rows > 0): ?>
    <div class="top-locations">
      <h2>⚠️ Most Reported Locations</h2>
      <ul>
        <?php while ($loc = $topLocationsResult->fetch_assoc()): ?>
          <li>📍 <strong><?= htmlspecialchars($loc['location_name'] ?? 'Unknown') ?></strong> — <?= $loc['total_reports'] ?> reports</li>
        <?php endwhile; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="incident-container">
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
    ?>
        <div class="card">
          <h3><?= htmlspecialchars($row['incident_type']) ?> Report</h3>
          <div class="meta">
            <span><i>📍</i> Location: <?= htmlspecialchars($row['location_name'] ?? 'Unknown') ?></span>
            <span><i>⏰</i> Incident Time: <?= date("M d, Y - h:i A", strtotime($row['incident_time'])) ?></span>
            <span><i>📅</i> Submitted On: <?= date("M d, Y - h:i A", strtotime($row['created_at'])) ?></span>
            <span><i>🧑</i> Reporter: 
              <?= !empty($row['reporter_name']) ? htmlspecialchars($row['reporter_name']) : '<i>Anonymous</i>' ?>
            </span>
            <?php if (!empty($row['feedback_response'])): ?>
              <span><i>🛡️</i> Are you safe now? <?= htmlspecialchars($row['feedback_response']) ?></span>
            <?php endif; ?>
            <?php if (!empty($row['contact_number']) && ($_SESSION['is_admin'] ?? false)): ?>
              <span><i>📞</i> Contact Number: <?= htmlspecialchars($row['contact_number']) ?></span>
            <?php endif; ?>
          </div>
          <div class="description"><?= nl2br(htmlspecialchars($row['description'])) ?></div>
          <?php if ($row['user_id'] == $user_id): ?>
            <div class="edit-delete">
              <a href="edit_report.php?id=<?= $row['id'] ?>" class="btn">✏️ Edit</a>
              <a href="delete_report.php?id=<?= $row['id'] ?>" class="btn" onclick="return confirm('Are you sure you want to delete this report?');">🗑️ Delete</a>
            </div>
          <?php endif; ?>
        </div>
    <?php
        }
    } else {
        echo "<p style='text-align:center;'>No incidents found matching your search.</p>";
    }
    ?>
  </div>

  <?php if ($flaggedResult && $flaggedResult->num_rows > 0): ?>
    <h1 style="margin-top:50px; color:red; text-align:center;">🚩 Flagged Reports</h1>
    <div class="incident-container">
      <?php while ($flag = $flaggedResult->fetch_assoc()): ?>
        <div class="card" style="border: 2px solid red;">
          <h3>🚩 <?= htmlspecialchars($flag['incident_type']) ?> Report</h3>
          <div class="meta">
            <span><i>📍</i> Location: <?= htmlspecialchars($flag['location_name'] ?? 'Unknown') ?></span>
            <span><i>⏰</i> Incident Time: <?= date("M d, Y - h:i A", strtotime($flag['incident_time'])) ?></span>
            <span><i>📅</i> Submitted On: <?= date("M d, Y - h:i A", strtotime($flag['created_at'])) ?></span>
            <span><i>🧑</i> Reporter: 
              <?= !empty($flag['reporter_name']) ? htmlspecialchars($flag['reporter_name']) : '<i>Anonymous</i>' ?>
            </span>
            <?php if (!empty($flag['contact_number']) && ($_SESSION['is_admin'] ?? false)): ?>
              <span><i>📞</i> Contact Number: <?= htmlspecialchars($flag['contact_number']) ?></span>
            <?php endif; ?>
          </div>
          <div class="description"><?= nl2br(htmlspecialchars($flag['description'])) ?></div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

<script>
  function toggleSearchBar(show) {
    const searchBar = document.getElementById("searchForm");
    const icon = document.querySelector(".floating-search-icon");
    if (show) {
      searchBar.style.display = "flex";
      icon.style.display = "none";
    } else {
      searchBar.style.display = "none";
      icon.style.display = "inline-flex";
    }
  }
  window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('location')) {
      toggleSearchBar(true);
    }
  };
</script>

</body>
</html>

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

if ($report_id > 0) {
    $stmt = $conn->prepare("DELETE FROM incidents WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $report_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: dashboard.php");
exit;

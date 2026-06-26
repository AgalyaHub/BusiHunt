<?php
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  echo json_encode(array("status" => "error", "message" => "Unauthorized."));
  exit;
}

require_once 'config.php';

$title       = isset($_POST['title'])       ? trim($_POST['title'])       : '';
$category    = isset($_POST['category'])    ? trim($_POST['category'])    : '';
$location    = isset($_POST['location'])    ? trim($_POST['location'])    : '';
$event_date  = isset($_POST['event_date'])  ? trim($_POST['event_date'])  : '';
$event_time  = isset($_POST['event_time'])  ? trim($_POST['event_time'])  : '';
$seats       = isset($_POST['seats'])       ? intval($_POST['seats'])     : 0;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

if (empty($title) || empty($category) || empty($location) || empty($event_date)) {
  echo json_encode(array("status" => "error", "message" => "All fields are required."));
  exit;
}

try {
  $stmt = $pdo->prepare("INSERT INTO events (title, category, location, event_date, event_time, seats, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute(array($title, $category, $location, $event_date, $event_time, $seats, $description));
  echo json_encode(array("status" => "success", "message" => "Event added successfully!"));
} catch (Exception $e) {
  echo json_encode(array("status" => "error", "message" => "Error: " . $e->getMessage()));
}
?>
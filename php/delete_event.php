<?php
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  echo json_encode(array("status" => "error", "message" => "Unauthorized."));
  exit;
}

require_once 'config.php';

$id = intval($_POST['id']);

if ($id <= 0) {
  echo json_encode(array("status" => "error", "message" => "Invalid event ID."));
  exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
  $stmt->execute(array($id));
  echo json_encode(array("status" => "success", "message" => "Event deleted successfully!"));
} catch (Exception $e) {
  echo json_encode(array("status" => "error", "message" => "Error: " . $e->getMessage()));
}
?>
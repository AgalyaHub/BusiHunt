<?php
header("Content-Type: application/json");
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  echo json_encode(array("status" => "error", "message" => "Unauthorized."));
  exit;
}
require_once 'config.php';
$id = intval($_POST['id']);
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute(array($id));
echo json_encode(array("status" => "success", "message" => "User deleted."));
?>
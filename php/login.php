<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email    = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  // Validation
  if (!$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Email and password are required."]);
    exit;
  }

  // Find user by email
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check password
  if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    exit;
  }

  // Set session
  $_SESSION['user_id']   = $user['id'];
  $_SESSION['user_name'] = $user['first_name'];
  $_SESSION['user_role'] = $user['role'];
  $_SESSION['user_email']= $user['email'];

  echo json_encode([
    "status"  => "success",
    "message" => "Login successful!",
    "user"    => [
      "id"         => $user['id'],
      "first_name" => $user['first_name'],
      "last_name"  => $user['last_name'],
      "email"      => $user['email'],
      "role"       => $user['role']
    ]
  ]);

} else {
  echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
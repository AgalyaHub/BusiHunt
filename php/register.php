<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $first_name = trim($_POST['first_name']);
  $last_name  = trim($_POST['last_name']);
  $email      = trim($_POST['email']);
  $password   = trim($_POST['password']);

  if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    $res = array("status" => "error", "message" => "All fields are required.");
    echo json_encode($res);
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $res = array("status" => "error", "message" => "Invalid email.");
    echo json_encode($res);
    exit;
  }

  if (strlen($password) < 6) {
    $res = array("status" => "error", "message" => "Password too short.");
    echo json_encode($res);
    exit;
  }

  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute(array($email));

  if ($stmt->rowCount() > 0) {
    $res = array("status" => "error", "message" => "Email already registered.");
    echo json_encode($res);
    exit;
  }

  $hashed = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
  $stmt->execute(array($first_name, $last_name, $email, $hashed));

  $res = array("status" => "success", "message" => "Registration successful!");
  echo json_encode($res);

} else {
  $res = array("status" => "error", "message" => "Invalid request.");
  echo json_encode($res);
}
?>
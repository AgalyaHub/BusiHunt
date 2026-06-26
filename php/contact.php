<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
  $last_name  = isset($_POST['last_name'])  ? trim($_POST['last_name'])  : '';
  $email      = isset($_POST['email'])      ? trim($_POST['email'])      : '';
  $phone      = isset($_POST['phone'])      ? trim($_POST['phone'])      : '';
  $company    = isset($_POST['company'])    ? trim($_POST['company'])    : '';
  $subject    = isset($_POST['subject'])    ? trim($_POST['subject'])    : '';
  $message    = isset($_POST['message'])    ? trim($_POST['message'])    : '';

  if (empty($first_name) || empty($last_name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(array("status" => "error", "message" => "All required fields must be filled."));
    exit;
  }

  try {
    $stmt = $pdo->prepare("INSERT INTO contacts (first_name, last_name, email, phone, company, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute(array($first_name, $last_name, $email, $phone, $company, $subject, $message));

    if ($result) {
      echo json_encode(array("status" => "success", "message" => "Thank you " . $first_name . "! We'll get back to you within 24 hours."));
    } else {
      echo json_encode(array("status" => "error", "message" => "Failed to save message."));
    }
  } catch (Exception $e) {
    echo json_encode(array("status" => "error", "message" => "Database error: " . $e->getMessage()));
  }

} else {
  echo json_encode(array("status" => "error", "message" => "Invalid request."));
}
?>
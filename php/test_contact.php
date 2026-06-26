<?php
require_once 'config.php';

try {
  $stmt = $pdo->prepare("INSERT INTO contacts (first_name, last_name, email, phone, company, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $result = $stmt->execute(array('Test', 'User', 'test@test.com', '1234567890', 'Test Co', 'General Query', 'This is a test message'));

  if ($result) {
    echo "SUCCESS! Data inserted into contacts table!";
  } else {
    echo "FAILED to insert data!";
  }
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage();
}
?>
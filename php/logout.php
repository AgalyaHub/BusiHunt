<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head></head>
<body>
<script>
  localStorage.removeItem('bh_user');
  window.location.href = '../index.html';
</script>
</body>
</html>
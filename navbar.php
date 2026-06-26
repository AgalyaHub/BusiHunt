<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $is_logged_in ? $_SESSION['user_name'] : '';
$user_role    = $is_logged_in ? $_SESSION['user_role'] : '';
?>
<nav class="navbar" id="navbar">
  <div class="container">
    <a href="index.php" class="logo">
      <div class="logo-icon">BH</div>
      <div class="logo-text">Busi<span>Hunt</span></div>
    </a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="members.php">Members</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
    <div class="nav-actions">
      <?php if ($is_logged_in): ?>
        <?php if ($user_role === 'admin'): ?>
          <a href="admin.php" class="btn btn-outline">⚙️ Dashboard</a>
        <?php endif; ?>
        <span style="color:white;font-size:0.9rem;font-weight:600;">👋 <?php echo htmlspecialchars($user_name); ?></span>
        <a href="php/logout.php" class="btn btn-primary">Logout</a>
      <?php else: ?>
        <a href="login.html" class="btn btn-outline">Sign In</a>
        <a href="register.html" class="btn btn-primary">Join Now</a>
      <?php endif; ?>
    </div>
    <button class="hamburger" aria-label="Open menu"><span></span><span></span><span></span></button>
  </div>
</nav>
<div class="mobile-menu">
  <button class="mobile-close">✕</button>
  <a href="index.php">Home</a>
  <a href="about.php">About</a>
  <a href="events.php">Events</a>
  <a href="members.php">Members</a>
  <a href="contact.php">Contact</a>
  <?php if ($is_logged_in): ?>
    <a href="php/logout.php" class="btn btn-primary">Logout</a>
  <?php else: ?>
    <a href="register.html" class="btn btn-primary">Join Now</a>
  <?php endif; ?>
</div>
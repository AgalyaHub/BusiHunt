<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: login.html");
  exit;
}

require_once 'php/config.php';

$total_users    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_contacts = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$total_events   = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();

$users    = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$contacts = $pdo->query("SELECT * FROM contacts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$events   = $pdo->query("SELECT * FROM events ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Busi Hunt</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy: #0B1F3A;
      --navy-dark: #071529;
      --gold: #C9A84C;
      --white: #FFFFFF;
      --off-white: #F5F7FA;
      --gray-light: #E4E8EF;
      --gray-mid: #8A96A8;
      --green: #1DB774;
      --red: #E53E3E;
      --accent: #1A6FC4;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--off-white); color: #4A5568; }

    .sidebar {
      position: fixed;
      top: 0; left: 0;
      width: 240px;
      height: 100vh;
      background: var(--navy-dark);
      padding: 1.5rem 1rem;
      z-index: 100;
      overflow-y: auto;
    }
    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-bottom: 2.5rem;
      padding: 0 0.5rem;
    }
    .sidebar-logo-icon {
      width: 36px; height: 36px;
      background: var(--gold);
      color: var(--navy);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.8rem;
    }
    .sidebar-logo-text {
      font-family: 'Playfair Display', serif;
      color: var(--white);
      font-size: 1.1rem;
      font-weight: 700;
    }
    .sidebar-logo-text span { color: var(--gold); }
    .sidebar-label {
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.3);
      padding: 0 0.75rem;
      margin-bottom: 0.5rem;
      margin-top: 1.5rem;
    }
    .nav-item {
      display: flex;
      align-items: center;
      gap: 0.7rem;
      padding: 0.7rem 0.75rem;
      border-radius: 8px;
      color: rgba(255,255,255,0.6);
      font-size: 0.88rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      margin-bottom: 0.2rem;
      border: none;
      background: none;
      width: 100%;
      text-align: left;
      font-family: 'Inter', sans-serif;
    }
    .nav-item:hover { background: rgba(255,255,255,0.08); color: var(--white); }
    .nav-item.active { background: var(--gold); color: var(--navy); font-weight: 700; }

    .main { margin-left: 240px; min-height: 100vh; }

    .topbar {
      background: var(--white);
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--gray-light);
      position: sticky;
      top: 0;
      z-index: 99;
    }
    .topbar h1 {
      font-size: 1.2rem;
      color: var(--navy);
      font-family: 'Playfair Display', serif;
    }
    .topbar-right { display: flex; align-items: center; gap: 1rem; }
    .admin-badge {
      background: var(--navy);
      color: var(--white);
      padding: 0.35rem 1rem;
      border-radius: 99px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    .logout-btn {
      background: var(--red);
      color: var(--white);
      padding: 0.35rem 1rem;
      border-radius: 99px;
      font-size: 0.8rem;
      font-weight: 600;
      text-decoration: none;
    }
    .logout-btn:hover { opacity: 0.85; }

    .content { padding: 2rem; }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.25rem;
      margin-bottom: 2rem;
    }
    .stat-card {
      background: var(--white);
      border-radius: 12px;
      padding: 1.5rem;
      border: 1px solid var(--gray-light);
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .stat-icon {
      width: 52px; height: 52px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      flex-shrink: 0;
    }
    .stat-icon.blue  { background: rgba(26,111,196,0.1); }
    .stat-icon.gold  { background: rgba(201,168,76,0.12); }
    .stat-icon.green { background: rgba(29,183,116,0.1); }
    .stat-num {
      font-size: 2rem;
      font-weight: 700;
      color: var(--navy);
      font-family: 'Playfair Display', serif;
      line-height: 1;
    }
    .stat-label { font-size: 0.82rem; color: var(--gray-mid); margin-top: 0.25rem; }

    .section-card {
      background: var(--white);
      border-radius: 12px;
      border: 1px solid var(--gray-light);
      margin-bottom: 2rem;
      overflow: hidden;
    }
    .tab-btns {
      display: flex;
      gap: 0.5rem;
      padding: 1rem 1.5rem;
      border-bottom: 1px solid var(--gray-light);
      flex-wrap: wrap;
    }
    .tab-btn {
      padding: 0.45rem 1rem;
      border-radius: 99px;
      border: 1px solid var(--gray-light);
      background: var(--white);
      font-family: 'Inter', sans-serif;
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      color: #4A5568;
      transition: all 0.2s;
    }
    .tab-btn.active { background: var(--navy); color: var(--white); border-color: var(--navy); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    table { width: 100%; border-collapse: collapse; }
    th {
      background: var(--off-white);
      padding: 0.75rem 1.5rem;
      text-align: left;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--gray-mid);
    }
    td {
      padding: 0.9rem 1.5rem;
      font-size: 0.88rem;
      border-bottom: 1px solid var(--gray-light);
    }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--off-white); }

    .role-badge {
      display: inline-block;
      padding: 0.2rem 0.65rem;
      border-radius: 99px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
    }
    .role-admin { background: rgba(201,168,76,0.15); color: #8B6914; }
    .role-user  { background: rgba(26,111,196,0.1); color: var(--accent); }

    .delete-btn {
      background: rgba(229,62,62,0.1);
      color: var(--red);
      border: none;
      padding: 0.3rem 0.75rem;
      border-radius: 6px;
      font-size: 0.78rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .delete-btn:hover { background: var(--red); color: var(--white); }

    .empty-state {
      text-align: center;
      padding: 3rem;
      color: var(--gray-mid);
      font-size: 0.9rem;
    }

    /* Add Event Form */
    .add-event-form {
      padding: 1.5rem;
      border-bottom: 1px solid var(--gray-light);
      background: var(--off-white);
    }
    .add-event-form h3 {
      font-size: 1rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 1rem;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .form-grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .add-event-form input,
    .add-event-form select,
    .add-event-form textarea {
      width: 100%;
      padding: 0.65rem 0.9rem;
      border: 1px solid var(--gray-light);
      border-radius: 6px;
      font-family: 'Inter', sans-serif;
      font-size: 0.88rem;
      background: var(--white);
      outline: none;
      transition: border-color 0.2s;
    }
    .add-event-form input:focus,
    .add-event-form select:focus,
    .add-event-form textarea:focus {
      border-color: var(--accent);
    }
    .add-event-form label {
      display: block;
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--navy);
      margin-bottom: 0.3rem;
    }
    .add-event-form textarea { min-height: 80px; resize: vertical; }
    .add-btn {
      background: var(--gold);
      color: var(--navy);
      border: none;
      padding: 0.65rem 1.5rem;
      border-radius: 6px;
      font-family: 'Inter', sans-serif;
      font-size: 0.88rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
    }
    .add-btn:hover { background: #E2C47A; }

    .event-tag {
      display: inline-block;
      padding: 0.2rem 0.65rem;
      border-radius: 99px;
      font-size: 0.72rem;
      font-weight: 700;
      background: rgba(201,168,76,0.12);
      color: #8B6914;
      text-transform: uppercase;
    }

    .alert-box {
      padding: 0.75rem 1rem;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 500;
      margin-bottom: 1rem;
      display: none;
    }
    .alert-box.success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      display: block;
    }
    .alert-box.error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      display: block;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon">BH</div>
    <div class="sidebar-logo-text">Busi<span>Hunt</span></div>
  </div>
  <div class="sidebar-label">Main Menu</div>
  <button class="nav-item active" id="sidebtn-users" onclick="showTab('users')">👥 Users</button>
  <button class="nav-item" id="sidebtn-contacts" onclick="showTab('contacts')">📩 Contact Messages</button>
  <button class="nav-item" id="sidebtn-events" onclick="showTab('events')">📅 Events</button>
  <div class="sidebar-label">Site</div>
  <a class="nav-item" href="index.html" target="_blank">🌐 View Website</a>
  <a class="nav-item" href="php/logout.php">🚪 Logout</a>
</div>

<!-- Main -->
<div class="main">
  <div class="topbar">
    <h1>Admin Dashboard</h1>
    <div class="topbar-right">
      <span class="admin-badge">👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <a href="php/logout.php" class="logout-btn">Logout</a>
    </div>
  </div>

  <div class="content">

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div>
          <div class="stat-num"><?php echo $total_users; ?></div>
          <div class="stat-label">Total Users</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon gold">📩</div>
        <div>
          <div class="stat-num"><?php echo $total_contacts; ?></div>
          <div class="stat-label">Contact Messages</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">📅</div>
        <div>
          <div class="stat-num"><?php echo $total_events; ?></div>
          <div class="stat-label">Total Events</div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="section-card">
      <div class="tab-btns">
        <button class="tab-btn active" id="tabbtn-users" onclick="showTab('users')">👥 Users</button>
        <button class="tab-btn" id="tabbtn-contacts" onclick="showTab('contacts')">📩 Contact Messages</button>
        <button class="tab-btn" id="tabbtn-events" onclick="showTab('events')">📅 Events</button>
      </div>

      <!-- Users Tab -->
      <div class="tab-content active" id="tab-users">
        <table>
          <thead>
            <tr>
              <th>#</th><th>Name</th><th>Email</th>
              <th>Role</th><th>Joined</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($users) > 0): ?>
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                  <span class="role-badge <?php echo $user['role'] === 'admin' ? 'role-admin' : 'role-user'; ?>">
                    <?php echo $user['role']; ?>
                  </span>
                </td>
                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                <td><button class="delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="empty-state">No users found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Contacts Tab -->
      <div class="tab-content" id="tab-contacts">
        <table>
          <thead>
            <tr>
              <th>#</th><th>Name</th><th>Email</th>
              <th>Phone</th><th>Subject</th><th>Message</th><th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($contacts) > 0): ?>
              <?php foreach ($contacts as $contact): ?>
              <tr>
                <td><?php echo $contact['id']; ?></td>
                <td><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></td>
                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                <td><?php echo htmlspecialchars(substr($contact['message'], 0, 60)) . '...'; ?></td>
                <td><?php echo date('d M Y', strtotime($contact['submitted_at'])); ?></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="empty-state">No messages found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Events Tab -->
      <div class="tab-content" id="tab-events">

        <!-- Add Event Form -->
        <div class="add-event-form">
          <h3>➕ Add New Event</h3>
          <div id="eventAlert" class="alert-box"></div>
          <div class="form-grid">
            <div>
              <label>Event Title *</label>
              <input type="text" id="ev_title" placeholder="South India Business Summit">
            </div>
            <div>
              <label>Category *</label>
              <select id="ev_category">
                <option value="">Select category</option>
                <option value="summit">Summit</option>
                <option value="workshop">Workshop</option>
                <option value="webinar">Webinar</option>
                <option value="meetup">Meetup</option>
              </select>
            </div>
            <div>
              <label>Location *</label>
              <input type="text" id="ev_location" placeholder="Chennai / Online">
            </div>
          </div>
          <div class="form-grid">
            <div>
              <label>Event Date *</label>
              <input type="date" id="ev_date">
            </div>
            <div>
              <label>Event Time</label>
              <input type="time" id="ev_time">
            </div>
            <div>
              <label>Total Seats</label>
              <input type="number" id="ev_seats" placeholder="100">
            </div>
          </div>
          <div class="form-grid-2">
            <div>
              <label>Description</label>
              <textarea id="ev_description" placeholder="Event description..."></textarea>
            </div>
            <div style="display:flex;align-items:flex-end;">
              <button class="add-btn" onclick="addEvent()">➕ Add Event</button>
            </div>
          </div>
        </div>

        <!-- Events Table -->
        <table>
          <thead>
            <tr>
              <th>#</th><th>Title</th><th>Category</th>
              <th>Location</th><th>Date</th><th>Seats</th><th>Action</th>
            </tr>
          </thead>
          <tbody id="eventsTableBody">
            <?php if (count($events) > 0): ?>
              <?php foreach ($events as $event): ?>
              <tr>
                <td><?php echo $event['id']; ?></td>
                <td><?php echo htmlspecialchars($event['title']); ?></td>
                <td><span class="event-tag"><?php echo htmlspecialchars($event['category']); ?></span></td>
                <td><?php echo htmlspecialchars($event['location']); ?></td>
                <td><?php echo date('d M Y', strtotime($event['event_date'])); ?></td>
                <td><?php echo $event['seats']; ?></td>
                <td><button class="delete-btn" onclick="deleteEvent(<?php echo $event['id']; ?>)">Delete</button></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="empty-state">No events found. Add one above!</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<script>
  function showTab(name) {
    document.querySelectorAll('.tab-content').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
    document.querySelectorAll('.nav-item').forEach(function(b) { b.classList.remove('active'); });
    document.getElementById('tab-' + name).classList.add('active');
    document.getElementById('tabbtn-' + name).classList.add('active');
    document.getElementById('sidebtn-' + name).classList.add('active');
  }

  function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user?')) return;
    var formData = new FormData();
    formData.append('id', id);
    fetch('php/delete_user.php', { method: 'POST', body: formData })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.status === 'success') { alert('User deleted!'); location.reload(); }
      else { alert('Error: ' + data.message); }
    });
  }

  function addEvent() {
    var title       = document.getElementById('ev_title').value.trim();
    var category    = document.getElementById('ev_category').value;
    var location    = document.getElementById('ev_location').value.trim();
    var event_date  = document.getElementById('ev_date').value;
    var event_time  = document.getElementById('ev_time').value;
    var seats       = document.getElementById('ev_seats').value;
    var description = document.getElementById('ev_description').value.trim();

    if (!title || !category || !location || !event_date) {
      showEventAlert('Please fill in all required fields.', 'error');
      return;
    }

    var formData = new FormData();
    formData.append('title',       title);
    formData.append('category',    category);
    formData.append('location',    location);
    formData.append('event_date',  event_date);
    formData.append('event_time',  event_time);
    formData.append('seats',       seats);
    formData.append('description', description);

    fetch('php/add_event.php', { method: 'POST', body: formData })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.status === 'success') {
        showEventAlert('✅ Event added successfully!', 'success');
        setTimeout(function() { location.reload(); }, 1200);
      } else {
        showEventAlert('❌ ' + data.message, 'error');
      }
    });
  }

  function deleteEvent(id) {
    if (!confirm('Are you sure you want to delete this event?')) return;
    var formData = new FormData();
    formData.append('id', id);
    fetch('php/delete_event.php', { method: 'POST', body: formData })
    .then(function(res) { return res.json(); })
    .then(function(data) {
      if (data.status === 'success') { alert('Event deleted!'); location.reload(); }
      else { alert('Error: ' + data.message); }
    });
  }

  function showEventAlert(msg, type) {
    var box = document.getElementById('eventAlert');
    box.textContent = msg;
    box.className = 'alert-box ' + type;
  }
</script>

</body>
</html>
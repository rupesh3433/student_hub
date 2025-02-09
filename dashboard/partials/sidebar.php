<?php
// Sidebar Navigation for Dashboard
// Save this file as sidebar.php in your partials directory (e.g., .../dashboard/partials/sidebar.php)
?>
<div class="sidebar" id="sidebar">
  <nav class="sidebar-nav">
    <ul>
      <li><a href="../dashboard/dashboard.php"><i class="icon-home"></i> Dashboard</a></li>
      <li><a href="../files/myfiles.php"><i class="icon-folder"></i> My Files</a></li>
      <li><a href="../files/upload.php"><i class="icon-upload"></i> Upload Files</a></li>
      <li><a href="../files/shared.php"><i class="icon-share"></i> Shared With Me</a></li>
      <li><a href="../notifications.php"><i class="icon-bell"></i> Notifications</a></li>
      <li><a href="../settings.php"><i class="icon-settings"></i> Settings</a></li>
    </ul>
  </nav>
</div>

<style>
/* ===== SIDEBAR BASE STYLES ===== */
.sidebar {
  background-color: #112240;   /* Darker blue background */
  color: #ccd6f6;
  width: 250px;
  min-height: 100vh;
  padding: 20px;
  box-shadow: 2px 0 8px rgba(0,0,0,0.15);
  transition: width 0.3s ease, left 0.3s ease;
}

/* Sidebar Header */
.sidebar .sidebar-header {
  margin-bottom: 30px;
}
.sidebar .sidebar-header h2 {
  font-size: 1.8rem;
  color: #64ffda;             /* Teal accent */
  margin: 0;
}

/* Sidebar Navigation */
.sidebar-nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.sidebar-nav ul li {
  margin-bottom: 15px;
}
.sidebar-nav ul li a {
  text-decoration: none;
  font-size: 1rem;
  color: #ccd6f6;
  display: flex;
  align-items: center;
  padding: 10px 15px;
  border-radius: 4px;
  transition: background-color 0.3s ease, color 0.3s ease;
}
.sidebar-nav ul li a:hover {
  background-color: #233554; /* Lighter blue on hover */
  color: #64ffda;
}

/* Placeholder Icon Styles (Replace with Font Awesome if desired) */
.sidebar-nav ul li a i {
  display: inline-block;
  width: 20px;
  text-align: center;
  margin-right: 10px;
}

/* ===== ADVANCED MEDIA QUERIES ===== */

/* For tablets and small desktops */
@media (max-width: 1024px) {
  .sidebar {
    width: 220px;
  }
  .sidebar .sidebar-header h2 {
    font-size: 1.6rem;
  }
  .sidebar-nav ul li a {
    font-size: 0.95rem;
    padding: 9px 13px;
  }
}

/* For mobile devices: Sidebar becomes off-canvas */
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    left: -250px;   /* Initially hidden off-canvas */
    top: 0;
    bottom: 0;
    width: 250px;
    z-index: 1000;
  }
  /* When the sidebar is toggled "active", slide it into view */
  .sidebar.active {
    left: 0;
  }
}

/* For very small devices */
@media (max-width: 480px) {
  .sidebar {
    width: 220px;
    left: -220px;  /* Adjust off-canvas width */
  }
  .sidebar.active {
    left: 0;
  }
  .sidebar .sidebar-header h2 {
    font-size: 1.4rem;
  }
  .sidebar-nav ul li a {
    font-size: 0.9rem;
    padding: 8px 12px;
  }
}
</style>

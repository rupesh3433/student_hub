<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<header class="main-header">
  <div class="header-container">
    <!-- Logo Section with Toggle Button for Mobile -->
    <div class="logo">
      <button id="sidebarToggle">&#9776;</button>
      <a href="../dashboard/dashboard.php">Student Hub</a>
    </div>
    
    <!-- Navigation Links -->
    <nav class="navigation">
      <ul class="nav-list">
        <li><a href="../dashboard/dashboard.php">Dashboard</a></li>
        <li><a href="../profile.php">Profile</a></li>
        <li><a href="../settings.php">Settings</a></li>
      </ul>
    </nav>
    
    <!-- User Menu with Greeting and Logout -->
    <div class="user-menu">
      <span class="user-greeting">Hello, <?php echo htmlspecialchars($username); ?></span>
      <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
  </div>
</header>

<style>
/* ===== HEADER STYLES ===== */
.main-header {
  background-color: #233554; /* Dark blue background */
  padding: 15px 0;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  color: #ccd6f6;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 95%;
  margin: 0 auto;
}

/* Logo Section */
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo a {
  text-decoration: none;
  font-size: 1.8rem;
  font-weight: bold;
  color: #64ffda; /* Teal accent */
}

/* Toggle Button for Mobile */
#sidebarToggle {
  font-size: 1.5rem;
  background: none;
  border: none;
  color: #ccd6f6;
  cursor: pointer;
  display: inline-block; /* Visible by default (will be hidden on larger screens) */
}

/* Navigation Styles */
.navigation .nav-list {
  list-style: none;
  display: flex;
  gap: 20px;
  margin: 0;
  padding: 0;
}

.navigation .nav-list li a {
  text-decoration: none;
  font-size: 1rem;
  color: #ccd6f6;
  transition: color 0.3s ease;
}

.navigation .nav-list li a:hover {
  color: #64ffda;
}

/* User Menu Styles */
.user-menu {
  display: flex;
  align-items: center;
  gap: 15px;
}

.user-greeting {
  font-size: 1rem;
}

.logout-btn {
  background-color: #e74c3c;
  color: #fff;
  padding: 8px 12px;
  text-decoration: none;
  border-radius: 5px;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
}

.logout-btn:hover {
  background-color: #c0392b;
}

/* ===== RESPONSIVE STYLES ===== */
@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .navigation .nav-list {
    flex-direction: column;
    gap: 10px;
  }
  
  .user-menu {
    align-self: flex-end;
  }
}

/* Hide toggle button on larger screens */
@media (min-width: 768px) {
  #sidebarToggle {
    display: none;
  }
}
</style>

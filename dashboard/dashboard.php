<?php
require_once '../includes/auth_check.php';
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <!-- Ensure proper scaling on mobile devices -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Student Hub</title>
  <!-- Link to your external dashboard CSS -->
  <link rel="stylesheet" type="text/css" href="../assets/css/dashboard.css">
</head>
<body>
  <!-- Sticky header is included from header.php -->
  <?php include_once '../includes/header.php'; ?>
  
  <div class="dashboard-container">
    <!-- Sticky sidebar (with id="sidebar") -->
    <aside class="sidebar" id="sidebar">
      <?php include_once 'partials/sidebar.php'; ?>
    </aside>
    
    <!-- Main Content Area -->
    <section class="main-content">
      <!-- Breadcrumb Navigation -->
      <div class="breadcrumb">
         <?php include_once 'partials/breadcrumb.php'; ?>
      </div>
      
      <!-- Dashboard Header Section with Welcome Message and Search -->
      <div class="dashboard-header">
         <h1>Welcome to Your Dashboard</h1>
         <div class="dashboard-actions">
            <input type="text" id="searchFiles" placeholder="Search files..." />
            <button id="btnSearch">Search</button>
         </div>
      </div>
      
      <!-- File Manager Section -->
      <div id="file-manager">
         <?php include_once 'partials/file_preview.php'; ?>
      </div>
      
      <!-- Context Menu (optional) -->
      <?php include_once 'partials/context_menu.php'; ?>
    </section>
  </div>
  
  <!-- Footer -->
  <?php include_once '../includes/footer.php'; ?>
  
  <!-- Dashboard specific JavaScript -->
  <script src="js/dashboard.js"></script>
</body>
</html>

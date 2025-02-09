<?php
// breadcrumb.php - Enhanced breadcrumb trail for your dashboard

// Define breadcrumb items as label => URL.
// For the current page, set the URL to an empty string.
$breadcrumbs = [
    "Home"      => "../dashboard/dashboard.php",
    "Dashboard" => ""  // Current page
];
?>
<nav class="breadcrumb-nav" aria-label="Breadcrumb">
  <ol class="breadcrumb-list">
    <?php 
      $total = count($breadcrumbs);
      $i = 0;
      foreach ($breadcrumbs as $label => $url):
          $i++;
          if (!empty($url) && $i < $total):
    ?>
      <li class="breadcrumb-item"><a href="<?php echo $url; ?>"><?php echo htmlspecialchars($label); ?></a></li>
    <?php else: ?>
      <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($label); ?></li>
    <?php 
          endif;
      endforeach; 
    ?>
  </ol>
</nav>

<style>
/* ===== ENHANCED BREADCRUMB STYLES ===== */
.breadcrumb-nav {
  background-color: #f9f9f9;
  padding: 10px 20px;
  border-radius: 6px;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: background-color 0.3s ease;
}

.breadcrumb-list {
  display: flex;
  flex-wrap: wrap;
  list-style: none;
  margin: 0;
  padding: 0;
}

.breadcrumb-item {
  display: flex;
  align-items: center;
  font-size: 1rem;
  color: #555;
  transition: color 0.3s ease;
}

/* Separator between items */
.breadcrumb-item + .breadcrumb-item::before {
  content: "›";
  padding: 0 10px;
  color: #999;
  transition: opacity 0.3s ease;
}

.breadcrumb-item a {
  text-decoration: none;
  color: #007bff;
  position: relative;
  padding: 2px 4px;
  transition: color 0.3s ease, background-color 0.3s ease;
  border-radius: 4px;
}

.breadcrumb-item a:hover {
  color: #0056b3;
  background-color: rgba(0, 123, 255, 0.1);
}

.breadcrumb-item.active {
  font-weight: bold;
  color: #333;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .breadcrumb-nav {
    padding: 8px 16px;
    margin-bottom: 1rem;
  }
  
  .breadcrumb-list {
    font-size: 0.9rem;
  }
  
  .breadcrumb-item + .breadcrumb-item::before {
    padding: 0 8px;
  }
}

@media (max-width: 480px) {
  .breadcrumb-nav {
    padding: 6px 12px;
  }
  
  .breadcrumb-list {
    font-size: 0.85rem;
  }
  
  .breadcrumb-item + .breadcrumb-item::before {
    padding: 0 6px;
  }
}
</style>

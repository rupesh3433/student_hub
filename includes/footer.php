<?php
// footer.php - Professional Footer for Student Hub
?>
<footer class="main-footer">
  <div class="footer-container">
    <!-- About Section -->
    <div class="footer-column">
      <h3>About Student Hub</h3>
      <p>
        Student Hub is your comprehensive portal for academic resources, course management, and collaborative learning. We empower students with tools and insights to succeed.
      </p>
    </div>
    <!-- Quick Links Section -->
    <div class="footer-column">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="../dashboard/dashboard.php">Dashboard</a></li>
        <li><a href="../profile.php">Profile</a></li>
        <li><a href="../settings.php">Settings</a></li>
        <li><a href="../help.php">Help Center</a></li>
      </ul>
    </div>
    <!-- Contact Section -->
    <div class="footer-column">
      <h3>Contact Us</h3>
      <p>Email: support@studenthub.com</p>
      <p>Phone: +1 (555) 123-4567</p>
      <div class="social-links">
        <a href="#" target="_blank">Facebook</a>
        <a href="#" target="_blank">Twitter</a>
        <a href="#" target="_blank">LinkedIn</a>
      </div>
    </div>
    
  </div>


  <div class="footer-container">
    <!-- About Section -->
    <div class="footer-column">
      <h3>About Student Hub</h3>
      <p>
        Student Hub is your comprehensive portal for academic resources, course management, and collaborative learning. We empower students with tools and insights to succeed.
      </p>
    </div>
    <!-- Quick Links Section -->
    <div class="footer-column">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="../dashboard/dashboard.php">Dashboard</a></li>
        <li><a href="../profile.php">Profile</a></li>
        <li><a href="../settings.php">Settings</a></li>
        <li><a href="../help.php">Help Center</a></li>
      </ul>
    </div>
    <!-- Contact Section -->
    <div class="footer-column">
      <h3>Contact Us</h3>
      <p>Email: support@studenthub.com</p>
      <p>Phone: +1 (555) 123-4567</p>
      <div class="social-links">
        <a href="#" target="_blank">Facebook</a>
        <a href="#" target="_blank">Twitter</a>
        <a href="#" target="_blank">LinkedIn</a>
      </div>
    </div>
    
  </div>


  
  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> Student Hub. All rights reserved.</p>
  </div>
</footer>

<style>
/* ===== FOOTER STYLES ===== */
  /* Ensure footer has higher z-index than sidebar */
  footer {
    position: relative;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 1001;
  }
.main-footer {
    background-color: #112240; /* Dark background */
    color: #ccd6f6;
    padding: 60px 20px;  /* Increased padding for a taller footer */
    margin-top: 10px;
    /* Optional: set a min-height if you need a fixed tall footer */
    min-height: 250px;
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-column {
    flex: 1;
    min-width: 250px;
    margin: 20px;
}

.footer-column h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: #64ffda; /* Teal accent */
}

.footer-column p,
.footer-column ul,
.footer-column li {
    font-size: 0.9rem;
    line-height: 1.5;
}

.footer-column ul {
    list-style: none;
    padding: 0;
}

.footer-column ul li {
    margin-bottom: 10px;
}

.footer-column ul li a {
    color: #ccd6f6;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-column ul li a:hover {
    color: #64ffda;
}

.social-links a {
    margin-right: 15px;
    color: #ccd6f6;
    text-decoration: none;
    font-size: 1rem;
    transition: color 0.3s ease;
}

.social-links a:hover {
    color: #64ffda;
}

.footer-bottom {
    border-top: 1px solid #233554;
    margin-top: 30px;
    padding-top: 45px;
    text-align: center;
    font-size: 0.85rem;
    color: #888;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .footer-container {
        flex-direction: column;
        align-items: center;
    }
    .footer-column {
        margin: 10px 0;
        text-align: center;
    }
}
</style>

document.addEventListener("DOMContentLoaded", function() {
    console.log("Dashboard loaded.");
  
    // Sidebar Toggle for Mobile Devices
    const toggleBtn = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener("click", function() {
        sidebar.classList.toggle("active");
      });
    }
    
    // Handle right-click to show a custom context menu
    document.addEventListener("contextmenu", function(e) {
      e.preventDefault();
      var menu = document.getElementById("context-menu");
      if (menu) {
        // Position the menu at the cursor's coordinates
        menu.style.left = e.pageX + "px";
        menu.style.top = e.pageY + "px";
        // Use a class to control visibility (CSS should handle .show)
        menu.classList.add("show");
      }
    });
  
    // Hide context menu when clicking elsewhere
    document.addEventListener("click", function(e) {
      var menu = document.getElementById("context-menu");
      if (menu) {
        menu.classList.remove("show");
        // Alternatively, you can use: menu.style.display = "none";
      }
    });
    
    // Placeholder functions for context menu actions
    window.renameItem = function(itemId) {
      alert("Rename functionality to be implemented for item: " + itemId);
    };
  
    window.deleteItem = function(itemId) {
      alert("Delete functionality to be implemented for item: " + itemId);
    };
  
    window.downloadItem = function(itemId) {
      alert("Download functionality to be implemented for item: " + itemId);
    };
  });
  document.addEventListener("DOMContentLoaded", function() {
  console.log("Dashboard loaded.");

  // Sidebar Toggle for Mobile Devices
  const toggleBtn = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("sidebar");
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener("click", function() {
      sidebar.classList.toggle("active");
    });
  }
  
  // Handle right-click to show a custom context menu
  document.addEventListener("contextmenu", function(e) {
    e.preventDefault();
    var menu = document.getElementById("context-menu");
    if (menu) {
      menu.style.left = e.pageX + "px";
      menu.style.top = e.pageY + "px";
      menu.classList.add("show");
    }
  });

  // Hide context menu when clicking elsewhere
  document.addEventListener("click", function(e) {
    var menu = document.getElementById("context-menu");
    if (menu) {
      menu.classList.remove("show");
    }
  });
  
  // Placeholder functions for context menu actions
  window.renameItem = function(itemId) {
    alert("Rename functionality to be implemented for item: " + itemId);
  };

  window.deleteItem = function(itemId) {
    alert("Delete functionality to be implemented for item: " + itemId);
  };

  window.downloadItem = function(itemId) {
    alert("Download functionality to be implemented for item: " + itemId);
  };
});

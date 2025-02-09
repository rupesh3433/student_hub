<?php
// context_menu.php
// Enhanced context menu with dynamic features and accessibility
?>
<div id="context-menu" class="context-menu" role="menu" aria-hidden="true">
  <div class="context-menu-header">
    <span class="file-name">Selected Item</span>
    <button class="close-btn" aria-label="Close menu">&times;</button>
  </div>
  <ul class="menu-items">
    <!-- Dynamic items will be injected here -->
  </ul>
  <div class="context-menu-footer">
    <small>Right click for more options</small>
  </div>
</div>

<style>
/* ===== ENHANCED CONTEXT MENU STYLES ===== */
:root {
  --context-menu-bg: #ffffff;
  --context-menu-border: #e0e0e0;
  --context-menu-text: #333333;
  --context-menu-hover: #f5f5f5;
  --context-menu-accent: #2196F3;
  --context-menu-radius: 8px;
  --context-menu-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.context-menu {
  position: fixed;
  display: none;
  background: var(--context-menu-bg);
  border: 1px solid var(--context-menu-border);
  border-radius: var(--context-menu-radius);
  box-shadow: var(--context-menu-shadow);
  min-width: 240px;
  z-index: 10000;
  opacity: 0;
  transform: translateY(-10px);
  transition: opacity 0.2s ease, transform 0.2s ease;
  font-family: 'Segoe UI', system-ui, sans-serif;
}

.context-menu.show {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

.context-menu-header {
  padding: 12px 16px;
  border-bottom: 1px solid var(--context-menu-border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.file-name {
  font-weight: 600;
  font-size: 0.9em;
  color: var(--context-menu-text);
  max-width: 180px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.2em;
  cursor: pointer;
  color: #666;
  padding: 0 4px;
  transition: color 0.2s ease;
}

.close-btn:hover {
  color: #333;
}

.menu-items {
  list-style: none;
  padding: 8px 0;
  margin: 0;
}

.menu-item {
  padding: 10px 16px;
  font-size: 0.95em;
  color: var(--context-menu-text);
  cursor: pointer;
  display: flex;
  align-items: center;
  transition: background-color 0.2s ease;
  position: relative;
}

.menu-item:hover {
  background-color: var(--context-menu-hover);
}

.menu-item[disabled] {
  color: #999;
  cursor: not-allowed;
  background-color: transparent;
}

.menu-item .icon {
  width: 18px;
  height: 18px;
  margin-right: 12px;
  fill: currentColor;
}

.menu-divider {
  height: 1px;
  background: var(--context-menu-border);
  margin: 8px 0;
}

.submenu-arrow {
  margin-left: auto;
  font-size: 0.9em;
  color: #666;
}

.context-menu-footer {
  padding: 8px 16px;
  border-top: 1px solid var(--context-menu-border);
  font-size: 0.8em;
  color: #666;
}

/* Submenu styles */
.submenu {
  position: absolute;
  left: 100%;
  top: 0;
  min-width: 200px;
  background: var(--context-menu-bg);
  border-radius: var(--context-menu-radius);
  box-shadow: var(--context-menu-shadow);
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s ease;
}

.menu-item:hover .submenu {
  opacity: 1;
  visibility: visible;
}

/* Mobile adaptations */
@media (max-width: 768px) {
  .context-menu {
    min-width: 200px;
    font-size: 14px;
  }
  
  .menu-item {
    padding: 12px 16px;
  }
  
  .submenu {
    left: auto;
    right: 100%;
  }
}

/* Keyboard navigation */
.context-menu .menu-item:focus {
  outline: 2px solid var(--context-menu-accent);
  background-color: var(--context-menu-hover);
}
</style>

<script>
// Enhanced Context Menu Module
(function() {
  const menu = document.getElementById('context-menu');
  let currentTarget = null;
  
  // Predefined menu structure
  const menuStructure = {
    file: [
      { label: 'Open', icon: '📂', action: 'open' },
      { label: 'Download', icon: '📥', action: 'download' },
      { label: 'Rename', icon: '✏️', action: 'rename' },
      { type: 'divider' },
      { 
        label: 'Share', 
        icon: '📤',
        submenu: [
          { label: 'Email', icon: '📧', action: 'share-email' },
          { label: 'Link', icon: '🔗', action: 'share-link' }
        ]
      },
      { type: 'divider' },
      { label: 'Delete', icon: '🗑️', action: 'delete', color: '#e53935' }
    ],
    folder: [
      { label: 'Open', icon: '📂', action: 'open' },
      { label: 'New File', icon: '📄', action: 'new-file' },
      { label: 'New Folder', icon: '📁', action: 'new-folder' }
    ]
  };

  // Initialize menu
  function initMenu() {
    document.addEventListener('click', hideMenu);
    document.addEventListener('keydown', handleKeyboard);
    document.addEventListener('contextmenu', (e) => {
      if (e.target.closest('#file-manager')) e.preventDefault();
    });
  }

  // Show context menu
  function showMenu(e, target, type = 'file') {
    currentTarget = target;
    const items = menuStructure[type] || [];
    
    // Build menu items
    const menuHTML = items.map(item => {
      if (item.type === 'divider') return '<li class="menu-divider"></li>';
      
      return `
        <li class="menu-item" 
            tabindex="0" 
            role="menuitem"
            data-action="${item.action || ''}"
            ${item.disabled ? 'disabled' : ''}
            style="${item.color ? `color: ${item.color}` : ''}">
          <span class="icon">${item.icon}</span>
          ${item.label}
          ${item.submenu ? '<span class="submenu-arrow">▶</span>' : ''}
          ${item.submenu ? `
            <ul class="submenu">
              ${item.submenu.map(subItem => `
                <li class="menu-item" 
                    tabindex="0" 
                    role="menuitem"
                    data-action="${subItem.action || ''}">
                  <span class="icon">${subItem.icon}</span>
                  ${subItem.label}
                </li>
              `).join('')}
            </ul>
          ` : ''}
        </li>
      `;
    }).join('');

    menu.querySelector('.menu-items').innerHTML = menuHTML;
    menu.querySelector('.file-name').textContent = target.dataset.name || 'Item';
    
    positionMenu(e);
    menu.classList.add('show');
    menu.setAttribute('aria-hidden', 'false');
    
    // Focus first item
    const firstItem = menu.querySelector('.menu-item');
    firstItem && firstItem.focus();
  }

  // Position menu with boundary checks
  function positionMenu(e) {
    const { clientX: x, clientY: y } = e;
    const { innerWidth, innerHeight } = window;
    const menuRect = menu.getBoundingClientRect();
    
    let posX = x;
    let posY = y;
    
    if (x + menuRect.width > innerWidth) posX = innerWidth - menuRect.width - 10;
    if (y + menuRect.height > innerHeight) posY = innerHeight - menuRect.height - 10;
    
    menu.style.left = `${posX}px`;
    menu.style.top = `${posY}px`;
  }

  // Hide menu
  function hideMenu() {
    menu.classList.remove('show');
    menu.setAttribute('aria-hidden', 'true');
    currentTarget = null;
  }

  // Handle keyboard navigation
  function handleKeyboard(e) {
    if (!menu.classList.contains('show')) return;
    
    const items = [...menu.querySelectorAll('.menu-item')];
    const currentIndex = items.indexOf(document.activeElement);
    
    switch(e.key) {
      case 'ArrowDown':
        e.preventDefault();
        focusItem(currentIndex + 1);
        break;
      case 'ArrowUp':
        e.preventDefault();
        focusItem(currentIndex - 1);
        break;
      case 'Enter':
        e.preventDefault();
        document.activeElement.click();
        break;
      case 'Escape':
        hideMenu();
        break;
    }
  }

  function focusItem(index) {
    const items = [...menu.querySelectorAll('.menu-item')];
    const newIndex = Math.max(0, Math.min(index, items.length - 1));
    items[newIndex].focus();
  }

  // Event delegation for menu actions
  menu.addEventListener('click', (e) => {
    const menuItem = e.target.closest('.menu-item');
    if (!menuItem) return;
    
    const action = menuItem.dataset.action;
    if (action) {
      console.log('Action:', action, 'on', currentTarget);
      // Dispatch custom event
      document.dispatchEvent(new CustomEvent('context-action', {
        detail: { action, target: currentTarget }
      }));
    }
    
    hideMenu();
  });

  document.addEventListener('context-action', (e) => {
  console.log('Context action:', e.detail.action);
  console.log('Target element:', e.detail.target);
});

document.querySelectorAll('.file-item').forEach(item => {
  item.addEventListener('contextmenu', (e) => {
    e.preventDefault();
    ContextMenu.show(e, item, item.dataset.type || 'file');
  });
});

let pressTimer;
document.querySelectorAll('.file-item').forEach(item => {
  item.addEventListener('touchstart', (e) => {
    pressTimer = setTimeout(() => {
      ContextMenu.show(e, item, item.dataset.type || 'file');
    }, 500);
  });
  
  item.addEventListener('touchend', () => clearTimeout(pressTimer));
});

  // Close button
  menu.querySelector('.close-btn').addEventListener('click', hideMenu);

  // Public API
  window.ContextMenu = {
    show: showMenu,
    hide: hideMenu,
    init: initMenu
  };

  // Initialize on DOM ready
  document.addEventListener('DOMContentLoaded', initMenu);
})();
</script>
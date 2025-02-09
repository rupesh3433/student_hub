<?php
// file_preview.php - Professional file preview section

// Simulated file data; in a real application, these could be retrieved from a database or filesystem.
$files = [
    [
        'name' => 'Document1.pdf',
        'size' => '2.5 MB',
        'type' => 'pdf',
        'modified' => '2023-01-10'
    ],
    [
        'name' => 'Presentation.pptx',
        'size' => '5.1 MB',
        'type' => 'ppt',
        'modified' => '2023-02-14'
    ],
    [
        'name' => 'Image1.jpg',
        'size' => '1.2 MB',
        'type' => 'image',
        'modified' => '2023-03-05'
    ],
    [
        'name' => 'Spreadsheet.xlsx',
        'size' => '3.8 MB',
        'type' => 'xls',
        'modified' => '2023-04-21'
    ],
    [
      'name' => 'Document1.pdf',
      'size' => '2.5 MB',
      'type' => 'pdf',
      'modified' => '2023-01-10'
  ],
  [
      'name' => 'Presentation.pptx',
      'size' => '5.1 MB',
      'type' => 'ppt',
      'modified' => '2023-02-14'
  ],
  [
      'name' => 'Image1.jpg',
      'size' => '1.2 MB',
      'type' => 'image',
      'modified' => '2023-03-05'
  ],
  [
      'name' => 'Spreadsheet.xlsx',
      'size' => '3.8 MB',
      'type' => 'xls',
      'modified' => '2023-04-21'
  ],
  [
    'name' => 'Document1.pdf',
    'size' => '2.5 MB',
    'type' => 'pdf',
    'modified' => '2023-01-10'
],
[
    'name' => 'Presentation.pptx',
    'size' => '5.1 MB',
    'type' => 'ppt',
    'modified' => '2023-02-14'
],
[
    'name' => 'Image1.jpg',
    'size' => '1.2 MB',
    'type' => 'image',
    'modified' => '2023-03-05'
],
[
    'name' => 'Spreadsheet.xlsx',
    'size' => '3.8 MB',
    'type' => 'xls',
    'modified' => '2023-04-21'
],
[
  'name' => 'Document1.pdf',
  'size' => '2.5 MB',
  'type' => 'pdf',
  'modified' => '2023-01-10'
],
[
  'name' => 'Presentation.pptx',
  'size' => '5.1 MB',
  'type' => 'ppt',
  'modified' => '2023-02-14'
],
[
  'name' => 'Image1.jpg',
  'size' => '1.2 MB',
  'type' => 'image',
  'modified' => '2023-03-05'
],
[
  'name' => 'Spreadsheet.xlsx',
  'size' => '3.8 MB',
  'type' => 'xls',
  'modified' => '2023-04-21'
],
[
  'name' => 'Document1.pdf',
  'size' => '2.5 MB',
  'type' => 'pdf',
  'modified' => '2023-01-10'
],
[
  'name' => 'Presentation.pptx',
  'size' => '5.1 MB',
  'type' => 'ppt',
  'modified' => '2023-02-14'
],
[
  'name' => 'Image1.jpg',
  'size' => '1.2 MB',
  'type' => 'image',
  'modified' => '2023-03-05'
],
[
  'name' => 'Spreadsheet.xlsx',
  'size' => '3.8 MB',
  'type' => 'xls',
  'modified' => '2023-04-21'
],
];
?>

<div class="file-preview">
  <?php if (!empty($files)): ?>
  <div class="file-grid">
    <?php foreach ($files as $file): ?>
      <div class="file-card">
        <div class="file-icon">
          <?php 
          // Choose an icon based on file type.
          switch ($file['type']) {
              case 'pdf':
                  $icon = '📄'; // Replace with an icon from your icon library if desired.
                  break;
              case 'ppt':
                  $icon = '📊';
                  break;
              case 'image':
                  $icon = '🖼️';
                  break;
              case 'xls':
                  $icon = '📈';
                  break;
              default:
                  $icon = '📁';
                  break;
          }
          echo "<span class='icon'>{$icon}</span>";
          ?>
        </div>
        <div class="file-info">
          <h3 class="file-name"><?php echo htmlspecialchars($file['name']); ?></h3>
          <p class="file-size"><?php echo htmlspecialchars($file['size']); ?></p>
          <p class="file-modified">Modified: <?php echo htmlspecialchars($file['modified']); ?></p>
        </div>
        <div class="file-actions">
          <button class="btn btn-view">View</button>
          <button class="btn btn-download">Download</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p>No files available.</p>
  <?php endif; ?>
</div>

<style>
/* ===== FILE PREVIEW STYLES ===== */
.file-preview {
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 6px;
}

.file-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.file-card {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.file-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.file-icon {
  font-size: 3rem;
  text-align: center;
  margin-bottom: 10px;
}

.file-info {
  text-align: center;
  margin-bottom: 10px;
}

.file-name {
  font-size: 1.1rem;
  margin: 5px 0;
  color: #233554;
}

.file-size,
.file-modified {
  font-size: 0.85rem;
  color: #666;
  margin: 2px 0;
}

.file-actions {
  display: flex;
  justify-content: space-around;
  margin-top: auto;
}

.btn {
  padding: 6px 10px;
  font-size: 0.85rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-view {
  background-color: #64ffda;
  color: #020c1b;
}

.btn-view:hover {
  background-color: #52d3b0;
}

.btn-download {
  background-color: #233554;
  color: #fff;
}

.btn-download:hover {
  background-color: #1b2a3a;
}

/* ===== RESPONSIVE ADJUSTMENTS ===== */
@media (max-width: 768px) {
  .file-grid {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  }
}

@media (max-width: 480px) {
  .file-card {
    padding: 10px;
  }
  .file-icon {
    font-size: 2.5rem;
  }
  .btn {
    padding: 4px 8px;
    font-size: 0.8rem;
  }
}
</style>

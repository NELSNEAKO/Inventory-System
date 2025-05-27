<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory System</title>
  <link rel="stylesheet" href="tabs/inventory.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="../js/inventory.js"></script>
</head>
<body>

<div class="tab-content active" id="inventory">

  <!-- Inventory Section -->
  <div class="inventory-container">
    <div class="inventory-grid">
      <!-- Left Column: Inventory List -->
      <div class="inventory-list">
        <div class="list-header">
          <h3>Inventory List</h3>
          <button class="add-btn" onclick="showAddItemForm()">+ Add Item</button>
        </div>
        <div class="inventory-table-container">
          <table class="inventory-table">
            <thead>
              <tr>
                <th class="product-col">Product</th>
                <th class="quantity-col">Inventory</th>
                <th class="actions-col">Actions</th>
              </tr>
            </thead>
            <tbody id="inventoryTableBody">
              <!-- Inventory items will be dynamically inserted here -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right Column: Add Item Form -->
      <div class="add-item-form" id="addItemForm">
        <!-- <form id="addItem" method="POST" action="php/upload.php" enctype="multipart/form-data"> -->
        <form id="addItem" method="POST" enctype="multipart/form-data">

          <div class="form-header">
            <h3>Add New Item</h3>
            <button type="button" class="close-btn" onclick="hideAddItemForm()">&times;</button>
          </div>
          <label for="name">Item Name:</label>
          <input type="text" name="name" required>

          <label for="quantity">Quantity:</label>
          <input type="number" name="quantity" required>

          <label for="image">Upload Image:</label>
          <input type="file" name="image" id="imageInput" accept="image/*" required onchange="previewImage(this)">
          <div class="image-preview-container">
            <img id="imagePreview" src="#" alt="Preview" style="display: none; max-width: 100%; margin-top: 10px; border-radius: 6px;">
          </div>

          <div class="form-buttons">
            <button type="submit">Add Item</button>
            <button type="button" onclick="hideAddItemForm()">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

</body>
</html>

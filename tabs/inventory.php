<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory System</title>
  <link rel="stylesheet" href="tabs/inventory.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="tab-content active" id="inventory">

  <div class="header-bar">
    <div class="header-left">
      <div class="header-title">AnalyticsBot</div>
      <div class="header-search">
        <input type="text" placeholder="Search..." />
        <i class="fas fa-search"></i>
      </div>
    </div>

    <div class="header-icons">
      <i class="fas fa-bell"></i>
      <i class="fas fa-envelope"></i>
      <i class="fas fa-cog"></i>
    </div>

    <div class="header-profile">
      <img src="https://i.pravatar.cc/40?img=1" alt="Profile" />
    </div>
  </div>

  <!-- Inventory Section -->
  <div class="inventory-container">
    <button class="add-btn" onclick="showAddItemForm()">+ Add Item</button>
    <table class="inventory-table">
      <thead>
        <tr>
          <th class="product-col">Product</th>
          <th class="quantity-col">Inventory</th>
        </tr>
      </thead>
      <tbody id="inventoryTableBody">
        <!-- Inventory items will be dynamically inserted here -->
      </tbody>
    </table>
  </div>

  <!-- Add Item Modal -->
  <div class="add-item-form" id="addItemForm">
    <form id="addItem" method="POST" action="upload.php" enctype="multipart/form-data">
      <label for="name">Item Name:</label>
      <input type="text" name="name" required>

      <label for="quantity">Quantity:</label>
      <input type="number" name="quantity" required>

      <label for="image">Upload Image:</label>
      <input type="file" name="image" accept="image/*" required>

      <button type="submit">Add Item</button>
    </form>
  </div>

</div>

<script src="../script.js">
  console.log("✅ inventory.php loaded into DOM");
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="tabs/dashboard.css">
  </head>
  <body>
    <div class="tab-content active" id="dashboard">
      <div class="main-content">
        <!-- Container 1: Overview -->
        <div class="dashboard-container">
          <h2>Inventory Overview</h2>
          <div class="overview-cards">
            <div class="card" id="total-items-card">
              <i class="fas fa-boxes"></i>
              <div>
                <h4>Total Items</h4>
                <p><span id="total-items">0</span> unique items</p>
              </div>
            </div>
            <div class="card" id="total-quantity-card">
              <i class="fas fa-layer-group"></i>
              <div>
                <h4>Total Quantity</h4>
                <p><span id="total-quantity">0</span> units in stock</p>
              </div>
            </div>
            <div class="card" id="low-stock-card">
              <i class="fas fa-exclamation-triangle"></i>
              <div>
                <h4>Low Stock Items</h4>
                <p><span id="low-stock">0</span> items need attention</p>
              </div>
            </div>
            <div class="card" id="recent-items-card">
              <i class="fas fa-clock"></i>
              <div>
                <h4>Recent Additions</h4>
                <p><span id="recent-items">0</span> items this week</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Stock Distribution Chart -->
        <div class="dashboard-container">
          <h2>Stock Distribution</h2>
          <canvas id="stockChart" height="200"></canvas>
        </div>

        <!-- Top Items -->
        <section class="dashboard-section">
          <h2 id="top-items-title">Top Items by Quantity</h2>
          <div class="top-items-container" id="topItemsContainer">
            <!-- Top items will be loaded here by JavaScript -->
          </div>
        </section>

        <!-- Recent Activity -->
        <div class="dashboard-container">
          <h2>Recent Activity</h2>
          <div class="activity-list" id="activityList">
            <!-- Activity items will be dynamically inserted here -->
          </div>
        </div>
      </div>
    </div>

    <script>
      function loadDashboardData() {
        // Fetch inventory statistics
        $.ajax({
          url: "../php/fetch_inventory_stats.php",
          method: "GET",
          dataType: "json",
          success: function(data) {
            // Update overview cards
            $("#total-items").text(data.total_items);
            $("#total-quantity").text(data.total_quantity);
            $("#low-stock").text(data.low_stock);
            $("#recent-items").text(data.recent_items);

            // Update top items
            const container = $("#topItemsContainer");
            container.empty();
            data.top_items.forEach(item => {
              const card = `
                <div class="item-card">
                  <div class="item-image">
                    <img src="${item.image || '../images/default-item.png'}" alt="${item.name}">
                  </div>
                  <div class="item-info">
                    <strong>${item.name}</strong>
                    <p>Quantity: ${item.quantity}</p>
                  </div>
                </div>
              `;
              container.append(card);
            });

            // Create stock distribution chart
            const ctx = document.getElementById('stockChart').getContext('2d');
            new Chart(ctx, {
              type: 'doughnut',
              data: {
                labels: ['In Stock', 'Low Stock'],
                datasets: [{
                  data: [data.total_items - data.low_stock, data.low_stock],
                  backgroundColor: ['#4dd0e1', '#ff9800']
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {
                    position: 'bottom'
                  }
                }
              }
            });
          },
          error: function(err) {
            console.error("Error loading dashboard data:", err);
          }
        });
      }

      // Load dashboard data when the page loads
      $(document).ready(function() {
        loadDashboardData();
      });
    </script>
  </body>
</html>
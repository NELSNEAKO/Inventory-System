<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
    <div class="tab-content active" id="dashboard">
          <!-- <div class="header-bar">
            <div class="header-left">
              <div class="header-title">Dashboard</div>
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
          </div> -->

          <!-- Other dashboard content goes here -->

          <div class="main-content">
            <!-- Container 1: Overview -->
            <div class="dashboard-container">
              <h2>Overview</h2>
              <div class="overview-cards">
                <div class="card" id="revenue-card">
                  <i class="fas fa-dollar-sign"></i>
                  <div>
                    <h4>Total Revenue</h4>
                    <p>This Week: ₱<span id="revenue-week">0</span></p>
                    <p>Last Week: ₱<span id="revenue-last">0</span></p>
                  </div>
                </div>
                <div class="card" id="profit-card">
                  <i class="fas fa-coins"></i>
                  <div>
                    <h4>Net Profit</h4>
                    <p>This Week: ₱<span id="profit-week">0</span></p>
                    <p>Last Week: ₱<span id="profit-last">0</span></p>
                  </div>
                </div>
                <div class="card" id="sold-card">
                  <i class="fas fa-shopping-cart"></i>
                  <div>
                    <h4>Items Sold</h4>
                    <p>This Week: <span id="sold-week">0</span></p>
                    <p>Last Week: <span id="sold-last">0</span></p>
                  </div>
                </div>
                <div class="card" id="growth-card">
                  <i class="fas fa-chart-line"></i>
                  <div>
                    <h4>Growth</h4>
                    <p><span id="growth-percent">0%</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div class="dashboard-container">
              <div class="attendace-report"> 
                <h2>Attendance Report</h2>
              </div>
              <div class="label-wrapper">
                <div class="weekday-labels">
                  <div>S</div>
                  <div>M</div>
                  <div>T</div>
                  <div>W</div>
                  <div>T</div>
                  <div>F</div>
                  <div>S</div>
                </div>
              </div>
              <div class="heatmap-wrapper">
                <div class="heatmap" id="heatmap-grid"></div>
              </div>
            </div>

            <!-- Revenue Report -->
            <div class="dashboard-container" id="revenue-report">
              <h2>Revenue Report (Last 7 Days)</h2>
              <canvas id="revenueChart" height="150"></canvas>
            </div>

            <!-- Top Selling Items -->
            <div class="dashboard-container" id="top-items">
              <h2>Most Popular Items</h2>
              <div class="top-items-container" id="topItemsContainer"></div>
            </div>
          </div>
    </div>
    <script src="../script.js">
    </script>
  </body>
</html>
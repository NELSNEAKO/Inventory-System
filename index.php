<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // adjust path if login.php is in /php/
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="styles.css" />
    <!-- Font Awesome CDN -->
    <script
      src="https://kit.fontawesome.com/fada9cdcb6.js"
      crossorigin="anonymous"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="container">
      <!-- SIDEBAR -->
      <nav class="sidebar">
        <div class="logo" id="tcc-logo">
          <img src="images/TCC.png" alt="Logo" width="100px" height="100px"/>
        </div>
        <ul>
          <br>
          <!-- <h3>NAVIGATION CENTER TEST</h3> -->
          <li class="tab-btn active" data-tab="dashboard">
            <i class="fas fa-tachometer-alt"></i> Dashboard
          </li>
          <!-- <li class="tab-btn" data-tab="analytics">
            <i class="fas fa-chart-line"></i> Analytics
          </li> -->
          <li class="tab-btn" data-tab="inventory">
            <i class="fas fa-boxes"></i> Inventory
          </li>
          <!-- <li class="tab-btn" data-tab="users">
            <i class="fas fa-user-friends"></i> Users
          </li> -->
          <li class="tab-btn" data-tab="settings">
            <i class="fas fa-cogs"></i> Settings</li>
          <li class="logout" onClick= "window.location.href='php/logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
          </li>
        </ul>
      </nav>

      <main class="content">
        
        <div id="tab-content-container">
          
        </div>
      </main>
    </div>
    <script src="js/script.js"></script>
  </body>
</html>

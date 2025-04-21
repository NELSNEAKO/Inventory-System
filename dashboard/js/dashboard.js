function loadDashboardData() {
    fetchOverview();
    fetchAttendance();
    fetchRevenue();
    fetchTopItems()
  }
  
  // ✅ Overview data
  function fetchOverview() {
    $.ajax({
      url: "php/fetch_overview.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#revenue-week").text(data.revenue_week);
        $("#revenue-last").text(data.revenue_last);
        $("#profit-week").text(data.profit_week);
        $("#profit-last").text(data.profit_last);
        $("#sold-week").text(data.sold_week);
        $("#sold-last").text(data.sold_last);
        $("#growth-percent").text(data.growth_percent + "%");
      },
      error: function (err) {
        console.error("Error fetching overview data:", err);
      },
    });
  }
  
  // ✅ Attendance Heatmap
  function fetchAttendance() {
    $.ajax({
      url: "php/fetch_attendance.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        const heatmap = $("#heatmap-grid");
        heatmap.empty(); // Clear previous data if tab is reloaded
  
        const maxCount = Math.max(
          ...Object.values(data).map((row) =>
            Math.max(...Object.values(row))
          )
        );
  
        const dayOrder = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
  
        dayOrder.forEach((day) => {
          for (let hour = 0; hour < 24; hour++) {
            const count =
              data[day] && data[day][hour] ? data[day][hour] : 0;
            const intensity = Math.ceil((count / maxCount) * 4); // 1 to 4 levels
            const div = $("<div></div>")
              .addClass("heatmap-square")
              .addClass(`level-${intensity || 1}`)
              .attr("title", `${day} @ ${hour}:00 → ${count} sale(s)`);
            heatmap.append(div);
          }
        });
      },
      error: function (err) {
        console.error("Heatmap load error:", err);
      },
    });
  }
  
  // ✅ Revenue chart
  function fetchRevenue() {
    $.ajax({
      url: "php/fetch_revenue.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        const canvas = document.getElementById("revenueChart");
        if (!canvas) return;
  
        const ctx = canvas.getContext("2d");
  
        // if (window.revenueChart) {
        //   window.revenueChart.destroy();
        // }
  
        window.revenueChart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: data.labels,
            datasets: [{
              label: "Revenue (₱)",
              data: data.revenue,
              backgroundColor: "#4dd0e1",
              borderRadius: 5,
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: { ticks: { color: "#fff" } },
              x: { ticks: { color: "#fff" } }
            }
          }
        });
      },
      error: function (err) {
        console.error("Chart load error:", err);
      }
    });
  }
  
  // ✅ Top-selling Items
function fetchTopItems() {
    $.ajax({
      url: "php/fetch_top_items.php",
      method: "GET",
      dataType: "json",
      success: function (items) {
        const container = $("#topItemsContainer");
        container.empty(); // Clear if reloaded
  
        items.forEach((item) => {
          const card = `
            <div class="item-card">
              <div class="item-bar"></div>
              <strong>${item.name}</strong>
              <p>Sold: ${item.total_qty}</p>
              <p>Revenue: ₱${item.total_revenue}</p>
            </div>
          `;
          container.append(card);
        });
      },
      error: function (err) {
        console.error("Top items load error:", err);
      },
    });
  }
function loadDashboardData() {
    fetchInventoryStats();
    fetchRecentActivity();
}

// Fetch all inventory statistics
function fetchInventoryStats() {
    $.ajax({
        url: "php/fetch_inventory_stats.php",
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
            if (window.stockChart) {
                window.stockChart.destroy();
            }
            window.stockChart = new Chart(ctx, {
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
                            position: 'bottom',
                            labels: {
                                color: '#fff'
                            }
                        }
                    }
                }
            });
        },
        error: function(err) {
            console.error("Error loading inventory stats:", err);
        }
    });
}

// Fetch recent activity
function fetchRecentActivity() {
    $.ajax({
        url: "php/fetch_recent_activity.php",
        method: "GET",
        dataType: "json",
        success: function(data) {
            const container = $("#activityList");
            container.empty();

            if (data.activities && data.activities.length > 0) {
                data.activities.forEach(activity => {
                    const activityItem = `
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas ${getActivityIcon(activity.type)}"></i>
                            </div>
                            <div class="activity-details">
                                <p>${activity.description}</p>
                                <span class="activity-time">${activity.time}</span>
                            </div>
                        </div>
                    `;
                    container.append(activityItem);
                });
            } else {
                container.html('<p class="no-activity">No recent activity</p>');
            }
        },
        error: function(err) {
            console.error("Error loading recent activity:", err);
        }
    });
}

// Helper function to get appropriate icon for activity type
function getActivityIcon(type) {
    const icons = {
        'add': 'fa-plus-circle',
        'update': 'fa-edit',
        'delete': 'fa-trash',
        'low_stock': 'fa-exclamation-triangle',
        'default': 'fa-info-circle'
    };
    return icons[type] || icons.default;
}

// Initialize dashboard when document is ready
$(document).ready(function() {
    loadDashboardData();
    
    // Refresh dashboard data every 5 minutes
    setInterval(loadDashboardData, 300000);
});
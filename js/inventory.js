// Fetch inventory items when the page loads
function fetchInventoryItems() {
    $.ajax({
        url: 'php/inventory.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            displayInventoryItems(data);
        },
        error: function(error) {
            console.error('Error fetching inventory:', error);
        }
    });
}


// Display fetched inventory items in the page
function displayInventoryItems(items) {
    const inventoryContainer = $('#inventoryItems');
    inventoryContainer.empty(); // Clear the container before adding items

    items.forEach(item => {
        const itemCard = `
            <div class="item-card">
                <img src="${item.image}" alt="${item.name}">
                <h3>${item.name}</h3>
                <p>Quantity: ${item.quantity}</p>
            </div>
        `;
        inventoryContainer.append(itemCard);
    });
}

// Show the form to add a new item
function showAddItemForm() {
    $('#addItemForm').show();
}

// Hide the add item form
function hideAddItemForm() {
    $('#addItemForm').hide();
}

// Handle form submission with image upload
document.getElementById('addItem').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = document.getElementById('addItem');
    const formData = new FormData(form);

    $.ajax({
        url: 'php/upload.php', // your PHP handler
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log('Upload successful:', response);
            // Reload or fetch updated inventory list
            fetchInventoryItems();
            form.reset();
            hideAddItemForm();
        },
        error: function (xhr, status, error) {
            console.error('Upload error:', error);
            alert("Failed to add item.");
        }
    });
});

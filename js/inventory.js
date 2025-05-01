// Fetch inventory items when the page loads
function fetchInventoryItems() {
    console.log("✅ loaded");

    $.ajax({
        url: 'php/inventory.php',
        method: 'GET',
        dataType: 'json',   
        success: function(data) {
            console.log("Fetched Data:", data);  // Check data in console
            displayInventoryItems(data);
        },
        error: function(error) {
            console.error('Error fetching inventory:', error);
        }
    });
}


// Display fetched inventory items in the page
function displayInventoryItems(items) {
    const tableBody = $('#inventoryTableBody');  // Get the table body element
    tableBody.empty();  // Clear any existing content in the table body

    items.forEach(item => {
        const row = `
            <tr class="inventory-row">
                <td class="product-col">
                    <div class="product-info">
                        <img src="${item.image}" alt="${item.name}" class="product-img" />
                        <div class="product-details">
                            <span class="product-name">${item.name}</span>
                            <span class="product-tag">Product</span>
                        </div>
                    </div>
                </td>
                <td class="quantity-col">
                    <span class="stock">${item.quantity}</span> Stock
                </td>
            </tr>
        `;
        tableBody.append(row);  // Add the new row to the table body
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

document.getElementById('addItem').addEventListener('submit', function (e) {
    e.preventDefault();  // Prevent default form submission

    const form = document.getElementById('addItem');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Disable the submit button to prevent multiple submissions
    submitButton.disabled = true;

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
            submitButton.disabled = false;  // Re-enable the submit button after success
        },
        error: function (xhr, status, error) {
            console.error('Upload error:', error);
            alert("Failed to add item.");
            submitButton.disabled = false;  // Re-enable the submit button if there's an error
        }
    });
});


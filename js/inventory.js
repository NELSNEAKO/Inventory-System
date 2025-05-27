// Cache DOM elements
const inventoryTableBody = $('#inventoryTableBody');
const addItemForm = document.getElementById('addItem');
const imagePreview = document.getElementById('imagePreview');

// Initialize inventory
function initializeInventory() {
    fetchInventoryItems();
}

// Call initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeInventory);

// Make initializeInventory available globally
window.initializeInventory = initializeInventory;

// Fetch inventory items when the page loads
function fetchInventoryItems() {
    $.ajax({
        url: 'php/inventory.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            displayInventoryItems(data);
            window.inventoryData = data;
        },
        error: function(error) {
            console.error('Error fetching inventory:', error);
            showNotification('Error loading inventory', 'error');
        }
    });
}


// Display inventory items with optimized rendering
function displayInventoryItems(items) {
    const fragment = document.createDocumentFragment();
    
    items.forEach(item => {
        const row = document.createElement('tr');
        row.className = 'inventory-row';
        row.dataset.id = item.id;
        
        row.innerHTML = `
            <td class="product-col">
                <div class="product-info">
                    <img src="${item.image}" alt="${item.name}" class="product-img" loading="lazy" />
                    <div class="product-details">
                        <span class="product-name">${item.name}</span>
                        <span class="product-tag">Product</span>
                    </div>
                </div>
            </td>
            <td class="quantity-col">
                <span class="stock">${item.quantity}</span> Stock
            </td>
            <td class="actions-col">
                <button class="action-btn edit-btn" onclick="editItem(${item.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" onclick="deleteItem(${item.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        fragment.appendChild(row);
    });
    
    inventoryTableBody.empty().append(fragment);
}

// Handle form submission with optimized image processing
// ✅ This is your form handler function
$(document).ready(function() {
    $('#addItem').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        const form = $(this);
        const formData = new FormData(this);  // Use FormData to handle form data including the file input

        // Disable the submit button and show a loading state
        const submitButton = form.find('button[type="submit"]');
        submitButton.prop('disabled', true);
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        // Use $.ajax() to submit the form data via POST
        $.ajax({
            url: 'php/upload.php', // PHP file that will handle the upload
            type: 'POST',
            data: formData,
            processData: false, // Don't process the data as a query string
            contentType: false, // Let jQuery set the content type
            success: function(response) {
                if (response.success) {
                    // Show success message and reset the form
                    alert(response.message); // Example: Show an alert with the success message
                    fetchInventoryItems(); // Refresh the inventory list
                    resetForm();
                } else {
                    // Show error message
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error submitting form:', error);
                alert('There was an error with the form submission.');
            },
            complete: function() {
                // Re-enable the submit button after the request is complete
                submitButton.prop('disabled', false);
                submitButton.html('Add Item');
            }
        });
    });
});



// Optimize image before upload
async function optimizeImage(imageFile, formData) {
    if (imageFile.size <= 1000000) return; // Skip if under 1MB

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Calculate dimensions
                const maxDimension = 800;
                let { width, height } = img;
                
                if (width > height && width > maxDimension) {
                    height = Math.round((height * maxDimension) / width);
                    width = maxDimension;
                } else if (height > maxDimension) {
                    width = Math.round((width * maxDimension) / height);
                    height = maxDimension;
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                // Convert to blob with compression
                canvas.toBlob(
                    blob => {
                        formData.set('image', blob, imageFile.name);
                        resolve();
                    },
                    imageFile.type,
                    0.85
                );
            };
            img.onerror = reject;
            img.src = e.target.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(imageFile);
    });
}

// Reset form and preview
function resetForm() {
    addItemForm.reset();
    imagePreview.style.display = 'none';
    imagePreview.src = '#';
    // hideAddItemForm(); optional if you want to hide or not
}

// Show notification with better styling


// Show the form to add a new item
function showAddItemForm() {
    const form = document.getElementById('addItemForm');
    form.style.display = 'block';
    form.classList.add('active');
}

// Hide the add item form
function hideAddItemForm() {
    const form = document.getElementById('addItem');
    form.reset();
    
    // Remove hidden ID input if it exists
    const idInput = form.querySelector('input[name="id"]');
    if (idInput) idInput.remove();
    
    // Reset button text
    form.querySelector('button[type="submit"]').textContent = 'Add Item';
    
    // Clear image preview
    const preview = document.getElementById('imagePreview');
    preview.style.display = 'none';
    preview.src = '#';
    
    // Hide form
    const formContainer = document.getElementById('addItemForm');
    formContainer.style.display = 'none';
    formContainer.classList.remove('active');
}

// Function to preview selected image
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        preview.src = '#';
    }
}

// Function to edit an item
function editItem(id) {
    // Fetch item details
    $.ajax({
        url: 'php/inventory.php',
        method: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(item) {
            showEditForm(item);
        },
        error: function(error) {
            console.error('Error fetching item:', error);
            alert('Failed to fetch item details');
        }
    });
}

// Function to show edit form
function showEditForm(item) {
    const form = document.getElementById('addItem');
    form.querySelector('[name="name"]').value = item.name;
    form.querySelector('[name="quantity"]').value = item.quantity;
    
    // Show current image
    const preview = document.getElementById('imagePreview');
    preview.src = item.image;
    preview.style.display = 'block';
    
    // Add hidden input for item ID
    let idInput = form.querySelector('input[name="id"]');
    if (!idInput) {
        idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        form.appendChild(idInput);
    }
    idInput.value = item.id;
    
    // Change button text
    form.querySelector('button[type="submit"]').textContent = 'Update Item';
    
    // Show form
    showAddItemForm();
}

// Function to delete an item
function deleteItem(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        $.ajax({
            url: 'php/delete.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                console.log('Delete successful:', response);
                fetchInventoryItems(); // Refresh the list
            },
            error: function(error) {
                console.error('Delete error:', error);
                alert('Failed to delete item');
            }
        });
    }
}


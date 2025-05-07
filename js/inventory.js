// Cache DOM elements
const inventoryTableBody = $('#inventoryTableBody');
const searchInput = document.querySelector('.header-search input');
const addItemForm = document.getElementById('addItem');
const imagePreview = document.getElementById('imagePreview');

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

// Function to search inventory items with debounce
let searchTimeout;
function searchInventory(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (!window.inventoryData) return;

        const searchTerm = query.toLowerCase().trim();
        const filteredItems = window.inventoryData.filter(item => {
            return item.name.toLowerCase().includes(searchTerm) ||
                   item.quantity.toString().includes(searchTerm);
        });

        displayInventoryItems(filteredItems);
        
        if (filteredItems.length === 0 && searchTerm !== '') {
            inventoryTableBody.append(`
                <tr class="no-results">
                    <td colspan="3" style="text-align: center; padding: 20px;">
                        No items found matching "${query}"
                    </td>
                </tr>
            `);
        }
    }, 300); // 300ms debounce
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            searchInventory(e.target.value);
        });
        
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                searchInventory('');
            }
        });
    }

    // Form submission with optimized image handling
    if (addItemForm) {
        addItemForm.addEventListener('submit', handleFormSubmit);
    }
});

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
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const isUpdate = form.querySelector('input[name="id"]') !== null;
    
    try {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        // Process image if present
        const imageFile = formData.get('image');
        if (imageFile && imageFile.size > 0) {
            await optimizeImage(imageFile, formData);
        }

        const response = await $.ajax({
            url: isUpdate ? 'php/update.php' : 'php/upload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false
        });

        if (response.error) {
            throw new Error(response.error);
        }

        await fetchInventoryItems(); // Refresh the inventory list
        resetForm();
        
    } catch (error) {
        console.error('Form submission error:', error);
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = isUpdate ? 'Update Item' : 'Add Item';
    }
}

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
    hideAddItemForm();
}

// Show notification with better styling
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

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


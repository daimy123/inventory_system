// DOM Elements
const productForm = document.getElementById('product-form');
const productList = document.getElementById('product-list');
const messageDiv = document.getElementById('message');
const loadingDiv = document.getElementById('loading');
const formTitle = document.getElementById('form-title');
const submitBtn = document.getElementById('submit-btn');
const cancelBtn = document.getElementById('cancel-btn');
const productIdInput = document.getElementById('product-id');
const searchInput = document.getElementById('search-input');

// Base URL for API
const API_URL = 'api/products/';

// State variable to track if we're editing or creating
let isEditing = false;

// Event Listeners
document.addEventListener('DOMContentLoaded', fetchProducts);
productForm.addEventListener('submit', handleFormSubmit);
cancelBtn.addEventListener('click', resetForm);
searchInput.addEventListener('input', handleSearch);

// Functions
function showMessage(message, isError = false) {
    messageDiv.textContent = message;
    messageDiv.className = isError ? 'error-message' : 'success-message';
    messageDiv.style.display = 'block';
    
    // Hide message after 3 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 3000);
}

function showLoading(show = true) {
    loadingDiv.style.display = show ? 'block' : 'none';
}

async function fetchProducts() {
    try {
        showLoading(true);
        
        const response = await fetch(`${API_URL}read.php`);
        const data = await response.json();
        
        showLoading(false);
        
        if (data.status === 'success') {
            displayProducts(data.data);
        } else {
            showMessage(data.message || 'Error fetching products', true);
        }
    } catch (error) {
        showLoading(false);
        showMessage('Failed to connect to the server', true);
        console.error('Error:', error);
    }
}

function displayProducts(products) {
    productList.innerHTML = '';
    
    if (products.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="6">No products found</td>';
        productList.appendChild(row);
        return;
    }
    
    products.forEach(product => {
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>${product.id}</td>
            <td>${product.name}</td>
            <td>${product.category}</td>
            <td>${product.quantity}</td>
            <td>$${parseFloat(product.price).toFixed(2)}</td>
            <td>
                <button class="action-btn edit-btn" data-id="${product.id}">Edit</button>
                <button class="action-btn delete-btn" data-id="${product.id}">Delete</button>
            </td>
        `;
        
        productList.appendChild(row);
    });
    
    // Add event listeners to edit and delete buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => editProduct(btn.dataset.id));
    });
    
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => deleteProduct(btn.dataset.id));
    });
}

async function handleFormSubmit(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
        category: document.getElementById('category').value,
        quantity: document.getElementById('quantity').value,
        price: document.getElementById('price').value
    };
    
    // Add ID if editing
    if (isEditing) {
        formData.id = productIdInput.value;
    }
    
    try {
        showLoading(true);
        
        const url = isEditing ? `${API_URL}update.php` : `${API_URL}create.php`;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        showLoading(false);
        
        if (data.status === 'success') {
            showMessage(data.message);
            fetchProducts();
            resetForm();
        } else {
            showMessage(data.message || 'Error processing request', true);
        }
    } catch (error) {
        showLoading(false);
        showMessage('Failed to connect to the server', true);
        console.error('Error:', error);
    }
}

async function editProduct(id) {
    try {
        showLoading(true);
        
        const response = await fetch(`${API_URL}read_one.php?id=${id}`);
        const data = await response.json();
        
        showLoading(false);
        
        if (data.status === 'success') {
            const product = data.data;
            
            // Populate form
            document.getElementById('name').value = product.name;
            document.getElementById('description').value = product.description;
            document.getElementById('category').value = product.category;
            document.getElementById('quantity').value = product.quantity;
            document.getElementById('price').value = product.price;
            productIdInput.value = product.id;
            
            // Change UI
            formTitle.textContent = 'Edit Product';
            submitBtn.textContent = 'Update Product';
            cancelBtn.style.display = 'inline-block';
            
            // Set state
            isEditing = true;
            
            // Scroll to form
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
        } else {
            showMessage(data.message || 'Error fetching product', true);
        }
    } catch (error) {
        showLoading(false);
        showMessage('Failed to connect to the server', true);
        console.error('Error:', error);
    }
}

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }
    
    try {
        showLoading(true);
        
        const response = await fetch(`${API_URL}delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });
        
        const data = await response.json();
        
        showLoading(false);
        
        if (data.status === 'success') {
            showMessage(data.message);
            fetchProducts();
        } else {
            showMessage(data.message || 'Error deleting product', true);
        }
    } catch (error) {
        showLoading(false);
        showMessage('Failed to connect to the server', true);
        console.error('Error:', error);
    }
}
function resetForm() {
    // Reset form fields
    productForm.reset();
    productIdInput.value = '';
    
    // Reset UI
    formTitle.textContent = 'Add New Product';
    submitBtn.textContent = 'Add Product';
    cancelBtn.style.display = 'none';
    
    // Reset state
    isEditing = false;
}

function handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#product-list tr');
    
    rows.forEach(row => {
        const name = row.cells[1]?.textContent.toLowerCase() || '';
        const category = row.cells[2]?.textContent.toLowerCase() || '';
        
        if (name.includes(searchTerm) || category.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
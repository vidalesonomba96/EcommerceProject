// EcommerceProject/src/scripts/script.js

// --- START: Robust CART_HANDLER_URL Definition ---
const getSiteBasePath = () => {
    let path = window.location.pathname;
    const scriptName = 'index.php'; // Or any other script that might be in the path at root level
    const pagesDir = '/src/pages/';

    if (path.includes(scriptName)) {
        // If index.php is in the path, get the path up to and including the directory of index.php
        path = path.substring(0, path.lastIndexOf(scriptName));
    } else if (path.includes(pagesDir)) {
        // If in /src/pages/, go up two levels to the project root
        path = path.substring(0, path.indexOf(pagesDir)) + '/';
    } else {
        // For other cases, try to get the directory of the current path
        // This might need adjustment based on your exact URL structures
        path = path.substring(0, path.lastIndexOf('/') + 1);
    }
    // Ensure it ends with a slash if it's not just "/" and is not empty
    if (path.length > 1 && !path.endsWith('/')) {
        path += '/';
    }
    // If the path is just the filename (e.g. on domain root, path = "/"), ensure it's just "/"
    if (path.split('/').length === 2 && path.startsWith('/') && !path.endsWith('/')) {
       // This case might be for when path is like "/filename" without a trailing slash
       // but usually window.location.pathname for root is just "/" or "/index.php"
    }
    if (path === "" || path === scriptName) path = "/"; // Default to root if path becomes empty

    return path;
};

// Assuming cart_handler.php is always at the project root.
const APP_BASE_PATH = getSiteBasePath();
// Construct the URL. If APP_BASE_PATH is just "/", then it becomes "/cart_handler.php"
// If APP_BASE_PATH is "/MyProject/", it becomes "/MyProject/cart_handler.php"
const CART_HANDLER_URL = `${APP_BASE_PATH}cart_handler.php`.replace(/\/\//g, '/'); // Replace double slashes if any

console.log("Calculated APP_BASE_PATH:", APP_BASE_PATH);
console.log("Attempting to use CART_HANDLER_URL:", CART_HANDLER_URL);
// --- END: Robust CART_HANDLER_URL Definition ---


/**
 * Updates the cart badge(s) in the header with the current item count.
 * @param {object} cartDetails - The cart details object from the server.
 */
function updateCartBadge(cartDetails) {
    const badges = document.querySelectorAll('.cart-badge');
    if (badges.length > 0) {
        const count = cartDetails.totalQuantity || 0;
        badges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    }
}

/**
 * Handles AJAX requests to the cart_handler.php.
 * @param {FormData} formData - The data to send to the cart handler.
 * @returns {Promise<object|null>} - The JSON response from the server or null on error.
 */
async function handleCartAction(formData) {
    console.log(`handleCartAction: Attempting to fetch from URL: ${CART_HANDLER_URL}`); // Log the exact URL
    console.log("Form Data being sent:");
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    try {
        const response = await fetch(CART_HANDLER_URL, {
            method: 'POST',
            body: formData
        });

        console.log(`handleCartAction: Fetch response status: ${response.status}, ok: ${response.ok}`); // Log status

        if (!response.ok) {
            const errorText = await response.text(); // Get raw text for more info
            console.error(`handleCartAction: HTTP error! Status: ${response.status}, Response Text: ${errorText}`);
            let errorDataMessage = `HTTP error! status: ${response.status}`;
            try {
                const errorJson = JSON.parse(errorText); // Try to parse as JSON
                if (errorJson && errorJson.message) {
                    errorDataMessage = errorJson.message;
                }
            } catch (e) {
                // Not JSON, or JSON parsing failed, use raw text if not too long
                if (errorText.length < 200) { // Avoid logging huge HTML error pages
                    errorDataMessage = errorText || errorDataMessage;
                }
            }
            throw new Error(errorDataMessage);
        }

        const result = await response.json();
        console.log("handleCartAction: JSON result from server:", result);

        if (result.success && result.cartDetails) {
            updateCartBadge(result.cartDetails);
            if (result.message && (formData.get('action') === 'add' || formData.get('action') === 'update' || formData.get('action') === 'remove')) {
                let iconType = 'success';
                if (result.status && (result.status === 'error' || result.status === 'warning')) {
                    iconType = result.status;
                }
                showSwalNotification(null, result.message, iconType, true);
            }
            return result;
        } else {
            console.error('handleCartAction: Cart action failed on server -', result.message || 'Unknown server error. Response:', result);
            showSwalNotification('Error', result.message || 'An unknown error occurred with the cart.', 'error');
            return null;
        }
    } catch (error) {
        console.error('handleCartAction: Failed to perform cart action (fetch/network or JSON parsing error) -', error.message, error);
        showSwalNotification('Connection Error', `Could not connect or process response: ${error.message}`, 'error');
        return null;
    }
}

/**
 * Initializes event listeners for "Add to Cart" buttons.
 */
function initializeAddToCartButtons() {
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    addToCartButtons.forEach(button => {
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        newButton.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;
            let productImage = this.dataset.productImage;
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('product_name', productName);
            formData.append('product_price', productPrice);
            formData.append('product_image', productImage);
            formData.append('quantity', 1);
            await handleCartAction(formData);
        });
    });
}

/**
 * Fetches the current cart state and updates the badge.
 */
async function refreshCartBadge() {
    console.log("refreshCartBadge called.");
    const formData = new FormData();
    formData.append('action', 'get');
    await handleCartAction(formData);
}

// --- START: Updated SweetAlert2 Helper Function ---
function showSwalNotification(title, text, icon, toast = false, position = 'top-end', timer = 3500) {
    if (toast) {
        Swal.fire({
            toast: true,
            position: position,
            icon: icon,
            title: text,
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            showClass: {
                popup: 'animate__animated animate__fadeInRight' // Modern slide-in animation
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutRight' // Matching slide-out animation
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    } else {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
        });
    }
}
// --- END: Updated SweetAlert2 Helper Function ---

// --- Dark Mode & Sidebar Logic ---
const body = document.body;
function updateToggleIcons(isDark) {
    const iconClass = isDark ? 'fa-moon' : 'fa-sun';
    document.querySelectorAll('.dark-mode-toggle-btn').forEach(btn => {
        const icon = btn.querySelector('i');
        if (icon) {
            icon.classList.remove('fa-sun', 'fa-moon');
            icon.classList.add(iconClass);
        }
    });
}
function applyDarkMode(isDark) {
    if (isDark) { body.classList.add('dark-mode'); localStorage.setItem('darkMode', 'enabled'); }
    else { body.classList.remove('dark-mode'); localStorage.setItem('darkMode', 'disabled'); }
    updateToggleIcons(isDark);
}
(function() {
    const savedMode = localStorage.getItem('darkMode');
    if (savedMode === 'enabled' && body) { body.classList.add('dark-mode'); }
})();

function initializeMainFunctionality() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    function openSidebar() { if (sidebar && sidebarOverlay) { sidebar.classList.add('sidebar-open'); sidebarOverlay.classList.add('sidebar-overlay-visible'); document.body.classList.add('overflow-hidden-sidebar'); } }
    function closeSidebar() { if (sidebar && sidebarOverlay) { sidebar.classList.remove('sidebar-open'); sidebarOverlay.classList.remove('sidebar-overlay-visible'); document.body.classList.remove('overflow-hidden-sidebar'); } }
    if (menuToggle) { const newMenuToggle = menuToggle.cloneNode(true); menuToggle.parentNode.replaceChild(newMenuToggle, menuToggle); newMenuToggle.addEventListener('click', (e) => { e.stopPropagation(); openSidebar(); }); }
    if (sidebarClose) { const newSidebarClose = sidebarClose.cloneNode(true); sidebarClose.parentNode.replaceChild(newSidebarClose, sidebarClose); newSidebarClose.addEventListener('click', (e) => { e.stopPropagation(); closeSidebar(); }); }
    if (sidebarOverlay) { const newSidebarOverlay = sidebarOverlay.cloneNode(true); sidebarOverlay.parentNode.replaceChild(newSidebarOverlay, sidebarOverlay); newSidebarOverlay.addEventListener('click', closeSidebar); }
    
    // --- START: New User Dropdown Logic ---
    const userDropdown = document.querySelector('.user-dropdown');
    if (userDropdown) {
        const dropdownToggle = userDropdown.querySelector('.dropdown-toggle');
        
        dropdownToggle.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent the click from bubbling to the window
            userDropdown.classList.toggle('open');
        });
    }
    // Close dropdown if clicked outside
    document.addEventListener('click', (event) => {
        const openDropdown = document.querySelector('.user-dropdown.open');
        if (openDropdown && !openDropdown.contains(event.target)) {
            openDropdown.classList.remove('open');
        }
    });
    // --- END: New User Dropdown Logic ---
    
    document.removeEventListener('keydown', escapeKeyHandler); document.addEventListener('keydown', escapeKeyHandler);
    document.querySelectorAll('.dark-mode-toggle-btn').forEach(btn => {
        const newBtn = btn.cloneNode(true); btn.parentNode.replaceChild(newBtn, btn);
        newBtn.addEventListener('click', () => { applyDarkMode(!body.classList.contains('dark-mode')); });
    });
    if (body) { updateToggleIcons(body.classList.contains('dark-mode')); }
}

function escapeKeyHandler(e) {
    const sidebar = document.getElementById('sidebar');
    const openDropdown = document.querySelector('.user-dropdown.open');
    if (e.key === 'Escape') {
        if (sidebar && sidebar.classList.contains('sidebar-open')) {
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            if (sidebarOverlay) { sidebar.classList.remove('sidebar-open'); sidebarOverlay.classList.remove('sidebar-overlay-visible'); document.body.classList.remove('overflow-hidden-sidebar'); }
        }
        if (openDropdown) {
            openDropdown.classList.remove('open');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded and parsed.");
    initializeMainFunctionality();
    initializeAddToCartButtons();
    refreshCartBadge();
    if (document.getElementById('cart-items-container')) {
        initializeCartPageEventListeners();
        refreshCartPageDisplay();
    }
});

function initializeCartPageEventListeners() {
    const cartItemsContainer = document.getElementById('cart-items-container');
    if (!cartItemsContainer) return;
    cartItemsContainer.addEventListener('change', async function(event) {
        if (event.target.classList.contains('quantity-input')) {
            const productId = event.target.dataset.productId;
            const quantity = parseInt(event.target.value);
            if (isNaN(quantity) || quantity < 0) { showSwalNotification('Invalid Quantity', "Please enter a valid quantity.", "error"); await refreshCartPageDisplay(); return; }
            const formData = new FormData();
            formData.append('action', quantity === 0 ? 'remove' : 'update');
            formData.append('product_id', productId);
            if (quantity > 0) { formData.append('quantity', quantity); }
            const result = await handleCartAction(formData);
            if (result && result.success) { await refreshCartPageDisplay(result.cartDetails); }
            else { await refreshCartPageDisplay(); }
        }
    });
    cartItemsContainer.addEventListener('click', async function(event) {
        const removeButton = event.target.closest('.remove-item-btn');
        if (removeButton) {
            event.preventDefault();
            const productId = removeButton.dataset.productId;
            const productName = removeButton.closest('.cart-item')?.querySelector('.cart-item-details h3')?.textContent || 'this item';
            Swal.fire({ title: 'Are you sure?', text: `Do you want to remove ${productName} from the cart?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, remove it!' })
            .then(async (dialogResult) => {
                if (dialogResult.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'remove');
                    formData.append('product_id', productId);
                    const result = await handleCartAction(formData);
                    if (result && result.success) { await refreshCartPageDisplay(result.cartDetails); }
                }
            });
        }
    });
}

async function refreshCartPageDisplay(cartDetails) {
    const container = document.getElementById('cart-items-container');
    const summaryContainer = document.querySelector('.cart-summary');
    if (!container) return;
    if (!cartDetails) {
        const formData = new FormData();
        formData.append('action', 'get');
        const result = await handleCartAction(formData);
        if (result && result.success) { cartDetails = result.cartDetails; }
        else { container.innerHTML = '<p>Error loading cart details. Please try refreshing the page.</p>'; return; }
    }
    const items = cartDetails.cart;
    const grandTotal = cartDetails.grandTotal;
    const totalItemsCount = cartDetails.totalItems;
    if (items.length === 0) {
        container.innerHTML = '<p class="empty-cart-message" style="text-align:center; padding: 2rem 0;">Your cart is currently empty. <a href="products.php">Continue shopping!</a></p>';
        if (summaryContainer) summaryContainer.style.display = 'none';
        return;
    }
    if (summaryContainer) summaryContainer.style.display = 'block';
    let itemsHTML = '';
    items.forEach(item => {
        const itemSubtotal = (parseFloat(item.price) * parseInt(item.quantity)).toFixed(2);
        let imagePath = 'https://placehold.co/100x100/eee/ccc?text=No+Image';
        if (item.image) { imagePath = `../../${item.image.startsWith('/') ? item.image.substring(1) : item.image}`; }
        itemsHTML += `
            <div class="cart-item" data-product-id="${item.id}">
                <div class="cart-item-image">
                    <img src="${imagePath}" alt="${item.name || 'Product Image'}" onerror="this.onerror=null; this.src='https://placehold.co/100x100/eee/ccc?text=Img+Error';">
                </div>
                <div class="cart-item-details">
                    <h3>${item.name || 'N/A'}</h3>
                    <p>Price: R${parseFloat(item.price).toFixed(2)}</p>
                    <p class="item-subtotal">Subtotal: R${itemSubtotal}</p>
                </div>
                <div class="cart-item-quantity">
                    <label for="quantity-${item.id}">Qty:</label>
                    <input type="number" id="quantity-${item.id}" class="quantity-input" value="${item.quantity}" min="0" data-product-id="${item.id}">
                </div>
                <div class="cart-item-actions">
                    <button class="remove-item-btn" data-product-id="${item.id}"><i class="fas fa-trash"></i> Remove</button>
                </div>
            </div>
        `;
    });
    container.innerHTML = itemsHTML;
    if (summaryContainer) {
        const cartTotalItemsEl = document.getElementById('cart-total-items');
        const cartGrandTotalEl = document.getElementById('cart-grand-total');
        if (cartTotalItemsEl) cartTotalItemsEl.textContent = totalItemsCount;
        if (cartGrandTotalEl) cartGrandTotalEl.innerHTML = `R${grandTotal.toFixed(2)}`;
    }
}
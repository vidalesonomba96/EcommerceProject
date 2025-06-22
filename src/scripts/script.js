// EcommerceProject/src/scripts/script.js

/**
 * ==================================================================================
 * HELPER FUNCTIONS & CONFIGURATION
 * ==================================================================================
 */

// --- Robust URL and Path Definitions ---
const getSiteBasePath = () => {
    let path = window.location.pathname;
    const pagesDir = '/src/pages/';
    if (path.includes(pagesDir)) {
        return path.substring(0, path.indexOf(pagesDir)) + '/';
    }
    const lastSlash = path.lastIndexOf('/');
    return path.substring(0, lastSlash + 1);
};
const APP_BASE_PATH = getSiteBasePath();
const CART_HANDLER_URL = `${APP_BASE_PATH}cart_handler.php`.replace(/\/\//g, '/');
const LIVE_SEARCH_URL = `${APP_BASE_PATH}live_search.php`.replace(/\/\//g, '/');


/**
 * Shows a customizable notification using SweetAlert2.
 * @param {string|null} title - The title of the alert.
 * @param {string} text - The main text of the alert.
 * @param {string} icon - The icon ('success', 'error', 'warning', 'info').
 * @param {boolean} [toast=false] - Whether to show as a small toast notification.
 */
function showSwalNotification(title, text, icon, toast = false) {
    const config = toast ? {
        toast: true, position: 'top-end', icon, title: text,
        showConfirmButton: false, timer: 3500, timerProgressBar: true,
        showClass: { popup: 'animate__animated animate__fadeInRight' },
        hideClass: { popup: 'animate__animated animate__fadeOutRight' },
        didOpen: (toastEl) => {
            toastEl.addEventListener('mouseenter', Swal.stopTimer);
            toastEl.addEventListener('mouseleave', Swal.resumeTimer);
        }
    } : { icon, title, text };
    Swal.fire(config);
}


/**
 * ==================================================================================
 * CART FUNCTIONALITY
 * ==================================================================================
 */

function updateCartBadge(cartDetails) {
    const badges = document.querySelectorAll('.cart-badge');
    if (badges.length > 0) {
        const count = cartDetails.totalQuantity || 0;
        badges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = 'flex';
        });
    }
}

async function handleCartAction(formData) {
    try {
        const response = await fetch(CART_HANDLER_URL, { method: 'POST', body: formData });
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        
        const result = await response.json();
        if (result.success && result.cartDetails) {
            updateCartBadge(result.cartDetails);
            if (result.message && formData.get('action') !== 'get' && formData.get('action') !== 'remove' && formData.get('action') !== 'update') {
                showSwalNotification(null, result.message, 'success', true);
            }
            return result;
        } else {
            throw new Error(result.message || 'Unknown server error.');
        }
    } catch (error) {
        console.error('Cart Action Error:', error);
        showSwalNotification('Error', `Cart operation failed: ${error.message}`, 'error');
        return null;
    }
}

function animateFlyToCart(button) {
    const sourceImage = button.closest('.product-card, .product-detail-layout')?.querySelector('img');
    const cartIcon = document.querySelector('.site-header .cart-link');

    if (sourceImage && cartIcon) {
        const imgRect = sourceImage.getBoundingClientRect();
        const flyingImage = document.createElement('img');
        flyingImage.src = sourceImage.src;
        flyingImage.classList.add('flying-product-image');
        document.body.appendChild(flyingImage);

        flyingImage.style.left = `${imgRect.left}px`;
        flyingImage.style.top = `${imgRect.top}px`;
        flyingImage.style.width = `${imgRect.width}px`;
        flyingImage.style.height = `${imgRect.height}px`;

        requestAnimationFrame(() => {
            const cartRect = cartIcon.getBoundingClientRect();
            flyingImage.style.left = `${cartRect.left + cartRect.width / 2}px`;
            flyingImage.style.top = `${cartRect.top + cartRect.height / 2}px`;
            flyingImage.style.width = '20px';
            flyingImage.style.height = '20px';
            flyingImage.style.opacity = '0.5';
        });
        
        setTimeout(() => flyingImage.remove(), 600);
    }
}

function initializeAddToCartButtons() {
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);

        newButton.addEventListener('click', async function() {
            animateFlyToCart(this);
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', this.dataset.productId);
            formData.append('product_name', this.dataset.productName);
            formData.append('product_price', this.dataset.productPrice);
            formData.append('product_image', this.dataset.productImage);
            formData.append('quantity', 1);
            const result = await handleCartAction(formData);
            if (result) {
                showSwalNotification(null, result.message, 'success', true);
                populateMiniCart();
            }
        });
    });
}

function refreshCartPageDisplay(cartDetails) {
    const container = document.getElementById('cart-items-container');
    const summaryContainer = document.querySelector('.cart-summary');
    if (!container || !summaryContainer) return;

    const items = cartDetails.cart;
    const grandTotal = cartDetails.grandTotal;
    const totalItemsCount = cartDetails.totalItems;

    if (items.length === 0) {
        container.innerHTML = '<p class="empty-cart-message">Your cart is currently empty. <a href="products.php">Continue shopping!</a></p>';
        summaryContainer.style.display = 'none';
        return;
    }
    
    summaryContainer.style.display = 'block';
    let itemsHTML = '';
    items.forEach(item => {
        const itemSubtotal = (parseFloat(item.price) * parseInt(item.quantity)).toFixed(2);
        let imagePath = item.image ? `${APP_BASE_PATH}${item.image}`.replace(/\/\//g, '/') : 'https://placehold.co/100x100/eee/ccc?text=No+Image';

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

    document.getElementById('cart-total-items').textContent = totalItemsCount;
    document.getElementById('cart-grand-total').innerHTML = `R${grandTotal.toFixed(2)}`;
}

function initializeCartPageEventListeners() {
    const cartItemsContainer = document.getElementById('cart-items-container');
    if (!cartItemsContainer) return;

    cartItemsContainer.addEventListener('change', async (event) => {
        if (event.target.classList.contains('quantity-input')) {
            const productId = event.target.dataset.productId;
            const quantity = parseInt(event.target.value);
            if (isNaN(quantity) || quantity < 0) { 
                showSwalNotification('Invalid Quantity', "Please enter a valid quantity.", "error");
                const getResult = await handleCartAction(new FormData().append('action', 'get'));
                if(getResult) refreshCartPageDisplay(getResult.cartDetails);
                return; 
            }
            const formData = new FormData();
            formData.append('action', quantity === 0 ? 'remove' : 'update');
            formData.append('product_id', productId);
            if (quantity > 0) formData.append('quantity', quantity);
            
            const result = await handleCartAction(formData);
            if (result) refreshCartPageDisplay(result.cartDetails);
        }
    });

    cartItemsContainer.addEventListener('click', async (event) => {
        const removeButton = event.target.closest('.remove-item-btn');
        if (removeButton) {
            event.preventDefault();
            const productId = removeButton.dataset.productId;
            const productName = removeButton.closest('.cart-item')?.querySelector('h3')?.textContent || 'this item';

            Swal.fire({
                title: 'Are you sure?', text: `Do you want to remove ${productName} from your cart?`,
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, remove it!'
            }).then(async (dialogResult) => {
                if (dialogResult.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'remove');
                    formData.append('product_id', productId);
                    const result = await handleCartAction(formData);
                    if (result) refreshCartPageDisplay(result.cartDetails);
                }
            });
        }
    });
}


/**
 * MODIFIED: Generates mini cart items with modern Font Awesome icons.
 */
async function populateMiniCart() {
    const miniCartItemsContainer = document.getElementById('mini-cart-items');
    const miniCartFooter = document.querySelector('.mini-cart-footer');
    if (!miniCartItemsContainer || !miniCartFooter) return;

    miniCartItemsContainer.innerHTML = '<p>Loading cart...</p>';

    const formData = new FormData();
    formData.append('action', 'get');
    const result = await handleCartAction(formData);

    if (result && result.cartDetails && result.cartDetails.cart) {
        const { cart, grandTotal } = result.cartDetails;
        
        if (cart.length === 0) {
            miniCartItemsContainer.innerHTML = '<p>Your cart is empty.</p>';
            miniCartFooter.innerHTML = `<a href="${APP_BASE_PATH}src/pages/cart.php" class="btn btn-secondary">View Cart</a>`;
        } else {
            let itemsHTML = '';
            cart.forEach(item => {
                const imagePath = item.image ? `${APP_BASE_PATH}${item.image}`.replace(/\/\//g, '/') : `https://placehold.co/60x60/eee/ccc?text=No+Img`;
                itemsHTML += `
                    <div class="mini-cart-item">
                        <img src="${imagePath}" alt="${item.name || 'Product'}" class="mini-cart-item-image">
                        <div class="mini-cart-item-details">
                            <div class="mini-cart-item-name">${item.name || 'N/A'}</div>
                            <div class="mini-cart-item-controls">
                                <div class="mini-cart-item-quantity">
                                    <button class="mini-cart-qty-btn minus" data-product-id="${item.id}" data-change="-1" aria-label="Decrease quantity"><i class="fas fa-minus"></i></button>
                                    <span>${item.quantity}</span>
                                    <button class="mini-cart-qty-btn plus" data-product-id="${item.id}" data-change="1" aria-label="Increase quantity"><i class="fas fa-plus"></i></button>
                                </div>
                                <div class="mini-cart-item-price">R${(item.price * item.quantity).toFixed(2)}</div>
                            </div>
                        </div>
                        <button class="mini-cart-remove-btn" data-product-id="${item.id}" aria-label="Remove item"><i class="fas fa-trash-alt"></i></button>
                    </div>
                `;
            });
            miniCartItemsContainer.innerHTML = itemsHTML;

            miniCartFooter.innerHTML = `
                <div class="mini-cart-subtotal">
                    <strong>Subtotal:</strong>
                    <span>R${grandTotal.toFixed(2)}</span>
                </div>
                <div class="mini-cart-actions">
                    <a href="${APP_BASE_PATH}src/pages/cart.php" class="btn btn-secondary">View Cart</a>
                    <a href="${APP_BASE_PATH}src/pages/checkout.php" class="btn btn-primary">Checkout</a>
                </div>
            `;
        }
    } else {
        miniCartItemsContainer.innerHTML = '<p>Could not load cart.</p>';
        miniCartFooter.innerHTML = `<a href="${APP_BASE_PATH}src/pages/cart.php" class="btn btn-secondary">View Cart</a>`;
    }
}


/**
 * ==================================================================================
 * UI INITIALIZATION FUNCTIONS
 * ==================================================================================
 */

function initializeLiveSearch() {
    const searchInput = document.getElementById('live-search-input');
    const searchResultsContainer = document.getElementById('live-search-results');
    const searchForm = document.getElementById('search-form-desktop');
    let debounceTimeout;

    if (searchInput && searchResultsContainer && searchForm) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimeout);
            if (query.length > 0) {
                debounceTimeout = setTimeout(() => {
                    fetch(`${LIVE_SEARCH_URL}?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.text();
                        })
                        .then(html => {
                            searchResultsContainer.innerHTML = html;
                            searchResultsContainer.style.display = 'block';
                        })
                        .catch(error => { console.error('Live Search Error:', error); });
                }, 300);
            } else {
                searchResultsContainer.style.display = 'none';
            }
        });
        document.addEventListener('click', (event) => {
            if (!searchForm.contains(event.target)) {
                searchResultsContainer.style.display = 'none';
            }
        });
    }
}

function initializeSidebar() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebarClose = document.getElementById('sidebar-close');
    const closeSidebar = () => {
        if (sidebar) sidebar.classList.remove('sidebar-open');
        if (sidebarOverlay) sidebarOverlay.classList.remove('sidebar-overlay-visible');
        document.body.classList.remove('overflow-hidden-sidebar');
    };
    if (menuToggle) menuToggle.addEventListener('click', (e) => { e.stopPropagation(); sidebar.classList.add('sidebar-open'); sidebarOverlay.classList.add('sidebar-overlay-visible'); document.body.classList.add('overflow-hidden-sidebar'); });
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });
}

function initializeUserDropdown() {
    const userDropdown = document.querySelector('.user-dropdown');
    if (userDropdown) {
        const dropdownToggle = userDropdown.querySelector('.dropdown-toggle');
        dropdownToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            userDropdown.classList.toggle('open');
        });
    }
    document.addEventListener('click', () => {
        const openDropdown = document.querySelector('.user-dropdown.open');
        if (openDropdown) openDropdown.classList.remove('open');
    });
}

function initializeDarkMode() {
    const body = document.body;
    const applyMode = (isDark) => {
        body.classList.toggle('dark-mode', isDark);
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        const iconClass = isDark ? 'fa-moon' : 'fa-sun';
        document.querySelectorAll('.stylish-switch-knob i').forEach(icon => { icon.className = `fas ${iconClass}`; });
    };
    document.querySelectorAll('.dark-mode-toggle-btn').forEach(btn => btn.addEventListener('click', () => applyMode(!body.classList.contains('dark-mode'))));
    applyMode(localStorage.getItem('darkMode') === 'enabled');
}

function initializeImageZoom() {
    const imageLink = document.getElementById('product-image-link');
    if (imageLink && typeof basicLightbox !== 'undefined') {
        imageLink.addEventListener('click', function(event) {
            event.preventDefault();
            basicLightbox.create(`<img src="${this.href}">`).show();
        });
    }
}


function initializeMiniCart() {
    const cartIcon = document.querySelector('.site-header .cart-link');
    const miniCart = document.getElementById('mini-cart');
    const miniCartClose = document.getElementById('mini-cart-close');
    let hoverTimeout;

    if (cartIcon && miniCart && miniCartClose) {
        const showCart = () => {
            clearTimeout(hoverTimeout);
            populateMiniCart(); 
            miniCart.classList.add('mini-cart-open');
        };

        const hideCart = () => {
            hoverTimeout = setTimeout(() => {
                if (!miniCart.matches(':hover')) {
                    miniCart.classList.remove('mini-cart-open');
                }
            }, 200);
        };

        cartIcon.addEventListener('mouseenter', showCart);
        cartIcon.addEventListener('mouseleave', hideCart);
        miniCart.addEventListener('mouseenter', () => clearTimeout(hoverTimeout));
        miniCart.addEventListener('mouseleave', () => miniCart.classList.remove('mini-cart-open'));
        miniCartClose.addEventListener('click', () => { miniCart.classList.remove('mini-cart-open'); });
    }
}

/**
 * MODIFIED: Uses .closest() to robustly handle clicks on buttons or their icons.
 */
function initializeMiniCartEventListeners() {
    const miniCart = document.getElementById('mini-cart');
    if (!miniCart) return;

    miniCart.addEventListener('click', async (event) => {
        const qtyButton = event.target.closest('.mini-cart-qty-btn');
        const removeButton = event.target.closest('.mini-cart-remove-btn');

        if (qtyButton) {
            const productId = qtyButton.dataset.productId;
            const change = parseInt(qtyButton.dataset.change, 10);
            
            const quantitySpan = qtyButton.parentElement.querySelector('span');
            const currentQuantity = parseInt(quantitySpan.textContent, 10);
            const newQuantity = currentQuantity + change;

            const formData = new FormData();
            formData.append('product_id', productId);

            if (newQuantity > 0) {
                formData.append('action', 'update');
                formData.append('quantity', newQuantity);
            } else {
                formData.append('action', 'remove');
            }
            
            await handleCartAction(formData);
            await populateMiniCart();
        }

        if (removeButton) {
            const productId = removeButton.dataset.productId;
            
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('product_id', productId);
            
            await handleCartAction(formData);
            await populateMiniCart();
        }
    });
}


/**
 * ==================================================================================
 * MAIN DOM CONTENT LOADED EVENT
 * ==================================================================================
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all global UI components
    initializeLiveSearch();
    initializeSidebar();
    initializeUserDropdown();
    initializeDarkMode();
    initializeAddToCartButtons();
    initializeImageZoom();
    initializeMiniCart();
    initializeMiniCartEventListeners();

    // Check if we are on the cart page and initialize its specific functionality
    if (document.getElementById('cart-items-container')) {
        initializeCartPageEventListeners();
        (async () => {
            const formData = new FormData();
            formData.append('action', 'get');
            const result = await handleCartAction(formData);
            if (result && result.cartDetails) {
                refreshCartPageDisplay(result.cartDetails);
            }
        })();
    } else {
        const formData = new FormData();
        formData.append('action', 'get');
        handleCartAction(formData);
    }
});
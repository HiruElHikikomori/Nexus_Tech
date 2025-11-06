// public/js/cart.js
document.addEventListener('DOMContentLoaded', function () {
    // =========================
    //  Bootstrap Toast helper
    // =========================
    const toastElement = document.getElementById('liveToast');
    const toast = toastElement ? new bootstrap.Toast(toastElement) : null;

    function showToast(message, type = 'info') { // type: success | danger | warning | info
        if (!toastElement || !toast) return;

        const toastTitle = document.getElementById('toast-title');
        const toastBody = document.getElementById('toast-body');

        if (toastTitle) toastTitle.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        if (toastBody) toastBody.textContent = message;

        toastElement.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
        switch (type) {
            case 'success': toastElement.classList.add('bg-success'); break;
            case 'danger':  toastElement.classList.add('bg-danger');  break;
            case 'warning': toastElement.classList.add('bg-warning'); break;
            case 'info':
            default:        toastElement.classList.add('bg-info');    break;
        }
        toast.show();
    }

    // ==========================================
    //  Resolución de URL de la página de productos
    // ==========================================
    const cartContainer = document.getElementById('cart-container');
    const productPageUrl = cartContainer && cartContainer.dataset.productUrl
        ? cartContainer.dataset.productUrl
        : '/products';

    // Si el carrito está inicialmente vacío, fija el enlace del botón
    const initialEmptyCartMessage = document.getElementById('empty-cart-message');
    if (initialEmptyCartMessage) {
        const goToProductsBtn = initialEmptyCartMessage.querySelector('#go-to-products-btn');
        if (goToProductsBtn && productPageUrl) {
            goToProductsBtn.href = productPageUrl;
        }
    }

    // =============================
    //  Actualización de totales UI
    // =============================
    function updateGeneralTotals(newSubtotalGeneral, newTotalProductsCount) {
        const subtotalGeneralElem   = document.getElementById('subtotal-general');
        const totalToPayElem        = document.getElementById('total-to-pay');
        const totalProductsCountElem= document.getElementById('total-products-count');
        const cartSummaryElem       = document.getElementById('cart-summary');
        const clearCartFormElem     = document.getElementById('clear-cart-form');
        const cartItemsWrapper      = document.getElementById('cart-items-wrapper');

        if (subtotalGeneralElem)    subtotalGeneralElem.textContent    = parseFloat(newSubtotalGeneral).toFixed(2);
        if (totalToPayElem)         totalToPayElem.textContent         = parseFloat(newSubtotalGeneral).toFixed(2);
        if (totalProductsCountElem) totalProductsCountElem.textContent = newTotalProductsCount;

        if (Number(newTotalProductsCount) === 0) {
            if (cartItemsWrapper) {
                cartItemsWrapper.innerHTML = `
                    <div id="empty-cart-message" class="text-center p-4">
                        <p class="lead">Tu carrito está vacío. ¡Añade algunos productos!</p>
                        <a href="${productPageUrl}" class="btn btn-primary" id="go-to-products-btn">Ver Productos</a>
                    </div>
                `;
            }
            if (cartSummaryElem)   cartSummaryElem.style.display = 'none';
            if (clearCartFormElem) clearCartFormElem.style.display = 'none';
        } else {
            const emptyMessage = document.getElementById('empty-cart-message');
            if (emptyMessage) emptyMessage.remove();
            if (cartSummaryElem)   cartSummaryElem.style.display = 'block';
            if (clearCartFormElem) clearCartFormElem.style.display = 'block';
        }
    }

    // Util para CSRF (Blade layout debe tener <meta name="csrf-token" content="...">)
    function getCsrfToken() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    // ======================================
    // 1) Actualizar cantidad (AJAX, PUT/PATCH)
    // ======================================
    document.querySelectorAll('.update-quantity-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            // ⛔️ Nunca interceptar el form de reseñas
            if (form.classList.contains('reviews-form') || (form.action && form.action.includes('/reviews'))) {
                return; // Deja que el navegador haga POST normal
            }

            e.preventDefault(); // Evita recarga

            const formData = new FormData(this); // Debe incluir _method=PUT o PATCH
            const itemRow  = this.closest('.cart-item-row');
            const itemId   = itemRow ? itemRow.dataset.itemId : null;
            const actionUrl= this.action;

            if (!itemId) {
                showToast('Error: No se pudo identificar el ID del producto.', 'danger');
                return;
            }

            fetch(actionUrl, {
                method: 'POST', // Laravel respetará _method
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async (response) => {
                if (!response.ok) {
                    let errorMessage = 'Error en la petición';
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || JSON.stringify(errorData);
                    } catch (err) {}
                    throw new Error(errorMessage);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.item_removed) {
                        if (itemRow) itemRow.remove();
                        showToast(data.message || 'Producto eliminado del carrito.', 'success');
                    } else {
                        if (itemRow) {
                            const countEl    = itemRow.querySelector('.item-count');
                            const subtotalEl = itemRow.querySelector('.item-subtotal');
                            if (countEl)    countEl.textContent    = data.newCount;
                            if (subtotalEl) subtotalEl.textContent = parseFloat(data.newSubtotalItem).toFixed(2);
                        }
                        showToast(data.message || 'Cantidad actualizada.', 'success');
                    }
                    updateGeneralTotals(data.newSubtotalGeneral, data.newTotalProductsCount);
                } else {
                    showToast('Error: ' + (data.message || 'Operación fallida'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al actualizar la cantidad: ' + error.message, 'danger');
            });
        });
    });

    // ==================================
    // 2) Eliminar ítem (AJAX, DELETE)
    // ==================================
    document.querySelectorAll('.delete-item-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            // ⛔️ Nunca interceptar el form de reseñas
            if (form.classList.contains('reviews-form') || (form.action && form.action.includes('/reviews'))) {
                return; // Deja que el navegador haga POST normal
            }

            e.preventDefault();

            if (!confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
                return;
            }

            const itemRow  = this.closest('.cart-item-row');
            const itemId   = itemRow ? itemRow.dataset.itemId : null;
            const actionUrl= this.action;

            if (!itemId) {
                showToast('Error: No se pudo identificar el ID del producto a eliminar.', 'danger');
                return;
            }

            fetch(actionUrl, {
                method: 'POST', // Laravel respetará _method=DELETE en el body
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            })
            .then(async (response) => {
                if (!response.ok) {
                    let errorMessage = 'Error en la petición';
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || JSON.stringify(errorData);
                    } catch (err) {}
                    throw new Error(errorMessage);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (itemRow) itemRow.remove();
                    showToast(data.message || 'Producto eliminado del carrito.', 'success');
                    updateGeneralTotals(data.newSubtotalGeneral, data.newTotalProductsCount);

                    // Si quedara vacío, puedes redirigir o dejar el mensaje vacío (ya lo maneja updateGeneralTotals)
                    if (data.cartEmpty && productPageUrl) {
                        // window.location.href = productPageUrl; // opcional
                    }
                } else {
                    showToast('Error: ' + (data.message || 'Operación fallida'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al eliminar el producto: ' + error.message, 'danger');
            });
        });
    });

    // =========================================
    // 3) Vaciar carrito / Checkout (AJAX, DELETE/POST)
    // =========================================
    const clearCartForm = document.getElementById('clear-cart-form');
    if (clearCartForm) {
        clearCartForm.addEventListener('submit', function (e) {
            // Este form es específico del carrito; no es reseñas.
            e.preventDefault();

            if (!confirm('¿Estás seguro de que quieres realizar la compra y vaciar tu carrito?')) {
                return;
            }

            const actionUrl = this.action;

            fetch(actionUrl, {
                method: 'POST', // Laravel respetará el _method del form
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            })
            .then(async (response) => {
                if (!response.ok) {
                    let errorMessage = 'Error en la petición';
                    try {
                        const errorData = await response.json();
                        errorMessage = errorData.message || JSON.stringify(errorData);
                    } catch (err) {}
                    throw new Error(errorMessage);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Compra realizada con éxito.', 'success');
                    if (data.cart_cleared) {
                        updateGeneralTotals(0, 0);
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    }
                } else {
                    showToast('Error: ' + (data.message || 'Operación fallida'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al procesar la compra: ' + error.message, 'danger');
            });
        });
    }
});

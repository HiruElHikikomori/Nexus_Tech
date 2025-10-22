document.addEventListener('DOMContentLoaded', function () {
    // Inicializar el Toast de Bootstrap (necesario para su funcionamiento)
    const toastElement = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastElement); // Asegúrate de tener Bootstrap JS cargado

    // Obtener la URL de la página de productos desde el atributo de datos
    const cartContainer = document.getElementById('cart-container');
    // Si el elemento no existe, o si no tiene el atributo, asigna una cadena vacía o una ruta por defecto si es necesario.
    const productPageUrl = cartContainer ? cartContainer.dataset.productUrl : '/products'; // Usar una ruta por defecto si no se encuentra

    // --- NUEVA LÓGICA: Actualizar el enlace si el carrito está inicialmente vacío ---
    const initialEmptyCartMessage = document.getElementById('empty-cart-message');
    if (initialEmptyCartMessage) {
        const goToProductsBtn = initialEmptyCartMessage.querySelector('#go-to-products-btn');
        if (goToProductsBtn && productPageUrl) {
            goToProductsBtn.href = productPageUrl;
        }
    }
    // --- FIN DE LA NUEVA LÓGICA ---

    // Función para mostrar el Toast
    function showToast(message, type = 'info') { // type can be 'success', 'danger', 'warning', 'info'
        const toastTitle = document.getElementById('toast-title');
        const toastBody = document.getElementById('toast-body');

        toastTitle.textContent = type.charAt(0).toUpperCase() + type.slice(1); // Capitaliza el tipo (Success, Error, etc.)
        toastBody.textContent = message;

        // Cambiar el color de fondo del Toast según el tipo
        toastElement.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
        switch (type) {
            case 'success':
                toastElement.classList.add('bg-success');
                break;
            case 'danger':
                toastElement.classList.add('bg-danger');
                break;
            case 'warning':
                toastElement.classList.add('bg-warning');
                break;
            case 'info':
            default:
                toastElement.classList.add('bg-info');
                break;
        }
        toast.show();
    }

    // Función para actualizar los totales generales
    function updateGeneralTotals(newSubtotalGeneral, newTotalProductsCount) {
        const subtotalGeneralElem = document.getElementById('subtotal-general');
        const totalToPayElem = document.getElementById('total-to-pay');
        const totalProductsCountElem = document.getElementById('total-products-count');
        const cartSummaryElem = document.getElementById('cart-summary');
        const clearCartFormElem = document.getElementById('clear-cart-form');
        const cartItemsWrapper = document.getElementById('cart-items-wrapper');


        if (subtotalGeneralElem) subtotalGeneralElem.textContent = parseFloat(newSubtotalGeneral).toFixed(2);
        if (totalToPayElem) totalToPayElem.textContent = parseFloat(newSubtotalGeneral).toFixed(2);
        if (totalProductsCountElem) totalProductsCountElem.textContent = newTotalProductsCount;

        // Si no hay items y el carrito se vació, mostrar mensaje de carrito vacío
        if (newTotalProductsCount == 0) {
            // Limpiar solo los items del carrito, manteniendo el contenedor principal
            cartItemsWrapper.innerHTML = `
                <div id="empty-cart-message" class="text-center p-4">
                    <p class="lead">Tu carrito está vacío. ¡Añade algunos productos!</p>
                    <a href="${productPageUrl}" class="btn btn-primary" id="go-to-products-btn">Ver Productos</a>
                </div>
            `;
            if (cartSummaryElem) cartSummaryElem.style.display = 'none';
            if (clearCartFormElem) clearCartFormElem.style.display = 'none';
        } else {
            const emptyMessage = document.getElementById('empty-cart-message');
            if (emptyMessage) {
                emptyMessage.remove(); // Quita el mensaje si hay items
            }
            if (cartSummaryElem) cartSummaryElem.style.display = 'block';
            if (clearCartFormElem) clearCartFormElem.style.display = 'block';
        }
    }

    // Manejar los formularios de actualización de cantidad
    document.querySelectorAll('.update-quantity-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Evitar la recarga de la página

            const formData = new FormData(this);
            const itemRow = this.closest('.cart-item-row'); // Obtener la fila del item
            const itemId = itemRow ? itemRow.dataset.itemId : null; // Obtener el ID del item
            const actionUrl = this.action; // Correcto: obtiene el action del formulario

            if (!itemId) {
                showToast('Error: No se pudo identificar el ID del producto.', 'danger');
                return;
            }

            fetch(actionUrl, {
                method: 'POST', // Será PATCH por el _method en formData
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    // Si la respuesta no es 2xx, lanza un error para que el catch lo capture
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Error en la petición');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.item_removed) {
                        // Si el ítem fue eliminado (cantidad <= 0)
                        if (itemRow) itemRow.remove();
                        showToast(data.message, 'success');
                    } else {
                        // Si la cantidad fue actualizada
                        if (itemRow) {
                            itemRow.querySelector('.item-count').textContent = data.newCount;
                            itemRow.querySelector('.item-subtotal').textContent = parseFloat(data.newSubtotalItem).toFixed(2);
                        }
                        showToast(data.message, 'success');
                    }
                    updateGeneralTotals(data.newSubtotalGeneral, data.newTotalProductsCount);
                } else {
                    showToast('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al actualizar la cantidad: ' + error.message, 'danger');
            });
        });
    });

    // Manejar los formularios de eliminación de ítem
    document.querySelectorAll('.delete-item-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Evitar la recarga de la página

            if (!confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
                return; // Si el usuario cancela, no hacer nada
            }

            const itemRow = this.closest('.cart-item-row');
            const itemId = itemRow ? itemRow.dataset.itemId : null;
            const actionUrl = this.action;

            if (!itemId) {
                showToast('Error: No se pudo identificar el ID del producto a eliminar.', 'danger');
                return;
            }

            fetch(actionUrl, {
                method: 'POST', // Será DELETE pero fetch lo envía como POST si no se especifica
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: new FormData(this) // Necesario para enviar _method DELETE
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Error en la petición');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (itemRow) itemRow.remove();
                    showToast(data.message, 'success');
                    updateGeneralTotals(data.newSubtotalGeneral, data.newTotalProductsCount);

                    // Si el carrito queda vacío después de eliminar el último ítem, redirigir a la página de productos
                    if (data.cartEmpty && productPageUrl) {
                        // Opcional: Si quieres redirigir automáticamente después de eliminar el último artículo
                        // window.location.href = productPageUrl;
                    }

                } else {
                    showToast('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al eliminar el producto: ' + error.message, 'danger');
            });
        });
    });

    // Manejar el formulario para vaciar todo el carrito (Realizar compra)
    const clearCartForm = document.getElementById('clear-cart-form');
    if (clearCartForm) {
        clearCartForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Evitar la recarga de la página

            if (!confirm('¿Estás seguro de que quieres realizar la compra y vaciar tu carrito?')) {
                return;
            }

            const actionUrl = this.action;

            fetch(actionUrl, {
                method: 'POST', // Será DELETE por el _method
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Error en la petición');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    if (data.cart_cleared) {
                        updateGeneralTotals(0, 0); // Establecer totales a 0 para vaciar visualmente
                        // Redirigir a la página de productos después de la compra
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    }
                } else {
                    showToast('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al procesar la compra: ' + error.message, 'danger');
            });
        });
    }
});
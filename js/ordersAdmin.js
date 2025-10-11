function loadCustomerOrders(){
    $.post('includes/loadServerData.inc.php', {action: 'loadCustomerOrders'}, function(response){
        console.log(response);
        
        if (!response || typeof response !== 'object') {
            console.error('Invalid JSON response:', response);
            $('.orders-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Encomendas!');
            return;
        }

        if (response.status === 'error') {
            console.warn('Server error:', response.message || 'Unknown error');
            $('.orders-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Encomendas!');
            return;
        }
        
        
        let HTMLcontent = '';
        const ordersProducts = response.ordersData;
        const orders = {};

        // agrupa os produtos pela respetiva orderId
        ordersProducts.forEach(product => {
            if (!orders[product.orderId]) {
                orders[product.orderId] = [];
            }
            orders[product.orderId].push(product);
        });

        //substitui o status para a traduçao em portugues
        const statusReplacements = {
            'pending': 'Pendente',
            'accepted': 'Aceite',
            'rejected': 'Recusado',
            'canceled': 'Cancelado',
            'received': 'Recebida',
            'dispatched': 'Enviada'
        };
        
        //cria a ordem
        for(const orderId in orders){
            const order = orders[orderId];
            const orderDate = order[0].orderDate;
            const status = order[0].orderStatus;
            const customerName = order[0].customerName;
            const customerAddress = order[0].customerAddress;
            const orderStatus = statusReplacements[status] || status;
            const orderTotal = order.reduce((sum, product) => sum + (parseInt(product.productQuantity) * parseFloat(product.productPrice)), 0);
            
            HTMLcontent += `
                <div class="order-container">
                    <div class="order-title">
                        <div class="order-info left">
                            <h3>ID: ${orderId}</h3>
                            <p>Nome: ${customerName}</p>
                            <p>Morada: ${customerAddress}</p>
                            </div>
                            <div class="order-info right">
                            <p class="${status}">${orderStatus}</p>
                            <p>${orderDate}</p>
                            <p>Total: ${orderTotal.toFixed(2)}€</p>
                        </div>
                        <div class="order-arrow"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <ul class="order-products">
            `;
            // cria os produtos dentro da ordem
            order.forEach(product => {
                //calcula o preço todal do produto
                const price = parseFloat(product.productPrice) || 0;
                const qty = parseInt(product.productQuantity) || 0;
                const total = (price * qty);

                let totalProductContainer = `${total.toFixed(2)}€`;
                if(qty > 1){
                    totalProductContainer += ` <span class='order-product-price-qty'>(${qty} x ${price.toFixed(2)}€)</span>`;
                }

                HTMLcontent += `        
                <li class='order-product'>
                    <img src='imgs/products/${product.productImgSrc}' alt='${product.productName}' class='order-product-img' onerror='this.src="imgs/products/defaultProduct.jpg"'>
                        <div class='order-product-info'>
                            <h4 class='order-product-name'>${product.productName}</h4>
                            <div class='order-product-total'>${totalProductContainer}</div>
                        </div>
                    </li>
                `;
            });
            HTMLcontent += `        
                </ul>
                <div class="order-btns">
            `;
            if(status === 'pending'){
                HTMLcontent += ` <button id="accept-btn" data-id="${orderId}">Aceitar</button> `;
                HTMLcontent += ` <button id="reject-btn" data-id="${orderId}">Recusar</button> `;
            } else if(status === 'accepted'){
                HTMLcontent += ` <button id="dispatch-btn" data-id="${orderId}">Enviar</button> `;
            }
            HTMLcontent += `
                    </div>
                </div>
            `;
        }

        $('.orders-container').html(HTMLcontent);

    }, 'json').fail(function () {
        $('.orders-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar as Encomendas!');
    });
}


function toggleOrder(){
    $(document).on('click', '.order-title',  function(){
        const container = $(this).closest('.order-container');
        if (!container.hasClass('open')){
            container.addClass('open');
        } else {
            container.removeClass('open');
        }
    });
}

function reviewOrder(orderId, review){
    $.post('includes/saveServerData.inc.php', {action: 'reviewOrder', orderId: orderId, review: review}, function(response){
        if (response.status === 'error') {
            console.error('Server error:', response.message || 'Unknown error');
        } else if (response.status !== 'success') {
            console.warn('Falha ao executar!');
        }
       
        loadCustomerOrders(); 
    }, 'json').fail(function () {
        console.error('Erro na ligação ao servidor.');
    });
}


$(document).ready(function(){
    loadCustomerOrders();
    toggleOrder();

    $(document).on('click', '#accept-btn', function() {
        const orderId = $(this).data('id');
        reviewOrder(orderId, 'accepted');
    });

    $(document).on('click', '#dispatch-btn', function() {
        const orderId = $(this).data('id');
        reviewOrder(orderId, 'dispatched');
    });

    $(document).on('click', '#reject-btn', function() {
        const orderId = $(this).data('id');
        reviewOrder(orderId, 'rejected');
    });

});
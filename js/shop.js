function loadShopProducts(){
    $.post('includes/loadServerData.inc.php', {action: 'loadShopProducts'}, function(response){
        if (!response || typeof response !== 'object') {
            console.error('Resposta JSON inválida:', response);
            $('.products-container').html('Ocorreu um erro. Não foi possível carregar os produtos!');
            return;
        }

        if (response.status === 'error') {
            console.error('Error:', response.message || 'Erro desconhecido');
            $('.products-container').html('Ocorreu um erro. Não foi possível carregar os produtos!');
            return;
        }

        const products = response.products;
        let HTMLcontent = '';

        const stockReplacements = {
            'unavailable': 'Indisponível',
            'limited': 'Limitado',
            'available': 'Disponível',
        };
        
        if(products.length > 0){
            products.forEach(product => {
                const stockStatus = stockReplacements[product.productStock] || product.productStock;    // troca para portugues
                HTMLcontent += `   
                    <li class='product-card'>
                        <img src='imgs/products/${product.productImgSrc}' alt='${product.productName}' class='product-img' onerror='this.src="imgs/products/defaultProduct.jpg"'>
                        <h4 class='product-name'>${product.productName}</h4>
                        <div class='product-price'>${product.productPrice}€</div>
                        <div class='product-stock ${product.productStock}'>Stock: <span>${stockStatus}</span></div>
                        <div class='product-actions'>
                            <button class='btn-buy' data-id='${product.id}'>Comprar</button>
                            <button class='btn-add-cart' data-id='${product.id}'>Adicionar ao Carrinho</button>
                        </div>
                    </li>
                `;
            });
        }
        
        $('.products-container').html(HTMLcontent);
    }, 'json').fail(function () {
        $('.products-container').html('Ocorreu um erro. Não foi possível carregar os produtos!');
    });
}

function loadCartProducts(){
    $.post('includes/loadServerData.inc.php', {action: 'loadShoppingCart'}, function(response){
        if (!response || typeof response !== 'object') {
            console.error('Resposta JSON Invalida:', response);
            $('.cart-container').html('Ocorreu um erro. Não foi possível carregar o carrinho!');
            return;
        }

        if (response.status === 'error') {
            console.error('Erro:', response.message || 'Erro desconhecido');
            $('.cart-container').html('Ocorreu um erro. Não foi possível carregar o carrinho!');
            return;
        }
        
        if (response.status === 'invalid') {
            console.warn('Erro:', response.message);
            $('.cart-container').html(`
                <h4 class='connect-warn'>É necessário iniciar sessão para utilizar o carrinho.</h4>
                <div class='connect'>
                    <a href='login.php'>Entrar</a>
                    <a href='signup.php'>Registar</a>
                </div>
            `);
            return;
        }

        const cartProducts = response.shoppingCart;
        let HTMLcontent = '';
        let totalCart = 0;


        if(cartProducts.length > 0){
            cartProducts.forEach(product => {
                // Calcula o preço total do produto
                const price = Number(product.productPrice) || 0;
                const qty = Number(product.productQuantity) || 0;
                const total = (price * qty);

                // Calcula o total o carrinho
                totalCart += total;

                let totalProductContainer = `${total.toFixed(2)}€`;
                if(qty > 1){
                    totalProductContainer += ` <span class='cart-product-price-qty'>(${qty} x ${price.toFixed(2)}€)</span>`;
                }

                HTMLcontent += `
                    <li class='cart-product'>
                        <img src='imgs/products/${product.productImgSrc}' alt='${product.productName}' class='cart-product-img' onerror='this.src="imgs/products/defaultProduct.jpg"'>
                        <div class='cart-product-info'>
                            <div class='cart-product-actions'>
                            <button class='btn-add' data-id='${product.productId}'>+</button>
                            <button class='btn-remove' data-id='${product.productId}'>-</button>
                            <button class='btn-delete' data-id='${product.productId}'><i class="fas fa-trash"></i></button>
                            </div>
                            <h4 class='cart-product-name'>${product.productName}</h4>
                            <div class='cart-product-total'>${totalProductContainer}</div>
                        </div>
                    </li>
                `;
            });
        } else{
            HTMLcontent = '<h4 class="connect-warn">Não existem produtos no carrinho!</h4>';
        }
        $('.cart-container').html(HTMLcontent);
        $('.cart-total').html(`${totalCart.toFixed(2)} €`);    // Adiciona o total ao carrinho
    }, 'json').fail(function () {
        $('.cart-container').html('<h4 class="connect-warn">Ocorreu um erro. Não foi possível carregar o carrinho!</h4>');
    });
}

function shoppingCartHandler(productId, cartAction){
    $.post('includes/saveServerData.inc.php', {action: 'cartHandler', productId: productId, cartAction: cartAction}, function(response){
        if (response.status === 'error') {
            console.error('Server error:', response.message || 'Erro desconhecido');
            showPopup('Erro na ligação ao servidor.');
            return;
        }
        if (response.status === 'processError') {
            console.warn('Erro: ', response.error);
            showPopup(response.message);
            return;
        }

        loadCartProducts();

    }, 'json').fail(function () {
        console.error('Erro na ligação ao servidor.');
    });
}

function checkout(type, productId){ // type = direct/cart
    // cria um formulário
    const form = $('<form>', { method: 'POST', action: 'checkout.php' });
    // adiciona os inputs
    form.append($('<input>', { type: 'hidden', name: 'type', value: type }));
    type === 'direct' && form.append($('<input>', { type: 'hidden', name: 'productId', value: productId }));
    // Insere o formulário no html e envia
    form.appendTo('body').submit();
}

function showPopup(msg, delay = 2000, success = false) {
    $('.popup').remove();// Remove um popup antes de criar outro (se existir)
    
    // Cria o elemento popup
    const popup = $('<div class="popup"></div>').text(msg);
    
    // Adiciona a classe "popup-success" apenas se success for true ou 1
    if (success === true || success === 1 || success === '1') {
        popup.addClass('popup-success');
    }
    // Insere no main e aplica delay + fadeOut
    popup.appendTo('main').delay(delay).fadeOut(300, function() { $(this).remove(); });
}

$(document).ready(function(){
    const params = new URLSearchParams(window.location.search);

    if (params.has('invalid')) {
        const status = params.get('invalid');
        
        const messages = {  
            login: 'É necessário iniciar sessão para efetuar a compra.',
            type: 'O tipo de checkout não é válido.',
            productId: 'O ID do produto não é válido.',
            notFound: 'O produto não existe.',
            empty: 'Não há produtos para efetuar o checkout.'
        };

        // Mostra uma msg personalizada para alguns status e uma genérica para todos os outros
        const msg = messages[status] || 'Ocorreu um erro. Tente novamente!';    

        showPopup(msg);
    } else if (params.has('checkout')) {
        const status = params.get('checkout');
        
        const messages = {  
            success: 'Encomenda concluída com sucesso.',
            error: 'Ocorreu um erro durante o processo. Tente novamente!',
            failed: 'Ocorreu um erro. A operação foi cancelada!',
        };
    
        // Mostra uma msg personalizada para alguns status e uma genérica para todos os outros
        const msg = messages[status] || 'Ocorreu um erro. Tente novamente!';    

        if (status === 'success'){
            showPopup(msg, 2000, true);
        } else {
            showPopup(msg);
        }
    }

    loadShopProducts();
    loadCartProducts();

    // Abrir e fechar o carrinho
    $('#open-cart-btn').on('click', function() {
        $('.shopping-cart').addClass('open');
        $(window).scrollTop(0);
    });
    
    $('#close-cart').on('click', function() {
        $('.shopping-cart').removeClass('open');
    });

    // Comprar (checkout) 
    $(document).on('click', '.btn-buy', function() {
        const isAvailable = !$(this).closest('.product-card').find('.product-stock').hasClass('unavailable');
        if(isAvailable){
            const productId = $(this).data('id');
            checkout('direct', productId);
        }
    });

    // Cart checkout
    $(document).on('click', '.pay-cart', function() {
        checkout('cart');
    });

    // Adicionar ao carrinho
    $(document).on('click', '.btn-add-cart', function() {
        const isAvailable = !$(this).closest('.product-card').find('.product-stock').hasClass('unavailable');
        if(isAvailable){
            const productId = $(this).data('id');
            shoppingCartHandler(productId, 'add');
        }
    });

    // add, remove, apaga o produto que está no carrinho
    $(document).on('click', '.btn-add', function() {
        const productId = $(this).data('id');
        shoppingCartHandler(productId, 'add');
    });

    $(document).on('click', '.btn-remove', function() {
        const productId = $(this).data('id');
        shoppingCartHandler(productId, 'remove');
    });

    $(document).on('click', '.btn-delete', function() {
        const productId = $(this).data('id');
        shoppingCartHandler(productId, 'delete');
    });

});
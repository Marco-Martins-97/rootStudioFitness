function loadShopProducts(){
    $.post('includes/loadServerData.inc.php', {action: 'loadShopProducts'}, function(response){
        if (!response || typeof response !== 'object') {
            console.error('Resposta JSON Invalida:', response);
            $('.products-container').html('Ocorreu Um Erro, Não Foi Possivel Carregar os Produtos!');
            return;
        }

        if (response.status === 'error') {
            console.error('Error:', response.message || 'Unknown error');
            $('.products-container').html('Ocorreu Um Erro, Não Foi Possivel Carregar os Produtos!');
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
        $('.products-container').html('Ocorreu Um Erro, Não Foi Possivel Carregar os Produtos');
    });
}

function loadCartProducts(){
    $.post('includes/loadServerData.inc.php', {action: 'loadShoppingCart'}, function(response){
        if (!response || typeof response !== 'object') {
            console.error('Resposta JSON Invalida:', response);
            $('.cart-container').html('Ocorreu Um Erro, Não Foi Possivel Carregar o Carrinho!');
            return;
        }

        if (response.status === 'error') {
            console.error('Error:', response.message || 'Unknown error');
            $('.cart-container').html('Ocorreu Um Erro, Não Foi Possivel Carregar o Carrinho!');
            return;
        }
        
        if (response.status === 'invalid') {
            console.warn('Error:', response.message);
            $('.cart-container').html(`
                <h4 class='connect-warn'>Necessita estar logado para poder utilizar o carrinho.</h4>
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

        // console.log(cartProducts);

        if(cartProducts.length > 0){
            cartProducts.forEach(product => {
                //calcula o preço total do produto
                const price = Number(product.productPrice) || 0;
                const qty = Number(product.productQuantity) || 0;
                const total = (price * qty);

                //calcula o total o carrinho
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
            HTMLcontent = '<h4 class="connect-warn">Não Existem produtos no Carrinho!</h4>';
        }
        $('.cart-container').html(HTMLcontent);
        $('.cart-total').html(`${totalCart.toFixed(2)} €`);    //adiciona o total ao carrinho
    }, 'json').fail(function () {
        $('.cart-container').html('<h4 class="connect-warn">Ocorreu Um Erro, Não Foi Possivel Carregar o Carrinho!</h4>');
    });
}

function shoppingCartHandler(productId, cartAction){
    const errorPopup = $(`<div class='popup'></div>`);

    $.post('includes/saveServerData.inc.php', {action: 'cartHandler', productId: productId, cartAction: cartAction}, function(response){
    
        if (response.status === 'error') {
            console.error('Server error:', response.message || 'Unknown error');
            $('.popup').remove();
            errorPopup.text('Erro na ligação ao servidor.').appendTo('main');
            setTimeout(function(){
                errorPopup.fadeOut(500, function(){ $(this).remove(); });
            }, 2000);
            return;
        }
        if (response.status === 'processError') {
            console.warn('Erro: ', response.error);
            $('.popup').remove();
            errorPopup.text(response.message).appendTo('main');
            setTimeout(function(){
                errorPopup.fadeOut(500, function(){ $(this).remove(); });
            }, 2000);
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
    // insere o formulário no html e envia
    form.appendTo('body').submit();
}

$(document).ready(function(){
    const popup = $(`<div class='popup'></div>`);
    const params = new URLSearchParams(window.location.search);

    if (params.has('invalid')) {
        const status = params.get('invalid');
        
        const errorMsg = {  
            login: 'Necessita estar logado para poder comprar.'
        };

        //mostra uma msg personalizada para alguns status e uma generica para todos os outros
        const msg = errorMsg[status] || 'Ocurreu um erro, Tente Novamente!';    

        $('.popup').remove();
        popup.text(msg).appendTo('main');
        setTimeout(function(){
            popup.fadeOut(500, function(){ $(this).remove(); });
        }, 2000);
    } else if (params.has('checkout')) {
        const status = params.get('checkout');

        if (status === 'success'){
            popup.css('background-color', 'green');
        } 
        const errorMsg = {  
            success: 'Ordem concluida com Sucesso.',
            error: 'Ocorreu um erro durante o processo, Tente Novamente!',
            failed: 'Ocorreu um erro, A operação foi cancelada!',
        };
    
        //mostra uma msg personalizada para alguns status e uma generica para todos os outros
        const msg = errorMsg[status] || 'Ocurreu um erro, Tente Novamente!';    
    
        $('.popup').remove();
        popup.text(msg).appendTo('main');
        setTimeout(function(){
        popup.fadeOut(500, function(){ $(this).remove(); });
        }, 2000);
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

    //Comprar (checkout) 
    $(document).on('click', '.btn-buy', function() {
        const isAvailable = !$(this).closest('.product-card').find('.product-stock').hasClass('unavailable');
        if(isAvailable){
            const productId = $(this).data('id');
            checkout('direct', productId); //via POST
            // window.location.href = `checkout.php?mode=direct&productId=${encodeURIComponent(productId)}}`; //via GET
        }
    });
    //Cart checkout
    $(document).on('click', '.pay-cart', function() {
        checkout('cart'); //via POST
    });

    //Adicionar ao carrinho
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
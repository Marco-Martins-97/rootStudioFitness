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

        console.log(cartProducts);

        if(cartProducts.length > 0){
            cartProducts.forEach(product => {
                HTMLcontent += `
                    <li class='cart-product'>
                        <img src='imgs/products/notebook.jpg' alt='Produto' class='cart-product-img' />
                        <div class='cart-product-info'>
                            <!-- Action buttons -->
                            <div class='cart-product-actions'>
                            <button class='btn-add' data-id='${i}'>+</button>
                            <button class='btn-remove' data-id='${i}'>-</button>
                                <button class='btn-delete' data-id='${i}'><i class="fas fa-trash"></i></button>
                            </div>
                            <!-- Product name -->
                            <h4 class='cart-product-name'>Produto Exemplo ${i}</h4>
                            <!-- Price and quantity -->
                        
                            <!-- Total price -->
                            <div class='cart-product-total'>
                                99,80€ <span class='cart-product-price-qty'>(44,90€ x2)</span>
                            </div>
                        </div>
                    </li>
                `;
            });
        }
        // <li class='product-card'>
        //     <img src='imgs/products/${product.productImgSrc}' alt='${product.productName}' class='product-img' onerror='this.src="imgs/products/defaultProduct.jpg"'>
        //     <h4 class='product-name'>${product.productName}</h4>
        //     <div class='product-price'>${product.productPrice}€</div>
        //     <div class='product-stock ${product.productStock}'>Stock: <span>${stockStatus}</span></div>
        //     <div class='product-actions'>
        //         <button class='btn-buy' data-id='${product.id}'>Comprar</button>
        //         <button class='btn-add-cart' data-id='${product.id}'>Adicionar ao Carrinho</button>
        //     </div>
        // </li>
        
        $('.products-container').html(HTMLcontent);

    }, 'json').fail(function () {
        $('.cart-container').html('Ocorreu Um Erro, Não Foi Possivel Carregar o Carrinho!');
    });
    /* let HTMLcontent = '';
    for (let i = 0; i < 5; i++) {
        HTMLcontent += `   
            <li class='cart-product'>
                <img src='imgs/products/notebook.jpg' alt='Produto' class='cart-product-img' />
                <div class='cart-product-info'>
                    <!-- Action buttons -->
                    <div class='cart-product-actions'>
                    <button class='btn-add' data-id='${i}'>+</button>
                    <button class='btn-remove' data-id='${i}'>-</button>
                        <button class='btn-delete' data-id='${i}'><i class="fas fa-trash"></i></button>
                    </div>
                    <!-- Product name -->
                    <h4 class='cart-product-name'>Produto Exemplo ${i}</h4>
                    <!-- Price and quantity -->
                   
                    <!-- Total price -->
                    <div class='cart-product-total'>
                        99,80€ <span class='cart-product-price-qty'>(44,90€ x2)</span>
                    </div>
                </div>
            </li>
        `;
    }
    $('.cart-container').html(HTMLcontent); */
}

function shoppingCartHandler(productId, cartAction){
    console.log(cartAction,':', productId);
    $.post('includes/saveServerData.inc.php', {action: 'cartHandler', productId: productId, cartAction: cartAction}, function(response){
    
        if (response.status === 'error') {
            console.error('Server error:', response.message || 'Unknown error');
            // CREATE A POPUP WITH A GENERIC MSG
            return;
        }
        if (response.status === 'processError') {
            console.error('Erro: ', response.error);
            console.warn('Erro: ', response.message);
            // CREATE A POPUP WITH THE ERRO MSG
            return;
        }

        console.log(response);



    }, 'json').fail(function () {
        console.error('Erro na ligação ao servidor.');
    });
}

$(document).ready(function(){
    loadShopProducts();
    loadCartProducts();

    // Abrir e fechar o carrinho
    $('#open-cart-btn').on('click', function() {
        $('.shopping-cart').addClass('open');
        // $('html, body').animate({ scrollTop: 0 }, 'slow');
        $(window).scrollTop(0);
    });
    $('#close-cart').on('click', function() {
        $('.shopping-cart').removeClass('open');
    });

    //Comprar (checkout) 
    $(document).on('click', '.btn-buy', function() {
        const productId = $(this).data('id');
        console.log(productId);
    });
    //Adicionar ao carrinho
    $(document).on('click', '.btn-add-cart', function() {
        const productId = $(this).data('id');
        shoppingCartHandler(productId, 'add');
    });



    // $(document).on('click', '.btn-delete-product', function() {
    //     const productId = $(this).data('id');
    //     const productName = $(this).closest('.product-card').find('.product-name').text();  //pega o nome do produto
    //     deleteProduct(productName, productId);
    // });
    // $(document).on('click', '.btn-edit-product', function() {
    //     const productId = $(this).data('id');
    //     editProduct(productId);
    // });
});
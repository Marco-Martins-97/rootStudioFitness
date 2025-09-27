function loadShopProducts(){
    let HTMLcontent = '';
    for (let i = 0; i < 8; i++) {
        HTMLcontent += `   
            <li class='product-card'>
                <img src='imgs/products/notebook.jpg' alt='Produto' class='product-img'>
                <h4 class='product-name'>Produto ${i}</h4>
                <div class='product-price'>R$ 49,90</div>
                <div class='product-actions'>
                    <button class='btn-buy' data-id='${i}'>Comprar</button>
                    <button class='btn-add-cart' data-id='${i}'>Adicionar ao Carrinho</button>
                </div>
            </li>
        `;
    }
    $('.products-container').html(HTMLcontent);
}

function loadCartProducts(){
    let HTMLcontent = '';
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
    $('.cart-container').html(HTMLcontent);
}
//  <div class='cart-item-meta'>
//                         <span class='cart-item-price'>R$ 49,90</span>
//                         <span class='cart-item-qty'>x2</span>
//                     </div>

$(document).ready(function(){
    loadShopProducts();
    loadCartProducts();

    $('#open-cart-btn').on('click', function() {
        $('.shopping-cart').addClass('open');
        // $('html, body').animate({ scrollTop: 0 }, 'slow');
        $(window).scrollTop(0);
    });
    $('#close-cart').on('click', function() {
        $('.shopping-cart').removeClass('open');
    });
});
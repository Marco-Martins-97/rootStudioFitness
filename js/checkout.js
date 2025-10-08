function validateField(input){
    const name = $(input).attr('name');
    const value = $(input).val();
    const field = $(input).closest('.field-container');

    $.post('includes/validateInputs.inc.php', {input: name, value: value}, function(response){
        if (response.status === 'error'){
            console.error('Erro:', response.message);
            return;
        }
        
        if (response.status === 'invalid'){
            console.warn('Input Invalido:', response.message);
            field.addClass('invalid').find('.error').html(response.message);
            return;
        }

        field.removeClass('invalid').find('.error').html('');

    }, 'json').fail(function () {
        console.error('Erro ao validar os dados.');
    });
}

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('.field-container.required').each(function(){
        let input = $(this).find('input').first();

        if (input.val() === null || input.val().trim() === ''){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Campo de preenchimento obrigatório!');
        }
    });
    return !emptyFields;
}

function isFormValid(formId){
    return $(formId).find('.field-container.invalid').length === 0;
}

function validateStock(productId, qty){
    const errorPopup = $(`<div class='popup'></div>`);
    return $.post('includes/validateInputs.inc.php', { input: 'stock', productId: productId, quantity: qty }, 'json')
    .then(response => {
        const res = typeof response === 'string' ? JSON.parse(response) : response;
        $('.popup').remove();

        if (res.status === 'error'){
            console.error('Erro:', res.message);
            errorPopup.text('Erro ao validar o stock.').appendTo('main');
            setTimeout(function(){
                errorPopup.fadeOut(500, function(){ $(this).remove(); });
            }, 2000);
            return false;
        }
        if (res.status === 'invalid'){
            console.warn('Input Invalido:', res.message);
            errorPopup.text('Sem Stock Disponivel!').appendTo('main');
            setTimeout(function(){
                errorPopup.fadeOut(500, function(){ $(this).remove(); });
            }, 2000);
            return false;
        }
        return true;
    })
    .catch(() => {
        console.error('Erro ao validar o stock.');
        $('.popup').remove();
        errorPopup.text('Erro ao validar o stock.').appendTo('main');
        setTimeout(function(){
            errorPopup.fadeOut(500, function(){ $(this).remove(); });
        }, 2000);
        return false;
    });
}


function updateOrderTotal() {
    let total = 0;

    $('.order-product').each(function() {
      const data = JSON.parse($(this).attr('data-product'));
      total += parseFloat(data.productPrice) * parseInt(data.productQuantity);
    });

    $('.order-total').text(total.toFixed(2) + ' €');
}

function updateOrderSummary($product, data){
    const price = parseFloat(data.productPrice);
    const qty = parseInt(data.productQuantity);
    const total = price * qty;
    const $totalContainer = $product.find('.order-product-total'); 

    let html = `${total.toFixed(2)}€`;
    if (qty > 1) html += ` <span class='cart-product-price-qty'>(${qty} x ${price.toFixed(2)}€)</span>`;
    $totalContainer.html(html);

    updateOrderTotal();
}

function checkEmptyOrder(){
    if (!$('.order-product').length) window.location.href = 'shop.php';
}

$(document).ready(function(){
    const checkoutForm = $('#checkout-form');
    

    $(document).on('click', '.btn-add', function() {
        const $product = $(this).closest('.order-product');
        const data = JSON.parse($product.attr('data-product'));
        const productId = data.productId;
        const addQty = data.productQuantity + 1;

        validateStock(productId, addQty).then(hasStock => {
            if (hasStock){
                data.productQuantity++;
                $product.attr('data-product', JSON.stringify(data));
                updateOrderSummary($product, data);
            } else {
                console.warn('Sem Stock Disponivel!');
                
            }
        });
        
    });

    $(document).on('click', '.btn-remove', function() {
        const $product = $(this).closest('.order-product');
        const data = JSON.parse($product.attr('data-product'));
        const qty = data.productQuantity;

        if (qty > 1){
            data.productQuantity--;
            $product.attr('data-product', JSON.stringify(data));
            updateOrderSummary($product, data);
        } else {
            $product.slideUp(200, function() {
                $(this).remove();
                updateOrderTotal();
                checkEmptyOrder();
            });
        }
    });
    
    $(document).on('click', '.btn-delete', function() {
        const $product = $(this).closest('.order-product');
        $product.slideUp(200, function() {
            $(this).remove();
            updateOrderTotal();
            checkEmptyOrder();
        });
    });




















    $('#fullName').on('input', function(){ validateField(this); });
    $('#birthDate').on('input', function(){ validateField(this); });
    $('#userAddress').on('input', function(){ validateField(this); });



    checkoutForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(checkoutForm) && isFormValid(checkoutForm)){
            $('.validSub').remove();
            const successDiv = $('<div class="validSub">Enviando...</div>');
            $('.form-disclaimer').after(successDiv);

            // depois de 1 segundo e envia o formulário
            setTimeout(function(){
                // checkoutForm.off('submit').submit();
                console.log("ENVIDADO!!")
            }, 1000);
        } else {
            console.error('Invalid Form!');
        }
    });

});
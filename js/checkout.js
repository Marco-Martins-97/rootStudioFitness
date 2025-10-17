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
            console.warn('Campo inválido:', response.message);
            field.addClass('invalid').find('.error').html(response.message);
            return;
        }

        field.removeClass('invalid').find('.error').html('');

    }, 'json').fail(function () {
        console.error('Ocorreu um erro ao validar os dados.');
    });
}

function noEmptyFields(formId){
    let emptyFields = false;
    $(formId).find('.field-container.required').each(function(){
        let input = $(this).find('input').first();

        if (input.val() === null || input.val().trim() === ''){
            emptyFields = true;
            $(this).closest('.field-container').addClass('invalid').find('.error').html('Preenchimento deste campo é obrigatório.');
        }
    });
    return !emptyFields;
}

function isFormValid(formId){
    return $(formId).find('.field-container.invalid').length === 0;
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

function validateStock(productId, qty){
    return $.post('includes/validateInputs.inc.php', { input: 'stock', productId: productId, quantity: qty }, null, 'json')
    .then(response => {
        $('.popup').remove();

        if (response.status === 'error'){
            console.error('Erro:', response.message);
            showPopup('Erro ao validar o stock.');
            return false;
        }
        if (response.status === 'invalid'){
            console.warn('Campo inválido:', response.message);
            showPopup('Sem Stock Disponivel!');
            return false;
        }
        return true;
    })
    .catch(() => {
        console.error('Erro ao validar o stock.');
        showPopup('Erro ao validar o stock.');
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
    const price = parseFloat(data.productPrice) || 0;
    const qty = parseInt(data.productQuantity) || 0;
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

function attachOrderData(formId){
    const products = [];
    
    $('.order-product').each(function() {
        const dataAttr = $(this).attr('data-product');
        if(!dataAttr) return;

        try {
            const data = JSON.parse(dataAttr);
            const productId = data.productId;
            const quantity = parseInt(data.productQuantity) || 0;

            if (productId && quantity > 0) {
                products.push({
                    id: productId,
                    qty: quantity
                });
            }
            
        } catch (e) {
            console.error('Erro ao carregar dados:', dataAttr);
        }
    });
    let orderInput = formId.find('input[name="orderData"]');
    if (!orderInput.length) {
        orderInput = $('<input>', { type: 'hidden', name: 'orderData' });
        formId.append(orderInput);
    }

    orderInput.val(JSON.stringify(products));
    
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

    $('#fullName, #birthDate18, #userAddress').on('input', function(){ validateField(this); });

    checkoutForm.on('submit', function(e){
        e.preventDefault();

        if(noEmptyFields(checkoutForm) && isFormValid(checkoutForm)){
            $('.validSub').remove();
            const successDiv = $('<div class="validSub">A enviar...</div>');
            $('.form-disclaimer').after(successDiv);

            // anexa os dados dos produtos no formulario
            attachOrderData(checkoutForm);

            // depois de 1 segundo e envia o formulário
            setTimeout(function(){
                checkoutForm.off('submit').submit();
            }, 1000);
        } else {
            console.error('O formulário contém erros. Verifique os campos assinalados.');
        }
    });

});
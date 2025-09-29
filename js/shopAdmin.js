function loadShopProducts(){
    $.post('includes/loadServerData.inc.php', {action: 'loadShopProducts'}, function(response){
        // console.log(response);

        if (!response || typeof response !== 'object') {
            console.error('Invalid JSON response:', response);
            $('.products-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar os Produtos!');
            return;
        }

        if (response.status === 'error') {
            console.warn('Server error:', response.message || 'Unknown error');
            $('.products-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar os Produtos!');
            return;
        }

        const products = response.products;
        let HTMLcontent = `   
            <li class='product-card'>
                <img src='imgs/products/newProduct.jpg' alt='Novo Produto' class='product-img'>
                <h4 class='product-name'>Novo Produto</h4>
                <div class='product-info'>0,00 €</div>
                <div class='product-info'>Stock: 0</div>
                <div class='product-actions'>
                    <button class='btn-add-product'>Adicionar</button>
                </div>
            </li>
        `;

        if(products.length > 0){
            products.forEach(product => {
                HTMLcontent += `   
                    <li class='product-card'>
                        <img src='imgs/products/${product.productImgSrc}' alt='${product.productName}' class='product-img' onerror='this.src="imgs/products/defaultProduct.jpg"'>
                        <h4 class='product-name'>${product.productName}</h4>
                        <div class='product-info'>${product.productPrice}€</div>
                        <div class='product-info'>Stock: ${product.productStock}</div>
                        <div class='product-actions'>
                            <button class='btn-edit-product' data-id='${product.id}'>Editar</button>
                            <button class='btn-delete-product' data-id='${product.id}'>Apagar</button>
                        </div>
                    </li>
                `;
            });
        }

        $('.products-container').html(HTMLcontent);
    }, 'json').fail(function () {
        $('.products-container').html('Ocurreu Um Erro, Não Foi Possivel Carregar os Produtos');
    });
}

function addNewProduct(){
    const modal =  `<div class='modal' id='createNewProduct'>
                        <div class='modal-content'>
                            <span id='close-add-modal'>&times;</span>
                            <h2>Addicionar um Produto Novo</h2>
                            <div class='field-container'>
                                <div class='field'>
                                    <label for='product-img'>Imagem (upload):</label>
                                    <input type='file' name='product-img' id='productImg' accept='image/*'>
                                </div>
                                <div class='field'>
                                    <label for='product-name'>Nome:</label>
                                    <input type='text' name='product-name' id='productName' maxlength='255'>
                                </div>
                                <div class='field'>
                                    <label for='product-price'>Preço(€):</label>
                                    <input type='number' name='product-price' id='productPrice' min='0' value='0' step='0.01'>
                                </div>
                                <div class='field'>
                                    <label for='product-stock'>Stock:</label>
                                    <input type='number' name='product-stock' id='productStock' min='1' value='1' step='1'>
                                </div>
                                <div class="error"></div>
                            </div>
                            <div class='btn-container'>
                                <button id='save'>Salvar</button>
                                <button id='cancelAdd'>Cancelar</button>
                            </div>
                        </div>
                    </div>`;
    $('.shop-content').append(modal);

    $('#close-add-modal, #cancelAdd').on('click', function() {
        $('#createNewProduct').remove(); // remove o modal
    });
    
    $('#save').on('click', function() {
        const productImg = $('#productImg')[0].files[0];
        const productName = $('#productName').val().trim();
        const productPrice = $('#productPrice').val().trim();
        const productStock = $('#productStock').val().trim();
        const $error = $('.error');

        let uploadImg = false;
        let imgSize = 0;
        let imgType = '';

        if (productImg){
            uploadImg = true;
            imgSize = productImg.size;
            imgType = productImg.type;
        }

        const datapack = {
            uploadImg: uploadImg,
            valueImgSize: imgSize,
            valueImgType: imgType,
            valueName: productName, 
            valuePrice: productPrice, 
            valueStock: productStock,
        };
        //VALIDA OS INPUTS
        $.post('includes/validateInputs.inc.php', {input: 'addNewProduct', datapack}, function(response){
            if (response.status === 'error'){
                console.error('Erro:', response.message);
                return;
            }
            if (response.status === 'invalid'){
                let msg = '';
                $.each(response.message, function(field, message){
                    msg += message + '<br>';
                    console.warn(`Input Invalido: ${field}: ${message}`);
                });

                $error.html(msg);
                return;
            }
            $error.text('');
            
            //SALVA O PRODUTO
            const formData = new FormData();
            formData.append('action', 'saveNewProduct');
            formData.append('imgFile', productImg);
            formData.append('valueName', productName);
            formData.append('valuePrice', productPrice);
            formData.append('valueStock', productStock);
            
            $.ajax({
                url: 'includes/saveServerData.inc.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                   if (response.status === 'error'){
                        console.error('Erro:', response.message);
                        return;
                    }
                    if (response.status === 'processError'){
                        console.error('Erro:', response.error);
                        $error.html(response.message);
                        return;
                    }
                    if (response.status === 'invalid'){
                        let msg = '';
                        $.each(response.message, function(field, message){
                            msg += message + '<br>';
                            console.warn(`Input Inválido: ${field}: ${message}`);
                        });

                        $error.html(msg);
                        return;
                    }


                    $error.css('color', 'green').text('Produto salvo com sucesso!');
                    setTimeout(() => {
                        $('#createNewProduct').remove();
                        loadShopProducts();
                    }, 1000);

                }, error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $error.text('Erro ao validar os dados.');
                }
            });
        }, 'json').fail(function () {
            console.error('Erro ao validar os dados.');
            $error.text('Erro ao validar os dados.');
        });
    });
            
    
}

function deleteProduct(productName, productId){
    console.log(productId);
    const modal =  `<div class='modal' id='deleteProduct'>
                        <div class='modal-content'>
                            <span id='close-del-modal'>&times;</span>
                            <h2>Apagar ${productName}</h2>
                            <div class="error"></div>
                            <div class='btn-container'>
                                <button id='delete'>Apagar</button>
                                <button id='cancelDel'>Cancelar</button>
                            </div>
                        </div>
                    </div>`;
    $('.shop-content').append(modal);

    $('#close-del-modal, #cancelDel').on('click', function() {
        $('#deleteProduct').remove(); // remove o modal
    });

    $('#delete').on('click', function() {
        $.post('includes/saveServerData.inc.php', {action: 'deleteProduct', productId: productId}, function(response){
            console.log(response);
            if (response.status === 'error') {
                console.error('Server error:', response.message || 'Unknown error');
                return;
            }
            if (response.status === 'processError') {
                console.error('Erro: ', response.error);
                $('.error').text(response.message);
                return;
            } 
            
            console.log(`${productName} foi apagado com sucesso!`);
            $('.error').css('color', 'green').text('Produto apagado com sucesso!');
            setTimeout(() => {
                $('#deleteProduct').remove();
                loadShopProducts();
            }, 1000);   
        
        }, 'json').fail(function () {
            console.error('Erro na ligação ao servidor.');
        });
    });
}






$(document).ready(function(){
    loadShopProducts();

    $(document).on('click', '.btn-add-product', function() {
        addNewProduct();
    });
    
    $(document).on('click', '.btn-delete-product', function() {
        const productId = $(this).data('id');
        const productName = $(this).closest('.product-card').find('.product-name').text();  //pega o nome do produto
        deleteProduct(productName, productId);
    });
    $(document).on('click', '.btn-edit-product', function() {
        const productId = $(this).data('id');
        // addNewproduct();
    });
    

});
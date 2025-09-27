function loadShopProducts(){
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
    for (let i = 0; i < 8; i++) {
        HTMLcontent += `   
            <li class='product-card'>
                <img src='imgs/products/notebook.jpg' alt='Produto' class='product-img'>
                <h4 class='product-name'>Produto ${i}</h4>
                <div class='product-info'>R$ 49,90</div>
                <div class='product-info'>Stock: 0</div>
                <div class='product-actions'>
                    <button class='btn-edit' data-id='${i}'>Editar</button>
                    <button class='btn-remove' data-id='${i}'>Remover</button>
                </div>
            </li>
        `;
    }
    $('.products-container').html(HTMLcontent);
}

function addNewproduct(){
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
                                <button id='cancel'>Cancelar</button>
                            </div>
                        </div>
                    </div>`;
    $('.shop-content').append(modal);

    $('#close-add-modal, #cancel').on('click', function() {
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
                $.each(response.message, function(error, message){
                    msg += message + '<br>';
                    console.warn(`Input Invalido: ${error}: ${message}`);
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

                    if (response.status === 'invalid'){
                        let msg = '';
                        $.each(response.message, function(error, message){
                            msg += message + '<br>';
                            console.warn(`Input Invalido: ${error}: ${message}`);
                        });

                        $error.html(msg);
                        return;
                    }

                    console.log(response);

                    $error.css('color', 'green').text('Produto Criado Com Sucesso.');
                    setTimeout(() => $('#createNewProduct').remove(), 1000);


                }, error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $error.text('Erro ao validar os dados.');
                }
            });
            // $.post('includes/saveServerData.inc.php', {action: 'saveNewProduct', imgFile: productImg, valueName: productName, valuePrice: productPrice, valueStock: productStock}, function(response){
            //     if (response.status === 'error'){
            //         console.error('Erro:', response.message);
            //         return;
            //     }
            //     if (response.status === 'invalid'){
            //         let msg = '';
            //         $.each(response.message, function(error, message){
            //             msg += message + '<br>';
            //             console.warn(`Input Invalido: ${error}: ${message}`);
            //         });

            //         error.html(msg);
            //         return;
            //     }
                
            //     error.css('color', 'green').text('Password alterada com sucesso!');
            //     setTimeout(() => $('#changePwdModal').remove(), 1000);

            // }, 'json').fail(function () {
            //     console.error('Erro ao validar os dados.');
            //     error.text('Erro ao validar os dados.');
            // });
        }, 'json').fail(function () {
            console.error('Erro ao validar os dados.');
            $error.text('Erro ao validar os dados.');
        });
    });
            

}
$(document).ready(function(){
    loadShopProducts();

    $('.btn-add-product').on('click', function() {
        addNewproduct();
    });
    

});
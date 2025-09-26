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

$(document).ready(function(){
    loadShopProducts();

});
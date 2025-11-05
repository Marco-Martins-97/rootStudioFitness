<?php
require_once 'includes/configSession.inc.php'; 

// Verifica se a página foi acedida através de um pedido POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Verifica se o utilizador está autenticado
if (!isset($_SESSION["userRole"], $_SESSION["userId"])) {
    header("Location: shop.php?invalid=login");
    exit;
}

// Verifica se o tipo de checkout é válido
if (!isset($_POST['type']) || !in_array($_POST['type'], ['direct', 'cart'])) {
    header("Location: shop.php?invalid=type");
    exit;
}

$type = trim($_POST['type']);
$checkoutProducts = [];
$userId = $_SESSION['userId'];

if ($type === 'direct'){
    // Verifica se o produto foi enviado corretamente
    if(!isset($_POST['productId'])) {
        header("Location: shop.php?invalid=productId");
        exit;
    }
 
    $productId = trim($_POST['productId']);

    // Carrega os dados do produto a partir da base de dados
    require_once "includes/ShopHandler.php";
    $shop = new Shop();
    $product = $shop->loadProductbyId($productId);

    // Verifica se o produto existe
    if (!$product) {
        header("Location: shop.php?invalid=notFound");
        exit;
    }
    
    // Ajusta os dados do produto para o checkout direto
    $product['productQuantity'] = 1;
    $product['productId'] = $productId;
    unset($product['id']);    // Remove dados desnecessários
    unset($product['productStock']);
    $checkoutProducts[] = $product;

} else {
    // Carrega os produtos do carrinho a partir da base de dados
    require_once "includes/ShopHandler.php";
    $shop = new Shop();
    $checkoutProducts = $shop->loadShoppingCart($userId);
}


function loadOrderSummary($checkoutProducts){
    // Verifica se existem produtos para o checkout (antes de enviar qualquer HTML)
    if (empty($checkoutProducts)) {
        header("Location: shop.php?invalid=empty");
        exit;
    }

    $totalCheckout = 0;
    $HTMLcontent = '<ul class="order-container">';


    foreach ($checkoutProducts as $product) {
        if ((int)$product['isActive'] !== 1) {
            header("Location: shop.php?invalid=inactive");
            exit;
        }

        $price = floatval($product['productPrice']);
        $qty = intval($product['productQuantity']);
        $total = $price * $qty;

        $totalCheckout += $total;
        
        $totalProductContainer = number_format($total, 2, '.', '') . '€';
        if ($qty > 1) {
            $totalProductContainer .= " <span class='cart-product-price-qty'>($qty x " . number_format($price, 2) . "€)</span>";
        }

        // Converte o produto para JSON e escapa para uso seguro em HTML
        $productJSON = htmlspecialchars(json_encode($product, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES);
        
        $HTMLcontent .= "
            <li class='order-product' data-product='$productJSON'>
                <img src='imgs/products/" . $product['productImgSrc'] . "' alt='" . $product['productName'] . "' class='order-product-img' onerror='this.src=\"imgs/products/defaultProduct.png\"'>
                <div class='order-product-info'>
                    <div class='order-product-actions'>
                        <button class='btn-add'>+</button>
                        <button class='btn-remove'>-</button>
                        <button class='btn-delete'><i class='fas fa-trash'></i></button>
                    </div>
                    <h4 class='order-product-name'>" . $product['productName'] . "</h4>
                    <div class='order-product-total'>$totalProductContainer</div>
                </div>
            </li>
        ";
    }
    $HTMLcontent .= '</ul>
        <div class="pay-summary">
            <span>Total a pagar:</span>
            <span class="order-total">'. number_format($totalCheckout, 2, '.', '') . '€</span>
        </div>
    ';
    echo $HTMLcontent;
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Loja - Checkout</title>
        <meta name="description" content="ROOT Studio Fitness - Loja - Checkout">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/checkout.css">
        <!-- Script -->
        <script src="https://kit.fontawesome.com/d132031da6.js?v=2" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <header>
            <nav>
                <a href="index.php" class="logo"><img src="imgs/logo/iconNomeOriginal.png" alt="Root Studio Fitness"></a>
                <div class="menu-toggle mobile-only"><i class="fas fa-bars"></i></div>
                <div class="menu">
                    <div class="left-menu">
                        <a href="index.php">Início</a>
                        <a href="plans.php">Planos de Treino</a>
                        <a href="about.php">Sobre Nós</a>
                    </div>
                    <div class="right-menu">
                        <a href="shop.php">Loja</a>
                        <?php if(!isset($_SESSION["userId"])){ ?>
                            <div class="guest">
                                <a href="login.php">Entrar</a>
                                <a href="signup.php">Registar</a>
                            </div>
                        <?php } else{ ?>
                            <a href="areaClient.php">Área de Cliente</a>
                            <div class="dropdown-toggle">
                                <i class="fas fa-chevron-down"></i>
                                <?php echo $_SESSION['username']; ?>
                            </div>
                            <div class="dropdown">
                                <a href="profile.php">Perfil</a>
                                <a href="orders.php">Encomendas</a>
                                <?php if(isset($_SESSION["userRole"]) && $_SESSION["userRole"] === 'admin'){ ?>
                                    <a href="clientsAdmin.php">Administração de Clientes</a>
                                    <a href="shopAdmin.php">Administração da Loja</a>
                                    <a href="ordersAdmin.php">Administração de Encomendas</a>
                                <?php } ?>
                                
                                <form action="includes/logout.inc.php" method="post">
                                    <button>Sair</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </nav>
        </header>
        <main>

            <div class="title-container">
                <h1>Lista de Compras</h1>
            </div>
            <div class="order-summary">
                <?php loadOrderSummary($checkoutProducts); ?>
            </div>
            <div class="customer-details">
                <div class="form-container">
                    <h2>Dados de Envio</h2>
                    <form action="includes/checkout.inc.php" method="post" id="checkout-form">
                        <input type="hidden" name="checkoutType" value="<?php echo $type; ?>">
                        <!-- Nome Completo -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="fullName">Nome completo:</label>
                                <input type="text" id="fullName" name="fullName" maxlength="255">
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Data de Nascimento -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="birthDate18">Data de nascimento:</label>
                                <input type="date" id="birthDate18" name="birthDate18">
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Morada -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="userAddress">Morada:</label>
                                <input type="text" id="userAddress" name="userAddress" maxlength="255">
                            </div>
                            <div class="error"></div>
                        </div>

         
                        <p class="form-disclaimer">Campos de preenchimento obrigatório.</p>
                        <button type="submit">Finalizar Compra</button>
                    </form>
                </div>
            </div>

        </main>
        <footer>
            <img src="imgs/logo/iconOriginal.png" alt="Root Studio logo">
            <div class="footer-content">
                <div class="social">
                    <a href="https://instagram.com/root.studiofitness" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://api.whatsapp.com/send?phone=351925677310" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                <p>&copy; 2025 Root Studio Fitness. Todos os direitos reservados.</p>
            </div>
        </footer>

        <script src="js/navMenu.js"></script>
        <script src="js/checkout.js"></script>
    </body>
</html>


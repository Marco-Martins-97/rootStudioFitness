<?php
require_once 'includes/configSession.inc.php'; 

//verifica se acessou a pagina ataves de um Post
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: shop.php");
    exit;
}

//verifica e o utilizador esta logado 
if(!isset($_SESSION["userRole"])){  
    header("Location: shop.php?invalid=login");
    exit;
}

//verifica se uma tipo de checkout é valido
if (!isset($_POST['type']) || !in_array($_POST['type'], ['direct', 'cart'])) {
    header("Location: shop.php?invalid=type");
    exit;
}

$type = htmlspecialchars(trim($_POST['type']));
$checkoutProducts = [];
$userId = $_SESSION['userId'];

if ($type === 'direct'){
    if(!isset($_POST['productId'])) {
        header("Location: shop.php?invalid=productId");
        exit;
    }
 
    $productId = htmlspecialchars(trim($_POST['productId']));

    //carrega os dados do produto da base de dados
    require_once "includes/ShopHandler.php";
    $shop = new Shop();
    // $checkoutProducts = $shop->loadShoppingCart($userId);

} else {
    
    //carrega os dados do produto no carrinho da base de dados
    require_once "includes/ShopHandler.php";
    $shop = new Shop();
    $checkoutProducts = $shop->loadShoppingCart($userId);
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
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/checkout.css">
        <script src="https://kit.fontawesome.com/d132031da6.js?v=2" crossorigin="anonymous"></script>
        <!-- Script -->
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
                            <div class="dropdown-toggle">
                                <i class="fas fa-chevron-down"></i>
                                <?php echo $_SESSION['username']; ?>
                            </div>
                            <div class="dropdown">
                                <a href="profile.php">Perfil</a>
                                <?php if(isset($_SESSION["userRole"]) && $_SESSION["userRole"] === 'admin'){ ?>
                                    <a href="shopAdmin.php">Administração Loja</a>
                                    <a href="clientsAdmin.php">Administração Clientes</a>
                                <?php } ?>
                                <a href="profile.php">Encomendas</a>
                                
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
            <?php 
                echo "Type: ".$type."<br>";
                if ($type === 'direct'){
                    echo "Id: ".$productId;
                }
            ?>
            <div class="order-summary">
                <?php 
                    foreach ($checkoutProducts as $index => $product) {
                        echo "<h3>Produto " . ($index + 1) . "</h3>";
                        foreach ($product as $key => $value) {
                            echo "<p><strong>$key:</strong> $value</p>";
                        }
                    }
                ?>
            </div>
            
        </main>
        <footer>
            <img src="imgs/logo/iconOriginal.png" alt="Root Studio logo">
            <div class="footer-content">
                <div class="social">
                    <a href="https://instagram.com/root.studiofitness" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://api.whatsapp.com/send?phone=351925677310" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
                <p>&copy; 2025 Root Studio Fitness</p>
            </div>
        </footer> 

        <script src="js/navMenu.js"></script>
        <script src="js/checkout.js"></script>
    </body>
</html>


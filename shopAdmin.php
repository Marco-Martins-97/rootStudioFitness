<?php
    require_once 'includes/configSession.inc.php';
    if(!isset($_SESSION["userRole"]) || $_SESSION["userRole"] !== "admin"){ 
        header("Location: index.php"); 
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Administração da Loja</title>
        <meta name="description" content="Root Studio Fitness - Administração da Loja">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/shopAdmin.css">
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
            <section class="shop-content">
                <ul class="products-container"></ul>
            </section>
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
        <script src="js/shopAdmin.js"></script>
    </body>
</html>
<?php require_once 'includes/configSession.inc.php'; ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Loja</title>
        <meta name="description" content="Descobre a loja do ROOT Studio Fitness em Esposende. Produtos de fitness, acessórios de treino e merchandising exclusivo para apoiar o teu estilo de vida saudável.">
        <meta name="keywords" content="loja ROOT Studio, loja fitness Esposende, acessórios de treino, material desportivo Esposende, roupa fitness, merchandising ROOT, suplementos Esposende, estúdio de fitness Braga, ginásio Esposende, saúde e bem-estar">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/main.css">
        <!-- Script -->
        <script src="https://kit.fontawesome.com/d132031da6.js?v=2" crossorigin="anonymous"></script>
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

    </body>
    <script src="js/navMenu.js"></script>
</html>
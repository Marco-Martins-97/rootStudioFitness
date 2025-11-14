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
        <title>Root Fitness Studio - Administração</title>
        <meta name="description" content="ROOT Studio Fitness - Administração">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/clientsAdmin.css">
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
            <div class="sub-menu-container">
                <select id="sub-menu">
                    <option value="general" selected>Geral</option>
                    <option value="applications">Candidaturas</option>
                    <option value="exercises">Exercicios</option>
                    <option value="trainingPlans">Planos de Treino</option>
                    <option value="nutrition">Planos Alimentares</option>
                    <option value="assessment">Avaliações Fisicas</option>
                    <option value="challenges">Desafios</option>
                    <option value="calendar">Calendario</option>
                </select>
                <div class="search-box">
                    <input type="text" id="search-input">
                    <button id="search-btn"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="display-container"></div>
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
        <script src="js/clientsAdmin.js"></script>
    </body>
</html>
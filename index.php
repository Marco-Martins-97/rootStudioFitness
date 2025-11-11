<?php require_once 'includes/configSession.inc.php'; ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Treino Funcional em Esposende</title>
        <meta name="description" content="O ROOT Studio Fitness em Esposende é para todos: iniciantes ou experientes. Treino funcional personalizado, com horários flexíveis e foco no teu bem-estar.">
        <meta name="keywords" content="fitness Esposende, treino funcional Esposende, personal trainer Esposende, aulas de grupo Esposende, estúdio de fitness Braga, treino personalizado Esposende, ginásio Esposende, Root Studio, saúde e bem-estar Esposende">
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
                            <?php if (isset($_SESSION["userRole"]) && ($_SESSION["userRole"] === 'client' || $_SESSION["userRole"] === 'admin')) {  ?>
                            <a href="areaClient.php">Área de Cliente</a>
                            <?php } ?>
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
            <section id="hero">
                <h1>" Everything Begins with a Strong Root. "</h1>
            </section>
            <section id="join">
                <div class="img-text-container">
                    <div class="img-container">
                        <img src="imgs/content/team.jpg" alt="membros da Root Studio">
                    </div>
                    <div class="text-container">
                        <h2>Junta-te a Nós</h2>
                        <p>Cada um de nós traz algo único, mas partilhamos o mesmo propósito: ajudar-te a mover melhor, a viver com mais consciência e a reencontrar a tua raiz.</p>
                        <a href="plans.php">Estamos aqui para ti</a>
                    </div>
                </div>
            </section>
            <section id="plans">
                <div class="plans-container">
                    <a href="plans.php#personalized" class="plan-container">
                        <img src="imgs/content/treinoPersonalizado.jpg" alt="Treino Personalizado">
                        <h3>Treinos Personalizados</h3>
                    </a>
                    <a href="plans.php#group" class="plan-container">
                        <img src="imgs/content/aulasGrupo.jpg" alt="Aulas de Grupo">
                        <h3>Aulas de Grupo</h3>
                    </a>
                    <a href="plans.php#specialties" class="plan-container">
                        <img src="imgs/content/treinoTerapeitico.jpg" alt="Treino terapêuticoo">
                        <h3>Treino Terapêutico</h3>
                    </a>
                    <a href="plans.php#specialties" class="plan-container">
                        <img src="imgs/content/padel.jpeg" alt="Pádel">
                        <h3>Pádel</h3>
                    </a>
                </div>
            </section>
            <section id="video">
                <div class="video-container">
                    <iframe 
                        src="https://www.youtube.com/embed/TDQ6FEE_sok"
                        title="Video YouTube Root"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                    ></iframe>
                </div>
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
        <script src="js/main.js"></script>
    </body>
</html>
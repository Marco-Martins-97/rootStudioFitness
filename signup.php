<?php 
    require_once 'includes/configSession.inc.php'; 
    
    if(isset($_SESSION["userId"])){ 
        header("Location: index.php"); 
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Registar</title>
        <meta name="description" content="Regista-te no ROOT Studio Fitness em Esposende e começa a tua jornada de treino funcional. Treinos personalizados, aulas em grupo e acompanhamento profissional para todos os níveis.">
        <meta name="keywords" content="registo ROOT Studio, inscrição fitness Esposende, treino funcional personalizado, aulas de grupo Esposende, personal trainer Esposende, estúdio de fitness Braga, ginásio Esposende, saúde e bem-estar, treino para iniciantes, treino avançado Esposende">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/signup.css">
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
                        <div class="guest">
                            <a href="login.php">Entrar</a>
                            <a href="signup.php">Registar</a>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="form-container">
                <h1>Registar</h1>
                <form action="includes/signup.inc.php" method="post" id="signup-form">
                    <!-- Primeiro Nome -->
                    <div class="field-container required">
                        <div class="field">
                            <label for="firstName">Nome:</label>
                            <input type="text" id="firstName" name="firstName" maxlength="255">
                        </div>
                        <div class="error"></div>
                    </div>
                    <!-- Apelido -->
                    <div class="field-container required">
                        <div class="field">
                            <label for="lastName">Apelido:</label>
                            <input type="text" id="lastName" name="lastName" maxlength="255">
                        </div>
                        <div class="error"></div>
                    </div>
                    <!-- Email -->
                    <div class="field-container required">
                        <div class="field">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" maxlength="255">
                        </div>
                        <div class="error"></div>
                    </div>
                    <!-- Password -->
                    <div class="field-container required">
                        <div class="field">
                            <label for="pwd">Palavra-passe:</label>
                            <input type="password" id="pwd" name="pwd" minlength="8" maxlength="255">
                        </div>
                        <div class="error"></div>
                    </div>
                    <!-- Confirmação de Password -->
                    <div class="field-container required">
                        <div class="field">
                            <label for="confirmPwd">Confirmação da palavra-passe:</label>
                            <input type="password" id="confirmPwd" name="confirmPwd" minlength="8" maxlength="255">
                        </div>
                        <div class="error"></div>
                    </div>

                    <p class="form-disclaimer">Campos de preenchimento obrigatório.</p>
                    <button type="submit">Registar</button>
                    <div class="link-container"><a href="login.php">Já tens conta? Entrar</a></div>
                </form>
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
        <script src="js/signup.js"></script>
    </body>
</html>
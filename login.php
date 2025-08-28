<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Entrar</title>
        <meta name="description" content="Acede à tua conta no ROOT Studio Fitness em Esposende. Consulta os teus treinos, marca aulas e acompanha o teu progresso de forma rápida e segura.">
        <meta name="keywords" content="login ROOT Studio, conta fitness Esposende, treino funcional online, marcar aulas Esposende, acompanhamento de treino, treino personalizado Esposende, estúdio de fitness Braga, ginásio Esposende, saúde e bem-estar, portal do cliente ROOT">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/login.css">
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
                    <h1>Entrar</h1>
                    <form action="" method="post" id="login-form">
                        <!-- Email -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" maxlength="255" require>
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="pwd">Password:</label>
                                <input type="password" id="pwd" name="pwd" maxlength="255" require>
                            </div>
                        </div>
                        
                        <div class="error">Email ou palavra-passe incorretos.</div>

                        <button type="submit">Entrar</button>
                        <div class="link-container"><a href="signup.php">Registar</a></div>
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
                <p>&copy; 2025 Root Studio Fitness</p>
            </div>
        </footer> 

    </body>
    <script src="js/navMenu.js"></script>
    <script src="js/login.js"></script>
</html>
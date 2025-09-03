<?php require_once 'includes/configSession.inc.php'; ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Planos de Treino</title>
        <meta name="description" content="Alcança os teus objetivos com os planos de treino do ROOT Studio Fitness em Esposende. Treino funcional adaptado a ti, com apoio profissional e ambiente motivador.">
        <meta name="keywords" content="fitness Esposende, treino funcional Esposende, personal trainer Esposende, aulas de grupo Esposende, estúdio de fitness Esposende, treino personalizado, ginásio em Esposende, Root Studio Fitness, saúde e bem-estar, atividade física Esposende">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/plans.css">
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
            <section id="info">
                <div class="info-container">
                    <h1>Planos de Treino</h1>
                    <p>Os planos de treino no Root vão muito além do físico. Aqui, o verdadeiro progresso começa com uma base sólida e é construído com hábitos saudáveis, consciência corporal e autonomia. Terás acesso a uma app personalizada, com planos pensados para o teu corpo, ritmo e rotina. Mais do que personal trainers, somos parceiros na tua jornada - ajudamos-te a gostar de te movimentar, a valorizar o processo e a evoluir com propósito.</p>
                </div>
            </section>

            <section id="personalized">
                <div class="plan-container">
                    <div class="plan-img-container">
                        <img src="imgs/content/treinoPersonalizado.jpg" alt="Treino Personalizado">
                    </div>
                    <div class="plan-description">
                        <h2>Treino Personalizado</h2>
                        <ul>
                            <li>Treinos de 1 Hora</li>
                            <li>De 1 a 3 Treinos por Semana</li>
                            <li>Acesso Livre ao Estudio (1x) *</li>
                        </ul>
                        <h3>Individual</h3>
                        <p>A Partir de 22,5€ por Semana</p>
                        <h3>Grupos Reduzidos <span class="no-bold-small">(Maximo 3 Pessoas)</span></h3>
                        <p>A Partir de 35€ por Semana</p>
                        <p class="disclaimer">*Dentro do horário estipulado</p>
                        <div class="btn-container">
                            <button class="join-btn" data-option="1">Individual</button>
                            <button class="join-btn" data-option="2">Grupos Reduzidos</button>
                        </div>
                    </div>
                </div>
            </section>
            <section id="group">
                <div class="plan-container">
                    <div class="plan-img-container">
                        <img src="imgs/content/aulasGrupo.jpeg" alt="Aulas de Grupo">
                    </div>
                    <div class="plan-description">
                        <h2>Aulas de Grupo</h2>
                        <ul>
                            <li>Aulas de 1 Hora</li>
                            <li>2 Aulas por Semana *</li>
                            <li>Grupos de 6 Pessoas</li>
                        </ul>
                        <p>Apenas 30€ por Mês</p>
                        <p class="disclaimer">*Aulas Com Marcações Pre-Defenidas</p>
                        <button class="join-btn" data-option="3">Adere Já</button>
                    </div>
                </div>
            </section>
            <section id="specialties">
                <div class="plan-container">
                    <div class="plan-img-container">
                        <img src="imgs/content/treinoTerapeitico.jpg" alt="Especialidades Adicionais">
                    </div>
                    <div class="plan-description">
                        <h2>Especialidades Adicionais</h2>
                        <ul>
                            <li>Treino Terapêutico</li>
                            <li>Padel</li>
                            <li>Planos Adapatados para Rendimento e Recuperação</li>
                        </ul>
                        <p>A Partir de 22,5€ por Semana</p>
                        <div class="btn-container">
                            <button class="join-btn" data-option="4">Terapêutico</button>
                            <button class="join-btn" data-option="5">Padel</button>
                        </div>
                    </div>
                </div>
            </section>
            <section id="open-studio">
                <div class="plan-container">
                    <div class="plan-img-container">
                        <img src="imgs/content/estudio.png" alt="Acesso ao Estudio">
                    </div>
                    <div class="plan-description">
                        <h2>Acesso ao Estudio</h2>
                        <ul>
                            <li>Acesso livre (4x) *</li>
                        </ul>
                        <p>Apenas 45€ por Mês</p>
                        <p class="disclaimer">*Dentro do horário estipulado</p>
                        <button class="join-btn" data-option="6">Adere Já</button>
                    </div>
                </div>
            </section>
            <section id="application">
                <div class="form-container">
                    <h2>Formulário de Inscrição</h2>
                    <?php 
                        if(isset($_SESSION["userRole"])){  
                            if($_SESSION["userRole"] === 'user' && !isset($_SESSION["userApplied"])){ 
                    ?>
                    <form action="includes/application.inc.php" method="post" id="application-form">
                        <!-- Nome Completo -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="fullName">Nome Completo:</label>
                                <input type="text" id="fullName" name="fullName" maxlength="255">
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Data de Nascimento -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="birthDate">Data de Nascimento:</label>
                                <input type="date" id="birthDate" name="birthDate">
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Género -->
                        <div class="field-container required">
                            <div class="field">
                                <legend>Gênero:</legend>
                                <div class="gender-container">
                                    <div class="gender-field">
                                        <input type="radio" id="genderMale" name="gender" value="male">
                                        <label for="genderMale">Masculino</label>
                                    </div>
                                    
                                    <div class="gender-field">
                                        <input type="radio" id="genderFemale" name="gender" value="female">
                                        <label for="genderFemale">Feminino</label>
                                    </div>
                                </div>
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
                        <!-- NIF -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="nif">NIF:</label>
                                <input type="text" id="nif" name="nif" pattern="[0-9]{9}" inputmode="numeric" maxlength="9">
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Telefone -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="phone">Telefone:</label>
                                <input type="tel" id="phone" name="phone" pattern="[0-9]{9}" inputmode="numeric" maxlength="9" placeholder="+351">
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Plano de Treino -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="training-plan">Plano de Treino:</label>
                                <select id="training-plan" name="training-plan">
                                    <option value="" disabled selected>Selecione um Plano</option>
                                    <option value="personalized1">Individual</option>
                                    <option value="personalized2">Grupos Reduzidos</option>
                                    <option value="group">Aulas de Grupo</option>
                                    <option value="terapy">Treino Terapêutico</option>
                                    <option value="padel">Padel</option>
                                    <option value="openStudio">Acesso ao Estúdio</option>
                                </select>
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Experiência -->
                        <div class="field-container required">
                            <div class="field">
                                <label for="experience">Experiência:</label>
                                <select id="experience" name="experience">
                                    <option value="" disabled selected>Selecione a sua Experiência</option>
                                    <option value="beginner">Iniciante (nunca treinou)</option>
                                    <option value="intermediate">Intermédio (já treinou antes)</option>
                                    <option value="advanced">Avançado (treina regularmente)</option>
                                </select>
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Plano Alimentar -->
                        <div class="field-container">
                            <div class="checkbox-field">
                                <input type="checkbox" id="nutrition-plan" name="nutrition-plan">
                                <label for="nutrition-plan">Aderir ao Plano Alimentar</label>
                            </div>
                            <div class="error"></div>
                        </div>
                        <!-- Problemas de Saude -->
                        <div class="field-container">
                            <div class="health-field">
                                <div class="checkbox-field">
                                    <input type="checkbox" id="health-issues" name="health-issues">
                                    <label for="health-issues">Tem algum problema de saúde?</label>
                                </div>
                                <textarea id="health-details" name="health-details" rows="3" placeholder="Detalhe os problemas de saude."></textarea>
                            </div>
                            <div class="error"></div>
                        </div>
                       
                        <!-- Termos & Condições -->
                        <div class="field-container required">
                            <div class="checkbox-field">
                                <input type="checkbox" id="terms" name="terms"><!-- required -->
                                <label for="terms">Aceito os <a href="termsAndConditions.php" target="_blank">Termos e Condições.</a></label>
                            </div>
                            <div class="error"></div>
                        </div>


                        <p class="form-disclaimer">Campos de preenchimento obrigatório.</p>
                        <button type="submit">Enviar Inscrição</button>
                    </form>
                    <?php } else if($_SESSION["userRole"] === 'user' && isset($_SESSION["userApplied"])){ ?>
                        <div class="form-unavailable">
                            <h3>Candidatura já foi enviada, aguarde confirmação.</h3>
                        </div>
                    <?php } else if($_SESSION["userRole"] === 'client'){ ?>
                        <div class="form-unavailable">
                            <h3>Já é um Cliente!</h3>
                        </div>
                    <?php } else { ?>
                        <div class="form-unavailable">
                            <h3>Não é Possivel Increver!</h3>
                        </div>
                    <?php } } else{  ?>
                        <div class="connect">
                            <a href="login.php">Entrar</a>
                            <a href="signup.php">Registar</a>
                        </div>
                    <?php } ?>
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
                <p>&copy; 2025 Root Studio Fitness</p>
            </div>
        </footer> 

    </body>
    <script src="js/navMenu.js"></script>
    <script src="js/plans.js"></script>
</html>
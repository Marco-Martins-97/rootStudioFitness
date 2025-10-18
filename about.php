<?php require_once 'includes/configSession.inc.php'; ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <!-- Titulo & Descrição -->
        <title>Root Fitness Studio - Sobre Nós</title>
        <meta name="description" content="ROOT Studio Fitness em Esposende: treinos funcionais personalizados, aulas de grupo dinâmicas e acompanhamento profissional para transformar a tua saúde e bem-estar num ambiente motivador.">
        <meta name="keywords" content="ROOT Studio Fitness, fitness Esposende, treino funcional Esposende, personal trainer Esposende, aulas de grupo Esposende, treino personalizado Esposende, estúdio de fitness Esposende, ginásio em Esposende, saúde e bem-estar Esposende, atividade física Esposende">
        <meta name="author" content="Marco Martins">
        <!-- Browser -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Style -->
        <link id="favicon" rel="shortcut icon" href="imgs/logo/iconPreto.png" type="image/x-icon">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/about.css">
        <!-- Script -->
        <script src="https://kit.fontawesome.com/d132031da6.js?v=2" crossorigin="anonymous"></script>
        <!-- OpenStreetMap -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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
            <section id="history">
                <div class="history-container">
                    <h1>Root Studio Fitness</h1>
                    <p>No domingo, dia 15 de julho de 2025, o Root abriu portas para um novo capítulo. No Root, acreditamos que tudo começa com uma base sólida — e essa base somos nós. Esta equipa não é feita só de personal trainers. É feita de histórias, experiências, diferentes olhares sobre o corpo e o movimento. É feita de pessoas que escolhem estar presentes, ouvir, adaptar, motivar e caminhar ao lado de quem confia em nós. Cada um de nós traz algo único, mas partilhamos o mesmo propósito: ajudar-te a mover melhor, a viver com mais consciência e a reencontrar a tua raiz.</p>
                </div>
            </section>

            <section id="profile-joao">
                <div class="img-text-container">
                    <div class="img-container">
                        <img src="imgs/content/joao.jpg" alt="João Figueiredo">
                    </div>
                    <div class="text-container">
                        <h2>João Figueiredo</h2>
                        <p>Chamo-me João e sou o fundador do Root - um estúdio que nasceu da minha história, da paixão pelo movimento e da convicção de que todo o progresso começa com uma base sólida: no corpo, na mente e na vida.</p>
                        <p>Sou licenciado em Ciências do Desporto, com mestrado em Ensino, pós-graduação em Treino Terapêutico e formação em Pilates.</p>
                        <p>Tive uma infância marcada pela obesidade, onde pesei mais de 130 kg. Uma realidade que mudou quando decidi transformar a minha vida através do movimento. Cheguei aos 60 kg e, desde então, nunca mais parei. Musculação, futebol, natação, padel… </p>
                    </div>
                </div>
            </section>

            <section id="profile-sophie">
                <div class="text-img-container">
                    <div class="img-container">
                        <img src="imgs/content/sophie.jpg" alt="Sophie Rocha">
                    </div>
                    <div class="text-container">
                        <h2>Sophie Rocha</h2>
                        <p>Olá, sou a Sophie e sempre vivi com o desporto no coração. Sou licenciada em Gestão Pública e mestre em Gestão das Organizações, mas foi durante a pandemia que a vida me desafiou a olhar para dentro — e decidir seguir o meu verdadeiro sonho: trabalhar na área do exercício físico.</p>
                        <p>Formei-me como Técnica de Exercício Físico pela <a href="https://www.instagram.com/fitnessacademy/"  target="_blank">@fitnessacademy</a>, onde obtive o meu título profissional. Desde então, especializei-me em aulas de grupo, e foi aí que encontrei a minha grande paixão: o step.</p>
                        <p>Sou movida por alegria, empatia e energia positiva. Gosto de criar um ambiente leve, onde o treino é mais do que esforço — é conexão, evolução e bem-estar.</p>
                    </div>
                </div>
            </section>

            <section id="contacts">
                <div class="contacts-container">
                    <div id="map" class="map"></div>
                    <div class="contact-info">
                        <p>Morada: <a href="https://maps.app.goo.gl/ToG9UKXNECdGPDrY6" target="_blank">Travessa Doutor Francisco Sá Carneiro 150 fração 1 G, 4740-010 Esposende</a></p>
                        <p>Telemóvel: <a href="tel:+351925677310">+351 925 677 310</a></p>
                        <div>
                            <p>Horários:</p>
                            <div class="schedule-container">
                                <div class="schedule">
                                    <div class="day">Segunda-Feira:</div><div class="time">07h00 - 20h30</div>
                                </div>
                                <div class="schedule">
                                    <div class="day">Terça-Feira:</div><div class="time">07h00 - 20h30</div>
                                </div>
                                <div class="schedule">
                                    <div class="day">Quarta-Feira:</div><div class="time">07h00 - 20h30</div>
                                </div>
                                <div class="schedule">
                                    <div class="day">Quinta-Feira:</div><div class="time">07h00 - 20h30</div>
                                </div>
                                <div class="schedule">
                                    <div class="day">Sexta-Feira:</div><div class="time">07h00 - 20h30</div>
                                </div>
                                <div class="schedule">
                                    <div class="day">Sábado:</div><div class="time">08h00 - 12h00</div>
                                </div>
                                <div class="schedule">
                                    <div class="day">Domingo:</div><div class="time">Encerrado</div>
                                </div>
                            </div>
                        </div>
                        <div class="social-container">
                            <a href="https://instagram.com/root.studiofitness" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://api.whatsapp.com/send?phone=351925677310" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </section>


            <section id="faq">
                <div class="faq-container">
                    <h3>FAQ</h3>
                    <div class="faq-item">
                        <div class="faq-question">
                            Quem Pode Treinar no Root?
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            Toda a Gente, O Root foi criado para pessoas reais, com rotinas, dúvidas, objetivos e ritmos diferentes.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como Funciona o Acompanhamento dos Treinos?
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            No Root, acreditamos que o verdadeiro progresso começa com uma base sólida.
                            Somos mais do que personal trainers. Queremos ajudar-te a gostar de te movimentar, a valorizar o processo e a desafiar-te continuamente.
                            Aqui, todos os treinos vão além do físico, procuramos promover hábitos saudáveis, consistência corporal e autonomia.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            Como Posso Marcar o Meu Treino?
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            Basta <a href="signup.php">registar</a> no site e enviar uma <a href="plans.php#application">candidatura</a>. Pode também enviar mensagem pelo <a href="https://api.whatsapp.com/send?phone=351925677310" target="_blank">WhatsApp</a> ou <a href="https://instagram.com/root.studiofitness" target="_blank">Instagram</a>.
                        </div>
                    </div>

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

        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="js/navMenu.js"></script>
        <script src="js/map.js"></script>
        <script src="js/about.js"></script>
    </body>
</html>
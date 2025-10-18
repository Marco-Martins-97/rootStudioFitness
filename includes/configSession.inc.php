<?php
// Configuração da sessão
ini_set('session.use_only_cookies',1);

// Parâmetros dos cookies
session_set_cookie_params([
    'lifetime' => 0,                // duração do cookie: até fechar o browser
    'domain' => 'localhost',        // alterar para o domínio real
    'path' => '/',
    'secure' => false,              // alterar para 'true' quando estiver online com HTTPS
    'httponly' => true              // impede acesso via JavaScript
]);

// Inicia a sessão
session_start();

$interval = 1800; // 30 min
$timeout = 3600; // 1 hora

// Regenera o ID da sessão a cada 30 minutos
if(!isset($_SESSION['last_regeneration'])){
    regenerateSessionId();
}else{
    if(time() - $_SESSION['last_regeneration'] >= $interval){
        regenerateSessionId();
    }
}

// Encerra a sessão depois de 1 hora de inatividade
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)){
    session_unset();
    session_destroy();

    // Apaga a cookie da sessão no navegador
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
} else {
    $_SESSION['last_activity'] = time();    // Atualiza a ultima interação
}

// Função para regenerar o ID da sessão
function regenerateSessionId(){
    session_regenerate_id(true);                // cria um novo ID e elimina o antigo
    $_SESSION['last_regeneration'] = time();    // actualiza o timestamp da última regeneração
}
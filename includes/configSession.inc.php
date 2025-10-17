<?php
// Configuração da sessão
ini_set('session.use_only_cookies',1);

// Parâmetros dos cookies
session_set_cookie_params([
    'lifetime' => 18000,            // duração do cookie: 30 min
    'domain' => 'localhost',        // alterar para o domínio real
    'path' => '/',
    'secure' => false,              // alterar para 'true' quando estiver online com HTTPS
    'httponly' => true              // impede acesso via JavaScript
]);

// Inicia a sessão
session_start();

$interval = 18000; // 30min

// Regenera o ID da sessão a cada 30 minutos
if(!isset($_SESSION['last_regeneration'])){
    regenerateSessionId();
}else{
    if(time() - $_SESSION['last_regeneration'] >= $interval){
        regenerateSessionId();
    }
}

// Função para regenerar o ID da sessão
function regenerateSessionId(){
    session_regenerate_id(true);                // cria um novo ID e elimina o antigo
    $_SESSION['last_regeneration'] = time();    // actualiza o timestamp da última regeneração
}
<?php
//config da sessão
ini_set('session.use_only_cookies',1);

//cookies paramters
session_set_cookie_params([
    'lifetime' => 1800,
    'domain' => 'localhost',    //alterar o dominio
    'path' => '/',
    'secure' => false,          //alterar para 'true' quando estiver online
    'httponly' => true
]);

//inicia a sessao
session_start();

// $interval = 1800; // 30min
$interval = 18000; // Teste


//regenera a id da sessão a cada 30 min
if(!isset($_SESSION['last_regeneration'])){
    regenerateSessionId();
}else{
    if(time() - $_SESSION['last_regeneration'] >= $interval){
        regenerateSessionId();
    }
}

//funçao para regenerar a sessao
function regenerateSessionId(){
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
<?php
session_start();    // Inicia a sessão
session_unset();    // Remove todas as variáveis de sessão
session_destroy();  // Destrói a sessão

// Apaga a cookie da sessão no navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

header('Location: ../index.php?logout=success');    // Redireciona para a página inicial
exit;
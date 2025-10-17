<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

function getPost($input){
    return trim($_POST[$input] ?? ''); 
}

$email = filter_input(INPUT_POST, 'loginEmail', FILTER_VALIDATE_EMAIL);
$pwd = getPost('loginPwd');
    
try { 
    require_once 'Login.php';
    $login = new Login($email, $pwd);
    $login -> login();
    
} catch (PDOException $e) {
    error_log('Erro: ' . $e->getMessage());
    header('Location: ../login.php?connection=error');
    exit;
}
<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

function getPost($input){
    return trim($_POST[$input] ?? ''); 
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$pwd = getPost('pwd');
$confPwd = getPost('confirmPwd');
$firstName = getPost('firstName');
$lastName = getPost('lastName');
    
try { 
    require_once 'Signup.php';
    $signup = new Signup($email, $pwd, $confPwd, $firstName, $lastName);
    $signup -> newSignup();
    
} catch (PDOException $e) {
    error_log('Erro: ' . $e->getMessage());
    header('Location: ../signup.php?connection=error');
    exit;
}

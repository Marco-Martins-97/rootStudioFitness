<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'loginEmail', FILTER_VALIDATE_EMAIL);
    $pwd = htmlspecialchars(trim($_POST['loginPwd']));
    
    try { 
        require_once 'Login.php';
        $login = new Login($email, $pwd);
        $login -> login();
        
    } catch (PDOException $e) {
        die ('Query Falhou: '.$e->getMessage());
    }

} else{
    header('Location: ../login.php');
    exit;
}
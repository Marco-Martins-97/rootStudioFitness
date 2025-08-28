<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //account
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pwd = htmlspecialchars(trim($_POST['pwd']));
    $confPwd = htmlspecialchars(trim($_POST['confirmPwd']));
    //user
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    
    try { 
        require_once 'Signup.php';
        $signup = new Signup($email, $pwd, $confPwd, $firstName, $lastName);
        $signup -> newSignup();
        
    } catch (PDOException $e) {
        die ('Query Falhou: '.$e->getMessage());
    }

} else{
    header('Location: ../signup.php');
    exit;
}
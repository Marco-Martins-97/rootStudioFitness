<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //account
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $pwd = htmlspecialchars(trim($_POST["pwd"]));
    $confirmPwd = htmlspecialchars(trim($_POST["confirmPwd"]));
    //user
    $firstName = htmlspecialchars(trim($_POST["firstName"]));
    $lastName = htmlspecialchars(trim($_POST["lastName"]));
    
    try { 
        echo "Email: ".$email."<br>";
        echo "Pwd: ".$pwd."<br>";
        echo "CPwd: ".$confirmPwd."<br>";
        echo "Nome: ".$firstName."<br>";
        echo "Apelido: ".$lastName."<br>";

        
        /* require_once "Signup.php";
        $signup = new Signup($email, $pwd, $confirmPwd, $firstName, $lastName);
        $signup -> newSignup(); */
        
    } catch (PDOException $e) {
        die ("Query Falhou: ".$e->getMessage());
    }

} else{
    header("Location: ../signup.php");
    exit;
}
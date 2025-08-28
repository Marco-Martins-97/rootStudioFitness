<?php
require_once 'Dbh.php';

/* 
    Este ficheiro contem as funções para todas as validações,
    as funções que serão usadas com o php, e tambem com ajax.
    Permitindo reutilizar o mesmo codigo para todo o website,
    ficando em um só lugar e de forma organizada.

*/

$pwdLength = 8;
// Funções de Validação

function isInputRequired($input){
    $requiredFields = ['firstName','lastName','email','pwd','confPwd','loginEmail'];
    return in_array($input, $requiredFields);
}

function isInputEmpty($value){
    return empty($value);
}

function isNameInvalid($value){
    return !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $value);
}

function isEmailInvalid($value) {
    return !filter_var($value, FILTER_VALIDATE_EMAIL);
}

function thisEmailExists($value) {  //conecta a base de dados, pesquisa se o email existe an base de dados e retorna true/false
    $dbh = new Dbh();
    $conn = $dbh->connect();
    $query = 'SELECT * FROM users WHERE email = :email;';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $value);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}

function isPwdShort($value){
    global $pwdLength;
    return strlen($value) < $pwdLength;
}

function isPwdNoMatch($pwd, $confPwd) {
    return $pwd !== $confPwd;
}
<?php
require_once 'Dbh.php';

/* 
    Este ficheiro contem as funções para todas as validações,
    as funções que serão usadas com o php, e tambem com ajax.
    Permitindo reutilizar o mesmo codigo para todo o website,
    ficando em um só lugar e de forma organizada.

*/

$pwdLength = 8; //minimo de caracters da password
// Funções de Validação

function isInputRequired($input){
    $requiredFields = ['firstName','lastName','email','pwd','confPwd','loginEmail','fullName','birthDate','gender','userAddress','nif','phone','trainingPlan','experience','terms'];
    return in_array($input, $requiredFields);
}

function isInputEmpty($value){
    return empty($value);
}

function isLengthInvalid($value, $length = 255){
    return mb_strlen($value) > $length;
}    

function isNameInvalid($value){
    return !preg_match('/^[\p{L}\s]+$/u', $value);
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

function isDateInvalid($value){  
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return !($date && $date->format('Y-m-d') === $value);
}

function isBirthInvalid($value){
    $todayDate = new DateTime('today');
    $valueDate = new DateTime($value);
    return $valueDate >= $todayDate;
}

function isGenderInvalid($value){
    return !($value === 'male' || $value === 'female');
}

function isAddressInvalid($value){
    return !preg_match('/^[\p{L}0-9\s,.\-#]+$/u', $value);
}

function isNifInvalid($value){
    return !preg_match('/^\d{9}$/', $value);
}

//Apenas aceita numeros Portugueses
function isPhoneInvalid($value){
    return !preg_match('/^[29]\d{8}$/', $value);
}

function isTrainingPlanInvalid($value){
    $trainingPlans = ['personalized1','personalized2','group','terapy','padel','openStudio'];
    return !in_array($value, $trainingPlans);
}

function isExperienceInvalid($value){
    $experience = ['beginner','intermediate','advanced'];
    return !in_array($value, $experience);
}

function isNotChecked($value) {
    return $value !== 'yes';
}

function isDescriptionInvalid($value) {
    return !preg_match('/^[\p{L}\p{N}\s.,()-]+$/u', $value);
}
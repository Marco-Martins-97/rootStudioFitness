<?php
require_once 'Dbh.php';
require_once 'configSession.inc.php';

/* 
    Este ficheiro contem as funções de todas as validações.
    As funções que serão usadas com o php, e tambem com ajax.
    Permitindo reutilizar o mesmo codigo para todo o website,
    ficando em um só lugar e de forma organizada.

*/

// $pwdLength = 8; //minimo de caracters da password

// Funções de Validação
function isInputRequired($input){
    $requiredFields = ['firstName','lastName','email','pwd','confPwd','loginEmail','fullName','birthDate','gender','userAddress','nif','phone','trainingPlan','experience','terms'];
    return in_array($input, $requiredFields);
}

function isInputEmpty($value){
    return empty($value);
}

function isLengthInvalid($value, $maxLength = 255){
    return mb_strlen($value) > $maxLength;
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
    $query = 'SELECT EXISTS(SELECT 1 FROM users WHERE email = :email)';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $value);
    $stmt->execute();
    
    return (bool) $stmt->fetchColumn();
}

function isPwdWrong($value, $userId = null) {  //conecta a base de dados, pesquisa se a pwd está correta e retorna true/false
    if ($userId ===  null){ // verifica se foi providenciado um userId
        if (!isset($_SESSION['userId'])){   // verifica se o está logado
            return true;
        }
        $userId = $_SESSION['userId'];  //atribui ao userId o id do user logado
    }

    $dbh = new Dbh();
    $conn = $dbh->connect();
    $query = 'SELECT pwd FROM users WHERE id = :userId;';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    
    $userData = $stmt->fetch();

    if (!$userData) {
        return true; // usuário não encontrado é considerado senha errada
    }
    return !password_verify($value, $userData['pwd']);
}

function isPwdShort($value, $minLength = 8){
    return mb_strlen($value) < $minLength;
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
    $minDate = new DateTime('1900-01-01');
    $valueDate = new DateTime($value);
    return $valueDate > $todayDate || $valueDate < $minDate;
}

function isGenderInvalid($value){
    return !($value === 'male' || $value === 'female');
}

function isAddressInvalid($value){
    return !preg_match('/^[\p{L}0-9\s,.\-#]+$/u', $value);
}

function isNifInvalid($value){
    return !preg_match('/^[0-9]{9}$/', $value);
}

//Apenas aceita numeros Portugueses, ou que contenha 9 digitos e começe por 2 ou 9.
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

function isYesOrNo($value) {
    return $value !== 'yes' && $value !== 'no';
}

function isDescriptionInvalid($value) {
    return !preg_match('/^[\p{L}\p{N}\s.,()-]+$/u', $value);
}
<?php
require_once 'Dbh.php';
require_once 'configSession.inc.php';

/* 
    Este ficheiro contem as funções de todas as validações.
    As funções que serão usadas com o php, e tambem com ajax.
    Permitindo reutilizar o mesmo codigo para todo o website,
    ficando em um só lugar e de forma organizada.

*/

// Funções de Validação
function isInputRequired($input){
    // Lista de campos que são obrigatórios
    $requiredFields = ['firstName','lastName','email','pwd','confPwd',  //registo
                        'loginEmail',   //login
                        'fullName','birthDate','gender','userAddress','nif','phone','trainingPlan','experience','terms',    //cliente
                        'productName', 'productPrice', 'productStock',   //criar novo produto
                        'exerciseName',  // criar novo exercicio
                        'trainingPlanName'  // criar novo plano de treino
                    ];
    return in_array($input, $requiredFields);
}

function isInputEmpty($value){
    return $value === null || $value === '';
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

function thisEmailExists($value) {  // Verifica se o email já existe na base de dados
    $dbh = new Dbh();
    $conn = $dbh->connect();
    $query = 'SELECT EXISTS(SELECT 1 FROM users WHERE email = :email)';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $value);
    $stmt->execute();
    
    return (bool) $stmt->fetchColumn();
}

function isPwdWrong($value, $userId = null) {  // Verifica se a password está correta para o userId fornecido ou logado
    if ($userId ===  null){
        if (!isset($_SESSION['userId'])){   // verifica se está conetado
            return true;
        }
        $userId = $_SESSION['userId'];  // Atribui id do user conetado
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

// Apenas aceita numeros Portugueses, ou que contenha 9 digitos e começe por 2 ou 9.
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

function isSizeInvalid($size, $maxSize = 2 * 1024 * 1024) {
    $size = (int)$size;
    return $size <= 0 || $size > $maxSize;
}

function isTypeInvalid($type, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
    return !in_array($type, $allowedTypes);
}

function isProductNameInvalid($value) {
    return !preg_match('/^[\p{L}\p{N}\s.,;:()\-\'"&+\/%!?@$€*#]+$/u', $value);
}

function isPriceInvalid($value) {
    return !preg_match('/^\d+(\.\d{1,2})?$/', $value) || (float)$value < 0;
}

function isStockInvalid($value) {
    return !preg_match('/^\d+$/', $value) || (int)$value < 0;
}
function isOutOfStock($productId, $requiredQty) {
    $dbh = new Dbh();
    $conn = $dbh->connect();

    $query = 'SELECT productStock FROM products WHERE id = :productId';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->execute();
    
    $quantity = $stmt->fetchColumn();
    return $quantity === false || $quantity < $requiredQty;
}

function isUnder18($birthDate) {
    $birth = DateTime::createFromFormat('Y-m-d', $birthDate);
    if (!$birth) return true;

    $today = new DateTime();
    $age = $today->diff($birth)->y;

    return $age < 18;
}

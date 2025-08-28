<?php
/* 
    Este ficheiro contem as validações de todos os inputs,
    as funções que serão usadas com o php,
    e tambem com ajax no javascript.
    Permitindo reutilizar o mesmo codigo para todo o website,
    ficando em um só lugar e de forma organizada.

*/

//GLOBAL Variables
$requiredFields = ['firstName','lastName','email','pwd','confPwd'];
$pwdLength = 8;

// Funções de Validação

function isInputRequired($input){
    global $requiredFields;
    return in_array($input, $requiredFields);
}

function isInputEmpty($value){
    return $value === '' || $value === null;
}

function isNameInvalid($value){
    return !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $value);
}

function isEmailInvalid($value) {
    return !filter_var($value, FILTER_VALIDATE_EMAIL);
}
/* 
function thisEmailExists($value) {  //limpa o input, conecta a base de dados, pesquisa se o email existe an base de dados e retorna true/false
    $email = htmlspecialchars($value);
    $dbh = new Dbh();
    $conn = $dbh->connect();
    $query = 'SELECT * FROM users WHERE email = :email;';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
 */
function isPwdShort($value){
    global $pwdLength;
    return strlen($value) < $pwdLength;
}

function isPwdNoMatch($pwd, $confPwd) {
    return $pwd !== $confPwd;
}


















// Validações com AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_POST['input'])){
        echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
        exit;
    }

    // lê o input
    $input = $_POST['input'];

    switch ($input) {
        case 'firstName':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O nome é obrigatório.';
            } elseif (isNameInvalid($value)){
                $error = 'O nome só pode conter letras e espaços.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;

        case 'lastName':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O apelido é obrigatório.';
            } elseif (isNameInvalid($value)){
                $error = 'O apelido só pode conter letras e espaços.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;

        case 'email':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O email é obrigatório.';
            } elseif (isEmailInvalid($value)){
                $error = 'O email não é válido.';
            /* } elseif (thisEmailExists($value)) {
                $error = 'Este email já se encontra em uso.'; */
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;

        case 'createPwd':
            $errors = [];
            $valuePwd = trim($_POST['valuePwd'] ?? '');
            $valueConfPwd = trim($_POST['valueConfPwd'] ?? '');

            if(isInputRequired('pwd') && isInputEmpty($valuePwd)){
                $errors['pwd'] = 'A password é obrigatória.';
            } elseif (isPwdShort($valuePwd)){
                $errors['pwd'] = 'A password deve ter pelo menos '.$pwdLength.' caracteres.';
            }
            if(isInputRequired('confPwd') && isInputEmpty($valueConfPwd)){
                $errors['confPwd'] = 'A confirmação da password é obrigatória.';
            } elseif (isPwdNoMatch($valuePwd, $valueConfPwd)){
                $errors['confPwd'] = 'As passwords não coincidem.';
            }

            if ($errors) {
                echo json_encode(['status' => 'invalid', 'message' => $errors]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;

        


        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid Input']);
            exit;
    }





} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
    exit;
}
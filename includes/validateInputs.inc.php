<?php
require_once 'validations.inc.php';
/* 
    Este ficheiro Faz a validaçao de todos os inputs do website via AJAX.
    $input - esta é a variavel que vai selecionar qual será a validação realizada.
*/


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
            } elseif (thisEmailExists(htmlspecialchars($value))) {
                $error = 'Este email já se encontra em uso.';
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

        case 'loginEmail':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O email é obrigatório.';
            } elseif (isEmailInvalid($value)){
                $error = 'O email não é válido.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;


        


        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid Input']);
            break;
    }
} else{
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
    exit;
}
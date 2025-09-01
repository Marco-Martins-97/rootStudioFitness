<?php
require_once 'validations.inc.php';
/* 
    Este ficheiro Faz a validaçao de todos os inputs do website via AJAX.
    $input - esta é a variavel que vai selecionar qual será a validação realizada.
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

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
                $error = 'O nome contém caracteres inválidos..';
            } elseif (isLengthInvalid($value)){
                $error = 'O nome excede o limite de caracteres.';
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
                $error = 'O apelido contém caracteres inválidos.';
            } elseif (isLengthInvalid($value)){
                $error = 'O apelido excede o limite de caracteres.';
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
            } elseif (isLengthInvalid($value)){
                $error = 'O email excede o limite de caracteres.';
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
            } elseif (isLengthInvalid($valuePwd)){
                $errors['pwd'] = 'A password excede o limite de caracteres.';
            }
            if(isInputRequired('confPwd') && isInputEmpty($valueConfPwd)){
                $errors['confPwd'] = 'A confirmação da password é obrigatória.';
            } elseif (isLengthInvalid($valueConfPwd)){
                $errors['confPwd'] = 'A confirmação da password excede o limite de caracteres.';
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
            } elseif (isLengthInvalid($value)){
                $error = 'O email excede o limite de caracteres.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;

        case 'fullName':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O nome completo é obrigatório.';
            } elseif (isNameInvalid($value)){
                $error = 'O nome contém caracteres inválidos.';
            } elseif (isLengthInvalid($value)){
                $error = 'O nome excede o limite de caracteres.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;

        case 'birthDate':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'A data de nascimento é obrigatório.';
            } elseif (isDateInvalid($value)){
                $error = 'O formato de data é inválido.';
            } elseif (isBirthInvalid($value)){
                $error = 'A data de nascimento é inválida.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;
        
        case 'userAddress':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'A morada é obrigatório.';
            } elseif (isAddressInvalid($value)){
                $error = 'A morada contém caracteres inválidos.';
            } elseif (isLengthInvalid($value)){
                $error = 'A morada excede o limite de caracteres.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;
        
        case 'nif':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O nif é obrigatório.';
            } elseif (isNifInvalid($value)){
                $error = 'O nif não é válido.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;

        case 'phone':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O telefone é obrigatório.';
            } elseif (isPhoneInvalid($value)){
                $error = 'O telefone não é válido.';
            }

            if ($error) {
                echo json_encode(['status' => 'invalid', 'message' => $error]);
            } else {
                echo json_encode(['status' => 'valid']);
            }
            exit;
        
        case 'health-details':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if (!isInputEmpty($value) && isDescriptionInvalid($value)){
                $error = 'A descrição contém caracteres inválidos.';
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

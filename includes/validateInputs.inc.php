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

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
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

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
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

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;

        case 'createPwd':
            $errors = [];
            $valuePwd = trim($_POST['valuePwd'] ?? '');
            $valueConfPwd = trim($_POST['valueConfPwd'] ?? '');

            if(isInputRequired('pwd') && isInputEmpty($valuePwd)){
                $errors['pwd'] = 'A password é obrigatória.';
            } elseif (isPwdShort($valuePwd)){
                $errors['pwd'] = 'A password deve ter pelo menos 8 caracteres.';
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

        case 'newPwd':
            $errors = [];
            $valueCurrentPwd = htmlspecialchars(trim($_POST['valueCurrentPwd']) ?? '');
            $valueNewPwd = trim($_POST['valueNewPwd'] ?? '');
            $valueConfirmNewPwd = trim($_POST['valueConfirmNewPwd'] ?? '');

            if(isPwdWrong($valueCurrentPwd)){
                $errors['currentPwd'] = 'A password atual não está correta.';
            }
            
            if(isInputRequired('pwd') && isInputEmpty($valueNewPwd)){
                $errors['newPwd'] = 'A nova password é obrigatória.';
            } elseif (isPwdShort($valueNewPwd)){
                $errors['newPwd'] = 'A nova password deve ter pelo menos 8 caracteres.';
            } elseif (isLengthInvalid($valueNewPwd)){
                $errors['newPwd'] = 'A nova password excede o limite de caracteres.';
            }

            if(isInputRequired('confPwd') && isInputEmpty($valueConfirmNewPwd)){
                $errors['confNewPwd'] = 'A confirmação da nova password é obrigatória.';
            } elseif (isLengthInvalid($valueConfirmNewPwd)){
                $errors['confNewPwd'] = 'A confirmação da nova password excede o limite de caracteres.';
            } elseif (isPwdNoMatch($valueNewPwd, $valueConfirmNewPwd)){
                $errors['confNewPwd'] = 'As novas passwords não coincidem.';
            }

           
            if ($errors) {
                echo json_encode(['status' => 'invalid', 'message' => $errors]);
            } else {
                echo json_encode(['status' => 'valid']);
            } 
           
            exit;
        
        case 'confPwd':
            $error = '';
            $value = htmlspecialchars(trim($_POST['value']) ?? '');

            if(isPwdWrong($value)){
                $error = 'A password não está correta.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
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

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
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

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
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

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
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

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;
        
        case 'nif':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O nif é obrigatório.';
            } elseif (isNifInvalid($value)){
                $error = 'O nif não é válido.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;

        case 'phone':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if(isInputRequired($input) && isInputEmpty($value)){
                $error = 'O telefone é obrigatório.';
            } elseif (isPhoneInvalid($value)){
                $error = 'O telefone não é válido.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;
        
        case 'health-details':
        case 'healthDetails':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if (!isInputEmpty($value) && isDescriptionInvalid($value)){
                $error = 'A descrição contém caracteres inválidos.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;
        
        case 'gender':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if (isGenderInvalid($value)){
                $error = 'O gênero escolhido não é válido.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;
        
        case 'trainingPlan':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if (isTrainingPlanInvalid($value)){
                $error = 'O plano de treino escolhido não é válido.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;
        
        case 'experience':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if (isExperienceInvalid($value)){
                $error = 'A experiência escolhida não é válida.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;
        
        case 'nutritionPlan':
        case 'healthIssues':
            $error = '';
            $value = trim($_POST['value'] ?? '');

            if (isYesOrNo($value)){
                $error = 'A opção escolhida não é válida.';
            }

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;
        
        case 'addNewProduct':
            $errors = [];
            $datapack = $_POST['datapack'] ?? [];

            if(!$datapack){
                echo json_encode(['status' => 'error', 'message' => 'Ocorreu um ERRO ao enviar os dados!']);
                exit;
            }
 
            $uploadImg = filter_var($datapack['uploadImg'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $valueImgSize = filter_var($datapack['valueImgSize'] ?? 0, FILTER_VALIDATE_INT);
            $valueImgType = trim($datapack['valueImgType'] ?? '');
            $valueName = trim($datapack['valueName'] ?? '');
            $valuePrice = filter_var($datapack['valuePrice'] ?? 0, FILTER_VALIDATE_FLOAT);
            $valueStock = filter_var($datapack['valueStock'] ?? 0, FILTER_VALIDATE_INT);


            if(!$uploadImg){
                $errors['productImg'] = 'A imagem do produto é obrigatória.';
            } else{
                if (isSizeInvalid($valueImgSize)){
                    $errors['productImg'] = 'A imagem excede o tamanho permitido.';
                } elseif (isTypeInvalid($valueImgType)){
                    $errors['productImg'] = 'A imagem não tem um formato válido (png, jpg, gif).';
                }
            }    

            if(isInputRequired('productName') && isInputEmpty($valueName)){
                $errors['productName'] = 'O nome do produto é obrigatório.';
            } elseif (isProductNameInvalid($valueName)){
                $errors['productName'] = 'O nome contém caracteres inválidos.';
            } elseif (isLengthInvalid($valueName)){
                $errors['productName'] = 'O nome excede o limite de caracteres.';
            }

            if (isInputRequired('productPrice') && isInputEmpty($valuePrice)) {
                $errors['productPrice'] = 'O preço é obrigatório.';
            } elseif (isPriceInvalid($valuePrice)) {
                $errors['productPrice'] = 'O preço deve ser um número válido maior que zero.';
            }

            if (isInputRequired('productStock') && isInputEmpty($valueStock)) {
                $errors['productStock'] = 'A quantidade de stock é obrigatório.';
            } elseif (isStockInvalid($valueStock)) {
                $errors['productStock'] = 'A quantidade de stock deve ser um número inteiro maior que zero.';
            }
         
            if ($errors) {
                echo json_encode(['status' => 'invalid', 'message' => $errors]);
            } else {
                echo json_encode(['status' => 'valid']);
            } 
           
            exit;
        
        case 'updateProduct':
            $errors = [];
            $datapack = $_POST['datapack'] ?? [];

            if(!$datapack){
                echo json_encode(['status' => 'error', 'message' => 'Ocorreu um ERRO ao enviar os dados!']);
                exit;
            }
 
            $uploadImg = filter_var($datapack['uploadImg'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $valueImgSize = filter_var($datapack['valueImgSize'] ?? 0, FILTER_VALIDATE_INT);
            $valueImgType = trim($datapack['valueImgType'] ?? '');
            $valueName = trim($datapack['valueName'] ?? '');
            $valuePrice = filter_var($datapack['valuePrice'] ?? 0, FILTER_VALIDATE_FLOAT);
            $valueStock = filter_var($datapack['valueStock'] ?? 0, FILTER_VALIDATE_INT);


            if($uploadImg){
                if (isSizeInvalid($valueImgSize)){
                    $errors['productImg'] = 'A imagem excede o tamanho permitido.';
                } elseif (isTypeInvalid($valueImgType)){
                    $errors['productImg'] = 'A imagem não tem um formato válido (png, jpg, gif).';
                }
            }    

            if(isInputRequired('productName') && isInputEmpty($valueName)){
                $errors['productName'] = 'O nome do produto é obrigatório.';
            } elseif (isProductNameInvalid($valueName)){
                $errors['productName'] = 'O nome contém caracteres inválidos.';
            } elseif (isLengthInvalid($valueName)){
                $errors['productName'] = 'O nome excede o limite de caracteres.';
            }

            if (isInputRequired('productPrice') && isInputEmpty($valuePrice)) {
                $errors['productPrice'] = 'O preço é obrigatório.';
            } elseif (isPriceInvalid($valuePrice)) {
                $errors['productPrice'] = 'O preço deve ser um número válido maior ou igual a zero.';
            }

            if (isInputRequired('productStock') && isInputEmpty($valueStock)) {
                $errors['productStock'] = 'A quantidade de stock é obrigatório.';
            } elseif (isStockInvalid($valueStock)) {
                $errors['productStock'] = 'A quantidade de stock deve ser um número inteiro maior ou igual a zero.';
            }
         
            if ($errors) {
                echo json_encode(['status' => 'invalid', 'message' => $errors]);
            } else {
                echo json_encode(['status' => 'valid']);
            } 
           
            exit;
        
        case 'stock':
            $error = '';
            $productId = htmlspecialchars(trim($_POST['productId']) ?? 0);
            $qty = htmlspecialchars(trim($_POST['quantity']) ?? 0);

            if (isOutOfStock($productId, $qty)) {
                $error = 'Não tem produto em stock.';
            } 

            echo json_encode($error 
                ? ['status' => 'invalid', 'message' => $error] 
                : ['status' => 'valid']
            );
            exit;


        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid Input']);
            break;
    }

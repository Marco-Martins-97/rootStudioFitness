<?php
require_once 'validations.inc.php';
/* 
    Este ficheiro faz a validação de todos os inputs do website via AJAX.
    $input - esta variável vai selecionar qual será a validação realizada.
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

if(!isset($_POST['input'])){
    echo json_encode(['status' => 'error', 'message' => 'Pedido inválido']);
    exit;
}

$input = trim($_POST['input']);   // lê o input

function getValue($value){
    return trim($_POST[$value] ?? ''); 
}

switch ($input) {
    case 'firstName':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O nome é obrigatório.';
        } elseif (isNameInvalid($value)){
            $error = 'O nome contém caracteres inválidos.';
        } elseif (isLengthInvalid($value)){
            $error = 'O nome excede o limite de caracteres.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;

    case 'lastName':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O apelido é obrigatório.';
        } elseif (isNameInvalid($value)){
            $error = 'O apelido contém caracteres inválidos.';
        } elseif (isLengthInvalid($value)){
            $error = 'O apelido excede o limite de caracteres.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;

    case 'email':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O email é obrigatório.';
        } elseif (isEmailInvalid($value)){
            $error = 'O email não é válido.';
        } elseif (thisEmailExists($value)) {
            $error = 'Este email já se encontra em uso.';
        } elseif (isLengthInvalid($value)){
            $error = 'O email excede o limite de caracteres.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;

    case 'createPwd':
        $errors = [];
        $valuePwd = getValue('valuePwd');
        $valueConfPwd = getValue('valueConfPwd');

        if(isInputRequired('pwd') && isInputEmpty($valuePwd)){
            $errors['pwd'] = 'A palavra-passe é obrigatória.';
        } elseif (isPwdShort($valuePwd)){
            $errors['pwd'] = 'A palavra-passe deve ter pelo menos 8 caracteres.';
        } elseif (isLengthInvalid($valuePwd)){
            $errors['pwd'] = 'A palavra-passe excede o limite de caracteres.';
        }

        if(isInputRequired('confPwd') && isInputEmpty($valueConfPwd)){
            $errors['confPwd'] = 'A confirmação da palavra-passe é obrigatória.';
        } elseif (isLengthInvalid($valueConfPwd)){
            $errors['confPwd'] = 'A confirmação da palavra-passe excede o limite de caracteres.';
        } elseif (isPwdNoMatch($valuePwd, $valueConfPwd)){
            $errors['confPwd'] = 'As palavras-passe não coincidem.';
        }

        echo json_encode($errors ? ['status' => 'invalid', 'message' => $errors] : ['status' => 'valid']);
        exit;

    case 'newPwd':
        $errors = [];
        $valueCurrentPwd = getValue('valueCurrentPwd');
        $valueNewPwd = getValue('valueNewPwd');
        $valueConfirmNewPwd = getValue('valueConfirmNewPwd');

        if(isPwdWrong($valueCurrentPwd)){
            $errors['currentPwd'] = 'A palavra-passe atual não está correta.';
        }
            
        if(isInputRequired('pwd') && isInputEmpty($valueNewPwd)){
            $errors['newPwd'] = 'A nova palavra-passe é obrigatória.';
        } elseif (isPwdShort($valueNewPwd)){
            $errors['newPwd'] = 'A nova palavra-passe deve ter pelo menos 8 caracteres.';
        } elseif (isLengthInvalid($valueNewPwd)){
            $errors['newPwd'] = 'A nova palavra-passe excede o limite de caracteres.';
        }

        if(isInputRequired('confPwd') && isInputEmpty($valueConfirmNewPwd)){
            $errors['confNewPwd'] = 'A confirmação da nova palavra-passe é obrigatória.';
        } elseif (isLengthInvalid($valueConfirmNewPwd)){
            $errors['confNewPwd'] = 'A confirmação da nova palavra-passe excede o limite de caracteres.';
        } elseif (isPwdNoMatch($valueNewPwd, $valueConfirmNewPwd)){
            $errors['confNewPwd'] = 'As novas palavras-passe não coincidem.';
        }

        echo json_encode($errors ? ['status' => 'invalid', 'message' => $errors] : ['status' => 'valid']);
        exit;
        
    case 'confPwd':
        $error = '';
        $value = getValue('value');

        if(isPwdWrong($value)){
            $error = 'A palavra-passe não está correta.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;

    case 'loginEmail':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O email é obrigatório.';
        } elseif (isEmailInvalid($value)){
            $error = 'O email não é válido.';
        } elseif (isLengthInvalid($value)){
            $error = 'O email excede o limite de caracteres.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;

    case 'fullName':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O nome completo é obrigatório.';
        } elseif (isNameInvalid($value)){
            $error = 'O nome contém caracteres inválidos.';
        } elseif (isLengthInvalid($value)){
            $error = 'O nome excede o limite de caracteres.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;

    case 'birthDate':
    case 'birthDate18':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'A data de nascimento é obrigatória.';
        } elseif (isDateInvalid($value)){
            $error = 'O formato de data é inválido.';
        } elseif (isBirthInvalid($value)){
            $error = 'A data de nascimento é inválida.';
        } elseif ($input === 'birthDate18' && isUnder18($value)){
            $error = 'É necessário ter pelo menos 18 anos.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
        
    case 'userAddress':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'A morada é obrigatória.';
        } elseif (isAddressInvalid($value)){
            $error = 'A morada contém caracteres inválidos.';
        } elseif (isLengthInvalid($value)){
            $error = 'A morada excede o limite de caracteres.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
        
    case 'nif':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O nif é obrigatório.';
        } elseif (isNifInvalid($value)){
            $error = 'O nif não é válido.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;

    case 'phone':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O telefone é obrigatório.';
        } elseif (isPhoneInvalid($value)){
            $error = 'O telefone não é válido.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
        
    case 'health-details':
    case 'healthDetails':
        $error = '';
        $value = getValue('value');

        if (!isInputEmpty($value) && isDescriptionInvalid($value)){
            $error = 'A descrição contém caracteres inválidos.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
    
    case 'gender':
        $error = '';
        $value = getValue('value');

        if (isGenderInvalid($value)){
            $error = 'O género escolhido não é válido.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
    
    case 'trainingPlan':
        $error = '';
        $value = getValue('value');
        if (isTrainingPlanInvalid($value)){
            $error = 'O plano de treino escolhido não é válido.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
    
    case 'experience':
        $error = '';
        $value = getValue('value');

        if (isExperienceInvalid($value)){
            $error = 'A experiência escolhida não é válida.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
    
    case 'nutritionPlan':
    case 'healthIssues':
        $error = '';
        $value = getValue('value');

        if (isYesOrNo($value)){
            $error = 'A opção escolhida não é válida.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
    
    case 'addNewProduct':
        $errors = [];
        $datapack = $_POST['datapack'] ?? [];

        if(!$datapack){
            echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao enviar os dados!']);
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
            $errors['productName'] = 'O nome do produto contém caracteres inválidos.';
        } elseif (isLengthInvalid($valueName)){
            $errors['productName'] = 'O nome do produto excede o limite de caracteres.';
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
        
        echo json_encode($errors ? ['status' => 'invalid', 'message' => $errors] : ['status' => 'valid']);
        exit;
        
    case 'updateProduct':
        $errors = [];
        $datapack = $_POST['datapack'] ?? [];

        if(!$datapack){
            echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao enviar os dados!']);
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
            $errors['productName'] = 'O nome do produto contém caracteres inválidos.';
        } elseif (isLengthInvalid($valueName)){
            $errors['productName'] = 'O nome do produto excede o limite de caracteres.';
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
         
        echo json_encode($errors ? ['status' => 'invalid', 'message' => $errors] : ['status' => 'valid']);
        exit;
        
    case 'stock':
        $error = '';
        $productId = getValue('productId');
        $qty = intval(getValue('quantity'));

        if (isOutOfStock($productId, $qty)) {
            $error = 'Não existem produtos suficientes em stock.';
        } 

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;


    case 'addNewExercise':
        $errors = [];
        $datapack = $_POST['datapack'] ?? [];

        if(!$datapack){
            echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao enviar os dados!']);
            exit;
        }
 
        $uploadImg = filter_var($datapack['uploadImg'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $valueImgSize = filter_var($datapack['valueImgSize'] ?? 0, FILTER_VALIDATE_INT);
        $valueImgType = trim($datapack['valueImgType'] ?? '');
        $valueName = trim($datapack['valueName'] ?? '');

        if(!$uploadImg){
            $errors['exerciseImg'] = 'A imagem do exercicio é obrigatória.';
        } else{
            if (isSizeInvalid($valueImgSize)){
                $errors['exerciseImg'] = 'A imagem excede o tamanho permitido.';
            } elseif (isTypeInvalid($valueImgType)){
                $errors['exerciseImg'] = 'A imagem não tem um formato válido (png, jpg, gif).';
            }
        }    

        if(isInputRequired('exerciseName') && isInputEmpty($valueName)){
            $errors['exerciseName'] = 'O nome do exercicio é obrigatório.';
        } elseif (isNameInvalid($valueName)){
            $errors['exerciseName'] = 'O nome do exercicio contém caracteres inválidos.';
        } elseif (isLengthInvalid($valueName)){
            $errors['exerciseName'] = 'O nome do exercicio excede o limite de caracteres.';
        }

        echo json_encode($errors ? ['status' => 'invalid', 'message' => $errors] : ['status' => 'valid']);
        exit;

    case 'updateExercise':
        $errors = [];
        $datapack = $_POST['datapack'] ?? [];

        if(!$datapack){
            echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao enviar os dados!']);
            exit;
        }
 
        $uploadImg = filter_var($datapack['uploadImg'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $valueImgSize = filter_var($datapack['valueImgSize'] ?? 0, FILTER_VALIDATE_INT);
        $valueImgType = trim($datapack['valueImgType'] ?? '');
        $valueName = trim($datapack['valueName'] ?? '');

        if($uploadImg){
            if (isSizeInvalid($valueImgSize)){
                $errors['productImg'] = 'A imagem excede o tamanho permitido.';
            } elseif (isTypeInvalid($valueImgType)){
                $errors['productImg'] = 'A imagem não tem um formato válido (png, jpg, gif).';
            }
        }    

        if(isInputRequired('productName') && isInputEmpty($valueName)){
            $errors['productName'] = 'O nome do produto é obrigatório.';
        } elseif (isNameInvalid($valueName)){
            $errors['productName'] = 'O nome do produto contém caracteres inválidos.';
        } elseif (isLengthInvalid($valueName)){
            $errors['productName'] = 'O nome do produto excede o limite de caracteres.';
        }

        echo json_encode($errors ? ['status' => 'invalid', 'message' => $errors] : ['status' => 'valid']);
        exit;

    case 'trainingPlanName':
        $error = '';
        $value = getValue('value');

        if(isInputRequired($input) && isInputEmpty($value)){
            $error = 'O nome é obrigatório.';
        } elseif (isNameInvalid($value)){
            $error = 'O nome contém caracteres inválidos.';
        } elseif (isLengthInvalid($value)){
            $error = 'O nome excede o limite de caracteres.';
        }

        echo json_encode($error ? ['status' => 'invalid', 'message' => $error] : ['status' => 'valid']);
        exit;
    


    default:
        echo json_encode(['status' => 'error', 'message' => 'Input inválido']);
        break;
}
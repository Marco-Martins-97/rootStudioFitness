<?php
require_once 'configSession.inc.php'; 

//verifica se acessou a pagina ataves de um Post
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

//verifica se uma action foi defenida
if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
    exit;
}

function getPost($property){
    return htmlspecialchars(trim($_POST[$property] ?? ''));
}
// lê a action
$action = trim($_POST['action']);


//executa o carregamento dos dados respetivos a action escolhida
switch ($action) {

    case 'reviewApplication':
        if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            exit;
        } else if($_SESSION["userRole"] !== "admin"){  //verifica e o utilizador é um admin
            echo json_encode(['status' => 'error', 'message' => 'Not an Admin']);
            exit;
        }

        $applicationId = getPost('applicationId');
        $review = getPost('review');


        try {
            require_once "Client.php";
            $client = new Client();
            $res = $client->reviewClientApplication($applicationId, $review);

            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Log interno
            echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
            exit;
        }
        break;

    case 'saveProfileField':
        if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            exit;
        }

        $field = getPost('field');
        $value = getPost('value');

        if(empty($field) || empty($value)){
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
            exit;
        }

        $userId = $_SESSION['userId'];
        try {
            require_once "ProfileHandler.php";
            $profile = new Profile($userId);
            $res = $profile->updateField($field, $value);

            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Log interno
            echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
            exit;
        }
        break;

    case 'saveNewPwd':
        if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            exit;
        }

        $currentPwd = getPost('valueCurrentPwd');
        $newPwd = getPost('valueNewPwd');
        $confirmNewPwd = getPost('valueConfirmNewPwd');
        $userId = $_SESSION['userId'];

        try {
            require_once "ProfileHandler.php";
            $profile = new Profile($userId);
            $res = $profile->updatePwd($currentPwd, $newPwd, $confirmNewPwd);

            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Log interno
            echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
            exit;
        }
        break;
    
    case 'saveNewProduct':
        if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            exit;
        } else if($_SESSION["userRole"] !== "admin"){  //verifica e o utilizador é um admin
            echo json_encode(['status' => 'error', 'message' => 'Not an Admin']);
            exit;
        }

        $productImg = $_FILES['imgFile'] ?? null;
        $productName = getPost('valueName');
        $productPrice = filter_var($_POST['valuePrice'] ?? 0, FILTER_VALIDATE_FLOAT);
        $productStock = filter_var($_POST['valueStock'] ?? 0, FILTER_VALIDATE_INT);

        try {
            require_once "ShopHandler.php";
            $shop = new Shop();
            $res = $shop->addNewProduct($productImg, $productName, $productPrice, $productStock);

            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
            exit;
        }
        break;


    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);
        break;
}
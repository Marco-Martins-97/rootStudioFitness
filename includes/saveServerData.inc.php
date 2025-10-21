<?php
require_once 'configSession.inc.php'; 

// Verifica se a página foi acedida via POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

// Verifica se a ação foi definida
if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Pedido inválido']);
    exit;
}

function getPost($property){
    return trim($_POST[$property] ?? '');
}

$action = trim($_POST['action']); // Lê a ação

function requireLogin() {   // Verifica se o utilizador está conectado
    if (!isset($_SESSION["userId"])) {
        echo json_encode(['status' => 'error', 'message' => 'É necessário efetuar login']);
        exit;
    }
}

function requireAdmin() {    // Verifica se o utilizador é administrador
    if (!isset($_SESSION["userRole"]) || $_SESSION["userRole"] !== "admin") {
        echo json_encode(['status' => 'error', 'message' => 'Acesso negado: apenas administradores']);
        exit;
    }
}

function handleDbError(PDOException $e){
    error_log("Erro na base de dados: ".$e->getMessage());
    echo json_encode(['status'=>'error','message'=>'Erro ao conectar ao servidor']);
    exit;
}

// Executa a função correspondente à ação
switch ($action) {
    case 'reviewApplication':
        requireAdmin();

        $applicationId = getPost('applicationId');
        $review = getPost('review');


        try {
            require_once "Client.php";
            $client = new Client();
            $res = $client->reviewClientApplication($applicationId, $review);

            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    case 'saveProfileField':
        requireLogin(); 

        $field = getPost('field');
        $value = getPost('value');

        if(empty($field) || empty($value)){
            echo json_encode(['status' => 'error', 'message' => 'Dados em falta']);
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
            handleDbError($e);
        }
        break;

    case 'saveNewPwd':
        requireLogin(); 

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
            handleDbError($e);
        }
        break;
    
    case 'saveNewProduct':
        requireAdmin();

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
            handleDbError($e);
        }
        break;

    case 'deleteProduct':
        requireAdmin();

        $productId = getPost('productId');

        try {
            require_once "ShopHandler.php";
            $shop = new Shop();
            $res = $shop->deleteProduct($productId);
            
            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;
    
    case 'activateProduct':
        requireAdmin();

        $productId = getPost('productId');

        try {
            require_once "ShopHandler.php";
            $shop = new Shop();
            $res = $shop->activateProduct($productId);
            
            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;
        
    case 'updateProduct':
        requireAdmin();

        $productId = getPost('productId');
        $productImg = $_FILES['imgFile'] ?? null;
        $productName = getPost('valueName');
        $productPrice = filter_var($_POST['valuePrice'] ?? 0, FILTER_VALIDATE_FLOAT);
        $productStock = filter_var($_POST['valueStock'] ?? 0, FILTER_VALIDATE_INT);

        try {
            require_once "ShopHandler.php";
            $shop = new Shop();
            $res = $shop->updateProduct($productId, $productImg, $productName, $productPrice, $productStock);

            echo json_encode($res);
            exit;
            
        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    case 'cartHandler':
        if(!isset($_SESSION["userId"])){    
            echo json_encode(['status' => 'processError', 'error' => 'É necessário efetuar login', 'message' => 'Precisa estar conectado para poder usar a loja.']);
            exit;
        }

        $productId = getPost('productId');
        $cartAction = getPost('cartAction');

        $userId = $_SESSION['userId'];
        
        try {
            require_once "ShopHandler.php";
            $shop = new Shop();

            switch ($cartAction) {
                case 'add':
                    $res = $shop->addProductToCart($productId, $userId);
                    break;

                case 'remove':
                    $res = $shop->removeProductFromCart($productId, $userId);
                    break;

                case 'delete':
                    $res = $shop->deleteProductInCart($productId, $userId);
                    break;
                
                default:
                    $res = ['status' => 'error', 'message' => 'Ação do carrinho inválida'];
                    break;
            }
        
            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    case 'reviewOrder':
        requireLogin(); 
        
        $orderId = getPost('orderId');
        $review = getPost('review');
        

        try {
            require_once 'OrdersHandler.php';
            $order = new Order();
            $res = $order->reviewOrder($orderId, $review);
            echo json_encode($res);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
        break;
}
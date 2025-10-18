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

$action = trim($_POST['action']); // Lê a ação

function requireLogin() {   // Verifica se o utilizador está conectado
    if (!isset($_SESSION["userId"])) {
        echo json_encode(['status' => 'error', 'message' => 'É necessário efetuar login']);
        exit;
    }
}

function requireAdmin() {   // Verifica se o utilizador é administrador
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
    case 'loadClientApplications':
        requireAdmin();

        try {
            require_once "Client.php";
            $client = new Client();
            
            $applicationsData = $client->loadApplications();

            $applications = [];

            foreach ($applicationsData as $applicationData) {
                $applications[] = [
                    'username' => $applicationData['username'],
                    'applicationId' => $applicationData['id'],  
                    'fullName' => $applicationData['fullName'],  
                    'birthDate' => $applicationData['birthDate'],  
                    'gender' => $applicationData['gender'],  
                    'userAddress' => $applicationData['userAddress'],  
                    'nif' => $applicationData['nif'],  
                    'phone' => $applicationData['phone'],  
                    'trainingPlan' => $applicationData['trainingPlan'],  
                    'experience' => $applicationData['experience'],  
                    'nutritionPlan' => $applicationData['nutritionPlan'],  
                    'healthIssues' => $applicationData['healthIssues'],  
                    'healthDetails' => $applicationData['healthDetails'],  
                    'status' => $applicationData['applicationStatus'],
                    'submissionDate' => $applicationData['submitted_at'],
                ];
            }
            echo json_encode(['status' => 'success', 'data' => $applications]);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    case 'loadProfile':
        requireLogin();

        $userId = $_SESSION['userId'];

        try {
            require_once "ProfileHandler.php";
            $profile = new Profile($userId);

            $userData = $profile->loadUserData();
            $clientData = null;
            if ($_SESSION["userRole"] === 'client'){
                $clientData = $profile->loadClientData();
            }
                
            echo json_encode(['status' => 'success', 'userData' => $userData, 'clientData' => $clientData]);
            exit;
    
        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;
        
    case 'loadShopAdmProducts':
        requireAdmin(); // Verifica se o utilizador é administrador

        try {
            require_once "ShopHandler.php";
            $shop = new Shop();

            $shopProducts = $shop->loadAdmProducts();
            
            echo json_encode(['status' => 'success', 'products' => $shopProducts]);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;
        
    case 'loadShopProducts':
        try {
            require_once "ShopHandler.php";
            $shop = new Shop();

            $shopProducts = $shop->loadProducts();
                
            echo json_encode(['status' => 'success', 'products' => $shopProducts]);
            exit;
    
        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    case 'loadProductById':
        requireLogin();

        $productId = trim($_POST['productId'] ?? '');

        try {
            require_once "ShopHandler.php";
            $shop = new Shop();

            $shopProduct = $shop->loadProductbyId($productId);
            
            if (!$shopProduct){
                echo json_encode(['status' => 'error', 'message' => 'Produto não encontrado']);
                exit;
            }
            echo json_encode(['status' => 'success', 'product' => $shopProduct]);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    case 'loadShoppingCart':
        // requireLogin();
        if (!isset($_SESSION["userId"])) {
            echo json_encode(['status' => 'invalid', 'message' => 'É necessário efetuar login']);
            exit;
        }

        $userId = $_SESSION['userId'];

        try {
            require_once "ShopHandler.php";
            $shop = new Shop();

            $shoppingCart = $shop->loadShoppingCart($userId);
            
            echo json_encode(['status' => 'success', 'shoppingCart' => $shoppingCart]);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;
        
    case 'loadOrders':
        requireLogin();

        $userId = $_SESSION['userId'];

        try {
            require_once 'OrdersHandler.php';
            $order = new Order();

            $ordersData = $order->loadOrders($userId);
            
            echo json_encode(['status' => 'success', 'ordersData' => $ordersData]);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;

    case 'loadCustomerOrders':
        requireAdmin(); // Verifica se o utilizador é administrador

        try {
            require_once 'OrdersHandler.php';
            $order = new Order();

            $ordersData = $order->loadCustomerOrders();

            echo json_encode(['status' => 'success', 'ordersData' => $ordersData]);
            exit;

        } catch (PDOException $e) {
            handleDbError($e);
        }
        break;


    default:
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
        break;
}
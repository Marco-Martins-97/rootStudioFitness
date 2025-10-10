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

// lê a action
$action = trim($_POST['action']);

//executa o carregamento dos dados respetivos a action escolhida
switch ($action) {
    case 'loadClientApplications':
        if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            exit;
        }
        if($_SESSION["userRole"] !== "admin"){  //verifica e o utilizador é um admin
            echo json_encode(['status' => 'error', 'message' => 'Not an Admin']);
            exit;
        }

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
            error_log("Database error: " . $e->getMessage()); // Log interno
            echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
            exit;
        }
        break;

        case 'loadProfile':
            if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
                echo json_encode(['status' => 'error', 'message' => 'Login required']);
                exit;
            } 

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
                error_log("Database error: " . $e->getMessage()); // Log interno
                echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
                exit;
            }
            break;
        
        case 'loadShopAdmProducts':
            if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
                echo json_encode(['status' => 'error', 'message' => 'Login required']);
                exit;
            } 
            if($_SESSION["userRole"] !== "admin"){  //verifica e o utilizador é um admin
                echo json_encode(['status' => 'error', 'message' => 'Not an Admin']);
                exit;
            }

            try {
                require_once "ShopHandler.php";
                $shop = new Shop();

                $shopProducts = $shop->loadAdmProducts();
                
                echo json_encode(['status' => 'success', 'products' => $shopProducts]);
                exit;
    
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage()); // Log interno
                echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
                exit;
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
                error_log("Database error: " . $e->getMessage()); // Log interno
                echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
                exit;
            }
            break;

        case 'loadProductById':
            if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
                echo json_encode(['status' => 'error', 'message' => 'Login required']);
                exit;
            } 

            $productId = htmlspecialchars(trim($_POST['productId'] ?? ''));

            try {
                require_once "ShopHandler.php";
                $shop = new Shop();

                $shopProduct = $shop->loadProductbyId($productId);
                
                if (!$shopProduct){
                    echo json_encode(['status' => 'error', 'message' => 'O produto não foi encontrado.']);
                    exit;
                }
                echo json_encode(['status' => 'success', 'product' => $shopProduct]);
                exit;
    
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage()); // Log interno
                echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
                exit;
            }
            break;

        case 'loadShoppingCart':
            if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
                echo json_encode(['status' => 'invalid', 'message' => 'Login required']);
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
                error_log("Database error: " . $e->getMessage()); // Log interno
                echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
                exit;
            }
            break;
        
        case 'loadOrders':
            if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
                echo json_encode(['status' => 'error', 'message' => 'Login required']);
                exit;
            } 

            $userId = $_SESSION['userId'];

            try {
                require_once 'OrdersHandler.php';
                $order = new Order();
                
                $ordersData = $order->loadOrders($userId);
                
                echo json_encode(['status' => 'success', 'ordersData' => $ordersData]);
                exit;
    
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage()); // Log interno
                echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
                exit;
            }
            break;

        /* case 'loadCustomerOrders':
            if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
                echo json_encode(['status' => 'error', 'message' => 'Login required']);
                exit;
            } 
            if($_SESSION["userRole"] !== "admin"){  //verifica e o utilizador é um admin
                echo json_encode(['status' => 'error', 'message' => 'Not an Admin']);
                exit;
            }

            $userId = $_SESSION['userId'];

            try {
                require_once 'OrdersHandler.php';
                $order = new Order();
                
                $ordersData = $order->loadCustomerOrders($userId);
                
                echo json_encode(['status' => 'success', 'ordersData' => $ordersData]);
                exit;
    
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage()); // Log interno
                echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
                exit;
            }
            break; */







    case 'someAction':
        if(!isset($_SESSION["userRole"])){  //verifica e o utilizador esta logado 
            echo json_encode(['status' => 'error', 'message' => 'Login required']);
            exit;
        } else if($_SESSION["userRole"] !== "admin"){  //verifica e o utilizador é um admin
            echo json_encode(['status' => 'error', 'message' => 'Not an Admin']);
            exit;
        }

        try {
            echo json_encode(['status' => 'success', 'data' => 'Some Data']);
            exit;

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Log interno
            echo json_encode(['status' => 'error', 'message' => 'Erro na ligação ao servidor.']);
            exit;
        }
        break;













    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid Action']);
        break;
}
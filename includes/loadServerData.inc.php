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
        } else if($_SESSION["userRole"] !== "admin"){  //verifica e o utilizador é um admin
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

            try {
                require_once "ProfileHandler.php";
                $userId = $_SESSION['userId'];

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
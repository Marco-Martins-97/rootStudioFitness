<?php
require_once 'Dbh.php';

class Client{
    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    // Carrega dados da base de dados
    public function loadApplications(){
        $query = "SELECT ca.*, u.firstName, u.lastName, CONCAT(u.firstName, ' ', u.lastName) AS username FROM clientApplications ca INNER JOIN users u ON ca.userId = u.id ORDER BY ca.submitted_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt -> execute();

        $result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    private function getEssencialData($applicationId){
        $query = "SELECT ca.*, u.userRole FROM clientApplications ca INNER JOIN users u ON ca.userId = u.id WHERE ca.id = :applicationId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
        $stmt -> execute();

        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    // Verifica se exite na base de dados
    private function hasUserApplied($userId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clientApplications WHERE userId = :userId AND applicationStatus IN ("pending", "accepted"))';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn(0);
    }

    private function applicationExists($applicationId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clientApplications WHERE id = :applicationId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicationId', $applicationId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn(0);
    }

    private function clientExists($userId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clients WHERE userId = :userId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn(0);
    }

    // Insere dados na base de dados
    private function updateUserRole($userId, $newRole){
        $query = "UPDATE users SET userRole = :newRole WHERE id = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":newRole", $newRole);
        $stmt -> bindParam(":userId", $userId);
        return $stmt->execute();
    }

    private function copyClientData($data){
        $query = "INSERT INTO clients (userId, fullName, birthDate, gender, userAddress, nif, phone, trainingPlan, experience, nutritionPlan, healthIssues, healthDetails)
                    VALUES (:userId, :fullName, :birthDate, :gender, :userAddress, :nif, :phone, :trainingPlan, :experience, :nutritionPlan, :healthIssues, :healthDetails)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $data['userId']);
        $stmt->bindParam(':fullName', $data['fullName']);
        $stmt->bindParam(':birthDate', $data['birthDate']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':userAddress', $data['userAddress']);
        $stmt->bindParam(':nif', $data['nif']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':trainingPlan', $data['trainingPlan']);
        $stmt->bindParam(':experience', $data['experience']);
        $stmt->bindParam(':nutritionPlan', $data['nutritionPlan']);
        $stmt->bindParam(':healthIssues', $data['healthIssues']);
        $stmt->bindParam(':healthDetails', $data['healthDetails']);
        return $stmt->execute();
    }

    private function saveUserApplication($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms){
        $query = "INSERT INTO clientApplications (userId, fullName, birthDate, gender, userAddress, nif, phone, trainingPlan, experience, nutritionPlan, healthIssues, healthDetails, terms) 
                    VALUES (:userId, :fullName, :birthDate, :gender, :userAddress, :nif, :phone, :trainingPlan, :experience, :nutritionPlan, :healthIssues, :healthDetails, :terms)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':fullName', $fullName);
        $stmt->bindParam(':birthDate', $birthDate);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':userAddress', $userAddress);
        $stmt->bindParam(':nif', $nif);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':trainingPlan', $trainingPlan);
        $stmt->bindParam(':experience', $experience);
        $stmt->bindParam(':nutritionPlan', $nutritionPlan);
        $stmt->bindParam(':healthIssues', $healthIssues);
        $stmt->bindParam(':healthDetails', $healthDetails);
        $stmt->bindParam(':terms', $terms);
        return $stmt->execute();
    }

    private function updateApplicationStatus($applicationId, $newStatus){
        $query = "UPDATE clientApplications SET applicationStatus = :newStatus WHERE id = :applicationId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":newStatus", $newStatus);
        $stmt -> bindParam(":applicationId", $applicationId);
        return $stmt->execute();
    }

    // Funçoes de execução
    public function submitClientApplication($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms){
        // Validação dos dados
        require_once 'validations.inc.php';
        
        // FullName
        if (isInputRequired('fullName') && isInputEmpty($fullName)){
            $this->errors['fullName'] = 'empty';
        } elseif (isNameInvalid($fullName)){
            $this->errors['fullName'] = 'invalid';
        } elseif (isLengthInvalid($fullName)){
            $this->errors['fullName'] = 'toLong';
        }

        // BirthDate
        if (isInputRequired('birthDate') && isInputEmpty($birthDate)){
            $this->errors['birthDate'] = 'empty';
        } elseif (isDateInvalid($birthDate)){
            $this->errors['birthDate'] = 'invalid';
        } elseif (isBirthInvalid($birthDate)){
            $this->errors['birthDate'] = 'birthInvalid';
        }

        // Gender
        if (isInputRequired('gender') && isInputEmpty($gender)){
            $this->errors['gender'] = 'empty';
        } elseif (isGenderInvalid($gender)){
            $this->errors['gender'] = 'invalid';
        }

        // UserAddress
        if (isInputRequired('userAddress') && isInputEmpty($userAddress)){
            $this->errors['userAddress'] = 'empty';
        } elseif (isAddressInvalid($userAddress)){
            $this->errors['userAddress'] = 'invalid';
        } elseif (isLengthInvalid($userAddress)){
            $this->errors['userAddress'] = 'toLong';
        }

        // NIF
        if (isInputRequired('nif') && isInputEmpty($nif)){
            $this->errors['nif'] = 'empty';
        } elseif (isNifInvalid($nif)){
            $this->errors['nif'] = 'invalid';
        }

        // Phone
        if (isInputRequired('phone') && isInputEmpty($phone)){
            $this->errors['phone'] = 'empty';
        } elseif (isPhoneInvalid($phone)){
            $this->errors['phone'] = 'invalid';
        }

        // TrainingPlan
        if (isInputRequired('trainingPlan') && isInputEmpty($trainingPlan)){
            $this->errors['trainingPlan'] = 'empty';
        } elseif (isTrainingPlanInvalid($trainingPlan)){
            $this->errors['trainingPlan'] = 'invalid';
        }
        // Experience
        if (isInputRequired('experience') && isInputEmpty($experience)){
            $this->errors['experience'] = 'empty';
        } elseif (isExperienceInvalid($experience)){
            $this->errors['experience'] = 'invalid';
        }
        // NutritionPlan
        if (isInputRequired('nutritionPlan') && isNotChecked($nutritionPlan)){
            $this->errors['nutritionPlan'] = 'notChecked';
        } 
        // HealthIssues
        if (isInputRequired('healthIssues') && isNotChecked($healthIssues)){
            $this->errors['healthIssues'] = 'notChecked';
        } 
        // HealthDetails
        if (isInputRequired('healthDetails') && isInputEmpty($healthDetails)){
            $this->errors['healthDetails'] = 'empty';
        } elseif (!isInputEmpty($healthDetails) && isDescriptionInvalid($healthDetails)){
            $this->errors['healthDetails'] = 'invalid';
        }
        // Terms
        if (isInputRequired('terms') && isNotChecked($terms)){
            $this->errors['terms'] = 'notChecked';
        } 

        // Verificação da ligação à base de dados
        if (!$this->conn) {
            $this->errors['connection'] = 'failed';
        }
        
        if (!$this->errors){
            // Verifica duplicados
            if ($this->hasUserApplied($userId)){
                header('Location: ../plans.php?application=duplicated#application');
                exit;
            }

            // Guarda os dados na base de dados
            if ($this->saveUserApplication($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms)){
                $_SESSION['userApplied'] = true;
                header('Location: ../plans.php?application=success#application');
                exit;
            } else {
                header('Location: ../plans.php?application=failed#application');
                exit;
            }
        } else {
            header('Location: ../plans.php?application=invalid#application');
            exit;
        }
    }

    // Revê a candidatura do cliente
    public function reviewClientApplication($applicationId, $review){
        // Validação dos dados
        $allowedReviews = ['accepted', 'rejected'];
        if(!in_array($review, $allowedReviews)){
            return ['status' => 'error', 'message' => 'Revisão inválida'];
        }
        if(!$this->applicationExists($applicationId)){
            return ['status' => 'error', 'message' => 'Candidatura não encontrada'];
        }

        // Aplica as alterações
        if ($review === 'rejected'){
            return $this->updateApplicationStatus($applicationId, $review) ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Falha ao alterar o estado'];
        } else {
            // carrega os dados da inscrição e o userRole do utilizador
            $data = $this->getEssencialData($applicationId);

            if ($this->clientExists($data['userId'])){
                return ['status' => 'error', 'message' => 'O cliente já existe'];
            }

            // implementa alterações, caso a alteraçao falhe, irá reverter essas alterações
            if (!$this->updateApplicationStatus($applicationId, $review)){
                return ['status' => 'error', 'message' => 'Falha ao alterar o estado'];
            }
            
            if (!$this->updateUserRole($data['userId'], 'client')){
                $this->updateApplicationStatus($applicationId, 'pending');
                return ['status' => 'error', 'message' => 'Falha ao alterar o cargo do utilizador'];
            }
            
            if (!$this->copyClientData($data)){
                $this->updateApplicationStatus($applicationId, 'pending');
                $this->updateUserRole($data['userId'], $data['userRole']);
                return ['status' => 'error', 'message' => 'Falha ao copiar os dados do cliente'];
            }

            return ['status' => 'success'];
        }
    }
}
<?php
require_once 'Dbh.php';

class Client{
    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    //Carrega dados da base de dados
    public function loadApplications(){
        $query = "SELECT ca.*, u.firstName, u.lastName, CONCAT(u.firstName, ' ', u.lastName) AS username FROM clientApplications ca INNER JOIN users u ON ca.userId = u.id";
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

    // Verifica se exite na base de dados
    private function hasUserApplied($userId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clientApplications WHERE userId = :userId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    private function applicationExists($applicationId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clientApplications WHERE id = :applicationId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':applicationId', $applicationId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    private function clientExists($userId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clients WHERE userId = :userId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    // Funçoes de execução
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

    private function showData($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms){
        echo 'ID: '. $userId.'<br>';
        echo 'Name: '. $fullName.'<br>';
        echo 'Date: '. $birthDate.'<br>';
        echo 'Gender: '. $gender.'<br>';
        echo 'Morada: '. $userAddress.'<br>';
        echo 'nif: '. $nif.'<br>';
        echo 'phone: '. $phone.'<br>';
        echo 'Plan: '. $trainingPlan.'<br>';
        echo 'experience: '. $experience.'<br>';
        echo 'nutrition: '. $nutritionPlan.'<br>';
        echo 'healthIssues: '. $healthIssues.'<br>';
        echo 'healthDetails: '. $healthDetails.'<br>';
        echo 'Terms: '. $terms.'<br>';
    }

    public function submitClientApplication($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms){
        //validaçao dos dados
        require_once 'validations.inc.php';
        
        // fullName
        if (isInputRequired('fullName') && isInputEmpty($fullName)){
            $this->errors['fullName'] = 'empty';
        } elseif (isNameInvalid($fullName)){
            $this->errors['fullName'] = 'invalid';
        } elseif (isLengthInvalid($fullName)){
            $this->errors['fullName'] = 'toLong';
        }
        // birthDate
        if (isInputRequired('birthDate') && isInputEmpty($birthDate)){
            $this->errors['birthDate'] = 'empty';
        } elseif (isDateInvalid($birthDate)){
            $this->errors['birthDate'] = 'invalid';
        } elseif (isBirthInvalid($birthDate)){
            $this->errors['birthDate'] = 'birthInvalid';
        }
        //gender
        if (isInputRequired('gender') && isInputEmpty($gender)){
            $this->errors['gender'] = 'empty';
        } elseif (isGenderInvalid($gender)){
            $this->errors['gender'] = 'invalid';
        }
        //userAddress
        if (isInputRequired('userAddress') && isInputEmpty($userAddress)){
            $this->errors['userAddress'] = 'empty';
        } elseif (isAddressInvalid($userAddress)){
            $this->errors['userAddress'] = 'invalid';
        } elseif (isLengthInvalid($userAddress)){
            $this->errors['userAddress'] = 'toLong';
        }
        //nif
        if (isInputRequired('nif') && isInputEmpty($nif)){
            $this->errors['nif'] = 'empty';
        } elseif (isNifInvalid($nif)){
            $this->errors['nif'] = 'invalid';
        }
        //phone
        if (isInputRequired('phone') && isInputEmpty($phone)){
            $this->errors['phone'] = 'empty';
        } elseif (isPhoneInvalid($phone)){
            $this->errors['phone'] = 'invalid';
        }
        //trainingPlan
        if (isInputRequired('trainingPlan') && isInputEmpty($trainingPlan)){
            $this->errors['trainingPlan'] = 'empty';
        } elseif (isTrainingPlanInvalid($trainingPlan)){
            $this->errors['trainingPlan'] = 'invalid';
        }
        //experience
        if (isInputRequired('experience') && isInputEmpty($experience)){
            $this->errors['experience'] = 'empty';
        } elseif (isExperienceInvalid($experience)){
            $this->errors['experience'] = 'invalid';
        }
        //nutritionPlan
        if (isInputRequired('nutritionPlan') && isNotChecked($nutritionPlan)){
            $this->errors['nutritionPlan'] = 'notChecked';
        } 
        //healthIssues
        if (isInputRequired('healthIssues') && isNotChecked($healthIssues)){
            $this->errors['healthIssues'] = 'notChecked';
        } 
        //healthDetails
        if (isInputRequired('healthDetails') && isInputEmpty($healthDetails)){
            $this->errors['healthDetails'] = 'empty';
        } elseif (!isInputEmpty($healthDetails) && isDescriptionInvalid($healthDetails)){
            $this->errors['healthDetails'] = 'invalid';
        }
        //terms
        if (isInputRequired('terms') && isNotChecked($terms)){
            $this->errors['terms'] = 'notChecked';
        } 

        // Conecção
        if (!$this->conn) {
            $this->errors['connection'] = 'connection failed';
        }
        

        if (!$this->errors){
            // duplicados
            if ($this->hasUserApplied($userId)){
                header('Location: ../plans.php?application=duplicated#application');
                exit;
            }

            // Salva Os Dados na Base de dados
            if ($this->saveUserApplication($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms)){
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

    public function reviewClientApplication($applicationId, $review){
        //Validaçao dos inputs
        $alloweReviews = ['accepted', 'rejected'];
        if(!in_array($review, $alloweReviews)){
            return ['status' => 'error', 'message' => 'Invalid Review'];
        }
        if(!$this->applicationExists($applicationId)){
            return ['status' => 'error', 'message' => 'Application not found'];
        }

        // Aplica as alterações

        if ($review === 'rejected'){
            return $this->updateApplicationStatus($applicationId, $review) ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Failed to Change Status'];
        } else {
            // carrega os dados da inscrição e o userRole do utilizador
            $data = $this->getEssencialData($applicationId);

            if ($this->clientExists($data['userId'])){
                return ['status' => 'error', 'message' => 'Client already Exists'];
            }

            // implementa alterações, caso a alteraçao falhe, irá reverter essas alterações
            if (!$this->updateApplicationStatus($applicationId, $review)){
                return ['status' => 'error', 'message' => 'Failed to Change Status'];
            }
            
            if (!$this->updateUserRole($data['userId'], 'client')){
                $this->updateApplicationStatus($applicationId, 'pending');
                return ['status' => 'error', 'message' => 'Failed to Change UserRole'];
            }
            
            if (!$this->copyClientData($data)){
                $this->updateApplicationStatus($applicationId, 'pending');
                $this->updateUserRole($data['userId'], $data['userRole']);
                return ['status' => 'error', 'message' => 'Failed to Copy Client Data'];
            }

            return ['status' => 'success']; //se tudo funcionar retorna success
        }
    }

    

}
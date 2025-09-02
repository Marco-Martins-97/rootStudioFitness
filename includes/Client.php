<?php
require_once 'Dbh.php';

class Client{
    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    private function hasUserApplied($userId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clientApplications WHERE userId = :userId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
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

    public function loadApplications(){
        $query = "SELECT ca.*, u.firstName, u.lastName, CONCAT(u.firstName, ' ', u.lastName) AS username FROM clientApplications ca INNER JOIN users u ON ca.userId = u.id";
        $stmt = $this->conn->prepare($query);
        $stmt -> execute();

        $result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

}
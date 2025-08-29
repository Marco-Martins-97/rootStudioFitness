<?php
require_once 'Dbh.php';

class Client{
    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }


    private function showData($fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $issues, $details, $terms){
        echo 'Name: '. $fullName.'<br>';
        echo 'Date: '. $birthDate.'<br>';
        echo 'Gender: '. $gender.'<br>';
        echo 'Morada: '. $userAddress.'<br>';
        echo 'nif: '. $nif.'<br>';
        echo 'phone: '. $phone.'<br>';
        echo 'Plan: '. $trainingPlan.'<br>';
        echo 'experience: '. $experience.'<br>';
        echo 'nutrition: '. $nutritionPlan.'<br>';
        echo 'issues: '. $issues.'<br>';
        echo 'details: '. $details.'<br>';
        echo 'Terms: '. $terms.'<br>';
    }
    public function submitClientApplication($fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $issues, $details, $terms){
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
        } elseif (isLengthInvalid($birthDate)){
            $this->errors['birthDate'] = 'toLong';
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
        //issues
        if (isInputRequired('issues') && isNotChecked($issues)){
            $this->errors['issues'] = 'notChecked';
        } 
        //details
        if (isInputRequired('details') && isInputEmpty($details)){
            $this->errors['details'] = 'empty';
        } elseif (!isInputEmpty($details) && isDescriptionInvalid($details)){
            $this->errors['details'] = 'invalid';
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
            /* if ($this->createNewUser()){
                header('Location: ../plans.php?application=success');
                exit;
            } else {
                header('Location: ../plans.php?application=failed');
                exit;
            } */
           $this->showData($fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $issues, $details, $terms);
        } else {
           /*  header('Location: ../plans.php?application=invalid');
            exit; */
            print_r($this->errors);
        }

    }

}
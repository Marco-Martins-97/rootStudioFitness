<?php
require_once 'Dbh.php';

class Profile{
    private $userId;
    private $conn;
    private $errors = [];

    public function __construct($userId){
        $this->userId = $userId;

        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }
    // get Data
    public function loadUserData(){
        $query = "SELECT firstName, lastName, email, userRole FROM users WHERE id = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":userId", $this->userId);
        $stmt -> execute();

        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function loadClientData(){
        $query = "SELECT fullName, birthDate, gender, userAddress, nif, phone, trainingPlan, experience, nutritionPlan, healthIssues, healthDetails FROM clients WHERE userId = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":userId", $this->userId);
        $stmt -> execute();

        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    // update Data
    private function saveField($field, $value){
        $table = in_array($field, ['firstName', 'lastName', 'email']) ? 'users' : 'clients';    // seleciona atabela correta para salvar os dados
        $index = in_array($field, ['firstName', 'lastName', 'email']) ? 'id' : 'userId';    // seleciona o index correto para encontrar os dados pretendidos
        
        $query = "UPDATE $table SET $field = :fieldValue WHERE $index = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":fieldValue", $value);
        $stmt -> bindParam(":userId", $this->userId);
        return $stmt->execute();
    }


    public function updateField($field, $value){
        //validaçao dos dados
        require_once 'validations.inc.php';

        switch ($field) {
            case 'firstName':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isNameInvalid($value)){
                    $this->errors[$field] = 'invalid';
                } elseif (isLengthInvalid($value)){
                    $this->errors[$field] = 'toLong';
                }
                break;
            case 'lastName':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isNameInvalid($value)){
                    $this->errors[$field] = 'invalid';
                } elseif (isLengthInvalid($value)){
                    $this->errors[$field] = 'toLong';
                }
                break;
            case 'email':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isEmailInvalid($value)){
                    $this->errors[$field] = 'invalid';
                } elseif (thisEmailExists($value)){
                    $this->errors[$field] = 'taken';
                } elseif (isLengthInvalid($value)){
                    $this->errors[$field] = 'toLong';
                }
                break;
            case 'fullName':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isNameInvalid($value)){
                    $this->errors[$field] = 'invalid';
                } elseif (isLengthInvalid($value)){
                    $this->errors[$field] = 'toLong';
                }
                break;
            case 'birthDate':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isDateInvalid($value)){
                    $this->errors[$field] = 'invalid';
                } elseif (isBirthInvalid($value)){
                    $this->errors[$field] = 'birthInvalid';
                }
                break;
            case 'gender':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isGenderInvalid($value)){
                    $this->errors[$field] = 'invalid';
                }
                break;
            case 'userAddress':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isAddressInvalid($value)){
                    $this->errors[$field] = 'invalid';
                } elseif (isLengthInvalid($value)){
                    $this->errors[$field] = 'toLong';
                }
                break;
            case 'nif':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isNifInvalid($value)){
                    $this->errors[$field] = 'invalid';
                }
                break;
            case 'phone':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isPhoneInvalid($value)){
                    $this->errors[$field] = 'invalid';
                }
                break;
            case 'trainingPlan':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isTrainingPlanInvalid($value)){
                    $this->errors[$field] = 'invalid';
                }
                break;
            case 'experience':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (isExperienceInvalid($value)){
                    $this->errors[$field] = 'invalid';
                }
                break;
            case 'nutritionPlan':
            case 'healthIssues':
            case 'terms':
                if (isInputRequired($field) && isNotChecked($value)){
                    $this->errors[$field] = 'notChecked';
                }
                break;
            case 'healthDetails':
                if (isInputRequired($field) && isInputEmpty($value)){
                    $this->errors[$field] = 'empty';
                } elseif (!isInputEmpty($value) && isDescriptionInvalid($value)){
                    $this->errors[$field] = 'invalid';
                }
                break;

            default:
                $this->errors['field'] = 'invalid';
                break;
        }

        // Conecção
        if (!$this->conn) {
            $this->errors['connection'] = 'connection failed';
        }

        if (!$this->errors){
            if ($this->saveField($field, $value)){
                return ['status' => 'success'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to save'];
            }
        } else {
            // Envias os erros na mensagem
            $message = '';
            foreach($this->errors as $field => $error){
                $message .= "$field: $error, ";
            }
            $message = rtrim($message, ', ');   //remove o ', ' no final

            return ['status' => 'error', 'message' => $message];
        }
    }
}
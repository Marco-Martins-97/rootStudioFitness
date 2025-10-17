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
    // Carrega dados da base de dados
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
    // Insere dados na base de dados
    private function saveField($field, $value){
        $table = in_array($field, ['firstName', 'lastName', 'email']) ? 'users' : 'clients';    // seleciona atabela correta para salvar os dados
        $index = in_array($field, ['firstName', 'lastName', 'email']) ? 'id' : 'userId';    // seleciona o index correto para encontrar os dados pretendidos
        
        $query = "UPDATE $table SET $field = :fieldValue WHERE $index = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":fieldValue", $value);
        $stmt -> bindParam(":userId", $this->userId);
        return $stmt->execute();
    }

    private function saveNewPwd($value){
        $query = "UPDATE users SET pwd = :newPwd WHERE id = :userId;";
        $stmt = $this->conn->prepare($query);

        $options = ['cost' => 12];
        $hashedPwd = password_hash($value, PASSWORD_BCRYPT, $options);

        $stmt -> bindParam(":newPwd", $hashedPwd);
        $stmt -> bindParam(":userId", $this->userId);
        return $stmt->execute();
    }

    // Funçoes de execução
    public function updatePwd($currentPwd, $newPwd, $confirmNewPwd){
        // Validação dos dados
        require_once 'validations.inc.php';

        if(isPwdWrong($currentPwd)){
            $this->errors['currentPwd'] = 'A palavra-passe atual está incorreta.';
        }
        
        if(isInputRequired('pwd') && isInputEmpty($newPwd)){
            $this->errors['newPwd'] = 'A nova palavra-passe é obrigatória.';
        } elseif (isPwdShort($newPwd)){
            $this->errors['newPwd'] = 'A nova palavra-passe deve ter, no mínimo, 8 caracteres.';
        } elseif (isLengthInvalid($newPwd)){
            $this->errors['newPwd'] = 'A nova palavra-passe excede o limite de caracteres permitido.';
        } elseif (!isPwdNoMatch($currentPwd, $newPwd)){
            $this->errors['newPwd'] = 'A nova palavra-passe não pode ser igual à atual.';
        }
        
        if(isInputRequired('confPwd') && isInputEmpty($confirmNewPwd)){
            $this->errors['confNewPwd'] = 'A confirmação da nova palavra-passe é obrigatória.';
        } elseif (isLengthInvalid($confirmNewPwd)){
            $this->errors['confNewPwd'] = 'A confirmação da nova palavra-passe excede o limite de caracteres permitido.';
        } elseif (isPwdNoMatch($newPwd, $confirmNewPwd)){
            $this->errors['confNewPwd'] = 'As novas palavras-passe não coincidem.';
        }

        // Verificação da ligação à base de dados
        if (!$this->conn) {
            $this->errors['connection'] = 'Falha na ligação ao servidor.';
        }

        // Processa o resultado se não ouver erros
        if (!$this->errors) {
            if ($this->saveNewPwd($newPwd)) {
                return ['status' => 'valid'];
            } else {
                return ['status' => 'invalid', 'message' => 'Ocorreu um erro ao guardar os dados. Tente novamente.'];
            }
        } else {
            return ['status' => 'invalid', 'message' => $this->errors];
        } 
    }

    public function updateField($field, $value){
        // Validação dos dados
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

        // Verificação da ligação à base de dados
        if (!$this->conn) {
            $this->errors['connection'] = 'connection failed';
        }

        // Retorna resultado final
        if (!$this->errors){
            if ($this->saveField($field, $value)){
                return ['status' => 'success'];
            } else {
                return ['status' => 'error', 'message' => 'Erro ao guardar os dados.'];
            }
        } else {
            // Monta mensagem de erro formatada
            $message = '';
            foreach($this->errors as $field => $error){
                $message .= "$field: $error, ";
            }
            $message = rtrim($message, ', ');

            return ['status' => 'error', 'message' => $message];
        }
    }
}
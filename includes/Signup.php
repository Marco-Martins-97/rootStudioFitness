<?php
require_once 'Dbh.php';

class Signup{
    private $email;
    private $pwd;
    private $confPwd;
    
    private $firstName;
    private $lastName;

    private $conn;
    private $errors = [];

    public function __construct($email, $pwd, $confPwd, $firstName, $lastName){
        $this->email = $email;
        $this->pwd = $pwd;
        $this->confPwd = $confPwd;
        
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    // Insere dados na base de dados
    private function createNewUser(){
        $query = 'INSERT INTO users (email, pwd, firstName, lastName) VALUES (:email, :pwd, :firstName, :lastName)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        
        $options = ['cost' => 12];
        $hashedPwd = password_hash($this->pwd, PASSWORD_BCRYPT, $options);
        
        $stmt->bindParam(':pwd', $hashedPwd);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':lastName', $this->lastName);

        return $stmt->execute() ? 'success' : 'fail';
    }

    // Regista o utilizador
    public function newSignup(){
        // Validação dos dados
        require_once 'validations.inc.php';

        // FirstName
        if (isInputRequired('firstName') && isInputEmpty($this->firstName)){
            $this->errors['firstName'] = 'empty';
        } elseif (isNameInvalid($this->firstName)){
            $this->errors['firstName'] = 'invalid';
        } elseif (isLengthInvalid($this->firstName)){
            $this->errors['firstName'] = 'toLong';
        } 

        // LastName
        if (isInputRequired('lastName') && isInputEmpty($this->lastName)){
            $this->errors['lastName'] = 'empty';
        } elseif (isNameInvalid($this->lastName)){
            $this->errors['lastName'] = 'invalid';
        } elseif (isLengthInvalid($this->lastName)){
            $this->errors['lastName'] = 'toLong';
        }

        // Email
        if (isInputRequired('email') && isInputEmpty($this->email)){
            $this->errors['email'] = 'empty';
        } elseif (isEmailInvalid($this->email)){
            $this->errors['email'] = 'invalid';
        } elseif (thisEmailExists($this->email)){
            $this->errors['email'] = 'taken';
        } elseif (isLengthInvalid($this->email)){
            $this->errors['email'] = 'toLong';
        }

        // Password
        if (isInputRequired('pwd') && isInputEmpty($this->pwd)){
            $this->errors['pwd'] = 'empty';
        } elseif (isPwdShort($this->pwd)){
            $this->errors['pwd'] = 'short';
        } elseif (isLengthInvalid($this->pwd)){
            $this->errors['pwd'] = 'toLong';
        } elseif (isInputRequired('confPwd') && isInputEmpty($this->confPwd)){
            $this->errors['confPwd'] = 'empty';
        } elseif (isLengthInvalid($this->confPwd)){
            $this->errors['confPwd'] = 'toLong';
        } elseif (isPwdNoMatch($this->pwd, $this->confPwd)){
            $this->errors['confPwd'] = 'noMatch';
        }

        // Verificação da ligação à base de dados
        if (!$this->conn) {
            $this->errors['connection'] = 'failed';
        }

        // Criar utilizador se não houver erros
        if (!$this->errors){
            if ($this->createNewUser()){
                header('Location: ../signup.php?signup=success');
                exit;
            } else {
                header('Location: ../signup.php?signup=failed');
                exit;
            }
        } else {
            header('Location: ../signup.php?signup=invalid');
            exit;
        }
    }
}
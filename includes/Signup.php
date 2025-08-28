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

    /* private function showData(){
        echo "Email: ".$this->email."<br>";
        echo "Pwd: ".$this->pwd."<br>";
        echo "CPwd: ".$this->confPwd."<br>";
        echo "Nome: ".$this->firstName."<br>";
        echo "Apelido: ".$this->lastName."<br>";
    } */

    private function createNewUser(){
        $query = "INSERT INTO users (email, pwd, firstName, lastName) VALUES (:email, :pwd, :firstName, :lastName)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        
        $options = ["cost" => 12];
        $hashedPwd = password_hash($this->pwd, PASSWORD_BCRYPT, $options);
        
        $stmt->bindParam(':pwd', $hashedPwd);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':lastName', $this->lastName);

        return $stmt->execute() ? 'success' : 'fail';
    }

    // regista ao uilizador
    public function newSignup(){
        //validaçao dos dados
        require_once 'validations.inc.php';

        // firstName
        if (isInputRequired('firstName') && isInputEmpty($this->firstName)){
            $this->errors["firstName"] = "empty";
        } elseif (isNameInvalid($this->firstName)){
            $this->errors["firstName"] = "invalid";
        } 

        // lastName
        if (isInputRequired('lastName') && isInputEmpty($this->lastName)){
            $this->errors["lastName"] = "empty";
        } elseif (isNameInvalid($this->lastName)){
            $this->errors["lastName"] = "invalid";
        }

        // Email
        if (isInputRequired('email') && isInputEmpty($this->email)){
            $this->errors["email"] = "empty";
        } elseif (isEmailInvalid($this->email)){
            $this->errors["email"] = "invalid";
        } elseif (thisEmailExists($this->email)){
            $this->errors["email"] = "taken";
        }

        // Pwd
        if (isInputRequired('pwd') && isInputEmpty($this->pwd)){
            $this->errors["pwd"] = "empty";
        } elseif (isPwdShort($this->pwd)){
            $this->errors["pwd"] = "short";
        } elseif (isInputRequired('confPwd') && isInputEmpty($this->confPwd)){
            $this->errors["confPwd"] = "empty";
        } elseif (isPwdNoMatch($this->pwd, $this->confPwd)){
            $this->errors["confPwd"] = "noMatch";
        }

        // Conecção
        if (!$this->conn) {
            $this->errors["connection"] = "connection failed";
        }

        // Criar utilizador
        if (!$this->errors){
            if ($this->createNewUser()){
                header("Location: ../login.php?signup=success");
                exit;
            } else {
                header("Location: ../signup.php?signup=failed");
                exit;
            }
        } else {
            header("Location: ../signup.php?signup=invalid");
            exit;
        }
    }
}
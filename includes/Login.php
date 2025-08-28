<?php
require_once 'Dbh.php';
require_once 'configSession.inc.php';

class Login{
    private $email;
    private $pwd;

    private $conn;

    public function __construct($email, $pwd){
        $this->email = $email;
        $this->pwd = $pwd;

        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    private function getUserData(){
        $query = 'SELECT * FROM users WHERE email = :email;';
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(':email', $this->email);
        $stmt -> execute();

        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    private function createSessionData($userData){
        $_SESSION['userId'] = $userData['id'];
        $_SESSION['userRole'] = $userData['userRole'];
        $_SESSION['username'] = $userData['firstName'].' '.$userData['lastName'];
    }

    private function returnError($message, $error){
        $_SESSION['loginError'] = $message;
        header("Location: ../login.php?login=$error");
        exit;
    }
    
    public function login(){
        //validaçao dos dados
        if (!$this->conn) {
            $this->returnError('Erro na ligação à base de dados.', 'failed');
        }

        if (empty($this->email) || empty($this->pwd)){
            $this->returnError('Campos de preenchimento obrigatório!', 'empty');
        }
        
        $userData = $this->getUserData();

        if (!$userData || !password_verify($this->pwd, $userData['pwd'])){
            $this->returnError('Email ou palavra-passe incorretos.', 'invalid');
        }

        //Inicia a Sessão
        $this->createSessionData($userData);

        header("Location: ../login.php?login=success");
        exit;

    }
}
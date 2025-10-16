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

    private function hasUserApplied($userId){
        $query = 'SELECT EXISTS(SELECT 1 FROM clientApplications WHERE userId = :userId AND applicationStatus IN ("pending", "accepted"))';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    private function createSessionData($userData){
        $_SESSION['userId'] = $userData['id'];
        $_SESSION['userRole'] = $userData['userRole'];
        $_SESSION['username'] = $userData['firstName'].' '.$userData['lastName'];
        if ($this->hasUserApplied($userData['id'])){
            $_SESSION['userApplied'] = true;
        }
    }

    private function returnLoginStatus($status){
        header('Location: ../login.php?login='.$status);
        exit;
    }
    
    public function login(){
        //validaçao dos dados
        if (!$this->conn) {
            $this->returnLoginStatus('failed');
        }

        if (empty($this->email) || empty($this->pwd)){
            $this->returnLoginStatus('empty');
        }
        
        $userData = $this->getUserData();

        if (!$userData || !password_verify($this->pwd, $userData['pwd'])){
            $this->returnLoginStatus('invalid');
        }

        //Inicia a Sessão
        $this->createSessionData($userData);
        
        $this->returnLoginStatus('success');
    }
}
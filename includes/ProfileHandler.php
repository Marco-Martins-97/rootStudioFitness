<?php
require_once 'Dbh.php';

class Profile{
    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    public function loadUserData($userId){
        $query = "SELECT firstName, lastName, email, userRole FROM users WHERE id = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":userId", $userId);
        $stmt -> execute();

        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function loadClientData($userId){
        $query = "SELECT fullName, birthDate, gender, userAddress, nif, phone, trainingPlan, experience, nutritionPlan, healthIssues, healthDetails FROM clients WHERE userId = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":userId", $userId);
        $stmt -> execute();

        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }

}
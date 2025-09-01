<?php
require_once 'configSession.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $birthDate = htmlspecialchars(trim($_POST["birthDate"]));
    $gender = htmlspecialchars(trim($_POST["gender"] ?? ''));
    $userAddress = htmlspecialchars(trim($_POST["userAddress"]));
    $nif = htmlspecialchars(trim($_POST["nif"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $trainingPlan = htmlspecialchars(trim($_POST["training-plan"] ?? ''));
    $experience = htmlspecialchars(trim($_POST["experience"] ?? ''));
    $nutritionPlan = isset($_POST['nutrition-plan']) ? 'yes' : 'no';
    $healthIssues = isset($_POST['health-issues']) ? 'yes' : 'no';
    $healthDetails = htmlspecialchars(trim($_POST["health-details"]));
    $terms = isset($_POST['terms']) ? 'yes' : 'no';

    $userId = $_SESSION['userId'];

    try { 
        require_once 'Client.php';
        $signup = new Client();
        $signup -> submitClientApplication($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms);
        
    } catch (PDOException $e) {
        die ('Query Falhou: '.$e->getMessage());
    }

} else{
    header('Location: ../signup.php');
    exit;
}
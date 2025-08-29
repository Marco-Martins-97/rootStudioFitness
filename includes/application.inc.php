<?php

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
    $issues = isset($_POST['health-issues']) ? 'yes' : 'no';
    $details = htmlspecialchars(trim($_POST["health-details"]));
    $terms = isset($_POST['terms']) ? 'yes' : 'no';

    try { 
        require_once 'Client.php';
        $signup = new Client();
        $signup -> submitClientApplication($fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $issues, $details, $terms);
        
    } catch (PDOException $e) {
        die ('Query Falhou: '.$e->getMessage());
    }

} else{
    header('Location: ../signup.php');
    exit;
}
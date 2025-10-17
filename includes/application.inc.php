<?php
require_once 'configSession.inc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

if (!isset($_SESSION['userId'])) {
    header('Location: ../login.php');
    exit;
}

function getPost($input){
    return trim($_POST[$input] ?? ''); 
}

$fullName = getPost('fullName');
$birthDate = getPost('birthDate');
$gender = getPost('gender');
$userAddress = getPost('userAddress');
$nif = getPost('nif');
$phone = getPost('phone');
$trainingPlan = getPost('training-plan');
$experience = getPost('experience');
$nutritionPlan = isset($_POST['nutrition-plan']) ? 'yes' : 'no';
$healthIssues = isset($_POST['health-issues']) ? 'yes' : 'no';
$healthDetails = getPost('health-details');
$terms = isset($_POST['terms']) ? 'yes' : 'no';

$userId = $_SESSION['userId'];

try { 
    require_once 'Client.php';
    $signup = new Client();
    $signup -> submitClientApplication($userId, $fullName, $birthDate, $gender, $userAddress, $nif, $phone, $trainingPlan, $experience, $nutritionPlan, $healthIssues, $healthDetails, $terms);
    
} catch (PDOException $e) {
    error_log('Erro: ' . $e->getMessage());
    header('Location: ../signup.php?connection=error');
    exit;
}
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

$orderDataJson = getPost('orderData');
$fullName = getPost('fullName');
$birthDate = getPost('birthDate18');
$userAddress = getPost('userAddress');
$checkoutType = getPost('checkoutType');
    
$userId = $_SESSION['userId'];

try { 
    require_once 'OrdersHandler.php';
    $order = new Order();
    $order -> processCheckout($userId, $fullName, $birthDate, $userAddress, $orderDataJson, $checkoutType);
    
} catch (PDOException $e) {
    error_log('Erro: ' . $e->getMessage());
    header('Location: ../shop.php?connection=error');
    exit;
}
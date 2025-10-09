<?php
require_once 'configSession.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderDataJson = $_POST['orderData'] ?? '';
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $birthDate = htmlspecialchars(trim($_POST["birthDate18"]));
    $userAddress = htmlspecialchars(trim($_POST["userAddress"]));
    $checkoutType = trim($_POST["checkoutType"]);
    
    $userId = $_SESSION['userId'];

    try { 
        require_once 'OrdersHandler.php';
        $order = new Order();
        $order -> processCheckout($userId, $fullName, $birthDate, $userAddress, $orderDataJson, $checkoutType);
        
    } catch (PDOException $e) {
        die ('Query Falhou: '.$e->getMessage());
    }

} else{
    header('Location: ../shop.php');
    exit;
}
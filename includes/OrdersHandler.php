<?php
require_once 'Dbh.php';

class Order{
    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    private function getStock($productId){
        $query="SELECT productStock FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['productStock'] ?? false;
    }

    private function getOrderStatus($orderId){
        $query="SELECT orderStatus FROM orders WHERE orderId = :orderId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['orderStatus'] ?? false;
    }

    private function getProductData($productId){
        $query="SELECT productName, productPrice FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    private function orderIdExists($orderId){
        $query = 'SELECT EXISTS(SELECT 1 FROM orders WHERE orderId = :orderId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    private function updateOrderStatus($orderId, $newStatus){
        $query = "UPDATE orders SET orderStatus = :newStatus WHERE orderId = :orderId;";
        $stmt = $this->conn->prepare($query);
        $stmt -> bindParam(":newStatus", $newStatus);
        $stmt -> bindParam(":orderId", $orderId);
        return $stmt->execute();
    }

    private function createNewOrder($orderId, $userId, $fullName, $userAddress, $checkoutType, $productId, $qty, $productName, $productPrice){
        $query = 'INSERT INTO orders (orderId, userId, productId, productName, productQuantity, productPrice, customerName, customerAddress, checkoutType) VALUES (:orderId, :userId, :productId, :productName, :productQuantity, :productPrice, :customerName, :customerAddress, :checkoutType)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':productId', $productId);
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productQuantity', $qty);
        $stmt->bindParam(':productPrice', $productPrice);
        $stmt->bindParam(':customerName', $fullName);
        $stmt->bindParam(':customerAddress', $userAddress);
        $stmt->bindParam(':checkoutType', $checkoutType);

        return $stmt->execute();
    }

    private function deleteProductFromCart($productId, $userId){
        $query = "DELETE FROM shoppingcart WHERE productId = :productId AND userId = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->bindParam(':userId', $userId);

        return $stmt->execute();
    }


    public function loadOrders($userId){
        $query = "SELECT o.orderId, o.productName, o.productQuantity, o.productPrice, o.orderDate, o.orderStatus, p.productImgSrc FROM orders AS o INNER JOIN products AS p ON o.productId = p.id WHERE userId = :userId ORDER BY orderDate DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt -> execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function loadCustomerOrders(){
        $query = "SELECT o.orderId, o.productName, o.productQuantity, o.productPrice, o.customerName, o.customerAddress, o.orderDate, o.orderStatus, p.productImgSrc FROM orders AS o INNER JOIN products AS p ON o.productId = p.id ORDER BY orderDate DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt -> execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    private function dispatchOrder($orderId){
        try {
            $this->conn->beginTransaction();    // inicia a transaçao

            // pega o productId e quantidade dos produtos da ordem
            $query="SELECT productId, productQuantity FROM orders WHERE orderId = :orderId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':orderId', $orderId);
            $stmt->execute();

            $orderProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //verifica se nao está vazio
            if(empty($orderProducts)){
                throw new Exception("Falha ao carregar os dados da encomenda: $orderId");
            }

            //remove a quantidade de cada produto do stock
            $updateQuery = "UPDATE products SET productStock = productStock - :productQty WHERE id = :productId AND productStock >= :productQty";
            $updateStmt = $this->conn->prepare($updateQuery);

            foreach($orderProducts as $product){
                $updateStmt->execute([
                    ':productQty' => $product['productQuantity'],
                    ':productId' => $product['productId']
                ]);

                //caso o stock seja insficiente, reverte operaçao
                if ($updateStmt->rowCount() === 0) {
                    throw new Exception("Stock Insuficiente");
                }
            }

            // altera o status para enviado            
            if(!$this->updateOrderStatus($orderId, 'dispatched')){
                throw new Exception("Falha ao atualizar o status");
            }

            //se tudo for bem suceddido , salva as alteraçoes;
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    private function createOrders($userId, $fullName, $userAddress, $orderData, $checkoutType){
        do {
            $uniqueId = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)); //cria uma string de 8 digitos aleatória com letras maiusculas e numeros
        } while ($this->orderIdExists($uniqueId));

        $this->conn->beginTransaction();
        try {
        
            foreach ($orderData as $product) {
                $productId = $product['id'];
                $qty = $product['qty'];
                $productData = $this->getProductData($productId);

                if(!$productData) {
                    $this->errors['loadProductData'] = 'failed';
                    throw new Exception("Falha ao carregar os dados do produto $productId");
                }

                if(!$this->createNewOrder($uniqueId, $userId, $fullName, $userAddress, $checkoutType, $productId, $qty, $productData['productName'], $productData['productPrice'])){
                    throw new Exception("Falha ao criar a ordem do produto $productId");
                }

                if($checkoutType === 'cart'){
                    if(!$this->deleteProductFromCart($productId, $userId)){
                        throw new Exception("Falha ao apagar o produto $productId do carrinho");
                    }
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

        


    public function processCheckout($userId, $fullName, $birthDate, $userAddress, $orderDataJson, $checkoutType){
        //validaçao dos dados
        require_once 'validations.inc.php';
        
        $orderData = json_decode($orderDataJson, true);

        // checkoutType
        if(!in_array($checkoutType, ['direct', 'cart'])){
            $this->errors['type'] = 'invalid';
        }

        //orderData
        if(json_last_error() !== JSON_ERROR_NONE){
            $this->errors['orderData'] = 'invalidJSON';
        } elseif (!is_array($orderData)){
            $this->errors['orderData'] = 'notArray';
        } elseif (empty($orderData)){
            $this->errors['orderData'] = 'empty';
        }

        // order products
        foreach ($orderData as $product) {
            $productId = htmlspecialchars($product['id']) ?? null;
            $qty = htmlspecialchars($product['qty']) ?? 0;
            $stock = $this->getStock($productId);
        
            if(!$productId || $qty <= 0){
                $this->errors['products'] = 'missingData';
            } elseif (!$stock){
                $this->errors['products'] = 'notFound';
            } elseif ($stock < $qty){
                $this->errors['products'] = 'outOfStock';
            }
        }

        // fullName
        if (isInputRequired('fullName') && isInputEmpty($fullName)){
            $this->errors['fullName'] = 'empty';
        } elseif (isNameInvalid($fullName)){
            $this->errors['fullName'] = 'invalid';
        } elseif (isLengthInvalid($fullName)){
            $this->errors['fullName'] = 'toLong';
        }

        // birthDate
        if (isInputRequired('birthDate') && isInputEmpty($birthDate)){
            $this->errors['birthDate'] = 'empty';
        } elseif (isDateInvalid($birthDate)){
            $this->errors['birthDate'] = 'invalid';
        } elseif (isBirthInvalid($birthDate)){
            $this->errors['birthDate'] = 'birthInvalid';
        } elseif (isUnder18($birthDate)){
            $this->errors['birthDate'] = 'under18';
        } 
        
        //userAddress
        if (isInputRequired('userAddress') && isInputEmpty($userAddress)){
            $this->errors['userAddress'] = 'empty';
        } elseif (isAddressInvalid($userAddress)){
            $this->errors['userAddress'] = 'invalid';
        } elseif (isLengthInvalid($userAddress)){
            $this->errors['userAddress'] = 'toLong';
        }

        // Conecção
        if (!$this->conn) {
            $this->errors['connection'] = 'failed';
        }


        // Criar utilizador
        if (!$this->errors){
            if ($this->createOrders($userId, $fullName, $userAddress, $orderData, $checkoutType)){
                header('Location: ../shop.php?checkout=success');
                exit;
            } else {
                header('Location: ../shop.php?checkout=failed');
                exit;
            }
        } else {
            header("Location: ../shop.php?checkout=error");
            exit;
        }
    }

    public function reviewOrder($orderId, $review){
        $allowedReviews = ['received', 'canceled', 'accepted', 'dispatched', 'rejected'];
        $validTransitions = [
            'pending'    => ['accepted', 'canceled', 'rejected'],
            'accepted'   => ['dispatched', 'canceled'],
            'dispatched' => ['received'],
        ];

        if(!in_array($review, $allowedReviews)){
            return ['status' => 'error', 'message' => 'Invalid Review'];
        }

        if($_SESSION["userRole"] !== "admin" && in_array($review, ['accepted', 'dispatched', 'rejected'])){ 
            return ['status' => 'error', 'message' => 'Not an Admin'];
        }

        $status = $this->getOrderStatus($orderId);

        if(!$status){
            return ['status' => 'error', 'message' => 'Order not found'];
        }

        if (!isset($validTransitions[$status]) || !in_array($review, $validTransitions[$status])) {
            return ['status' => 'error', 'message' => 'Invalid transition'];
        }

        if ($review === 'dispatched'){
            //remove stock da loja e altera o status da encomenda para enviado
            if(!$this->dispatchOrder($orderId)){
                return ['status' => 'error', 'message' => 'Failed to process dispatch'];
            }
        } else {

            if(!$this->updateOrderStatus($orderId, $review)){
                return ['status' => 'error', 'message' => 'Failed to Change Status'];
            }
        }
       
        return ['status' => 'success'];
    }
}
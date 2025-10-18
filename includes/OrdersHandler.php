<?php
require_once 'Dbh.php';
require_once 'configSession.inc.php';

class Order{
    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    // Carrega dados da base de dados
    private function getStock($productId){
        $query="SELECT productStock FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['productStock'] : false;
    }

    private function getOrderStatus($orderId){
        $query="SELECT orderStatus FROM orders WHERE orderId = :orderId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['orderStatus'] : false;
    }

    private function getProductData($productId){
        $query="SELECT productName, productPrice FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
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

    // Verifica se exite na base de dados
    private function orderIdExists($orderId){
        $query = 'SELECT EXISTS(SELECT 1 FROM orders WHERE orderId = :orderId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':orderId', $orderId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    // Insere dados na base de dados
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

    // Apaga dados na base de dados
    private function deleteProductFromCart($productId, $userId){
        $query = "DELETE FROM shoppingcart WHERE productId = :productId AND userId = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->bindParam(':userId', $userId);

        return $stmt->execute();
    }

    // Funçoes de execução
    private function dispatchOrder($orderId){
        try {
            $this->conn->beginTransaction();    // inicia a transação

            // Obtém o productId e quantidade dos produtos da encomenda
            $query="SELECT productId, productQuantity FROM orders WHERE orderId = :orderId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':orderId', $orderId);
            $stmt->execute();

            $orderProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verifica se a encomenda existe
            if(empty($orderProducts)){
                throw new Exception("Falha ao carregar os dados da encomenda: $orderId");
            }

            // Atualiza o stock dos produtos
            $updateQuery = "UPDATE products SET productStock = productStock - :productQty WHERE id = :productId AND productStock >= :productQty";
            $updateStmt = $this->conn->prepare($updateQuery);

            foreach($orderProducts as $product){
                $updateStmt->execute([
                    ':productQty' => $product['productQuantity'],
                    ':productId' => $product['productId']
                ]);

                // Se o stock for insuficiente, reverte a operação
                if ($updateStmt->rowCount() === 0) {
                    throw new Exception("Stock Insuficiente");
                }
            }

            // Altera o estado da encomenda para "enviado"       
            if(!$this->updateOrderStatus($orderId, 'dispatched')){
                throw new Exception("Falha ao atualizar o estado");
            }

            // Confirma a transação
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
                    throw new Exception("Falha ao criar a encomenda do produto $productId");
                }

                if($checkoutType === 'cart'){
                    if(!$this->deleteProductFromCart($productId, $userId)){
                        throw new Exception("Falha ao remover o produto $productId do carrinho");
                    }
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function processCheckout($userId, $fullName, $birthDate, $userAddress, $orderDataJson, $checkoutType){
        // Validação dos dados
        require_once 'validations.inc.php';
        
        $orderData = json_decode($orderDataJson, true);

        // Tipo de checkout
        if(!in_array($checkoutType, ['direct', 'cart'])){
            $this->errors['type'] = 'invalid';
        }

        // Dados da encomenda
        if(json_last_error() !== JSON_ERROR_NONE){
            $this->errors['orderData'] = 'invalidJSON';
        } elseif (!is_array($orderData)){
            $this->errors['orderData'] = 'notArray';
        } elseif (empty($orderData)){
            $this->errors['orderData'] = 'empty';
        }

        // Validação dos produtos
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

        // FullName
        if (isInputRequired('fullName') && isInputEmpty($fullName)){
            $this->errors['fullName'] = 'empty';
        } elseif (isNameInvalid($fullName)){
            $this->errors['fullName'] = 'invalid';
        } elseif (isLengthInvalid($fullName)){
            $this->errors['fullName'] = 'toLong';
        }

        // BirthDate
        if (isInputRequired('birthDate') && isInputEmpty($birthDate)){
            $this->errors['birthDate'] = 'empty';
        } elseif (isDateInvalid($birthDate)){
            $this->errors['birthDate'] = 'invalid';
        } elseif (isBirthInvalid($birthDate)){
            $this->errors['birthDate'] = 'birthInvalid';
        } elseif (isUnder18($birthDate)){
            $this->errors['birthDate'] = 'under18';
        } 
        
        // UserAddress
        if (isInputRequired('userAddress') && isInputEmpty($userAddress)){
            $this->errors['userAddress'] = 'empty';
        } elseif (isAddressInvalid($userAddress)){
            $this->errors['userAddress'] = 'invalid';
        } elseif (isLengthInvalid($userAddress)){
            $this->errors['userAddress'] = 'toLong';
        }

        // Verificação da ligação à base de dados
        if (!$this->conn) {
            $this->errors['connection'] = 'failed';
        }

        // Criar a encomenda
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
        $allowedReviews = ['received', 'canceled', 'accepted', 'dispatched', 'rejected'];    // Lista de estados possíveis que podem ser atribuídos a uma encomenda
        $validTransitions = [   // Define transições válidas entre estados das encomendas
            'pending'    => ['accepted', 'canceled', 'rejected'],
            'accepted'   => ['dispatched', 'canceled'],
            'dispatched' => ['received'],
        ];

        // Verifica se o estado fornecido é válido
        if(!in_array($review, $allowedReviews)){    
            return ['status' => 'error', 'message' => 'Revisão inválida'];
        }

        // Verifica permissões: apenas administradores podem mudar certos estados
        if((!isset($_SESSION["userRole"]) || $_SESSION["userRole"] !== "admin") && in_array($review, ['accepted', 'dispatched', 'rejected'])){ 
            return ['status' => 'error', 'message' => 'Acesso negado: apenas administradores'];
        }

        $status = $this->getOrderStatus($orderId);

        // Verifica se a encomenda existe
        if(!$status){   
            return ['status' => 'error', 'message' => 'Encomenda não encontrada'];
        }

        // Verifica se a transição de estado é válida
        if (!isset($validTransitions[$status]) || !in_array($review, $validTransitions[$status])) {
            return ['status' => 'error', 'message' => 'Invalid transition'];
        }

        // Se o novo estado for 'dispatched', processa envio (retira stock e atualiza status)
        if ($review === 'dispatched'){
            if(!$this->dispatchOrder($orderId)){
                return ['status' => 'error', 'message' => 'Falha ao processar envio'];
            }
        } else {    // Para os outros estados, apenas atualiza o status da encomenda
            if(!$this->updateOrderStatus($orderId, $review)){
                return ['status' => 'error', 'message' => 'Falha ao alterar estado'];
            }
        }
       
        return ['status' => 'success'];
    }
}
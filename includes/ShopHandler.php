<?php
require_once 'Dbh.php';

class Shop{
    private $uploadDir = "../imgs/products/";
    private $uploadedImg = null;
    private $backupImg = null;

    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }
    // Carrega dados da base de dados
    private function getImgSrc($productId){
        $query="SELECT productImgSrc FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['productImgSrc'] : false;
    }

    private function getStock($productId){
        $query="SELECT productStock FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['productStock'] : false;
    }

    private function getProductInCart($productId, $userId){
        $query="SELECT * FROM shoppingcart WHERE productId = :productId AND userId = :userId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : false;
    }

    public function loadAdmProducts(){
        $query = "SELECT * FROM products ORDER BY id DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt -> execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function loadProducts(){
        $query = 'SELECT products.*, CASE WHEN productStock = 0 THEN "unavailable" WHEN productStock < 10 THEN "limited" ELSE "available" END AS productStock FROM products WHERE isActive = TRUE ORDER BY id DESC;';
        $stmt = $this->conn->prepare($query);
        $stmt -> execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function loadProductbyId($productId){
        $query="SELECT * FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function loadShoppingCart($userId){
        $query = 'SELECT p.productImgSrc, p.productName, p.productPrice, p.isActive, p.id AS productId, sc.productQuantity FROM shoppingcart sc JOIN products p ON sc.productId = p.id WHERE sc.userId = :userId;';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt -> execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // Verifica se exite na base de dados
    private function productExists($productId){
        $query = 'SELECT EXISTS(SELECT 1 FROM products WHERE id = :productId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    private function isProductReferenced($productId) {
        $query = 'SELECT EXISTS(SELECT 1 FROM shoppingcart WHERE productId = :productId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $inCart = $stmt->fetchColumn() > 0;

        $query = 'SELECT EXISTS(SELECT 1 FROM orders WHERE productId = :productId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
        $inOrder = $stmt->fetchColumn() > 0;

        return $inCart || $inOrder;
    }

    // Insere dados na base de dados
    private function createNewProduct($productImgSrc, $productName, $productPrice, $productStock){
        $query = 'INSERT INTO products (productImgSrc, productName, productPrice, productStock) VALUES (:productImgSrc, :productName, :productPrice, :productStock)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productImgSrc', $productImgSrc);
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productPrice', $productPrice);
        $stmt->bindParam(':productStock', $productStock);

        return $stmt->execute();
    }
    
    private function updatedProductData($productId, $productName, $productPrice, $productStock, $productImgSrc = null){
        $query = 'UPDATE products SET ';
        if($productImgSrc !== null) {
            $query .= 'productImgSrc = :productImgSrc, ';
        }
        $query .= 'productName = :productName, productPrice = :productPrice, productStock = :productStock  WHERE id = :productId';

        $stmt = $this->conn->prepare($query);
        if ($productImgSrc !== null) {
            $stmt->bindParam(':productImgSrc', $productImgSrc);
        }
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productPrice', $productPrice);
        $stmt->bindParam(':productStock', $productStock);
        $stmt->bindParam(':productId', $productId);

        return $stmt->execute();
    }

    private function updateCartProductQty($cartProductId, $cartProductQty){
        $query = 'UPDATE shoppingcart SET productQuantity = :productQuantity WHERE id = :cartProductId;';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productQuantity', $cartProductQty);
        $stmt->bindParam(':cartProductId', $cartProductId);

        return $stmt->execute();
    }

    private function setProductStatus($ProductId, $productStatus = 0){
        $query = 'UPDATE products  SET isActive = :productStatus WHERE id = :ProductId;';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ProductId', $ProductId);
        $stmt->bindParam(':productStatus', $productStatus);

        return $stmt->execute();
    }

    private function addNewProductToCart($productId, $userId, $productQty = 1){
        $query = 'INSERT INTO shoppingcart (userId, productId, productQuantity) VALUES (:userId, :productId, :productQuantity)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':productId', $productId);
        $stmt->bindParam(':productQuantity', $productQty);

        return $stmt->execute();
    }
    
    // Apaga dados na base de dados
    private function deleteProductData($productId){
        $query = "DELETE FROM products WHERE id = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);

        return $stmt->execute();
    }
    
    private function deleteProductFromCart($cartProductId){
        $query = "DELETE FROM shoppingcart WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $cartProductId);

        return $stmt->execute();
    }
    
    private function removeProductFromCarts($productId){
        $query = "DELETE FROM shoppingcart WHERE productId = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);

        return $stmt->execute();
    }


    // Funçoes de execução
    private function uploadImg(){
        // Verifica se a pasta existe e cria se necessário
        if (!file_exists($this->uploadDir)){
            if(!mkdir($this->uploadDir, 0777, true)){
                return ['status' => 'processError', 'error' => 'Não foi possível criar o diretório.', 'message' => 'Ocorreu um erro. Não foi possível guardar a imagem.'];
            }
        }

        // Obtém a extensão do ficheiro
        $ext = pathinfo($this->uploadedImg['name'], PATHINFO_EXTENSION);
        $uniqueId = uniqid('', true);
        // Renomeia o ficheiro com um nome unico (#uniqueId.$ext)
        $this->uploadedImg['name'] = $uniqueId.'.'.$ext;

        $tmpDir = $this->uploadedImg['tmp_name'];
        $destDir = $this->uploadDir.$this->uploadedImg['name'];

        //move o ficheiro para o local correto
        if(!move_uploaded_file($tmpDir, $destDir)){
            return ['status' => 'processError', 'error' => 'Falha ao mover o ficheiro.', 'message' => 'Ocorreu um erro. Não foi possível guardar a imagem.'];
        }

        return ['status' => 'valid'];
    }

    private function deleteProductImg($productId){
        $imgSrc = $this->getImgSrc($productId);
        if (!$imgSrc){
            return ['status' => 'processError', 'error' => 'Não foi possível obter a imagem.', 'message' => 'Ocorreu um erro. Não foi possível apagar o produto.'];
        }
        
        $imgDir = $this->uploadDir.$imgSrc;
        if(file_exists($imgDir)){
            // $this->backupImg = file_get_contents($imgDir);    //salva uma copia da imagem antes de a apagar
            $backup = file_get_contents($imgDir);
            if ($backup === false){
                return ['status'=>'processError','error'=>'Não foi possível criar backup da imagem','message'=>'Ocorreu um erro. Não foi possível apagar o produto.'];
            }
            $this->backupImg = $backup;
            if(!unlink($imgDir)){
                return ['status' => 'processError', 'error' => 'Não foi possível apagar a imagem.', 'message' => 'Ocorreu um erro. Não foi possível apagar o produto.'];
            }
        }
        return ['status' => 'valid', 'dir' => $imgDir];
    }

    public function addNewProduct($productImg, $productName, $productPrice, $productStock){
        $this->errors = [];
        // Validação dos dados
        require_once 'validations.inc.php';

        if(!$productImg){
            $this->errors['productImg'] = 'A imagem do produto é obrigatória.';
        } elseif ($productImg['error'] !== 0){
            $this->errors['productImg'] = 'Não foi possível carregar a imagem.';
        } else if (isSizeInvalid($productImg['size'])){
            $this->errors['productImg'] = 'A imagem excede o tamanho permitido.';
        } elseif (isTypeInvalid($productImg['type'])){
            $this->errors['productImg'] = 'A imagem não tem um formato válido (png, jpg, gif).';
        }

        if(isInputRequired('productName') && isInputEmpty($productName)){
            $this->errors['productName'] = 'O nome do produto é obrigatório.';
        } elseif (isProductNameInvalid($productName)){
            $this->errors['productName'] = 'O nome contém caracteres inválidos.';
        } elseif (isLengthInvalid($productName)){
            $this->errors['productName'] = 'O nome excede o limite de caracteres.';
        }

        if (isInputRequired('productPrice') && isInputEmpty($productPrice)) {
            $this->errors['productPrice'] = 'O preço é obrigatório.';
        } elseif (isPriceInvalid($productPrice)) {
            $this->errors['productPrice'] = 'O preço deve ser um número válido maior que zero.';
        }

        if (isInputRequired('productStock') && isInputEmpty($productStock)) {
            $this->errors['productStock'] = 'A quantidade de stock é obrigatório.';
        } elseif (isStockInvalid($productStock)) {
            $this->errors['productStock'] = 'A quantidade de stock deve ser um número inteiro maior que zero.';
        }

        // Verificação da ligação à base de dados
        if (!$this->conn) {
            $this->errors['connection'] = 'failed';
        }

        if (!$this->errors){
            $this->uploadedImg = $productImg;
            $uploadRes = $this->uploadImg();
            if($uploadRes['status'] !== 'valid'){
                return $uploadRes;
            }

            $productImgSrc = $this->uploadedImg['name'];
            if(!$this->createNewProduct($productImgSrc, $productName, $productPrice, $productStock)){
                return ['status' => 'processError', 'error' => 'Falha ao criar o produto.', 'message' => 'Ocorreu um erro. Não foi possível criar o produto.'];
            }

            return ['status' => 'valid'];

        } else {
            return ['status' => 'invalid', 'message' => $this->errors];
        }
    }

    public function deleteProduct($productId){
        //verifica se o produto existe
        if(!$this->productExists($productId)){
            return ['status' => 'processError', 'error' => 'O produto não existe.', 'message' => 'Ocorreu um erro. Não foi possível apagar o produto.'];
        }
        try {
            $this->conn->beginTransaction();

            $isReferenced = $this->isProductReferenced($productId); // Verifica se o produto existe em algum carrinho ou encomenda

            if ($isReferenced) {
                if(!$this->setProductStatus($productId)){
                    throw new Exception('Falha ao desativar o produto.');
                }
                
                // Remove o produto de todos os carrinhos (Opcional)
                if (!$this->removeProductFromCarts($productId)) {
                    throw new Exception('Falha ao remover o produto dos carrinhos.');
                }

                $this->conn->commit();
                return ['status' => 'valid', 'message' => 'O produto foi desativado.'];
            }

            $delImgRes = $this->deleteProductImg($productId);
            if ($delImgRes['status'] !== 'valid') {
                $this->conn->rollBack();
                return $delImgRes;
            }

            if (!$this->deleteProductData($productId)) {
                $imgDir = $delImgRes['dir'];
                if ($this->backupImg !== null) {
                    if (!file_put_contents($imgDir, $this->backupImg)) {
                        throw new Exception('Falha ao repor o backup da imagem.');
                    }
                }
                throw new Exception('Não foi possivel apagar os dados do produto.');
            }

            $this->conn->commit();
            return ['status' => 'valid'];

        } catch (Exception $e) {
            $this->conn->rollBack();

            if (isset($delImgRes['dir']) && $this->backupImg !== null) {
                file_put_contents($delImgRes['dir'], $this->backupImg);
            }

            return ['status' => 'processError', 'error' => $e->getMessage(), 'message' => 'Ocorreu Um Erro, Não Foi Possivel Apagar o Produto!'];
        }
    }

    public function updateProduct($productId, $productImg, $productName, $productPrice, $productStock){
        $this->errors = [];
        //validaçao dos dados
        require_once 'validations.inc.php';

        if(!$this->productExists($productId)){
            $this->errors['productId'] = 'O produto não existe.';
        }

        if($productImg){
            if ($productImg['error'] !== 0){
                $this->errors['productImg'] = 'Não foi possível carregar a imagem.';
            } else if (isSizeInvalid($productImg['size'])){
                $this->errors['productImg'] = 'A imagem excede o tamanho permitido.';
            } elseif (isTypeInvalid($productImg['type'])){
                $this->errors['productImg'] = 'A imagem não tem um formato válido (png, jpg, gif).';
            }
        }
        
        if(isInputRequired('productName') && isInputEmpty($productName)){
            $this->errors['productName'] = 'O nome do produto é obrigatório.';
        } elseif (isProductNameInvalid($productName)){
            $this->errors['productName'] = 'O nome contém caracteres inválidos.';
        } elseif (isLengthInvalid($productName)){
            $this->errors['productName'] = 'O nome excede o limite de caracteres.';
        }
        
        if (isInputRequired('productPrice') && isInputEmpty($productPrice)) {
            $this->errors['productPrice'] = 'O preço é obrigatório.';
        } elseif (isPriceInvalid($productPrice)) {
            $this->errors['productPrice'] = 'O preço deve ser um número válido maior ou igual a zero.';
        }

        if (isInputRequired('productStock') && isInputEmpty($productStock)) {
            $this->errors['productStock'] = 'A quantidade de stock é obrigatório.';
        } elseif (isStockInvalid($productStock)) {
            $this->errors['productStock'] = 'A quantidade de stock deve ser um número inteiro maior ou igual a zero.';
        }

        // Verificação da ligação à base de dados
        if (!$this->conn) {
            $this->errors['connection'] = 'failed';
        }
        
        if ($this->errors){
            return ['status' => 'invalid', 'message' => $this->errors];
        }

        try {
            $this->conn->beginTransaction();
            $productImgSrc = null;
            $delImgRes = null;

            if ($productImg) {
                // Carrega a imagem
                $this->uploadedImg = $productImg;
                $uploadRes = $this->uploadImg();

                if ($uploadRes['status'] !== 'valid') {
                    $this->conn->rollBack();
                    return $uploadRes;
                }

                $productImgSrc = $this->uploadedImg['name'];
                
                // Faz uma cópia e apaga a imagem antiga
                $delImgRes = $this->deleteProductImg($productId);
                if($delImgRes['status'] !== 'valid'){
                    $this->conn->rollBack();
                    return $delImgRes;
                }
            }

            if (!$this->updatedProductData($productId, $productName, $productPrice, $productStock, $productImgSrc)) {
                throw new Exception('Falha ao atualizar os dados do produto.');
            }

            $this->conn->commit();

            return ['status' => 'valid'];
        } catch (Exception $e) {
            $this->conn->rollBack();    // Reverte as alterações
            
            // Tenta restaurar a imagem antiga
            if ($productImg && isset($delImgRes['dir']) && $this->backupImg !== null) {
                file_put_contents($delImgRes['dir'], $this->backupImg);
            }

            // Apaga a imagem carregada
            if ($productImg && isset($this->uploadedImg['name'])) {
                $newImgDir = $this->uploadDir . $this->uploadedImg['name'];
                if (file_exists($newImgDir)) {
                    unlink($newImgDir);
                }
            }

            return ['status' => 'processError', 'error' => $e->getMessage(), 'message' => 'Ocorreu um erro. Não foi possível guardar o produto.'];
        }
    }

    public function activateProduct($productId){
        if(!$this->productExists($productId)){
            return ['status' => 'processError', 'error' => 'O produto não existe.', 'message' => 'Ocorreu um erro. Não foi possível ativar o produto.'];
        }

        if(!$this->setProductStatus($productId, 1)){
            return ['status' => 'processError', 'error' => 'Falha ao ativar o produto.', 'message' => 'Ocorreu um erro. Não foi possível ativar o produto.'];
        }

        return ['status' => 'valid'];
    }

    //Shop
    public function addProductToCart($productId, $userId){
        $productStock = $this->getStock($productId);

        if($productStock === false){
            return ['status' => 'processError', 'error' => 'O produto não existe.', 'message' => 'Ocorreu um erro. Não foi possível adicionar o produto ao carrinho.'];
        }
        
        $cartProduct = $this->getProductInCart($productId, $userId);
        if($cartProduct){   // caso ja exista o produto no carrinho add +1
            $cartProductId = $cartProduct['id'];
            $cartProductQty= $cartProduct['productQuantity'];

            $cartProductQty++;  

            if($cartProductQty > $productStock){
                return ['status' => 'processError', 'error' => 'Não existe stock suficiente do produto.', 'message' => 'Não existe stock suficiente do produto.'];
            }

            if(!$this->updateCartProductQty($cartProductId, $cartProductQty)){
                return ['status' => 'processError', 'error' => 'Não foi possivel adicionar ao produto no carrinho', 'message' => 'Ocorreu Um Erro, Não Foi Possivel Adicionar o Produto.'];
            }
        } else {    //adiciona o produto ao carrinho
            if($productStock < 1){
                return ['status' => 'processError', 'error' => 'Não existe stock do produto.', 'message' => 'Não existe stock suficiente do produto.'];
            }

            if(!$this->addNewProductToCart($productId, $userId)){
                return ['status' => 'processError', 'error' => 'Não foi possivel adicionar o produto no carrinho', 'message' => 'Ocorreu Um Erro, Não Foi Possivel Adicionar o Produto.'];
            }
        }

        return ['status' => 'valid'];
    }
    
    public function removeProductFromCart($productId, $userId){
        $cartProduct = $this->getProductInCart($productId, $userId);

        if(!$cartProduct){    //retorna um erro caso o id seja invalido
            return ['status' => 'processError', 'error' => 'O produto não existe no carrinho.', 'message' => 'Ocorreu um erro. Não foi possível remover o produto do carrinho.'];
        }   
        
        $cartProductId = $cartProduct['id'];
        $cartProductQty= $cartProduct['productQuantity'];

        $cartProductQty--;  

        if($cartProductQty < 1){
            if(!$this->deleteProductFromCart($cartProductId)){
                return ['status' => 'processError', 'error' => 'Não foi possível apagar o produto do carrinho', 'message' => 'Ocorreu um erro. Não foi possível apagar o produto do carrinho.'];
            }
        } else {    //apaga o produto ao carrinho
            if(!$this->updateCartProductQty($cartProductId, $cartProductQty)){
                return ['status' => 'processError', 'error' => 'Não foi possível atualizar a quantidade do produto', 'message' => 'Ocorreu um erro. Não foi possível remover o produto do carrinho.'];
            }
        }

        return ['status' => 'valid'];
    }

    public function deleteProductInCart($productId, $userId){
        $cartProduct = $this->getProductInCart($productId, $userId);

        if(!$cartProduct){    //retorna um erro caso o id seja invalido
            return ['status' => 'processError', 'error' => 'O produto não existe no carrinho.', 'message' => 'Ocorreu um erro. Não foi possível apagar o produto do carrinho.'];
        }   
        
        $cartProductId = $cartProduct['id'];

        if(!$this->deleteProductFromCart($cartProductId)){
            return ['status' => 'processError', 'error' => 'Não foi possível apagar o produto do carrinho', 'message' => 'Ocorreu um erro. Não foi possível apagar o produto do carrinho.'];
        }

        return ['status' => 'valid'];
    }
}
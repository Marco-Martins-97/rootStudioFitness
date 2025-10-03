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
    // PRIVATE QUERY
    private function createNewProduct($productImgSrc, $productName, $productPrice, $productStock){
        $query = 'INSERT INTO products (productImgSrc, productName, productPrice, productStock) VALUES (:productImgSrc, :productName, :productPrice, :productStock)';
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':productImgSrc', $productImgSrc);
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productPrice', $productPrice);
        $stmt->bindParam(':productStock', $productStock);

        return $stmt->execute();
    }
    
    private function saveUpdatedProduct($productId, $productImgSrc = null, $productName, $productPrice, $productStock){
        $query = 'UPDATE products SET ';
        $productImgSrc !== null && $query .= 'productImgSrc = :productImgSrc, ';
        $query .= 'productName = :productName, productPrice = :productPrice, productStock = :productStock  WHERE id = :productId';

        $stmt = $this->conn->prepare($query);

        $productImgSrc !== null && $stmt->bindParam(':productImgSrc', $productImgSrc);
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productPrice', $productPrice);
        $stmt->bindParam(':productStock', $productStock);
        $stmt->bindParam(':productId', $productId);

        return $stmt->execute();
    }

    public function loadproducts(){
        $query = "SELECT * FROM products ORDER BY id DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt -> execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    private function productExists($productId){
        $query = 'SELECT EXISTS(SELECT 1 FROM products WHERE id = :productId)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();
    
        return (bool) $stmt->fetchColumn();
    }

    private function getImgSrc($productId){
        $query="SELECT productImgSrc FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['productImgSrc'] ?? false;
    }

    private function deleteProductData($productId){
        $query = "DELETE FROM products WHERE id = :productId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);

        return $stmt->execute();
    }
    // PUBLIC QUERY
    public function loadProductbyId($productId){
        $query="SELECT * FROM products WHERE id = :productId;";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':productId', $productId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    // PRIVATE FUNCTIONS
    private function uploadImg(){
        // verifica se o ficheiro nao existe e cria um
        if (!file_exists($this->uploadDir)){
            if(!mkdir($this->uploadDir, 0777, true)){
                return ['status' => 'processError', 'error' => 'Não foi possível criar o diretório.', 'message' => 'Ocorreu um erro, Não foi possivel salvar a imagem.'];
            }
        }

        //pega a extensao do ficheiro
        $ext = pathinfo($this->uploadedImg['name'], PATHINFO_EXTENSION);
        //cria um id unico
        $uniqueId = uniqid('', true);
        // Renomeia o ficheiro com um nome unico (#uniqueId.$ext)
        $this->uploadedImg['name'] = $uniqueId.'.'.$ext;

        $tmpDir = $this->uploadedImg['tmp_name'];
        $destDir = $this->uploadDir.$this->uploadedImg['name'];

        //move o ficheiro para o local correto
        if(!move_uploaded_file($tmpDir, $destDir)){
            return ['status' => 'processError', 'error' => 'Falla ao realocar o ficheiro.', 'message' => 'Ocorreu um erro, Não foi possivel salvar a imagem.'];
        }

        return ['status' => 'valid'];
    }

    private function deleteProductImg($productId){
        $imgSrc = $this->getImgSrc($productId);
        if (!$imgSrc){
            return ['status' => 'processError', 'error' => 'Não foi possivel obter a Src da imagem.', 'message' => 'Ocorreu Um Erro, Não Foi Possivel Apagar o Produto!'];
        }
        
        $imgDir = $this->uploadDir.$imgSrc;
        if(file_exists($imgDir)){
            $this->backupImg = file_get_contents($imgDir);    //salva uma copia da imagem antes de a apagar
            if(!unlink($imgDir)){
                return ['status' => 'processError', 'error' => 'Não foi possivel apagar a imagem.', 'message' => 'Ocorreu Um Erro, Não Foi Possivel Apagar o Produto!'];
            }
        }
        return ['status' => 'valid', 'dir' => $imgDir];
    }

    // PUBLIC FUNCTIONS
    public function addNewProduct($productImg, $productName, $productPrice, $productStock){
        //validaçao dos dados
        require_once 'validations.inc.php';

        if(!$productImg){
            $this->errors['productImg'] = 'A imagem do produto é obrigatória.';
        } elseif ($productImg['error'] !== 0){
            $this->errors['productImg'] = 'Não foi carregar a imagem.';
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
            $errors['productStock'] = 'A quantidade de stock é obrigatório.';
        } elseif (isStockInvalid($productStock)) {
            $errors['productStock'] = 'A quantidade de stock deve ser um número inteiro maior que zero.';
        }

        // Conecção
        if (!$this->conn) {
            $this->errors['connection'] = 'connection failed';
        }

        if (!$this->errors){
            $this->uploadedImg = $productImg;   //salva o ficheiro na variavel
            $uploadRes = $this->uploadImg();
            if($uploadRes['status'] !== 'valid'){
                return $uploadRes;
            }

            $productImgSrc = $this->uploadedImg['name'];
            if(!$this->createNewProduct($productImgSrc, $productName, $productPrice, $productStock)){
                return ['status' => 'processError', 'error' => 'Falla ao criar o produto.', 'message' => 'Ocorreu um erro, Não foi possivel criar o produto.'];
            }

            return ['status' => 'valid'];

        } else {
            return ['status' => 'invalid', 'message' => $this->errors];
        }
    }

    public function deleteProduct($productId){
        //verifica se o produto existe
        if(!$this->productExists($productId)){
            return ['status' => 'processError', 'error' => 'O produto não existe.', 'message' => 'Ocorreu Um Erro, Não Foi Possivel Apagar o Produto!'];
        }
  
        $delImgRes = $this->deleteProductImg($productId);
        if($delImgRes['status'] !== 'valid'){
            return $delImgRes;
        }
        
        if (!$this->deleteProductData($productId)){
            $imgDir = $delImgRes['dir'];
            if ($this->backupImg !== null){
                if (!file_put_contents($imgDir, $this->backupImg)) {
                    return ['status' => 'processError', 'error' => 'Falha ao repor o backup da imagem.', 'message' => 'Ocorreu Um Erro, Não Foi Possivel Apagar o Produto!'];
                }
            }
            return ['status' => 'processError', 'error' => 'Não foi possivel apagar os dados do produto.', 'message' => 'Ocorreu Um Erro, Não Foi Possivel Apagar o Produto!'];
        }
        return ['status' => 'valid'];
    }

    public function updateProduct($productId, $productImg, $productName, $productPrice, $productStock){
        //validaçao dos dados
        require_once 'validations.inc.php';

        if(!$this->productExists($productId)){
            $this->errors['productId'] = 'O produto não existe.';
        }

        if($productImg){
            if ($productImg['error'] !== 0){
                $this->errors['productImg'] = 'Não foi carregar a imagem.';
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
            $errors['productStock'] = 'A quantidade de stock é obrigatório.';
        } elseif (isStockInvalid($productStock)) {
            $errors['productStock'] = 'A quantidade de stock deve ser um número inteiro maior ou igual a zero.';
        }

        // Conecção
        if (!$this->conn) {
            $this->errors['connection'] = 'connection failed';
        }
        
        
        if (!$this->errors){
            $productImgSrc = null;
            if($productImg){    // salva a imagem se existir
                $this->uploadedImg = $productImg;
                $uploadRes = $this->uploadImg();
                if($uploadRes['status'] !== 'valid'){
                    return $uploadRes;
                }
                $productImgSrc = $this->uploadedImg['name'];
            }

            //apaga img antiga
            // $delImgRes = $this->deleteProductImg($productId);
            // if($delImgRes['status'] !== 'valid'){
            //     return $delImgRes;
            // }


            if(!$this->saveUpdatedProduct($productId, $productImgSrc, $productName, $productPrice, $productStock)){
                // apaga a imagem carregada
                /* $imgDir = $this->uploadDir.$this->uploadedImg['name'];
                if(file_exists($imgDir)){
                    if(!unlink($imgDir)){
                        return ['status' => 'processError', 'error' => 'Não foi possivel apagar a imagem.', 'message' => 'Ocorreu Um Erro, Não Foi Salvar o Produto!'];
                    }
                } */
                return ['status' => 'processError', 'error' => 'Falla ao salvar o produto.', 'message' => 'Ocorreu Um Erro, Não Foi Salvar o Produto.'];
            }

            return ['status' => 'valid'];
        } else {
            return ['status' => 'invalid', 'message' => $this->errors];
        }
    }

    
}


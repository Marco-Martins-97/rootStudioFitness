<?php
require_once 'Dbh.php';

class Shop{
    private $uploadDir = "../imgs/products/";
    private $uploadedImg;

    private $conn;
    private $errors = [];

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    private function createNewProduct($productImgSrc, $productName, $productPrice, $productStock){
        $query = 'INSERT INTO products (productImgSrc, productName, productPrice, productStock) VALUES (:productImgSrc, :productName, :productPrice, :productStock)';
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':productImgSrc', $productImgSrc);
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productPrice', $productPrice);
        $stmt->bindParam(':productStock', $productStock);

        return $stmt->execute();
    }

    public function loadproducts(){
        $query = "SELECT * FROM products ORDER BY id DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt -> execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

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
}
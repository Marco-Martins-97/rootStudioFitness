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

    public function addNewProduct($productImg, $productName, $productPrice, $productStock){
       /*  if ($productImg) {
            // File info
            $tmpName = $productImg['tmp_name'];  // Temporary uploaded file
            $fileName = $productImg['name'];     // Original file name
            $fileSize = $productImg['size'];     // File size in bytes
            $fileType = $productImg['type'];     // MIME type (from browser)
            $error    = $productImg['error'];    // Upload error code
        } */
    }
}
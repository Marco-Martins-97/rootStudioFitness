<?php
/* require_once 'Dbh.php';

class Validator{
    private $requiredFields = ['firstName','lastName','email','pwd','confPwd'];
    private $pwdLength = 8;
    private $conn;

    public function __construct(){
        $dbh = new Dbh();
        $this->conn = $dbh->connect();
    }

    public function isInputRequired($input){
        return in_array($input, $this->requiredFields);
    }

    public function isInputEmpty($value){
        return empty($value);
    }
    public function isNameInvalid($value){
        return !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $value);
    }
} */
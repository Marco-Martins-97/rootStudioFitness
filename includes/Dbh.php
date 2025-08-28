<?php

class Dbh{
    private $host = 'localhost';
    private $dbname = 'root_studio_fitness';

    public function connect(){
        require_once 'configSession.inc.php'; 
        $userRole = $_SESSION['userRole'] ?? 'guest';

        switch($userRole){
            case 'admin':
                $dbusername = 'root_admin';
                $dbpassword = 'admin';
                break;
            case 'client':
                $dbusername = 'root_user';
                $dbpassword = 'user';
                break;
            case 'user':
                $dbusername = 'root_user';
                $dbpassword = 'user';
                break;
            default:
                $dbusername = 'root_guest';
                $dbpassword = 'guest';
                break;
        }

        try{
            $pdo = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname, $dbusername, $dbpassword);
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e){
            die ('Conecção Falhou: '.$e->getMessage());
        }
    }
}
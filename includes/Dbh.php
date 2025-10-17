<?php
require_once 'configSession.inc.php'; 

class Dbh{
    private $host = 'localhost';
    private $dbname = 'root_studio_fitness';
    private $pdo;

    public function connect(){
        $userRole = $_SESSION['userRole'] ?? 'guest';

        switch($userRole){  // Escolher credenciais de acordo com o papel do utilizador
            case 'admin':
                $dbusername = 'root_admin';
                $dbpassword = 'admin';
                break;
            case 'user':
            case 'client':
                $dbusername = 'root_user';
                $dbpassword = 'user';
                break;
            default:
                $dbusername = 'root_guest';
                $dbpassword = 'guest';
                break;
        }

        try{    // Criar a ligação PDO se ainda não existir
            if(!$this->pdo) {
                $this->pdo = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname, $dbusername, $dbpassword);
                $this->pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            return $this->pdo;
        } catch (PDOException $e){
            error_log('Erro na ligação à base de dados: ' . $e->getMessage());
            die('Falha na ligação à base de dados.');
        }
    }
}
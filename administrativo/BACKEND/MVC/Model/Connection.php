<?php
class Connection{
    private static $instance = null;

    public static function getInstance(){
    if(!self::$instance){
        try{
            $host = 'localhost';
            $dbname = 'tech_fit';
            $user = 'root';
            $senha = 'senaisp';

            self::$instance = new PDO( 
                "mysql:host=$host;charset=utf8",
                $user,
                $senha
            );
            self::$instance->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            self::$instance->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            self::$instance->exec("USE $dbname");
        }catch (PDOException $e){
            die("Erro ao conectar ao banco de dados: ". $e->getMessage());
        }
    }
    return self::$instance;

    }
}
?>
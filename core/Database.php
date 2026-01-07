<?php

class Database {
    private static ?pdo $connexion = null;

    public static function getconnection(): PDO {
       if (self::$connexion === null) {
            $config = require __DIR__ . '/../config/database.php';
            try {
                self::$connexion = new PDO ("mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset=utf8", $config ['user'], $config ['password']);
                self::$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) {
                die("database connection failed try again!". $e->getMessage());
            }
       }
        return self::$connexion;
    }
}



?>
 
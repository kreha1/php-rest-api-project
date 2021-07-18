<?php
namespace Src\System;

class Database {

    private $dbConnection = NULL;

    public function __construct() {
        $host = getenv('DB_HOST');
        $db   = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');
        

        try {
            $this->dbConnection = new \PDO(
                "mysql:host=$host;dbname=$db",
                $user,
                $pass
            );
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection() {
        return $this->dbConnection;
    }
}
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// connection.php

require_once 'constant.php';

class DatabaseConnection {
    private $connection;

    public function __construct() {
        $this->connection = new mysqli(HOST_NAME, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME); // Object-oriented style

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
?>

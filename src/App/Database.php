<?php

declare(strict_types=1);

namespace App;

use PDO;

class Database
{

    public function __construct(private string $host, private string $port, private string $database,
                                private string $username, private string $password)
    {
    }

    public function getConnection(): PDO
    {
        $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->database;";

        $pdo = new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        return $pdo;
    }
}
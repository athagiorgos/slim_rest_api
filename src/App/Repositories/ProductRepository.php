<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class ProductRepository
{
    public function __construct(private Database $database)
    {
    }

    public function getAll() : array
    {
        $pdo = $this->database->getConnection();

        $stmt = $pdo->query("SELECT * FROM products");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|bool
    {
        $sql = "SELECT * FROM products where id = :id";

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string
    {
        $sql = 'INSERT INTO products (name, description, created) values (:name, :description, :created)';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $data['name']);

        $stmt->bindValue(':description', $data['description']);

        $stmt->bindValue(':created', date("Y-m-d"));

        $stmt->execute();

        return $pdo->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $sql = 'UPDATE products SET name = :name, description = :description WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $data['name']);

        $stmt->bindValue(':description', $data['description']);

        $stmt->bindValue(':id', $id);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = 'DELETE FROM products WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}
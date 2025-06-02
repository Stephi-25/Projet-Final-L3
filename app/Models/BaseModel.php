<?php

namespace App\Database;

use PDO;


/**
 * Class BaseModel
 *
 * An abstract base model class providing basic CRUD operations.
 *
 * @package App\Database
 */
abstract class BaseModel
{
    /**
     * @var PDO The PDO database connection object.
     */
    protected PDO $pdo;

    /**
     * @var string The database table name. This should be set in child classes.
     */
    protected string $table;

    /**
     * BaseModel constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Creates a new record in the database.
     *
     * @param array $data An associative array of column => value pairs.
     * @return string|false The ID of the newly inserted row, or false on failure.
     */
    public function create(array $data): string|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->pdo->lastInsertId();
    }

    /**
     * Retrieves a record by its ID.
     *
     * @param string $id The ID of the record to retrieve.
     * @return mixed The record object or false if not found.
     */
    public function find(string $id): mixed
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates a record by its ID.
     *
     * @param string $id The ID of the record to update.
     * @param array $data An associative array of column => value pairs to update.
     * @return bool True on success, false on failure.
     */
    public function update(string $id, array $data): bool
    {
        $setClauses = [];
        foreach (array_keys($data) as $key) {
            $setClauses[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClauses);
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    /**
     * Deletes a record by its ID.
     *
     * @param string $id The ID of the record to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Retrieves all records from the table.
     *
     * @return array An array of all records.
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

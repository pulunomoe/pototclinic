<?php

namespace App\Model\Common;

use PDO;
use Ulid\Ulid;

abstract class CrudModel extends Model
{
    protected string $table;

    public function __construct(
        PDO $pdo,
        string $table,
    ) {
        parent::__construct($pdo);
        $this->table = $table;
    }

    public function readAll(): array
    {
        return $this->fetchAll('SELECT * FROM ' . $this->table);
    }

    public function readOne(string $id): ?array
    {
        return $this->fetch('SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 1', [
            new Parameter('id', $id),
        ]);
    }

    public function delete(string $id): void
    {
        $this->execute('DELETE FROM ' . $this->table . ' WHERE id = :id', [
            new Parameter('id', $id),
        ]);
    }

    protected function doCreate(array $data): string
    {
        $id = Ulid::generate();

        $data = array_merge(['id' => $id], $data);

        $sql = 'INSERT INTO ' . $this->table . ' ( ';
        $sql .= implode(', ', array_keys($data));
        $sql .= ' ) VALUES ( ';
        $sql .= implode(', ', array_map(fn($column) => ':' . $column, array_keys($data)));
        $sql .= ' )';

        $this->execute(
            $sql,
            array_map(fn($key, $value) => new Parameter($key, $value), array_keys($data), $data),
        );

        return $id;
    }

    protected function doUpdate(
        string $id,
        array $data,
    ): void {
        $sql = 'UPDATE ' . $this->table . ' SET ';
        $sql .= implode(', ', array_map(fn($column) => $column . ' = :' . $column, array_keys($data)));
        $sql .= ' WHERE id = :id';

        $data = array_merge(['id' => $id], $data);

        $this->execute(
            $sql,
            array_map(fn($key, $value) => new Parameter($key, $value), array_keys($data), $data),
        );
    }
}

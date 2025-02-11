<?php

namespace App\Model\Common;

use PDO;
use PDOStatement;

abstract class Model
{
    public function __construct(
        protected PDO $pdo,
    ) {}

    /**
     * @param Parameter[] $params
     * @return array<string, string>[]
     * @return array
     */
    protected function fetchAll(
        string $sql,
        array $params = [],
    ): array {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * @param Parameter[] $params
     * @return array<string, string>|false
     */
    protected function fetch(
        string $sql,
        array $params = [],
    ): array|false {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }

    /**
     * @param Parameter[] $params
     */
    protected function execute(
        string $sql,
        array $params = [],
    ): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $param) {
            $stmt->bindValue(
                $param->getName(),
                $param->getValue(),
                $param->getType(),
            );
        }
        $stmt->execute();

        return $stmt;
    }
}

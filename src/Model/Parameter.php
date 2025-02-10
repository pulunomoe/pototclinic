<?php

namespace App\Model;

use PDO;

readonly class Parameter
{
    public function __construct(
        private string $name,
        private mixed $value,
        private int $type = PDO::PARAM_STR,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getType(): int
    {
        return $this->type;
    }
}

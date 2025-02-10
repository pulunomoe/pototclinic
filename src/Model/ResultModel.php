<?php

namespace App\Model;

use PDO;

readonly class ResultModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'results');
    }

    public function create(
        string $testId,
        string $value,
        string $color,
        string $description,
    ): string {
        return $this->doCreate([
            'test_id' => $testId,
            'value' => $value,
            'color' => $color,
            'description' => $description,
        ]);
    }

    public function update(
        string $id,
        string $value,
        string $color,
        string $description,
    ): void {
        $this->doUpdate($id, [
            'value' => $value,
            'color' => $color,
            'description' => $description,
        ]);
    }
}

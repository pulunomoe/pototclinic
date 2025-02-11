<?php

namespace App\Model;

use App\Model\Common\CrudModel;
use App\Model\Common\Parameter;
use PDO;

class ResultModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'results');
    }

    public function readAllByTest(string $testId): array
    {
        return $this->fetchAll('SELECT * FROM results WHERE test_id = :testId', [
            new Parameter(':testId', $testId),
        ]);
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

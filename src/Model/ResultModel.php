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
        return $this->fetchAll('SELECT * FROM results WHERE test_id = :test_id', [
            new Parameter(':test_id', $testId),
        ]);
    }

    public function readAllForSelectByTest(string $testId): array
    {
        $results = $this->fetchAll('SELECT id, value, color FROM results WHERE test_id = :test_id', [
            new Parameter(':test_id', $testId),
        ]);

        $options = [];
        foreach ($results as $result) {
            $options[$result['id']] = [
                'value' => $result['value'],
                'color' => $result['color'],
            ];
        }
        return $options;
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

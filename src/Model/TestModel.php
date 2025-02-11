<?php

namespace App\Model;

use App\Model\Common\CrudModel;
use PDO;

readonly class TestModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'tests');
    }

    public function create(
        string $name,
        string $description,
    ): string {
        return $this->doCreate([
            'name' => $name,
            'description' => $description,
        ]);
    }

    public function update(
        string $id,
        string $name,
        string $description,
    ): void {
        $this->doUpdate($id, [
            'name' => $name,
            'description' => $description,
        ]);
    }
}

<?php

namespace App\Model;

use PDO;

readonly class NurseModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'nurses');
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

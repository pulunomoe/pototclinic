<?php

namespace App\Model;

use PDO;

readonly class PatientModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'patients');
    }

    public function create(
        string $name,
        string $race,
        string $description,
    ): string {
        return $this->doCreate([
            'name' => $name,
            'race' => $race,
            'description' => $description,
        ]);
    }

    public function update(
        string $id,
        string $name,
        string $race,
        string $description,
    ): void {
        $this->doUpdate($id, [
            'name' => $name,
            'race' => $race,
            'description' => $description,
        ]);
    }
}

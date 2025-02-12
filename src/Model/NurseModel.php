<?php

namespace App\Model;

use App\Model\Common\CrudModel;
use PDO;

class NurseModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'nurses');
    }

    public function readAllForSelect(): array
    {
        $nurses = $this->fetchAll('SELECT id, name FROM nurses ORDER BY name');
        $options = [];
        foreach ($nurses as $nurse) {
            $options[$nurse['id']] = $nurse['name'];
        }
        return $options;
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

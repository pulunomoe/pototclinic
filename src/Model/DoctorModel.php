<?php

namespace App\Model;

use App\Model\Common\CrudModel;
use PDO;
use Slim\Psr7\UploadedFile;

readonly class DoctorModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'doctors');
    }

    public function readAllForSelect(): array
    {
        $doctors = $this->fetchAll('SELECT id, name FROM doctors ORDER BY name ASC');
        return array_column($doctors, 'name', 'id');
    }

    public function create(
        string $name,
        string $description,
        ?UploadedFile $signature,
    ): string {
        $id = $this->doCreate([
            'name' => $name,
            'description' => $description,
        ]);

        if (!empty($signature->getFilePath())) {
            $this->saveSignature($id, $signature);
        }

        return $id;
    }

    public function update(
        string $id,
        string $name,
        string $description,
        ?UploadedFile $signature,
    ): void {
        $this->doUpdate($id, [
            'name' => $name,
            'description' => $description,
        ]);

        if (!empty($signature->getFilePath())) {
            $this->saveSignature($id, $signature);
        }
    }

    public function deleteSignature(string $id): void
    {
        $signature = __DIR__ . '/../../public/var/signatures/' . $id . '.png';
        if (file_exists($signature)) {
            unlink($signature);
        }
    }

    private function saveSignature(string $id, UploadedFile $signature): void
    {
        $image = imagecreatefromstring($signature->getStream()->getContents());
        imagepng($image, __DIR__ . '/../../public/var/signatures/' . $id . '.png');
    }
}

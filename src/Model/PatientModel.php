<?php

namespace App\Model;

use App\Model\Common\CrudModel;
use PDO;
use Slim\Psr7\UploadedFile;

class PatientModel extends CrudModel
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'patients');
    }

    public function create(
        string $name,
        string $race,
        string $description,
        ?UploadedFile $photo,
    ): string {
        $id = $this->doCreate([
            'name' => $name,
            'race' => $race,
            'description' => $description,
        ]);

        if (!empty($photo->getFilePath())) {
            $this->savePhoto($id, $photo);
        }

        return $id;
    }

    public function update(
        string $id,
        string $name,
        string $race,
        string $description,
        ?UploadedFile $photo,
    ): void {
        $this->doUpdate($id, [
            'name' => $name,
            'race' => $race,
            'description' => $description,
        ]);

        if (!empty($photo->getFilePath())) {
            $this->savePhoto($id, $photo);
        }
    }

    public function deletePhoto(string $id): void
    {
        $photo = __DIR__ . '/../../public/var/patients/' . $id . '.jpg';
        if (file_exists($photo)) {
            unlink($photo);
        }
    }

    private function savePhoto(string $id, UploadedFile $signature): void
    {
        $image = imagecreatefromstring($signature->getStream()->getContents());
        imagejpeg($image, __DIR__ . '/../../public/var/patients/' . $id . '.jpg', 90);
    }
}

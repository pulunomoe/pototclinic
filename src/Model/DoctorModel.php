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

    private function saveSignature(string $id, UploadedFile $signature): void
    {
        $image = imagecreatefromstring($signature->getStream()->getContents());
        imagepng($image, __DIR__ . '/../../public/var/signatures/' . $id . '.png');
    }
}

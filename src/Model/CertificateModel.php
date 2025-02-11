<?php

namespace App\Model;

use App\Model\Common\Model;
use App\Model\Common\Parameter;
use GdImage;
use SimpleSoftwareIO\QrCode\Generator;
use Ulid\Ulid;

readonly class CertificateModel extends Model
{
    private const MOTTO = '"We believe in your health—and our profit margins!"';

    public function readAllByPatient(
        string $patientId,
    ): array {
        return $this->fetchAll('SELECT * FROM certificates_view WHERE patient_id = :patient_id', [
            new Parameter(':patient_id', $patientId),
        ]);
    }

    public function readOne(
        string $id,
    ): array {
        return $this->fetch('SELECT * FROM certificates_view WHERE id = :id LIMIT 1', [
            new Parameter(':id', $id),
        ]);
    }

    public function create(
        string $patientId,
        string $doctorId,
        string $description,
    ): string {
        $id = Ulid::generate()->__toString();

        $this->execute(
            'INSERT INTO certificates (id, patient_id, doctor_id, created, description) VALUES (:id, :patient_id, :doctor_id, :created, :description)',
            [
                new Parameter(':id', $id),
                new Parameter(':patient_id', $patientId),
                new Parameter(':doctor_id', $doctorId),
                new Parameter(':created', date('Y-m-d H:i:s')),
                new Parameter(':description', $description),
            ],
        );

        $patient = $this->fetch('SELECT name FROM patients WHERE id = :patient_id', [
            new Parameter(':patient_id', $patientId),
        ]);

        $doctor = $this->fetch('SELECT id, name FROM doctors WHERE id = :doctor_id', [
            new Parameter(':doctor_id', $doctorId),
        ]);

        $this->generate(
            $id,
            $description,
            $patient['name'],
            $doctor['id'],
            $doctor['name'],
        );

        return $id;
    }

    public function delete(
        string $id,
    ): void {
        $this->execute(
            'DELETE FROM certificates WHERE id = :id',
            [
                new Parameter(':id', $id),
            ],
        );
    }

    public function deleteFile(string $id): void
    {
        $certificates = __DIR__ . '/../../public/var/certificates/' . $id . '.png';
        if (file_exists($certificates)) {
            unlink($certificates);
        }
    }

    private function generate(
        string $id,
        string $description,
        string $patientName,
        string $doctorId,
        string $doctorName,
    ): void {
        $width = 1200;
        $height = 1600;
        $image = imagecreatetruecolor($width, $height);

        $background = imagecolorallocate($image, 255, 255, 255);
        $dark = imagecolorallocate($image, 64, 64, 64);
        $black = imagecolorallocate($image, 0, 0, 0);
        $pink = imagecolorallocate($image, 255, 226, 226);

        imagefill($image, 0, 0, $background);

        $font = __DIR__ . '/../../public/static/comic.ttf';
        $logo = __DIR__ . '/../../public/static/logo.png';
        $logoImage = imagecreatefrompng($logo);
        $signature = __DIR__ . '/../../public/var/signatures/' . $doctorId . '.png';
        $signatureImage = imagecreatefrompng($signature);

        $qr = __DIR__ . '/../../public/var/certificates/' . $id . '_qr.png';
        $generator = new Generator();
        $generator
            ->size(250)
            ->margin(2)
            ->format('png')
            ->generate($id, $qr);
        $qrImage = imagecreatefrompng($qr);

        // Header
        imagefilledrectangle($image, 0, 0, $width, 250, $dark);
        imagecopyresampled($image, $logoImage, 20, 10, 0, 0, 475, 230, imagesx($logoImage), imagesy($logoImage));
        imagettftext($image, 40, 0, 520, 140, $pink, $font, "MEDICAL CERTIFICATE");

        // Content
        $description = str_replace(['<br>', '<br/>', '<br />'], "\n", $description);
        $description = wordwrap($description, 70);
        $lines = explode("\n", $description);

        $y = 400;
        foreach ($lines as $line) {
            $this->writeTextCentered($image, $font, 24, $black, $line, $y);
            $y += 24 * 2;
        }

        $this->writeTextCentered($image, $font, 24, $black, 'Patient name', $y + 50);
        $this->writeTextCentered($image, $font, 48, $black, $patientName, $y + 120);

        $this->drawImageCentered($image, $signatureImage, 1100, 200);
        $this->writeTextCentered($image, $font, 24, $black, $doctorName, 1350);

        imagecopyresampled($image, $qrImage, 900, 1100, 0, 0, 250, 250, imagesx($qrImage), imagesy($qrImage));

        // Footer
        imagefilledrectangle($image, 0, 1450, $width, 1600, $dark);
        imagecopyresampled($image, $logoImage, 500, 1450, 0, 0, 300, 150, imagesx($logoImage), imagesy($logoImage));

        $this->writeTextCentered($image, $font, 16, $black, self::MOTTO, 1400);

        // Output
        header('Content-Type: image/png');
        imagepng($image, __DIR__ . '/../../public/var/certificates/' . $id . '.png');
        imagedestroy($image);
        unlink($qr);
    }

    private function writeTextCentered(
        GdImage $image,
        string $font,
        int $fontSize,
        int $color,
        string $text,
        int $y,
    ): void {
        $bbox = imagettfbbox($fontSize, 0, $font, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = (1200 - $textWidth) / 2;
        imagettftext($image, $fontSize, 0, $x, $y, $color, $font, $text);
    }

    private function drawImageCentered(
        GdImage $dest,
        GdImage $src,
        int $y,
        int $h,
    ): void {
        $srcW = imagesx($src);
        $srcH = imagesy($src);

        $scale = $h / $srcH;
        $w = $srcW * $scale;

        $destWidth = imagesx($dest);
        $x = ($destWidth - $w) / 2;

        imagecopyresampled($dest, $src, $x, $y, 0, 0, $w, $h, $srcW, $srcH);
    }
}

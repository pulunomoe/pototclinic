<?php

namespace App\Model;

use App\Model\Common\GenerateTrait;
use App\Model\Common\Model;
use App\Model\Common\Parameter;
use Ulid\Ulid;

class CertificateModel extends Model
{
    use GenerateTrait;

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
        $id = Ulid::generate();
        $created = date('Y-m-d H:i:s');

        $this->execute(
            'INSERT INTO certificates (id, patient_id, doctor_id, created, description) VALUES (:id, :patient_id, :doctor_id, :created, :description)',
            [
                new Parameter(':id', $id),
                new Parameter(':patient_id', $patientId),
                new Parameter(':doctor_id', $doctorId),
                new Parameter(':created', $created),
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
            $created,
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
        string $created,
        string $description,
        string $patientName,
        string $doctorId,
        string $doctorName,
    ): void {
        // SetUp
        $this->setUp(1200, 1600);

        // Header
        $this->drawRectangle($this->dark, 0, 0, $this->w, 250);
        $this->drawImage($this->logo, 20, 10, 475, 230);
        $this->writeText(40, $this->pink, 'MEDICAL CERTIFICATE', 520, 140);

        // Description
        $description = str_replace(['<br>', '<br/>', '<br />'], "\n", $description);
        $description = wordwrap($description, 70);
        $lines = explode("\n", $description);
        $y = 400;
        foreach ($lines as $line) {
            $this->writeTextCentered(24, $this->black, $line, $y);
            $y += 24 * 2;
        }

        // Name
        $this->writeTextCentered(24, $this->black, 'Patient Name', $y + 50);
        $this->writeTextCentered(48, $this->black, $patientName, $y + 120);

        // Date
        $date = date('d/m/Y', strtotime($created));
        $this->writeTextCentered(24, $this->black, 'Certificate Date', $y + 180);
        $this->writeTextCentered(32, $this->black, $date, $y + 230);

        // Doctor's signature
        $signature = imagecreatefrompng(__DIR__ . '/../../public/var/signatures/' . $doctorId . '.png');
        $this->drawImageCentered($signature, 1100, 200);
        $this->writeTextCentered(24, $this->black, $doctorName, 1350);

        // QR
        $url = $_ENV['APP_URL'] . '/var/certificates/' . $id . '.png';
        $qr = $this->generateQr($url, 250);
        $this->drawImage($qr, 900, 1100, 250, 250);

        // Footer
        $this->drawRectangle($this->dark, 0, 1450, $this->w, 1600);
        $this->drawImage($this->logo, 500, 1450, 300, 150);
        $this->writeTextCentered(16, $this->black, self::MOTTO, 1400);

        // Output
        $this->save(__DIR__ . '/../../public/var/certificates/' . $id . '.png');
    }
}

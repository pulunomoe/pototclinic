<?php

namespace App\Model;

use App\Model\Common\Model;
use App\Model\Common\Parameter;
use Ulid\Ulid;

class ReportModel extends Model
{
    public function readAllByPatient(
        string $patientId,
    ): array {
        return $this->fetchAll('SELECT * FROM reports_view WHERE patient_id = :patient_id', [
            new Parameter(':patient_id', $patientId),
        ]);
    }

    public function readOne(
        string $id,
    ): array {
        return $this->fetch('SELECT * FROM reports_view WHERE id = :id LIMIT 1', [
            new Parameter(':id', $id),
        ]);
    }

    public function create(
        string $patientId,
        string $doctorId,
        string $nurseId,
        string $summary,
        string $color,
        string $description,
    ): string {
        $id = Ulid::generate()->__toString();
        $created = date('Y-m-d H:i:s');

        $this->execute(
            'INSERT INTO reports (id, patient_id, doctor_id, nurse_id, created, summary, color, description) VALUES (:id, :patient_id, :doctor_id, :nurse_id, :created, :summary, :color, :description)',
            [
                new Parameter(':id', $id),
                new Parameter(':patient_id', $patientId),
                new Parameter(':doctor_id', $doctorId),
                new Parameter(':nurse_id', $nurseId),
                new Parameter(':created', $created),
                new Parameter(':summary', $summary),
                new Parameter(':color', $color),
                new Parameter(':description', $description),
            ],
        );

        $patient = $this->fetch('SELECT name FROM patients WHERE id = :patient_id', [
            new Parameter(':patient_id', $patientId),
        ]);

        $doctor = $this->fetch('SELECT id, name FROM doctors WHERE id = :doctor_id', [
            new Parameter(':doctor_id', $doctorId),
        ]);

        $nurse = $this->fetch('SELECT name FROM nurses WHERE id = :nurse_id', [
            new Parameter(':nurse_id', $nurseId),
        ]);

        return $id;
    }

    public function delete(
        string $id,
    ): void {
        $this->execute(
            'DELETE FROM reports WHERE id = :id',
            [
                new Parameter(':id', $id),
            ],
        );
    }

    public function deleteFile(string $id): void
    {
        $report = __DIR__ . '/../../public/var/reports/' . $id . '.png';
        if (file_exists($report)) {
            unlink($report);
        }
    }
}
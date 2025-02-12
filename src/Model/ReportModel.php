<?php

namespace App\Model;

use App\Model\Common\GenerateTrait;
use App\Model\Common\Model;
use App\Model\Common\Parameter;
use Ulid\Ulid;

class ReportModel extends Model
{
    use GenerateTrait;

    private const MOTTO = '"We believe in your health—and our profit margins!"';

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
        array $tests,
        string $summary,
        string $color,
    ): string {
        $id = Ulid::generate();
        $created = date('Y-m-d H:i:s');

        $this->execute(
            'INSERT INTO reports (id, patient_id, doctor_id, nurse_id, created, summary, color, description) VALUES (:id, :patient_id, :doctor_id, :nurse_id, :created, :summary, :color, null)',
            [
                new Parameter(':id', $id),
                new Parameter(':patient_id', $patientId),
                new Parameter(':doctor_id', $doctorId),
                new Parameter(':nurse_id', $nurseId),
                new Parameter(':created', $created),
                new Parameter(':summary', $summary),
                new Parameter(':color', $color),
            ],
        );

        $details = [];
        foreach ($tests as $testId => $resultId) {
            $detailId = Ulid::generate();
            $this->execute(
                'INSERT INTO report_details (id, report_id, test_id, result_id) VALUES (:id, :report_id, :test_id, :result_id)',
                [
                    new Parameter(':id', $detailId),
                    new Parameter(':report_id', $id),
                    new Parameter(':test_id', $testId),
                    new Parameter(':result_id', $resultId),
                ],
            );
            $detail = $this->fetch(
                'SELECT test_name, result_value, result_color FROM report_details_view WHERE id = :id LIMIT 1',
                [
                    new Parameter(':id', $detailId),
                ],
            );
            $details[] = [
                'name' => $detail['test_name'],
                'value' => $detail['result_value'],
                'color' => $detail['result_color'],
            ];
        }

        $patient = $this->fetch('SELECT name, race FROM patients WHERE id = :patient_id', [
            new Parameter(':patient_id', $patientId),
        ]);

        $doctor = $this->fetch('SELECT name FROM doctors WHERE id = :doctor_id', [
            new Parameter(':doctor_id', $doctorId),
        ]);

        $nurse = $this->fetch('SELECT name FROM nurses WHERE id = :nurse_id', [
            new Parameter(':nurse_id', $nurseId),
        ]);

        $this->generate(
            $id,
            $created,
            $summary,
            $color,
            $details,
            $patient['name'],
            $patient['race'],
            $doctor['name'],
            $nurse['name'],
        );

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

    public function generate(
        string $id,
        string $created,
        string $summary,
        string $summaryColor,
        array $details,
        string $patientName,
        string $patientRace,
        string $doctorName,
        string $nurseName,
    ) {
        // SetUp
        $this->setUp(1200, 1600);

        // Header
        $this->drawRectangle($this->dark, 0, 0, $this->w, 250);
        $this->drawImage($this->logo, 20, 10, 475, 230);
        $this->writeText(40, $this->pink, 'MEDICAL REPORT', 600, 140);

        // Patient
        $this->writeText(24, $this->black, 'Patient Name', 100, 350);
        $this->writeText(32, $this->black, ucwords($patientName), 100, 390);
        $this->writeText(24, $this->black, 'Patient Race', 100, 450);
        $this->writeText(32, $this->black, ucfirst($patientRace), 100, 490);

        // Number
        $number = substr($id, -8);
        $this->writeTextRight(24, $this->black, 'Report Number', 1100, 350);
        $this->writeTextRight(32, $this->black, $number, 1100, 390);
        // Date
        $date = date('d/m/Y', strtotime($created));
        $this->writeTextRight(24, $this->black, 'Report Date', 1100, 450);
        $this->writeTextRight(32, $this->black, $date, 1100, 490);

        // Tests results
        $this->writeTextLine(
            24,
            $this->black,
            $this->black,
            'Lab Tests',
            'Results',
            600,
            100,
        );

        $y = 650;
        foreach ($details as $detail) {
            $color = $this->registerColor($detail['color']);
            $this->writeTextLine(24, $this->black, $color, $detail['name'], $detail['value'], $y, 100);
            $y += 50;
        }

        // Summary
        $summaryColor = $this->registerColor($summaryColor);
        $this->writeTextCentered(32, $this->black, 'Summary:', $y + 50);
        $this->writeTextCentered(32, $summaryColor, $summary, $y + 100);

        // QR
        $url = $_ENV['APP_URL'] . '/var/reports/' . $id . '.png';
        $qr = $this->generateQr($url, 250);
        $this->drawImageCentered($qr, 1100, 250);

        // Doctor
        $this->writeText(24, $this->black, 'Nurse Name', 100, 1280);
        $this->writeText(32, $this->black, $nurseName, 100, 1330);
        // Nurse
        $this->writeTextRight(24, $this->black, 'Doctor Name', 1100, 1280);
        $this->writeTextRight(32, $this->black, $doctorName, 1100, 1330);

        // Footer
        $this->drawRectangle($this->dark, 0, 1450, $this->w, 1600);
        $this->drawImage($this->logo, 500, 1450, 300, 150);
        $this->writeTextCentered(16, $this->black, self::MOTTO, 1400);

        // Output
        $this->save(__DIR__ . '/../../public/var/reports/' . $id . '.png');
    }
}

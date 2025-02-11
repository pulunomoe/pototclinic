<?php

namespace App\Controller;

use App\Model\CertificateModel;
use App\Model\DoctorModel;
use App\Model\PatientModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

readonly class CertificateController
{
    private const DEFAULT_CONTENT = "This is to certify that the patient was struck down by a mysterious yet very serious medical condition. One so severe that work, chores, and all forms of responsibility were strictly forbidden.<br><br>Patient is advised to extend recovery time if symptoms persist (or if they just need another day off). Avoid all stressful activities, including but not limited to: replying to emails, attending meetings, and making small talk with coworkers.";

    public function create(
        string $patientId,
        Response $response,
        PatientModel $patientModel,
        DoctorModel $doctorModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'certificates/create.twig', [
            'patient' => $patientModel->readOne($patientId),
            'doctors' => $doctorModel->readAllForSelect(),
            'content' => self::DEFAULT_CONTENT,
        ]);
    }

    public function createPost(
        string $patientId,
        ServerRequest $request,
        Response $response,
        CertificateModel $certificateModel,
    ): ResponseInterface {
        $doctorId = $request->getParam('doctor_id');
        $description = $request->getParam('description');

        $certificateModel->create($patientId, $doctorId, $description);

        return $response->withRedirect('/patients/' . $patientId);
    }

    public function delete(
        string $patientId,
        string $certificateId,
        Response $response,
        PatientModel $patientModel,
        CertificateModel $certificateModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'certificates/delete.twig', [
            'patient' => $patientModel->readOne($patientId),
            'certificate' => $certificateModel->readOne($certificateId),
        ]);
    }

    public function deletePost(
        string $patientId,
        string $certificateId,
        Response $response,
        CertificateModel $certificateModel,
    ): ResponseInterface {
        $certificateModel->delete($certificateId);
        $certificateModel->deleteFile($certificateId);

        return $response->withRedirect('/patients/' . $patientId);
    }
}
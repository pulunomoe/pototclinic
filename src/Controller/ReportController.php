<?php

namespace App\Controller;

use App\Model\DoctorModel;
use App\Model\NurseModel;
use App\Model\PatientModel;
use App\Model\ReportModel;
use App\Model\ResultModel;
use App\Model\TestModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

class ReportController
{
    public function create(
        string $patientId,
        Response $response,
        PatientModel $patientModel,
        DoctorModel $doctorModel,
        NurseModel $nurseModel,
        TestModel $testModel,
        ResultModel $resultModel,
        Twig $twig,
    ): ResponseInterface {
        $tests = $testModel->readAll();
        foreach ($tests as &$test) {
            $test['results'] = $resultModel->readAllForSelectByTest($test['id']);
        }

        return $twig->render($response, 'reports/create.twig', [
            'patient' => $patientModel->readOne($patientId),
            'doctors' => $doctorModel->readAllForSelect(),
            'nurses' => $nurseModel->readAllForSelect(),
            'tests' => $tests,
        ]);
    }

    public function createPost(
        string $patientId,
        ServerRequest $request,
        Response $response,
        ReportModel $reportModel,
    ): ResponseInterface {
        $doctorId = $request->getParam('doctor_id');
        $nurseId = $request->getParam('nurse_id');
        $summary = $request->getParam('summary');
        $tests = $request->getParam('tests');
        $color = $request->getParam('color');

        $reportModel->create($patientId, $doctorId, $nurseId, $tests, $summary, $color);

        return $response->withRedirect('/patients/' . $patientId);
    }

    public function delete(
        string $patientId,
        string $reportId,
        Response $response,
        PatientModel $patientModel,
        ReportModel $reportModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'reports/delete.twig', [
            'patient' => $patientModel->readOne($patientId),
            'report' => $reportModel->readOne($reportId),
        ]);
    }

    public function deletePost(
        string $patientId,
        string $reportId,
        Response $response,
        ReportModel $reportModel,
    ): ResponseInterface {
        $reportModel->delete($reportId);
        $reportModel->deleteFile($reportId);

        return $response->withRedirect('/patients/' . $patientId);
    }
}
<?php

namespace App\Controller;

use App\Model\CertificateModel;
use App\Model\PatientModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

readonly class PatientController
{
    private const RACES = [
        'hyur' => 'Hyur',
        'elezen' => 'Elezen',
        'lalafell' => 'Lalafell',
        'miqote' => 'Miqote',
        'roegadyn' => 'Roegadyn',
        'hrothgar' => 'Hrothgar',
        'viera' => 'Viera',
    ];

    public function index(
        Response $response,
        PatientModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'patients/index.twig', [
            'patients' => $model->readAll(),
        ]);
    }

    public function create(
        Response $response,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'patients/create.twig', [
            'races' => self::RACES,
        ]);
    }

    public function createPost(
        ServerRequest $request,
        Response $response,
        PatientModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $race = $request->getParam('race');
        $description = $request->getParam('description');
        $photo = $request->getUploadedFiles()['photo'];

        $id = $model->create($name, $race, $description, $photo);

        return $response->withRedirect('/patients/' . $id);
    }

    public function view(
        string $id,
        Response $response,
        PatientModel $patientModel,
        CertificateModel $certificateModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'patients/view.twig', [
            'patient' => $patientModel->readOne($id),
            'certificates' => $certificateModel->readAllByPatient($id),
        ]);
    }

    public function edit(
        string $id,
        Response $response,
        PatientModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'patients/edit.twig', [
            'patient' => $model->readOne($id),
            'races' => self::RACES,
        ]);
    }

    public function editPost(
        string $id,
        ServerRequest $request,
        Response $response,
        PatientModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $race = $request->getParam('race');
        $description = $request->getParam('description');
        $photo = $request->getUploadedFiles()['photo'];

        $model->update($id, $name, $race, $description, $photo);

        return $response->withRedirect('/patients/' . $id);
    }

    public function delete(
        string $id,
        Response $response,
        PatientModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'patients/delete.twig', [
            'patient' => $model->readOne($id),
        ]);
    }

    public function deletePost(
        string $id,
        Response $response,
        PatientModel $model,
    ): ResponseInterface {
        $model->delete($id);
        $model->deletePhoto($id);

        return $response->withRedirect('/patients');
    }
}

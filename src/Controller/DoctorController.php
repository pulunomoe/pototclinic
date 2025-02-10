<?php

namespace App\Controller;

use App\Model\DoctorModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

class DoctorController
{
    public function index(
        Response $response,
        DoctorModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'doctors/index.twig', [
            'doctors' => $model->readAll(),
        ]);
    }

    public function create(
        Response $response,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'doctors/create.twig');
    }

    public function createPost(
        ServerRequest $request,
        Response $response,
        DoctorModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $description = $request->getParam('description');
        $signature = $request->getUploadedFiles()['signature'];

        $id = $model->create($name, $description, $signature);

        return $response->withRedirect('/doctors/' . $id);
    }

    public function view(
        string $id,
        Response $response,
        DoctorModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'doctors/view.twig', [
            'doctor' => $model->readOne($id),
        ]);
    }

    public function edit(
        string $id,
        Response $response,
        DoctorModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'doctors/edit.twig', [
            'doctor' => $model->readOne($id),
        ]);
    }

    public function editPost(
        string $id,
        ServerRequest $request,
        Response $response,
        DoctorModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $description = $request->getParam('description');
        $signature = $request->getUploadedFiles()['signature'];

        $model->update($id, $name, $description, $signature);

        return $response->withRedirect('/doctors/' . $id);
    }

    public function delete(
        string $id,
        Response $response,
        DoctorModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'doctors/delete.twig', [
            'doctor' => $model->readOne($id),
        ]);
    }

    public function deletePost(
        string $id,
        Response $response,
        DoctorModel $model,
    ): ResponseInterface {
        $model->delete($id);

        $signature = __DIR__ . '/../../public/var/signatures/' . $id . '.png';
        if (file_exists($signature)) {
            unlink($signature);
        }

        return $response->withRedirect('/doctors');
    }
}

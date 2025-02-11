<?php

namespace App\Controller;

use App\Model\NurseModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

readonly class NurseController
{
    public function index(
        Response $response,
        NurseModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'nurses/index.twig', [
            'nurses' => $model->readAll(),
        ]);
    }

    public function create(
        Response $response,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'nurses/create.twig');
    }

    public function createPost(
        ServerRequest $request,
        Response $response,
        NurseModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $description = $request->getParam('description');

        $id = $model->create($name, $description);

        return $response->withRedirect('/nurses/' . $id);
    }

    public function view(
        string $id,
        Response $response,
        NurseModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'nurses/view.twig', [
            'nurse' => $model->readOne($id),
        ]);
    }

    public function edit(
        string $id,
        Response $response,
        NurseModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'nurses/edit.twig', [
            'nurse' => $model->readOne($id),
        ]);
    }

    public function editPost(
        string $id,
        ServerRequest $request,
        Response $response,
        NurseModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $description = $request->getParam('description');

        $model->update($id, $name, $description);

        return $response->withRedirect('/nurses/' . $id);
    }

    public function delete(
        string $id,
        Response $response,
        NurseModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'nurses/delete.twig', [
            'nurse' => $model->readOne($id),
        ]);
    }

    public function deletePost(
        string $id,
        Response $response,
        NurseModel $model,
    ): ResponseInterface {
        $model->delete($id);

        return $response->withRedirect('/nurses');
    }
}

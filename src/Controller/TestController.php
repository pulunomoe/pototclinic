<?php

namespace App\Controller;

use App\Model\ResultModel;
use App\Model\TestModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

class TestController
{
    public function index(
        Response $response,
        TestModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'tests/index.twig', [
            'tests' => $model->readAll(),
        ]);
    }

    public function create(
        Response $response,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'tests/create.twig');
    }

    public function createPost(
        ServerRequest $request,
        Response $response,
        TestModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $description = $request->getParam('description');

        $id = $model->create($name, $description);

        return $response->withRedirect('/tests/' . $id);
    }

    public function view(
        string $id,
        Response $response,
        TestModel $testModel,
        ResultModel $resultModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'tests/view.twig', [
            'test' => $testModel->readOne($id),
            'results' => $resultModel->readAllByTest($id),
        ]);
    }

    public function edit(
        string $id,
        Response $response,
        TestModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'tests/edit.twig', [
            'test' => $model->readOne($id),
        ]);
    }

    public function editPost(
        string $id,
        ServerRequest $request,
        Response $response,
        TestModel $model,
    ): ResponseInterface {
        $name = $request->getParam('name');
        $description = $request->getParam('description');

        $model->update($id, $name, $description);

        return $response->withRedirect('/tests/' . $id);
    }

    public function delete(
        string $id,
        Response $response,
        TestModel $model,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'tests/delete.twig', [
            'test' => $model->readOne($id),
        ]);
    }

    public function deletePost(
        string $id,
        Response $response,
        TestModel $model,
    ): ResponseInterface {
        $model->delete($id);

        return $response->withRedirect('/tests');
    }
}

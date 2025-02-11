<?php

namespace App\Controller;

use App\Model\ResultModel;
use App\Model\TestModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

readonly class ResultController
{
    public function create(
        string $testId,
        Response $response,
        TestModel $testModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'results/create.twig', [
            'test' => $testModel->readOne($testId),
        ]);
    }

    public function createPost(
        string $testId,
        ServerRequest $request,
        Response $response,
        ResultModel $resultModel,
    ): ResponseInterface {
        $value = $request->getParam('value');
        $color = $request->getParam('color');
        $description = $request->getParam('description');

        $resultModel->create($testId, $value, $color, $description);

        return $response->withRedirect('/tests/' . $testId);
    }

    public function edit(
        string $testId,
        string $resultId,
        Response $response,
        TestModel $testModel,
        ResultModel $resultModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'results/edit.twig', [
            'test' => $testModel->readOne($testId),
            'result' => $resultModel->readOne($resultId),
        ]);
    }

    public function editPost(
        string $testId,
        string $resultId,
        ServerRequest $request,
        Response $response,
        ResultModel $resultModel,
    ): ResponseInterface {
        $value = $request->getParam('value');
        $color = $request->getParam('color');
        $description = $request->getParam('description');

        $resultModel->update($resultId, $value, $color, $description);

        return $response->withRedirect('/tests/' . $testId);
    }

    public function delete(
        string $testId,
        string $resultId,
        Response $response,
        TestModel $testModel,
        ResultModel $resultModel,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'results/delete.twig', [
            'test' => $testModel->readOne($testId),
            'result' => $resultModel->readOne($resultId),
        ]);
    }

    public function deletePost(
        string $testId,
        string $resultId,
        Response $response,
        ResultModel $resultModel,
    ): ResponseInterface {
        $resultModel->delete($resultId);

        return $response->withRedirect('/tests/' . $testId);
    }
}
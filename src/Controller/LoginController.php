<?php

namespace App\Controller;

use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;

class LoginController
{
    public function login(
        Response $response,
        Twig $twig,
    ): ResponseInterface {
        return $twig->render($response, 'login.twig');
    }

    public function loginPost(
        ServerRequest $request,
        Response $response,
        UserModel $model,
    ): ResponseInterface {
        $username = $request->getParam('username');
        $password = $request->getParam('password');

        $user = $model->login($username, $password);

        if (empty($user)) {
            die('LOL NOPE!');
        }

        $_SESSION['user'] = $user;

        return $response->withRedirect('/patients');
    }
}

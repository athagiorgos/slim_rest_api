<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;
use Valitron\Validator;

class Profile
{
    public function __construct(private PhpRenderer $view,
                                private UserRepository $userRepository)
    {
    }

    public function show(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');

        $encryption_key = Key::loadFromAsciiSafeString("def00000a03022580346ab7c202e0ab500cbc8807188f16484ed2f829c931993d429da8ce96ec1ea2d1ea620b2c8d14ec773ee3094daab90d45edebf0987c1b14c2edc4e");

        $api_key = Crypto::decrypt($user['api_key'], $encryption_key);

        return $this->view->render($response, 'profile.php', [
            'api_key' => $api_key
        ]);
    }
}
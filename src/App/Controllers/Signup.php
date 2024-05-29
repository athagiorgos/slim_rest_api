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

class Signup
{
    public function __construct(private PhpRenderer $view,
                                private Validator $validator,
                                private UserRepository $userRepository)
    {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', ['lengthMin', 6]],
            'password_confirmation' => ['required', ['equals', 'password']]
        ]);

        $this->validator->rule(function ($field, $value, $params, $fields) {
            return $this->userRepository->find('email', $value) === false;
        }, 'email')->message('{field} is already taken');
    }

    public function new(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'signup.php');
    }

    public function create(Request $request, Response $response): Response
    {
       $data = $request->getParsedBody();

       $this->validator = $this->validator->withData($data);

       if (!$this->validator->validate()) {
           return $this->view->render($response, 'signup.php', [
               "errors" => $this->validator->errors(),
               "data" => $data
           ]);
       }

       $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);

       $api_key = bin2hex(random_bytes(16));

       $encryption_key = Key::loadFromAsciiSafeString("def00000a03022580346ab7c202e0ab500cbc8807188f16484ed2f829c931993d429da8ce96ec1ea2d1ea620b2c8d14ec773ee3094daab90d45edebf0987c1b14c2edc4e");

       $data['api_key'] = Crypto::encrypt($api_key, $encryption_key);

       $data['api_key_hash'] = hash_hmac('sha256', $api_key, "6HyCfbyjleRaCUSA");

       $this->userRepository->create($data);

       return $response
           ->withHeader('Location', '/signup/success')
           ->withStatus(302);
    }

    public function success(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'signup-success.php');
    }
}
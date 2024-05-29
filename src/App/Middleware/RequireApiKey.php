<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory;

class RequireApiKey
{
    public function __construct(private readonly ResponseFactory $responseFactory,
                                private readonly UserRepository $userRepository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler) : Response
   {

       if (!$request->hasHeader('x-api-key'))
       {
           $response = $this->responseFactory->createResponse();

           $response->getBody()->write(json_encode(['api-key missing from request']));

           return $response->withStatus(400);
       }

       $api_key = $request->getHeaderLine('x-api-key');

       $api_key_hash = hash_hmac('sha256', $api_key, "6HyCfbyjleRaCUSA");

       $user = $this->userRepository->find('api_key_hash', $api_key_hash);

       if (!$user)
       {
           $response = $this->responseFactory->createResponse();

           $response->getBody()->write(json_encode(['api-key invalid']));

           return $response->withStatus(401);
       }

       return $handler->handle($request);
   }
}
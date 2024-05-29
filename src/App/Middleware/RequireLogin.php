<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory;

class RequireLogin
{
    public function __construct(private readonly ResponseFactory $responseFactory,
                                private readonly UserRepository $userRepository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler) : Response
   {
       if (isset($_SESSION['user_id']))
       {
           $user = $this->userRepository->find('id', $_SESSION['user_id']);

           if ($user)
           {
               $request = $request->withAttribute('user', $user);

               return $handler->handle($request);
           }
       }

       $response = $this->responseFactory->createResponse();

       $response->getBody()->write('Unauthorized');

       return $response->withStatus(401);
   }
}
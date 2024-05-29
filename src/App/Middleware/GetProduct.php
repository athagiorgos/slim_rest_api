<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

class GetProduct
{

    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler) : Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $data = $this->productRepository->getById((int) $id);

        if ($data === false) throw new HttpNotFoundException($request, message: "product not found");

        $request = $request->withAttribute("data", $data);

        return $handler->handle($request);
    }
}
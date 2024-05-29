<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProductRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Valitron\Validator;

class Products
{
    public function __construct(private readonly ProductRepository $productRepository,
                                private Validator $validator)
    {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'description' => ['required'],
        ]);
    }

    public function get(Request $request, Response $response): Response
    {
        $data = $this->productRepository->getAll();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function getById(Request $request, Response $response): Response
    {
        $product = $request->getAttribute("data");

        $body = json_encode($product);

        $response->getBody()->write($body);

        return $response;
    }

    public function create(Request $request, Response $response) : Response
    {
        $body = $request->getParsedBody();

        $this->validator = $this->validator->withData($body);

        if(!$this->validator->validate())
        {
            $response->getBody()->write(json_encode($this->validator->errors()));

            return $response->withStatus(422);
        }

        $id = $this->productRepository->create($body);

        $body = json_encode([
            'message' => 'Product created',
            'id' => $id,
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }

    public function update(Request $request, Response $response, string $id) : Response
    {
        $body = $request->getParsedBody();

        $this->validator = $this->validator->withData($body);

        if(!$this->validator->validate())
        {
            $response->getBody()->write(json_encode($this->validator->errors()));

            return $response->withStatus(422);
        }

        $rows = $this->productRepository->update((int) $id, $body);

        $body = json_encode([
            'message' => 'Product updated',
            'rows' => $rows,
        ]);

        $response->getBody()->write($body);

        return $response;
    }

    public function delete(Request $request, Response $response, string $id) : Response
    {
        $rows = $this->productRepository->delete($id);

        $body = json_encode([
            "message" => "Product deleted",
            "rows" => $rows,
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
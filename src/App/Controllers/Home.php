<?php

declare(strict_types=1);

namespace App\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;

class Home
{
    public function __construct(private PhpRenderer $view)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'home.php');
    }
}
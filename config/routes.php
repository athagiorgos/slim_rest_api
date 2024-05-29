<?php

declare(strict_types=1);

use App\Controllers\Home;
use App\Controllers\Login;
use App\Controllers\Products;
use App\Controllers\Profile;
use App\Controllers\Signup;
use App\Middleware\ActivateSession;
use App\Middleware\AddJsonResponseHeader;
use App\Middleware\GetProduct;
use App\Middleware\RequireApiKey;
use App\Middleware\RequireLogin;
use Slim\Routing\RouteCollectorProxy;

if(!isset($app)) die('$app not defined');

$app->group('', function (RouteCollectorProxy $group) {

    $group->get('/', Home::class);

    $group->get('/signup', [Signup::class, 'new']);

    $group->post('/signup', [Signup::class, 'create']);

    $group->get('/signup/success', [Signup::class, 'success']);

    $group->get('/login', [Login::class, 'new']);

    $group->post('/login', [Login::class, 'create']);

    $group->get('/logout', [Login::class, 'destroy']);

    $group->get('/profile', [Profile::class, 'show'])->add(RequireLogin::class);

})->add(ActivateSession::class);

$app->group('/api', function (RouteCollectorProxy $group) {

    $group->get('/products', [Products::class, 'get']);

    $group->post('/products', [Products::class, 'create']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/products/{id:[0-9]+}', Products::class . ':getById');

        $group->patch('/products/{id:[0-9]+}', [Products::class, 'update']);

        $group->delete('/products/{id:[0-9]+}', [Products::class, 'delete']);

    })->add(GetProduct::class);

})->add(RequireApiKey::class)->add(AddJsonResponseHeader::class);
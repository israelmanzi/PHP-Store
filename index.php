<?php

require 'vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// use League\Plates\Engine;
use Valitron\Validator;

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode(['message' => 'Welcome to PHP-Store API!']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();

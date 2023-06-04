<?php

require 'vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once 'Models/Product.php';
require_once 'Models/Category.php';
require_once 'Models/Purchase.php';
require 'util.php';

use Valitron\Validator;

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode(['message' => 'Welcome to PHP-Store API!']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/product/create', function (Request $request, Response $response, $args) {
    $data = json_decode($request->getBody(), true);

    $v = new Validator($data);
    $v->rule('required', ['name', 'price', 'description', 'category', 'tax', 'amount']);
    $v->rule('numeric', ['tax', 'amount', 'price']);
    $v->rule('min', 'amount', 1);
    $v->rule('min', 'price', 0.01);
    $v->rule('min', 'tax', 0.01);
    $v->rule('max', 'tax', 100);

    if (!$v->validate()) {
        $response->getBody()->write(json_encode(['message' => $v->errors()]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $data['tax'] = $data['tax'] / 100;

    $product = new Models\Product($data['name'], $data['price'], $data['description'], $data['category'], $data['tax'], $data['amount']);

    $new_prd = $product->save();

    if (!$new_prd) {
        $response = $response->withStatus(500);
        $response->getBody()->write(json_encode(['message' => 'Some thing went wrong on the server!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response = $response->withStatus(201);
    $response->getBody()->write(json_encode(['message' => 'Product created successfully!']));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/product/all', function (Request $request, Response $response, $args) {
    $products = Models\Product::all();

    $response = $response->withStatus(200);
    if (count($products) == 0) {
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode(['message' => 'No products found!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response = $response->withStatus(200);
    $response->getBody()->write(json_encode(['products' => $products]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/product/{id}', function (Request $request, Response $response, $args) {
    if (!Util::isUUID($args['id'])) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode(['message' => 'Invalid product id!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $product = Models\Product::find($args['id']);

    if (!$product) {
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode(['message' => 'Product with id #' . $args['id'] . ' not found!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response = $response->withStatus(200);
    $response->getBody()->write(json_encode(['product' => $product]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/product/category/{category_id}', function (Request $request, Response $response, $args) {
    if (!Util::isUUID($args['category_id'])) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode(['message' => 'Invalid category id!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $category = Models\Category::find($args['category_id']);

    if (!$category) {
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode(['message' => 'Category with id #' . $args['category_id'] . ' not found!']));
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    $products = Models\Product::findByCategory($args['category_id']);

    if (!$products) {
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode(['message' => 'No products found in category ' . $category['name'] . '!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response = $response->withStatus(200);
    $response->getBody()->write(json_encode(['products' => $products]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ut/list-categories', function (Request $request, Response $response, $args) {
    $categories = Models\Product::listCategories();

    if (!$categories) {
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode(['message' => 'No categories found!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response = $response->withStatus(200);
    $response->getBody()->write(json_encode(['categories' => $categories]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/category/create', function (Request $request, Response $response, $args) {
    $data = json_decode($request->getBody(), true);

    $v = new Validator($data);
    $v->rule('required', ['name', 'description']);
    $v->rule('lengthMin', 'name', 3);
    $v->rule('lengthMax', 'name', 50);
    $v->rule('lengthMin', 'description', 3);
    $v->rule('lengthMax', 'description', 255);

    if (!$v->validate()) {
        $response->getBody()->write(json_encode(['message' => $v->errors()]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $category = new Models\Category($data['name'], $data['description']);

    $new_cat = $category->save();

    if (!$new_cat) {
        $response = $response->withStatus(500);
        $response->getBody()->write(json_encode(['message' => 'Some thing is wrong on the server!']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $response = $response->withStatus(201);
    $response->getBody()->write(json_encode(['message' => 'Category created successfully!']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();

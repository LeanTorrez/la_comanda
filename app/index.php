<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Routing\RouteCollectorProxy;


require __DIR__ . '/../vendor/autoload.php';
require "../src/entidades/usuario.php";
require "../src/entidades/productoController.php";
require "../src/entidades/mesa.php";
require "../src/entidades/pedido.php";

// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/slim-php-deployment/app');
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group("/usuario",function (RouteCollectorProxy $group){
    $group->get('/',\UsuarioController::class . ":TraerTodos");
    $group->post('/alta', \UsuarioController::class . ":CargarUno");
});

$app->group('/producto', function (RouteCollectorProxy $group){  
    $group->post('/alta', \ProductoController::class.":CargarUno");
    $group->get('/',\ProductoController::class.":TraerTodos");
});

$app->group('/mesa',function(RouteCollectorProxy $group){
    $group->get('/',\Mesa::class.":TraerTodos");
    $group->post('/alta',\Mesa::class.":CargarUno");
});

$app->group('/pedido',function(RouteCollectorProxy $group){
    $group->get('/',\Pedido::class.":TraerTodos");
    $group->post('/alta',\Pedido::class.":CargarUno");
});


$app->run();

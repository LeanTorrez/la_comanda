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
require "../src/controllers/empleadoController.php";
require "../src/controllers/productoController.php";
require "../src/controllers/mesaController.php";
require "../src/controllers/pedidoController.php";

// Instantiate App
$app = AppFactory::create();
//$app->setBasePath('/slim-php-deployment/app');
//$app->setBasePath('/app');
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group("/usuario",function (RouteCollectorProxy $group){
    $group->get('/',\UsuarioController::class . ":TraerTodos");
    $group->get('/{id}',\UsuarioController::class . ":TraerUno");
    $group->delete('/borrar',\UsuarioController::class . ":BorrarUno");
    $group->post('/alta', \UsuarioController::class . ":CargarUno");
    $group->put('/actualizar', \UsuarioController::class . ":ModificarUno");
});

$app->group('/producto', function (RouteCollectorProxy $group){  
    $group->post('/alta', \ProductoController::class.":CargarUno");
    $group->get('/',\ProductoController::class.":TraerTodos");
    $group->get('/{id}',\ProductoController::class . ":TraerUno");
    $group->delete('/borrar',\ProductoController::class . ":BorrarUno");
    $group->put('/actualizar', \ProductoController::class . ":ModificarUno");
});

$app->group('/mesa',function(RouteCollectorProxy $group){
    $group->get('/',\MesaController::class.":TraerTodos");//->add(\MW::class.":VerificarSocio")
    $group->get('/{id}',\MesaController::class . ":TraerUno");
    $group->post('/alta',\MesaController::class.":CargarUno");
    $group->delete('/borrar',\MesaController::class . ":BorrarUno");
    $group->put('/actualizar', \MesaController::class . ":ModificarUno");
});//->add(\MW::class.":VerificarSocio");

$app->group('/pedido',function(RouteCollectorProxy $group){
    $group->get('/',\PedidoController::class.":TraerTodos");
    $group->get('/{id}',\PedidoController::class.":TraerUno");
    $group->put('/actualizar', \PedidoController::class . ":ModificarUno");
    $group->delete('/borrar',\PedidoController::class . ":BorrarUno");
    $group->post('/alta',\PedidoController::class.":CargarUno");
});


$app->run();

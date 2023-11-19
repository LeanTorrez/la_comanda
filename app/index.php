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
require "../src/middleWare/MW.php";
require "../src/middleWare/AuthMiddleware.php";
require "../src/controllers/login.php";

// Instantiate App
$app = AppFactory::create();
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->post("/login",\Loggin::class.":Login")->add(\MW::class.":VerificarClave")->add(\MW::class.":VerificarEmail");

$app->group("/usuario",function (RouteCollectorProxy $group){
    $group->get('/',\UsuarioController::class . ":TraerTodos");

    $group->get('/id',\UsuarioController::class . ":TraerUno")->add(\MW::class.":VerificarIdQuery");

    $group->delete('/borrar',\UsuarioController::class . ":BorrarUno")->add(\MW::class.":VerificarIdQuery");

    $group->post('/alta', \UsuarioController::class . ":CargarUno")
    ->add(\MW::class.":VerificarClave")
    ->add(\MW::class.":VerificarEmail")
    ->add(\MW::class.":VerificarNombre")
    ->add(\MW::class.":VerificarRol");

    $group->put('/actualizar', \UsuarioController::class . ":ModificarUno")
    ->add(\MW::class.":VerificarId")
    ->add(\MW::class.":VerificarClave")
    ->add(\MW::class.":VerificarEmail")
    ->add(\MW::class.":VerificarNombre")
    ->add(\MW::class.":VerificarRol");
})->add(\MW::class.":VerificarPermisosUsuario")->add(\AuthMiddleware::class .":verificarToken");

$app->group('/producto', function (RouteCollectorProxy $group){  
    $group->post('/alta', \ProductoController::class.":CargarUno")
    ->add(\MW::class.":VerificarTiempoPreparacion")
    ->add(\MW::class.":VerificarPrecio")
    ->add(\MW::class.":VerificarNombre")
    ->add(\MW::class.":VerificarTipo");

    $group->get('/',\ProductoController::class.":TraerTodos");

    $group->get('/id',\ProductoController::class . ":TraerUno")->add(\MW::class.":VerificarIdQuery");

    $group->delete('/borrar',\ProductoController::class . ":BorrarUno")->add(\MW::class.":VerificarIdQuery");

    $group->put('/actualizar', \ProductoController::class . ":ModificarUno")
    ->add(\MW::class.":VerificarId")
    ->add(\MW::class.":VerificarTiempoPreparacion")
    ->add(\MW::class.":VerificarPrecio")
    ->add(\MW::class.":VerificarNombre")
    ->add(\MW::class.":VerificarTipo");
})->add(\MW::class.":VerificarPermisosProducto")->add(\AuthMiddleware::class .":verificarToken");

$app->group('/mesa',function(RouteCollectorProxy $group){
    $group->get('/',\MesaController::class.":TraerTodos");

    $group->get('/id',\MesaController::class . ":TraerUno")->add(\MW::class.":VerificarIdQuery");

    $group->post('/alta',\MesaController::class.":CargarUno")->add(\MW::class.":VerificarEstado");

    $group->post('/foto',\MesaController::class.":Foto")
    ->add(\MW::class.":VerificarId")
    ->add(\MW::class.":VerificarAlfanumerico");

    $group->post('/encuesta',\MesaController::class.":Encuesta")
    ->add(\MW::class.":VerificarId")
    ->add(\MW::class.":VerificarAlfanumerico");
    //verificar comentario y puntuacion

    $group->get('/comentarios',\MesaController::class.":Comentarios");

    $group->delete('/borrar',\MesaController::class . ":BorrarUno")->add(\MW::class.":VerificarIdQuery");

    $group->put('/actualizar/estado', \MesaController::class . ":ModificarEstado")->add(\MW::class.":VerificarCerrada");

    $group->get('/cobrar',\MesaController::class . ":CobrarMesa")->add(\MW::class.":VerificarAlfanumericoQuery");

    $group->put('/actualizar', \MesaController::class . ":ModificarUno")
    ->add(\MW::class.":VerificarId")
    ->add(\MW::class.":VerificarEstado")
    ->add(\MW::class.":VerificarIdMozo")
    ->add(\MW::class.":VerificarIdPedido");
})->add(\MW::class .":VerificarPermisosMesa")->add(\AuthMiddleware::class .":verificarToken");

$app->group('/pedido',function(RouteCollectorProxy $group){
    $group->get('/',\PedidoController::class.":TraerTodos");

    $group->get('/id',\PedidoController::class.":TraerUno")->add(\MW::class.":VerificarAlfanumericoQuery");

    $group->put('/actualizar', \PedidoController::class . ":ModificarUno")
    ->add(\MW::class.":VerificarAlfanumerico")
    ->add(\MW::class.":VerificarId")
    ->add(\MW::class.":VerificarNombre");

    $group->put('/actualizar/estado', \PedidoController::class . ":ModificarEstado")
    ->add(\MW::class.":VerificarAlfanumerico")
    ->add(\MW::class.":VerificarId")
    ->add(\MW::class.":VerificarEstado");

    $group->delete('/borrar',\PedidoController::class . ":BorrarUno")->add(\MW::class.":VerificarAlfanumericoQuery");

    $group->post('/alta',\PedidoController::class.":CargarUno")->add(\MW::class.":VerificarUser");
})->add(\AuthMiddleware::class .":verificarToken");

$app->run();

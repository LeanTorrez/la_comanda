<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MW{

    public function VerificarSocio(Request $request, RequestHandler $handler): Response{

        $parametros = $request->getQueryParams();

        if(isset($parametros["tipoUsuario"]) && $parametros["tipoUsuario"] === "socio"){
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Necesitas ser socio para acceder"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
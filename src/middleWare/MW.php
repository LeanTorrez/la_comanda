<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MW{

    public function VerificarPermisosUsuario(Request $request, RequestHandler $handler): Response{

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        if($data->rol === "socio"){
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Necesitas ser socio para acceder"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarPermisosProducto(Request $request, RequestHandler $handler): Response{

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        if($data->rol === "socio" || 
           $data->rol === "cocinero" || 
           $data->rol === "bartender" || 
           $data->rol === "cervecero"){
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Necesitas tener permisos para poder entrar"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarPermisosMesa(Request $request, RequestHandler $handler): Response{
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        if($data->rol === "socio" || 
           $data->rol === "mozo"){
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Necesitas tener permisos para poder entrar"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarEmail(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["email"])){
            if(!empty($parametros["email"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El email esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro mail"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarClave(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["clave"])){
            if(!empty($parametros["clave"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "La clave esta vacia"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro clave"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarIdQuery(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getQueryParams();
        if(isset($parametros["id"])){
            if(!empty($parametros["id"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El id esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro id"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarId(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["id"])){
            if(!empty($parametros["id"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El id esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro id"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarNombre(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["nombre"])){
            if(!empty($parametros["nombre"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El nombre esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro nombre"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarRol(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["rol"])){
            if(!empty($parametros["rol"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El rol esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro rol"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarRolQuery(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getQueryParams();
        if(isset($parametros["rol"])){
            if(!empty($parametros["rol"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El rol esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro rol"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarTipo(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["tipo"])){
            if(!empty($parametros["tipo"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El tipo esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro tipo"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarPrecio(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["precio"])){
            if(!empty($parametros["precio"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El precio esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro precio"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarTiempoPreparacion(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["tiempoPreparacion"])){
            if(!empty($parametros["tiempoPreparacion"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El tiempoePreparacion esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro tiempoPrepacion"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarEstado(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["estado"])){
            if(!empty($parametros["estado"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El estado esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro estado"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarIdMozo(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["id_mozo"])){
            if(!empty($parametros["id_mozo"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El id_mozo esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro id_mozo"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarIdPedido(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["id_pedido"])){
            if(!empty($parametros["id_pedido"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El id_pedido esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro id_pedido"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarUser(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["user"])){
            if(!empty($parametros["user"])){
                $datos = $parametros["user"];
                if(isset($datos["nombre"]) && isset($datos["platos"])){
                    $response = $handler->handle($request);
                }else{
                    $response = new Response();
                    $payload = json_encode(array("Error" => "Faltan los parametros nombre o platos"));
                    $response->getBody()->write($payload);
                }
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El json user esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro user"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarAlfanumerico(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getParsedBody();
        if(isset($parametros["alfanumerico"])){
            if(!empty($parametros["alfanumerico"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El alfanumerico esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro alfanumerico"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarAlfanumericoQuery(Request $request, RequestHandler $handler): Response{
        $parametros = $request->getQueryParams();
        if(isset($parametros["alfanumerico"])){
            if(!empty($parametros["alfanumerico"])){
                $response = $handler->handle($request);
            }else{
                $response = new Response();
                $payload = json_encode(array("Error" => "El alfanumerico esta vacio"));
                $response->getBody()->write($payload);
            }
        }else{
            $response = new Response();
            $payload = json_encode(array("Error" => "Es necesario el parametro alfanumericos"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
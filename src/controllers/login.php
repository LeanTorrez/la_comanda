<?php
include_once __DIR__."/../utils/autenticadorJWT.php";
include_once __DIR__."/../entidades/empleado.php";

class Loggin{

    public function Login($request, $response, $args){
        $parametros = $request->getParsedBody();
        $email = $parametros["email"];
        $clave = $parametros["clave"];
        $empleado = Empleado::Verificar($email,$clave);
        if($empleado !== false){
            $token = AutentificadorJWT::CrearToken($empleado);
            $payload = json_encode(array('jwt' => $token));
        }else{
            $payload = json_encode(array("Error" => "Contraseña o Email es erroneo"));
            $response->withStatus(424,"ERROR"); 
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

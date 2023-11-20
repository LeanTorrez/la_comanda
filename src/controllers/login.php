<?php
include_once __DIR__."/../utils/autenticadorJWT.php";
include_once __DIR__."/../entidades/empleado.php";

class Loggin{

    /**
     * POST entra los datos del usuario y se comparan con los existentes en la bd,
     * si existe se crea un JWT con sus datos id,nombre y rol
     */
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

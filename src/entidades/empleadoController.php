<?php
include_once __DIR__."/../Interfaces/IApiUsable.php";
include_once __DIR__."/../db/accesoDatos.php";
include_once "empleado.php";

class UsuarioController implements IApiUsable
{
    public $_id;
    public $_nombre;
    public $_email;
    public $_clave;

    public function TraerTodos($request, $response, $args)
    {  
        
        $lista = Empleado::ObtenerTodos();
        if(!is_array($lista)){
            $payload = json_encode(array("Error" => false));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array("empleados" => $lista));
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        //VERIFICAR PARAMETROS DESPUES
        $nombre =  $parametros["nombre"];
        $clave =  $parametros["clave"];
        $email =  $parametros["email"];
        $rol = $parametros["rol"];

        $empleado = new Empleado();
        $empleado->nombre = $nombre;
        $empleado->clave = $clave;
        $empleado->email = $email;
        $empleado->rol = $rol;
        $retorno = $empleado->Insertar();

        if($retorno === 0){
            $payload = json_encode(array("Error" => "Error al intentar Insertar"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array('Exito' => "Se inserto al usuario Correctamente"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

}
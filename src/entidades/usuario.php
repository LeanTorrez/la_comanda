<?php
include_once __DIR__."/../Interfaces/IApiUsable.php";
include_once __DIR__."/../db/accesoDatos.php";
include_once "mozo.php";
include_once "bartender.php";
include_once "cervecero.php";
include_once "cocinero.php";
include_once "socio.php";

class UsuarioController implements IApiUsable
{
    public $_id;
    public $_nombre;
    public $_email;
    public $_clave;

    public function TraerTodos($request, $response, $args)
    {  
        $tipo = $request->getQueryParams()["tipoUsuario"];
        $lista = array();

        switch(strtolower($tipo)){
            case "mozo":
                $lista = Mozo::ObtenerTodos();
                break;
            case "bartender":
                $lista = Bartender::ObtenerTodos();
                break;
            case "cocinero":
                $lista = Cocinero::ObtenerTodos();
                break;
            case "cervecero":
                $lista = Cervecero::ObtenerTodos();
                break;
            case "socio":
                $lista = Socio::ObtenerTodos();
                break;
            default:
                $lista = false;
                break;
        }

        if(!is_array($lista)){
            $payload = json_encode(array("Error" => false));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array($tipo => $lista));
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros["tipoUsuario"];

        //VERIFICAR PARAMETROS DESPUES
        $nombre =  $parametros["nombre"];
        $clave =  $parametros["clave"];
        $email =  $parametros["email"];

        ///MEJORAR DESPUES
        switch(strtolower($usuario)){
            case "mozo":
                $mozo = new Mozo();
                $mozo->SetDatos($nombre,$email,$clave);
                $retorno = $mozo->Insertar();
                break;
            case "bartender":
                $bartender = new Bartender();
                $bartender->SetDatos($nombre,$email,$clave);
                $retorno = $bartender->Insertar();
                break;
            case "cocinero":
                $cocinero = new Cocinero();
                $cocinero->SetDatos($nombre,$email,$clave);
                $retorno = $cocinero->Insertar();
                break;
            case "cervecero":
                $cervecero = new Cervecero();
                $cervecero->SetDatos($nombre,$email,$clave);
                $retorno = $cervecero->Insertar();
                break;
            case "socio":
                $socio = new Socio();
                $socio->SetDatos($nombre,$email,$clave);;
                $retorno = $socio->Insertar();
                break;
            default:
                $retorno = 0;
                break;
        }

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
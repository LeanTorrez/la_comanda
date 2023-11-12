<?php
include_once "mesa.php";
class MesaController{

    public function TraerTodos($request, $response, $args){
        $lista = Mesa::ObtenerTodos();
        if($lista !== false){
            $payload = json_encode(array('Mesa' => $lista));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload);
        }else{
            $payload = json_encode(array("Error" => "Error al mostrar la lista"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
	public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $estado = $parametros["estado"];

        $mesa = new Mesa();
        $mesa->estado = $estado;
        $retorno = $mesa->Insertar();

        if($retorno === 0){
            $payload = json_encode(array("Error" => "Error al intentar Insertar"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array('Exito' => "Se inserto la nueva mesa"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
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

    public function TraerUno($request, $response, $args){
        $id = $request->getQueryParams()["id"];
        $empleado = Mesa::ObtenerUno($id);
        //si no lo encuentra devuelve array vacio ARREGLAR
        if(is_array($empleado)){
            $payload = json_encode(array("Mesa" => $empleado)); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Id Inexistente"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args){
        $id = $request->getQueryParams()["id"];
        $retorno = Mesa::Borrar($id);
        if($retorno !== 0){
            $payload = json_encode(array("Exito" => "Se elimino la mesa")); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "La mesa no existe"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
   
    public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->id = $parametros["id"];
        $mesa->estado = $parametros["estado"];
        $mesa->id_mozo = $parametros["id_mozo"];
        $mesa->id_pedido = $parametros["id_pedido"];

        $retorno = $mesa->Modificar();

        if($retorno){
            $payload = json_encode(array('Exito' => "Se modifico Correctamente"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Error al modificar"));
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
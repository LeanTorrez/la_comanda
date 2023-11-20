<?php
include_once __DIR__."/../entidades/mesa.php";
include_once __DIR__."/../entidades/productosPedidos.php";
include_once __DIR__."/../entidades/encuesta.php";
use \Slim\Psr7\Factory\StreamFactory;

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
        $mesa = Mesa::ObtenerUno($id);
        //si no lo encuentra devuelve array vacio ARREGLAR
        if(is_array($mesa)){
            $payload = json_encode(array("Mesa" => $mesa)); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Id Inexistente"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function MesaMasUsada($request, $response, $args){
        $mesa = Mesa::ObtenerMasUsada();
        if($mesa instanceof stdClass){
            $payload = json_encode(array("Mesa" => $mesa)); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Id Inexistente"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Foto($request, $response, $args){
        $parametros = $request->getParsedBody();
        $id = $parametros["id"];
        $alfanumerico = $parametros["alfanumerico"];
        $foto = $request->getUploadedFiles()["foto"];
        
        $retorno = Mesa::Foto($id, $alfanumerico, $foto);

        if( $retorno){
            $payload = json_encode(array("Exito" => "Se agrego existosamente la foto")); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Error: al subir la foto al servidor"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CobrarMesa($request, $response, $args){
        $alfanumerico = $request->getQueryParams()["alfanumerico"];
        $costo = ProductoPedido::ObtenerCosto($alfanumerico);
        if(isset($costo->costo)){
            $payload = json_encode(array("Exito" => "La cuenta es de {$costo->costo}")); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Existio un error en el calculo de la cuenta su comida es gratis"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Encuesta($request, $response, $args){
        $parametros = $request->getParsedBody();
        $id_mesa = $parametros["id"];
        $puntuacion = $parametros["puntuacion"];
        $alfanumerico = $parametros["alfanumerico"];
        $comentario = $parametros["comentario"];

        $encuesta = new Encuesta($alfanumerico,$id_mesa,$puntuacion,$comentario);
        $retorno = $encuesta->Insertar();
        if($retorno !== 0){
            $payload = json_encode(array("Exito" => "Muchas gracias por la encuesta")); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Error al guardar la encuesta"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Comentarios($request, $response, $args){
        $lista = Encuesta::MejoresPuntuaciones();
        if($lista !== false){
            $payload = json_encode(array("Exito" => $lista)); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Error en cargar las puntuaciones"));
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

    public function Descargar($request, $response, $args){
        $str = Archivos::BaseDatosCSV(array("Mesa","ObtenerTodos"),array("id", "estado", "id_mozo", "id_pedido"));
        if($str !== false){
            $streamFactory = new StreamFactory();
            $stream = $streamFactory->createStreamFromFile($str);
            $response->withStatus(200,"Exito");
            unlink($str);
            $response = $response->withHeader('Content-Type', 'text/csv');
            $response = $response->withHeader('Content-Disposition', 'attachment;filename=mesa.csv');
            return $response->withBody($stream);
        }else{
            $payload = json_encode(array("Error" => "error en la descarga del archivo"));
            $response->withStatus(404,"Error");
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstado($request, $response, $args){
        $parametros = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->id = $parametros["id"];
        $mesa->estado = $parametros["estado"];
        
        $retorno = $mesa->ModificarEstado();
        
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
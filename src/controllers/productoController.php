<?php
include_once __DIR__."/../entidades/producto.php";
use \Slim\Psr7\Factory\StreamFactory;

class ProductoController{

    public function TraerTodos($request, $response, $args){
        $lista = Producto::ObtenerTodos();
        
        if(!is_array($lista)){
            $payload = json_encode(array("Error" => false));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array("Productos" => $lista));
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Descargar($request, $response, $args){
        $str = Archivos::BaseDatosCSV(array("Producto","ObtenerTodos"),array("id", "nombre", "tipo", "precio", "tiempoPreparacion", "cantidadVendida"));
        if($str !== false){
            $streamFactory = new StreamFactory();
            $stream = $streamFactory->createStreamFromFile($str);
            $response->withStatus(200,"Exito");
            unlink($str);
            $response = $response->withHeader('Content-Type', 'text/csv');
            $response = $response->withHeader('Content-Disposition', 'attachment;filename=producto.csv');
            return $response->withBody($stream);
        }else{
            $payload = json_encode(array("Error" => "error en la descarga del archivo"));
            $response->withStatus(404,"Error");
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args){
        $id = $request->getQueryParams()["id"];
        $producto = Producto::ObtenerUno($id);
        //si no lo encuentra devuelve array vacio ARREGLAR
        if(is_array($producto)){
            $payload = json_encode(array("Producto" => $producto)); 
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
        $retorno = Producto::Borrar($id);
        if($retorno !== 0){
            $payload = json_encode(array("Exito" => "Se elimino el producto")); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "El producto no existe"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
   
    public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $producto = new Producto();
        $producto->id = $parametros["id"];
        $producto->nombre = $parametros["nombre"];
        $producto->tipo = $parametros["tipo"];
        $producto->precio = $parametros["precio"];
        $producto->tiempoPreparacion = $parametros["tiempoPreparacion"];

        $retorno = $producto->Modificar();

        if($retorno){
            $payload = json_encode(array('Exito' => "Se Modifico el producto con exito"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Error al Modificar"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload); 
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $nombre = $parametros["nombre"];
        $tipo = $parametros["tipo"];
        $precio = $parametros["precio"];
        $tiempoPreparacion = $parametros["tiempoPreparacion"];

        $producto = new Producto();

        $producto->nombre = $nombre;
        $producto->tipo = $tipo;
        $producto->precio = $precio;
        $producto->tiempoPreparacion = $tiempoPreparacion;
        $producto->cantidadVendida = 0;

        $retorno = $producto->Insertar();

        if($retorno === 0){
            $payload = json_encode(array("Error" => "Error al intentar Insertar"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array('Exito' => "Se inserto el producto correctamente"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
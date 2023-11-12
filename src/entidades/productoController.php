<?php
include_once "producto.php";

class ProductoController{

    public function TraerTodos($request, $response, $args){
        $tipo = $request->getQueryParams()["tipo"];
        $lista = Producto::ObtenerTodos($tipo);
        
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
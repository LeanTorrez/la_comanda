<?php
include_once __DIR__."/../entidades/producto.php";
use \Slim\Psr7\Factory\StreamFactory;

class ProductoController{

    /**
     * Trae todas los producto existentes en la bd
     */
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

    /**
     * Descarga la informacion del bd Mesa a un csv y lo entrega por el body
     * 'USAR en postman la opcion "send and download" para obtener el archivo'
     */
    public function Descargar($request, $response, $args){
        /* Primer argumento es un callback, en este caso llama al metodo estatico ObtenerTodos de la clase
           Empleado, segundo argumento los nombres de las columnas que seran el primer renglon del CSV  
        */
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

    /* 
    Por get entra el id del producto que se quiere buscar y se lo retorna
    */
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

    /**
     * Entra el id por GET y se borra (soft-delete) de la base de datos respectivas
     */
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
   
    /**
     * Entra los datos mas importantes por PUT y se modifican en la base de datos
     * con su ID respectiva
     */
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

    /* 
    Maneja el CSV pasado por POST y lo Insertar en la base de datos 
    */
    public function SubirCSV($request, $response, $args){
        $file = $request->getUploadedFiles()["csv"];
        $retorno = Archivos::CSVBaseDatos($file,array("Producto","Instanciar"));
        if($retorno){
            $payload = json_encode(array("Exito" => "Exito al cargar los usuarios a la BD"));
            $response->withStatus(200,"Exito");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array("Error" => "ERROR al subir los datos al BD"));
            $response->withStatus(404,"Error");
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Entra por POST con los datos del nuevo empleado que se inserta a la base de datos
     */
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
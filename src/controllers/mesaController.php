<?php
include_once __DIR__."/../entidades/mesa.php";
include_once __DIR__."/../entidades/productosPedidos.php";
include_once __DIR__."/../entidades/encuesta.php";
use \Slim\Psr7\Factory\StreamFactory;

class MesaController{

    /**
     * Trae todas las mesas existentes en la bd
     */
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

    /* 
    Por get entra el id del empleado que se quiere buscar y se lo retorna
    */
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

    /**
     * Trae la mesa mas usada desde la base de datos
     */
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

    /**
     * Obtiene la foto por POST y la guarda en la server, y su ruta en la bd
     */
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

    /**
     * Obtiene el costo de la mesa con el alfanumerico que entra por Query GET
     */
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

    /**
     * POST sube los datos de la encuesta dada al cliente, a la bd Encuestas
     */
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

    /**
     * Obtiene los mejores comentarios de la bd encuesta ordenados descendientemente
     */
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

    /**
     * Entra el id por GET y se borra (soft-delete) de la base de datos respectivas
     */
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

    /**
     * Descarga la informacion del bd Mesa a un csv y lo entrega por el body
     * 'USAR en postman la opcion "send and download" para obtener el archivo'
     */
    public function Descargar($request, $response, $args){
        /* Primer argumento es un callback, en este caso llama al metodo estatico ObtenerTodos de la clase
           Empleado, segundo argumento los nombres de las columnas que seran el primer renglon del CSV  
        */
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

    /* 
    Maneja el CSV pasado por POST y lo Insertar en la base de datos 
    */
    public function SubirCSV($request, $response, $args){
        $file = $request->getUploadedFiles()["csv"];
        $retorno = Archivos::CSVBaseDatos($file,array("Mesa","Instanciar"));
        if($retorno){
            $payload = json_encode(array("Exito" => "Exito al cargar los usuarios a la BD"));
            $response->withStatus(200,"Exito");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array("Error" => "ERRO al subir los datos al BD"));
            $response->withStatus(404,"Error");
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Modifico el estado del id que es pasado por PUT
     */
    public function ModificarEstado($request, $response, $args){
        $parametros = $request->getParsedBody();
        $mesa = new Mesa();
        $mesa->id = $parametros["id"];
        $mesa->estado = $parametros["estado"];
        
        $retorno = $mesa->ModificarEstado();
        if($mesa->estado === "con cliente comiendo"){
            //en caso que sea con cliente comiendo significa que ya se entrego la comida
            //por lo tanto se cambia el horario_entrega en pedido db
            $alfanumerico = strtolower($parametros["alfanumerico"]);
            Pedido::EntregaPedido($alfanumerico);
        }

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
   
    /**
     * Entra los datos mas importantes por PUT y se modifican en la base de datos
     * con su ID respectiva
     */
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
    
    /**
     * Entra por POST con los datos del nuevo empleado que se inserta a la base de datos
     */
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
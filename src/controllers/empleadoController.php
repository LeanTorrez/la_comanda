<?php
include_once __DIR__."/../Interfaces/IApiUsable.php";
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../entidades/empleado.php";
include_once __DIR__."/../utils/archivo.php";
use \Slim\Psr7\Factory\StreamFactory;

class UsuarioController implements IApiUsable
{
    public $_id;
    public $_nombre;
    public $_email;
    public $_clave;

    /*
    Obtiene todos los Empleados que existen en la base de datos y no estan eliminados
    */
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

    /* 
    Maneja el CSV pasado por POST y lo Insertar en la base de datos 
    */
    public function SubirCSV($request, $response, $args){
        $file = $request->getUploadedFiles()["csv"];
        $retorno = Archivos::CSVBaseDatos($file,array("Empleado","Instanciar"));
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
     * Descarga la informacion del bd Mesa a un csv y lo entrega por el body
     * 'USAR en postman la opcion "send and download" para obtener el archivo'
     */
    public function Descargar($request, $response, $args){
        /* Primer argumento es un callback, en este caso llama al metodo estatico ObtenerTodos de la clase
           Empleado, segundo argumento los nombres de las columnas que seran el primer renglon del CSV  
        */
        $str = Archivos::BaseDatosCSV(array("Empleado","ObtenerTodos"),array("id", "nombre", "email", "clave", "rol"));

        if($str !== false){
            $streamFactory = new StreamFactory();
            $stream = $streamFactory->createStreamFromFile($str);
            $response->withStatus(200,"Exito");
            unlink($str);
            $response = $response->withHeader('Content-Type', 'text/csv');
            $response = $response->withHeader('Content-Disposition', 'attachment;filename=empleados.csv');
            return $response->withBody($stream);
        }else{
            $payload = json_encode(array("Error" => "error en la descarga del archivo"));
            $response->withStatus(404,"Error");
            $response->getBody()->write($payload);    
        }
        return $response->withHeader('Content-Type', 'application/json');
    }


    /* 
    Por get entra el id del empleado que se quiere buscar y se lo retorna
    */
    public function TraerUno($request, $response, $args){
        $id = $request->getQueryParams()["id"];
        $empleado = Empleado::ObtenerUno($id);
        //si no lo encuentra devuelve array vacio ARREGLAR
        if(is_array($empleado)){
            $payload = json_encode(array("empleados" => $empleado)); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "No se encontre el empleado"));
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
        $retorno = Empleado::Borrar($id);
        if($retorno !== 0){
            $payload = json_encode(array("Exito" => "Se elimino el empleado exitosamente")); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "No se encontre el empleado"));
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
        $empleado = new Empleado();
        $empleado->id = $parametros["id"];
        $empleado->nombre = $parametros["nombre"];
        $empleado->clave = $parametros["clave"];
        $empleado->email = $parametros["email"];
        $empleado->rol =$parametros["rol"];

        $retorno = $empleado->Modificar();

        if($retorno){
            $payload = json_encode(array('Exito' => "Se inserto al usuario Correctamente"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Error al intentar Insertar"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload); 
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Entra por POST con los datos del nuevo empleado que se inserta a la base de datos
     */
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

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
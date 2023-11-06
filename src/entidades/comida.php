<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";

class Comida implements IPdoUsable{
    
    public $id;
    public $nombre;
    public $precio;
    public $cantidadVendida;
    public $tiempoPreparacion;

    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $nombre = $parametros["nombre"];
        $precio = $parametros["precio"];
        $tiempoPreparacion = $parametros["tiempoPreparacion"];

        $comida = new Comida();
        $comida->nombre = $nombre;
        $comida->precio = $precio;
        $comida->tiempoPreparacion = $tiempoPreparacion;
        $comida->cantidadVendida = 0;

        $retorno = $comida->Insertar();

        if($retorno === 0){
            $payload = json_encode(array("Error" => "Error al intentar Insertar"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array('Exito' => "Se inserto la Comida Correctamente"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, precio, tiempoPreparacion, cantidadVendida FROM comidas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Comida");
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO comidas ( nombre, precio, tiempoPreparacion, cantidadVendida) 
        VALUES (:nombre, :precio, :tiempoPreparacion, :cantidadVendida)");

        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $this->precio , PDO::PARAM_STR);
        $consulta->bindValue(":tiempoPreparacion", $this->tiempoPreparacion , PDO::PARAM_INT);
        $consulta->bindValue(":cantidadVendida", $this->cantidadVendida , PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
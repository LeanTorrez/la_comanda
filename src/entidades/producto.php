<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";

class Producto implements IPdoUsable{
    
    public $id;
    public $nombre;
    public $tipo;
    public $precio;
    public $cantidadVendida;
    public $tiempoPreparacion;
    
    public static function ObtenerTodos($tipo = "coctel"){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, tipo, precio, tiempoPreparacion, cantidadVendida FROM productos WHERE tipo = :tipo");
        $consulta->bindValue(":tipo", $tipo);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO productos ( nombre, tipo, precio, tiempoPreparacion, cantidadVendida) 
        VALUES (:nombre, :tipo, :precio, :tiempoPreparacion, :cantidadVendida)");

        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":tipo", $this->tipo,PDO::PARAM_STR);
        $consulta->bindValue(":precio", $this->precio , PDO::PARAM_STR);
        $consulta->bindValue(":tiempoPreparacion", $this->tiempoPreparacion , PDO::PARAM_INT);
        $consulta->bindValue(":cantidadVendida", $this->cantidadVendida , PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
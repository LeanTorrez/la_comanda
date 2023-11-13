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
    
    public static function NuevoPedido($arrayPlatos){
        $retorno = 0;
        foreach($arrayPlatos as $plato){
            $retorno = $plato->ActualizarCantidadVendidad();
            if($retorno === 0){
                break;
            }
        }
        return $retorno;
    }

    public static function ObtenerPlatos($platos){
        $strPlatos = self::ParsePlatos($platos);
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, tipo, tiempoPreparacion FROM productos WHERE nombre IN ($platos)");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    static function ParsePlatos($arrayPlatos){
        return "'".$arrayPlatos[0]."','".$arrayPlatos[1]."','".$arrayPlatos[2]."','".$arrayPlatos[3]."'";
    }

    public function ActualizarCantidadVendidad(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE productos SET cantidadVendida = cantidadVendida + 1 WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }


    public static function ObtenerTodos($tipo = "coctel"){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, tipo, precio, tiempoPreparacion, cantidadVendida FROM productos WHERE tipo = :tipo");
        $consulta->bindValue(":tipo", $tipo);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    public static function ObtenerUno($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, tipo, precio, tiempoPreparacion, cantidadVendida FROM productos WHERE id = :id LIMIT 1");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    public static function Borrar($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("DELETE FROM productos WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    public function Modificar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE productos SET nombre = :nombre, tipo = :tipo, precio = :precio, tiempoPreparacion = :tiempoPreparacion WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":tipo", $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(":tiempoPreparacion", $this->tiempoPreparacion, PDO::PARAM_STR);
        return $consulta->execute();
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
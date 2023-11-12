<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";
class Mesa implements IPdoUsable{
    public $id;
    public $estado;
    public $id_mozo;
    public $id_pedido;

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function IdMesaDisponible(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id FROM mesas WHERE estado = 'disponible' LIMIT 1");
        $consulta->execute();
        return $consulta->fetchObject();
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO mesas ( estado ) VALUES (:estado)");
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
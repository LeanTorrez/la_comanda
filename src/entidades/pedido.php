<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";
include_once "mesa.php";

class Pedido implements IPdoUsable{

    public $alfanumerico;
    public $nombre;
    public $mozo_id;
    public $platos;
    public $tiempo_estimado;
    public $fecha_emision;
    public $fecha_entrega;

    public static function FormatoPlatos($lista){

        foreach($lista as $pedido){
            $formato = array();
            $parser = explode(",",$pedido->platos);
            for($i = 1;$i<count($parser);$i++){
                $formato["plato".$i] = $parser[$i - 1];
            }
            $pedido->platos = $formato;
        }
        return $lista;
    }

    public static function Borrar($alfanumerico){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE pedidos SET es_eliminado = :es_eliminado
        WHERE alfanumerico = :alfanumerico");
        $consulta->bindValue(":es_eliminado", 1, PDO::PARAM_INT);
        $consulta->bindValue(":alfanumerico", $alfanumerico, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT pedidos.alfanumerico, pedidos.nombre, 
        (SELECT GROUP_CONCAT(productos_pedidos.nombre_producto) 
        FROM productos_pedidos 
        WHERE productos_pedidos.alfanumerico = pedidos.alfanumerico ) AS platos, horario_estimado, horario_entrega FROM pedidos
        WHERE es_eliminado = 0");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "stdClass");
    }

    public static function ObtenerTodosRol($rol){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT pedidos.alfanumerico, pedidos.nombre, productos_pedidos.id_producto, productos_pedidos.nombre_producto AS plato, 
        productos_pedidos.estado, horario_estimado, horario_entrega 
        FROM pedidos 
        INNER JOIN productos_pedidos ON productos_pedidos.alfanumerico = pedidos.alfanumerico 
        WHERE productos_pedidos.tipo_producto = :rol AND pedidos.es_eliminado = 0");
        $consulta->bindValue(":rol", $rol, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "stdClass");
    }

    //cambiar comportamiento de tiempo estimado
    public static function ObtenerPedidosMozo($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT pedidos.alfanumerico, pedidos.nombre, productos_pedidos.nombre_producto AS plato, productos_pedidos.estado 
        FROM pedidos 
        INNER JOIN productos_pedidos ON productos_pedidos.alfanumerico = pedidos.alfanumerico 
        WHERE pedidos.mozo_id = :id AND pedidos.es_eliminado = 0;");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "stdClass");
    }


    public static function ObtenerUno($alfanumerico){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT pedidos.alfanumerico, pedidos.nombre, 
        (SELECT GROUP_CONCAT(productos_pedidos.nombre_producto) 
        FROM productos_pedidos 
        WHERE productos_pedidos.alfanumerico = pedidos.alfanumerico ) AS platos, horario_estimado, horario_entrega 
        FROM pedidos 
        WHERE pedidos.alfanumerico = :alfanumerico AND es_eliminado = 0 LIMIT 1" );
        $consulta->bindValue(":alfanumerico", $alfanumerico, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "stdClass");
    }
    
    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO pedidos ( alfanumerico, nombre, mozo_id, horario_estimado ) 
        VALUES (:alfanumerico, :nombre, :mozo_id , DATE_ADD(:fecha_emision,INTERVAL '{$this->tiempo_estimado}' MINUTE))");
        $consulta->bindValue(":alfanumerico", $this->alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":mozo_id", $this->mozo_id, PDO::PARAM_STR);
        $consulta->bindValue(":fecha_emision", date("Y-m-d h:i:s"), PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    public function CodigoCliente(){
        $letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $numeros = "12345678998765432112345678";
        $array = array();
        for($i = 0;$i < 5;$i++){
            $index = rand(0,24);
            $array[] = $i % 2 === 0 ? $letras[$index] : $numeros[$index];
        }
        return implode($array);
    }
}
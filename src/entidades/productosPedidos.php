<?php
include_once __DIR__."/../db/accesoDatos.php";
class ProductoPedido{
    public $id;
    public $alfanumerico;
    public $id_producto;
    public $tipo_producto;
    public $nombre_producto;
    public $estado;

    public static function PedidosInsertar($alfanumerico,$arrayProductos){
        $retorno = true;
        foreach($arrayProductos as $producto){
            $productoPedido = new ProductoPedido();
            $productoPedido->alfanumerico = $alfanumerico;
            $productoPedido->id_producto = $producto->id;
            $productoPedido->tipo_producto = $producto->tipo;
            $productoPedido->nombre_producto = $producto->nombre;

            $r = $productoPedido->Insertar();
            if($r === 0){
                $retorno = false;
                break;
            }
        }
        return $retorno;
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO productos_pedidos ( alfanumerico, id_producto, tipo_producto, nombre_producto) 
        VALUES (:alfanumerico, :id_producto, :tipo_producto, :nombre_producto) ");
        $consulta->bindValue(":alfanumerico", $this->alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":id_producto", $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(":tipo_producto", $this->tipo_producto, PDO::PARAM_STR);
        $consulta->bindValue(":nombre_producto", $this->nombre_producto, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
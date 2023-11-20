<?php
include_once __DIR__."/../db/accesoDatos.php";
class ProductoPedido{
    public $id;
    public $alfanumerico;
    public $id_producto;
    public $tipo_producto;
    public $nombre_producto;
    public $estado;

    /**
     * Crea instancia de productos_pedidos en la alta de pedidos, y se los guarda individualmente 
     * en la base de datos
     * @param alfanumerico
     * identificador del pedido
     * @param arrayProductos
     * lista de productos que seran insertados en la bd
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
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

    /**
     * Suma los valores que tengan en comun el alfanumerico, y lo retorna
     * 
     * @param alfanumerico
     * el codigo que sera buscado en la bd
     * @return
     * retorna el monto que se tiene que pagar ese pedido
     */
    public static function ObtenerCosto($alfanumerico){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT SUM(productos.precio) AS costo 
        FROM productos_pedidos 
        INNER JOIN productos ON productos.id = productos_pedidos.id_producto 
        WHERE productos_pedidos.alfanumerico = :alfanumerico");       
        $consulta->bindValue(":alfanumerico", $alfanumerico, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject();
    }

    /**
     * Modifica el estado de un producto_pedido en la bd por su alfanumerico y id_producto
     * 
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
    public function ModificarEstado(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE productos_pedidos 
        SET estado = :estado
        WHERE id_producto = :id_producto AND alfanumerico = :alfanumerico");
        $consulta->bindValue(":id_producto", $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(":alfanumerico", $this->alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        return $consulta->execute();
    }

    /**
     * Se modifica un producto_pedido del id respectivo que se manda
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
    public function Modificar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE productos_pedidos 
        SET nombre_producto = :nombre_producto, tipo_producto = :tipo_producto, id_producto = :id_producto
        WHERE id = :id AND alfanumerico = :alfanumerico");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":alfanumerico", $this->alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":id_producto", $this->id_producto, PDO::PARAM_STR);
        $consulta->bindValue(":nombre_producto", $this->nombre_producto, PDO::PARAM_STR);
        $consulta->bindValue(":tipo_producto", $this->tipo_producto, PDO::PARAM_STR);
        return $consulta->execute();
    }

    /**
     * Inserta el nuevo producto_pedido a la bd
     * 
     * @return int
     * retorna el id que fue insertado
     */
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
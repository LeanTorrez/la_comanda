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

    /**
     * Se parsean los platos de los pedidos que estan separados por ,
     * y se crean como arrays asociativos para cada plato
     * 
     *  @param lista
     * lista de los pedidos
     * @return array
     * retorna el array con los string de los platos
     */
    public static function FormatoPlatos($lista){
        foreach($lista as $pedido){
            $formato = array();
            $parser = explode(",",$pedido->platos);
            for($i = 0;$i<count($parser);$i++){
                $formato["plato".$i+1] = $parser[$i];
            }
            $pedido->platos = $formato;
        }
        return $lista;
    }

    /**
     * Elimina el pedido con id pasado por parametro
     * 
     * @param id
     * El id del pedido que sera borrado de la bd (soft-delete)
     * @return 
     * retorna el ultimo id eliminado
     */
    public static function Borrar($alfanumerico){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE pedidos SET es_eliminado = :es_eliminado
        WHERE alfanumerico = :alfanumerico");
        $consulta->bindValue(":es_eliminado", 1, PDO::PARAM_INT);
        $consulta->bindValue(":alfanumerico", $alfanumerico, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    /**
     * Obtiene todos los datos de la base de datos de pedidos
     * @return 
     * retorna todas las instancias de los pedidos en la Base de datos
     */
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

    /**
     * cuando cambia el estado de la mesa a cliente comiendo, se registra el horario de la entrega de mismo
     * en la db
     * @return bool
     * retorno true en caso de exito, false en caso fallido
     */
    public static function EntregaPedido($alfanumerico){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE pedidos SET horario_entrega = :horario_entrega
        WHERE alfanumerico = :alfanumerico");
        $consulta->bindValue(":alfanumerico", $alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":horario_entrega",date("Y-m-d h:i:s"),PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->execute();
    }

    /**
     * Obtiene los datos de la bd que macheen con ese rol, basandose en el tipo del producto
     * 
     * @param rol
     * rol en cual se creara la lista de los pedidos
     * @return stdClass
     * retorna una stdclass con los diferentes pedidos
     */
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

    /**
     * Obtiene el pedido en base al id del mozo
     * 
     * @param id
     * id del pedido
     * @return stdClass
     * retorna la lista de los pedidos de este mozo en cuestion
     */
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

    /**
     * Se modifica la columna foto de la bd 
     * @param rutaFoto
     * ruta relativa de la foto
     * @return bool
     * retorna true en caso exitoso, false en case de error
     */
    public function ModificaFoto($rutaFoto){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE pedidos SET ruta_foto = :ruta_foto
        WHERE alfanumerico = :alfanumerico ");
        $consulta->bindValue(":alfanumerico", $this->alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":ruta_foto", $rutaFoto, PDO::PARAM_STR);
        return  $consulta->execute();
    }

    /**
     * Obtiene el pedido que con el id respectivo
     * 
     * @param id
     * El id del pedido que sera instanciado
     * @return 
     * retorna el pedido
     */
    public static function ObtenerUno($alfanumerico){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT pedidos.alfanumerico, pedidos.nombre, 
        (SELECT GROUP_CONCAT(productos_pedidos.nombre_producto) 
        FROM productos_pedidos 
        WHERE productos_pedidos.alfanumerico = pedidos.alfanumerico ) AS platos, horario_estimado, horario_entrega 
        FROM pedidos 
        WHERE pedidos.alfanumerico = :alfanumerico LIMIT 1" );
        $consulta->bindValue(":alfanumerico", $alfanumerico, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "stdClass");
    }
    
    /**
     * /**
     * Inserta el nuevo pedido a la bd
     * 
     * @return int
     * retorna el id que fue insertado
     */
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

    /**
     * Crea un codigo alfanumerico
     * 
     * @return string
     * retorna el string alfanumerico creado
     */
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
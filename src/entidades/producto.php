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
    
    /**
     * @param array
     * Entra el array con la lectura del csv con sus diferentes parametros
     * el mismo crea el mesa
     * @return 
     * Devuelve el mesa con sus parametros seteados
     */
    public static function Instanciar($array){
        $mesa = new Producto();
        $mesa->id = $array[0];
        $mesa->nombre = $array[1];
        $mesa->tipo = $array[2];
        $mesa->precio = $array[3];
        $mesa->tiempoPreparacion = $array[4];
        $mesa->cantidadVendida = $array[5];
        return $mesa;
    }

    /**
     * modifica la lista de los platos sumandole la cantidad vendida de los mismos
     * 
     * @param arrayPlatos
     * lista con los platos del nuevo pedido
     * 
     * @return int
     * retorna el id insertado, en caso de error 0
     */
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

    /**
     * Obtiene el numero maximo de tiempo que tiene la lista de productos
     * 
     * @param arrayProductos
     * lista de los platos
     * 
     * @return int
     * retorna el tiempo del producto con mayor preparacion
     */
    public static function ObtenerTiempoMasAlto($arrayProductos){
        foreach($arrayProductos as $producto){
            $tiempos[] = $producto->tiempoPreparacion;
        }
        return max($tiempos);
    }
    
    /**
     * Crea un array con los atributos del empleado, usado para la creacion del CSV
     */
    public function CrearArray(){
        return array($this->id, $this->nombre, $this->tipo, $this->precio, $this->tiempoPreparacion, $this->cantidadVendida);
    }

    /**
     * Obtiene las instancias de los productos que son pasados como parametros dentro 'array'
     * 
     * @param platos
     * string que contiene los nombres que seran buscados en la bd
     * @return Producto
     * retorna las instancias de los productos
     */
    public static function ObtenerPlatos($platos){
        $strPlatos = self::ParsePlatos($platos);
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, tipo, tiempoPreparacion 
        FROM productos 
        WHERE nombre IN ($strPlatos)");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    /**
     * parsea los platos para que sus nombres sean separados por una ','
     * 
     * @param arrayPlatos
     * lista de productos
     * @return string
     * retorna el string de los platos
     */
    static function ParsePlatos($arrayPlatos){
        if(is_array($arrayPlatos)){
            $str = "'";
            $contador = 0;
            foreach($arrayPlatos as $plato){
                $contador++;
                $str .= count($arrayPlatos) === $contador ? $plato."'": $plato."','";
            }
        }else{
            $str = $arrayPlatos;
        }
        return $str;
    }

    /**
     * aumenta el contador de la cantidad vendida de cierto producto en la bd
     * 
     *  @return int
     * retorna el ultimo id modificado
     */
    public function ActualizarCantidadVendidad(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE productos SET cantidadVendida = cantidadVendida + 1 WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    /**
     * Obtiene todos los datos de la base de datos de producto
     * @return Producto
     * retorna todas las instancias de los producto en la Base de datos
     */
    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, tipo, precio, tiempoPreparacion, cantidadVendida 
        FROM productos 
        WHERE es_eliminado = 0");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    /**
     * Obtiene el producto que con el id respectivo
     * 
     * @param id
     * El id del producto que sera instanciado
     * @return 
     * retorna el producto
     */
    public static function ObtenerUno($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, tipo, precio, tiempoPreparacion, cantidadVendida
        FROM productos 
        WHERE id = :id AND es_eliminado = 0 LIMIT 1");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
    }

    /**
     * Elimina el producto con id pasado por parametro
     * 
     * @param id
     * El id del producto que sera borrado de la bd (soft-delete)
     * @return 
     * retorna el ultimo id modificado
     */
    public static function Borrar($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE productos SET es_eliminado = 1
        WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    /**
     * Se modifica un producto del id respectivo que se manda
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
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

    /**
     * Inserta el nuevo producto a la bd
     * 
     * @return int
     * retorna el id que fue insertado
     */
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
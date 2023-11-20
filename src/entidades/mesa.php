<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";
include_once __DIR__."/../entidades/pedido.php";

class Mesa implements IPdoUsable{
    public $id;
    public $estado;
    public $id_mozo;
    public $id_pedido;

    /**
     * @param array
     * Entra el array con la lectura del csv con sus diferentes parametros
     * el mismo crea el mesa
     * @return 
     * Devuelve el mesa con sus parametros seteados
     */
    public static function Instanciar($array){
        $mesa = new Mesa();
        $mesa->id = $array[0];
        $mesa->estado = $array[1];
        $mesa->id_mozo = $array[2];
        $mesa->id_pedido = $array[3];
        return $mesa;
    }

    /**
     * Crea un array con los atributos del empleado, usado para la creacion del CSV
     */
    public function CrearArray(){
        return array($this->id, $this->estado, $this->id_mozo, $this->id_pedido);
    }

    /**
     * Obtiene todos los datos de la base de datos de Mesa
     * @return 
     * retorna todas las instancias de los mesa en la Base de datos
     */
    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido 
        FROM mesas
        WHERE es_eliminado = 0");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    /**
     * Mueve la foto que entra por POST y la guarda en el servidor y guarda la ruta en la bd pedidos
     * 
     * @param idMesa
     * id de la mesa que es necesaria para la creacion del nombre de la imagen
     * @param alfanumerico
     * alfanumerico para la imagen
     * @param foto
     * stream de la iamgen
     * @return bool
     * retorna true en caso exitoso, false en case de error
     */
    public static function Foto($idMesa, $alfanumerico, $foto){
        $ruta = __DIR__."/../imagenes/mesas";
        $nombre = $idMesa."-".$alfanumerico;
        $extension = pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION); 
        $filename = sprintf('%s.%0.8s', $nombre , $extension);
        $rutaRelativa = "/../imagenes/mesas/".$filename;
        $foto->moveTo($ruta.DIRECTORY_SEPARATOR.$filename);

        $pedido = new Pedido();
        $pedido->alfanumerico = $alfanumerico;

        return $pedido->ModificaFoto($rutaRelativa);
    }

    /**
     * Obtiene la mesa que con el id respectivo
     * 
     * @param id
     * El id de la emsa que sera instanciado
     * @return 
     * retorna la mesa
     */
    public static function ObtenerUno($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido 
        FROM mesas 
        WHERE id = :id AND es_eliminado = 0 LIMIT 1");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    /**
     * Obtiene la mesa que tuvo mas clientes
     * @return stdClass
     * retorna la mesa mas usada
     */
    public static function ObtenerMasUsada(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido, cantidad_usos
        FROM mesas ORDER BY cantidad_usos DESC LIMIT 1");
        $consulta->execute();
        return $consulta->fetchObject();
    }


    /**
     * Elimina la mesa con el id pasado por parametro
     * 
     * @param id
     * El id de la mesa que sera borrado de la bd (soft-delete)
     * @return 
     * retorna el ultimo id modificado
     */
    public static function Borrar($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET es_eliminado = 1
        WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    /**
     * Se modifica la mesa del id respectivo que se manda
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
    public function Modificar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET estado = :estado, id_mozo = :id_mozo, id_pedido = :id_pedido WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(":id_mozo", $this->id_mozo, PDO::PARAM_STR);
        $consulta->bindValue(":id_pedido", $this->id_pedido, PDO::PARAM_STR);
        return $consulta->execute();
    }

    /**
     * Modifica la mesa cuando ingresa un nuevo cliente en la mesa, el mismo hace su pedido y se lo
     * asigna a la mesa disponible
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
    public function ModificarNuevoCliente(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET estado = :estado, id_mozo = :id_mozo, id_pedido = :id_pedido, cantidad_usos = cantidad_usos + 1 WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(":id_mozo", $this->id_mozo, PDO::PARAM_STR);
        $consulta->bindValue(":id_pedido", $this->id_pedido, PDO::PARAM_STR);
        return $consulta->execute();
    }

    /**
     * Modifica el estado de la mesa
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
    public function ModificarEstado(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        return $consulta->execute();
    }

    /**
     * obtiene la mesa que no tiene ningun cliente
     * 
     * @return stdClass
     * retorna la clase con la id de la mesa cerrada
     */
    public static function IdMesaDisponible(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id FROM mesas WHERE estado = 'cerrada' LIMIT 1");
        $consulta->execute();
        return $consulta->fetchObject();
    }

    /**
     * Inserta el nuevo empleado a la bd
     * 
     * @return int
     * retorna el id que fue modificado
     */
    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO mesas ( estado ) VALUES (:estado)");
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
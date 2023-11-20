<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";
class Mesa implements IPdoUsable{
    public $id;
    public $estado;
    public $id_mozo;
    public $id_pedido;

    public function CrearArray(){
        return array($this->id, $this->estado, $this->id_mozo, $this->id_pedido);
    }

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido 
        FROM mesas
        WHERE es_eliminado = 0");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function Foto($idMesa, $alfanumerico, $foto){
        $ruta = __DIR__."/../imagenes/mesas";
        $nombre = $idMesa."-".$alfanumerico;
        $extension = pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION); 
        $filename = sprintf('%s.%0.8s', $nombre , $extension);
        $rutaRelativa = "/../imagenes/mesas/".$filename;
        $foto->moveTo($ruta.DIRECTORY_SEPARATOR.$filename);

        $mesa = new Mesa();
        $mesa->id_pedido = $alfanumerico;
        $mesa->id = $idMesa;

        return $mesa->ModificaFoto($rutaRelativa);
    }

    public function ModificaFoto($rutaFoto){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET ruta_foto = :ruta_foto
        WHERE id = :id AND id_pedido = :id_pedido");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":id_pedido", $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(":ruta_foto", $rutaFoto, PDO::PARAM_STR);
        return  $consulta->execute();
    }

    public static function ObtenerUno($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido 
        FROM mesas 
        WHERE id = :id AND es_eliminado = 0 LIMIT 1");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function ObtenerMasUsada(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido, cantidad_usos
        FROM mesas ORDER BY cantidad_usos DESC LIMIT 1");
        $consulta->execute();
        return $consulta->fetchObject();
    }

    public static function Borrar($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET es_eliminado = 1
        WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    public function Modificar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET estado = :estado, id_mozo = :id_mozo, id_pedido = :id_pedido WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(":id_mozo", $this->id_mozo, PDO::PARAM_STR);
        $consulta->bindValue(":id_pedido", $this->id_pedido, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function ModificarNuevoCliente(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET estado = :estado, id_mozo = :id_mozo, id_pedido = :id_pedido, cantidad_usos = cantidad_usos + 1 WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(":id_mozo", $this->id_mozo, PDO::PARAM_STR);
        $consulta->bindValue(":id_pedido", $this->id_pedido, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function ModificarEstado(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":estado", $this->estado, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public static function IdMesaDisponible(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id FROM mesas WHERE estado = 'cerrada' LIMIT 1");
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
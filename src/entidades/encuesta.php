<?php
include_once __DIR__."/../db/accesoDatos.php";
class Encuesta{
    public $alfanumerico;
    public $id_mesa;
    public $puntuacion;
    public $comentario;

    public function __construct($alfanumerico, $id_mesa, $puntuacion, $comentario)
    {
        $this->alfanumerico = $alfanumerico;
        $this->id_mesa = $id_mesa;
        $this->puntuacion = $puntuacion;
        $this->comentario = $comentario;
    }

    /**
     * Obtiene todas las encuestas ordenadas por las mejores puntuaciones de la bd 'encuestas'
     * 
     * @return stdClass
     * Retorna las clases de de las puntuaciones
     */
    public static function MejoresPuntuaciones(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT puntuacion, comentario 
        FROM encuesta ORDER BY puntuacion DESC");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "stdClass");
    }

    /**
     * Agrega las encuestas a la bd con sus respectivos datos
     * 
     * @return int
     * retorna el ultimo id agregado
     */
    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO encuesta ( alfanumerico, id_mesa, puntuacion, comentario) 
        VALUES (:alfanumerico, :id_mesa, :puntuacion, :comentario)");
        $consulta->bindValue(":alfanumerico", $this->alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":id_mesa", $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(":puntuacion", $this->puntuacion, PDO::PARAM_INT);
        $consulta->bindValue(":comentario", $this->comentario, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
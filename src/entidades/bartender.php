<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once "trabajador.php";

class Bartender extends Trabajador{
    
    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, email, clave FROM bartenders");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Bartender");
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO bartenders ( nombre, email, clave) VALUES (:nombre, :email, :clave)");
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":email", $this->email, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $this->clave, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
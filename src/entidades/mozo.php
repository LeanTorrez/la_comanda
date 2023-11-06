<?php
//include_once "usuario.php";
include_once __DIR__."/../db/accesoDatos.php";
include_once "trabajador.php";

class Mozo extends Trabajador{

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

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, email, clave FROM mozos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mozo");
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO mozos ( nombre, email, clave) VALUES (:nombre, :email, :clave)");
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":email", $this->email, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $this->clave, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
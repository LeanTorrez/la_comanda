<?php

use Slim\CallableResolver;

class Empleado{
    
    public $id;
    public $nombre;
    public $email;
    public $clave;
    public $rol;
    
    public static function Verificar($email,$clave){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, rol 
        FROM empleados WHERE email = :email AND clave = :clave AND es_eliminado = 0");
        $consulta->bindValue(":email",$email,PDO::PARAM_STR);
        $consulta->bindValue(":clave",$clave,PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject();
    }

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, email, clave, rol 
        FROM empleados WHERE es_eliminado = 0");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
    }
    
    public static function ObtenerUno($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, email, clave, rol 
        FROM empleados 
        WHERE id = :id AND es_eliminado = 0 LIMIT 1");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
    }

    public static function Borrar($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE empleados SET es_eliminado = 1
        WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    public function Modificar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE empleados SET nombre = :nombre, email = :email, clave = :clave, rol = :rol WHERE id = :id");
        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":email", $this->email, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(":rol", $this->rol, PDO::PARAM_STR);
        return $consulta->execute();;
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO empleados ( nombre, email, clave, rol) VALUES (:nombre, :email, :clave, :rol)");
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":email", $this->email, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(":rol", $this->rol, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }
}
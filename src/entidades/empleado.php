<?php
class Empleado{
    
    public $id;
    public $nombre;
    public $email;
    public $clave;
    public $rol;
    
    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, email, clave FROM empleados");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
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
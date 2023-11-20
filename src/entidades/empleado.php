<?php

use Slim\CallableResolver;

class Empleado{
    
    public $id;
    public $nombre;
    public $email;
    public $clave;
    public $rol;
    
    /**
     * @param array
     * Entra el array con la lectura del csv con sus diferentes parametros
     * el mismo crea el Empleado
     * @return 
     * Devuelve el empleado con sus parametros seteados
     */
    public static function Instanciar($array){
        $empleado = new Empleado();
        $empleado->id = $array[0];
        $empleado->nombre = $array[1];
        $empleado->email = $array[2];
        $empleado->clave = $array[3];
        $empleado->rol = $array[4];
        return $empleado;
    }

    /**
     * Crea un array con los atributos del empleado, usado para la creacion del CSV
     */
    public function CrearArray(){
        return array($this->id,$this->nombre,$this->email,$this->clave,$this->rol);
    }


    /**
     * Verifica la informacion pasada contra la base de datos.
     * 
     * @param email
     * email que sera enviado como parametro a la base de datos
     * @param clave
     * clave que sera enviado como parametro a la base de datos
     * @return 
     * retorna el objeto con dichas propiedades
     */
    public static function Verificar($email,$clave){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, rol 
        FROM empleados WHERE email = :email AND clave = :clave AND es_eliminado = 0");
        $consulta->bindValue(":email",$email,PDO::PARAM_STR);
        $consulta->bindValue(":clave",$clave,PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject();
    }

    /**
     * Obtiene todos los datos de la base de datos de empleados
     * @return 
     * retorna todas las instancias de los empleados en la Base de datos
     */
    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, email, clave, rol 
        FROM empleados WHERE es_eliminado = 0");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
    }
    
    /**
     * Obtiene el empleado que con el id respectivo
     * 
     * @param id
     * El id del empleado que sera instanciado
     * @return 
     * retorna el empleado
     */
    public static function ObtenerUno($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, nombre, email, clave, rol 
        FROM empleados 
        WHERE id = :id AND es_eliminado = 0 LIMIT 1");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");
    }

    /**
     * Elimina el empleado con id pasado por parametro
     * 
     * @param id
     * El id del empleado que sera borrado de la bd (soft-delete)
     * @return 
     * retorna el ultimo id modificado
     */
    public static function Borrar($id){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("UPDATE empleados SET es_eliminado = 1
        WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

    /**
     * Se modifica un empleado del id respectivo que se manda
     * @return bool
     * retorna true si fue exitoso, false si hubo un error
     */
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

    /**
     * Inserta el nuevo empleado a la bd
     * 
     * @return int
     * retorna el id que fue insertado
     */
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
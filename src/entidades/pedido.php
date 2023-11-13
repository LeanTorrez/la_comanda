<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";
include_once "mesa.php";

class Pedido implements IPdoUsable{

    public $alfanumerico;
    public $nombre;
    public $plato_uno;
    public $plato_dos;
    public $plato_tres;
    public $plato_cuatro;
    public $tiempo_estimado;
    public $fecha_emision;
    public $fecha_entrega;
    public $estado_individual;
    public $estado_general;

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT alfanumerico, nombre, plato_uno, plato_dos, plato_tres, plato_cuatro, tiempo_estimado, fecha_emision, fecha_entrega, estado_individual, estado_general FROM pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function ObtenerTodosRol($rol){
        $tipo = "";
        switch($rol){
            case "cocinero":
                $tipo = "comida";           
                break;
            case "bartender";
                $tipo = "coctel";
                break;
            case "cervecero";
                $tipo = "cerveza";
                break;
        }
        $lista = self::ObtenerTodosColumnas($tipo,"plato_uno");
        $lista = self::MergePedidos($lista,self::ObtenerTodosColumnas($tipo,"plato_dos"));
        $lista = self::MergePedidos($lista,self::ObtenerTodosColumnas($tipo,"plato_tres"));
        $lista = self::MergePedidos($lista,self::ObtenerTodosColumnas($tipo,"plato_cuatro"));
        return $lista;
    }

    private static function MergePedidos($lista, $listaMerge){
        if($lista !== false && $listaMerge !== false){
            if(!is_array($lista)){
                $listaNueva = array($lista);
                if(is_array($listaMerge)){
                    array_merge($listaNueva,$listaMerge);
                }else{
                    $listaNueva[] = $listaMerge;
                }
                return $listaNueva;
            }else{
                if(is_array($listaMerge)){
                    array_merge($lista,$listaMerge);
                }else{
                    $lista[] = $listaMerge;
                }
            }
        }else{
            if($listaMerge !== false){
                $lista = $listaMerge;
            }
        }
        return $lista;
    }

    public static function ObtenerTodosColumnas($tipo, $columna){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT alfanumerico, pedidos.nombre, $columna AS plato FROM pedidos INNER JOIN productos ON productos.nombre = $columna WHERE productos.tipo = '$tipo'");
        $consulta->execute();
        return $consulta->fetchObject("stdClass");
    }

    public function Insertar(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("INSERT INTO pedidos ( alfanumerico, nombre, plato_uno, plato_dos, plato_tres, plato_cuatro, tiempo_estimado, fecha_emision, estado_individual, estado_general ) 
        VALUES (:alfanumerico, :nombre, :plato_uno, :plato_dos, :plato_tres, :plato_cuatro, :tiempo_estimado, :fecha_emision, :estado_individual, :estado_general)");
        $consulta->bindValue(":alfanumerico", $this->alfanumerico, PDO::PARAM_STR);
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":plato_uno", $this->plato_uno, PDO::PARAM_STR);
        $consulta->bindValue(":plato_dos", $this->plato_dos, PDO::PARAM_STR);
        $consulta->bindValue(":plato_tres", $this->plato_tres, PDO::PARAM_STR);
        $consulta->bindValue(":plato_cuatro", $this->plato_cuatro, PDO::PARAM_STR);
        $consulta->bindValue(":tiempo_estimado", $this->tiempo_estimado, PDO::PARAM_INT);
        $consulta->bindValue(":fecha_emision", $this->fecha_emision, PDO::PARAM_STR);
        $consulta->bindValue(":estado_individual", $this->estado_individual, PDO::PARAM_STR);
        $consulta->bindValue(":estado_general", $this->estado_general, PDO::PARAM_STR);
        $consulta->execute();
        return $db->obtenerUltimoId();
    }

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
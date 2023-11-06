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

    public function TraerTodos($request, $response, $args){
        $lista = self::ObtenerTodos();
        if($lista !== false){
            $payload = json_encode(array('Pedido' => $lista));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload);
        }else{
            $payload = json_encode(array("Error" => "Error al mostrar la lista"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $nombre = $parametros["nombre"];
        $platoUno = $parametros["platoUno"];
        $platoDos = $parametros["platoDos"];
        $platoTres = $parametros["platoTres"];
        $platoCuatro = $parametros["platoCuatro"];

        $objId = Mesa::IdMesaDisponible();

        if(isset($objId->id)){
            //MEJORAR
            $pedido = new Pedido();
            $pedido->nombre =  $nombre;
            $pedido->plato_uno = $platoUno;
            $pedido->plato_dos = $platoDos;
            $pedido->plato_tres = $platoTres;
            $pedido->plato_cuatro = $platoCuatro;
            //MODIFICAR PARA TOMAR EL VALOR MAYOR ENTRE LOS PLATOS
            $pedido->tiempo_estimado = rand(5,20);
            //
            $pedido->fecha_emision = date("d-m-Y h:i:s",time());
            $pedido->estado_individual = "sin empezar";
            $pedido->estado_general = "pendiente";
            $pedido->alfanumerico = (new Mozo())->CodigoCliente();
            $retorno = $pedido->Insertar();

            if($retorno === 0){
                $payload = json_encode(array("Error" => "Erro en el alta de pedido"));
                $response->withStatus(424,"ERROR");
                $response->getBody()->write($payload);
            }else{
                $payload = json_encode(array('Exito' => "El codigo de su pedido es {$pedido->alfanumerico}"));
                $response->withStatus(200,"EXITO");
                $response->getBody()->write($payload);
            }
        }else{
            $payload = json_encode(array("Error" => "No existen Mesas disponibles"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload); 
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT alfanumerico, nombre, plato_uno, plato_dos, plato_tres, plato_cuatro, tiempo_estimado, fecha_emision, fecha_entrega, estado_individual, estado_general FROM pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
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
}
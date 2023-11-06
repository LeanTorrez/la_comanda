<?php
include_once __DIR__."/../db/accesoDatos.php";
include_once __DIR__."/../Interfaces/IPdoUsable.php";
class Mesa implements IPdoUsable{
    public $id;
    public $estado;
    public $id_mozo;
    public $id_pedido;

    public function TraerTodos($request, $response, $args){
        $lista = self::ObtenerTodos();
        if($lista !== false){
            $payload = json_encode(array('Mesa' => $lista));
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
        $estado = $parametros["estado"];

        $mesa = new Mesa();
        $mesa->estado = $estado;
        $retorno = $mesa->Insertar();

        if($retorno === 0){
            $payload = json_encode(array("Error" => "Error al intentar Insertar"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }else{
            $payload = json_encode(array('Exito' => "Se inserto la nueva mesa"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function ObtenerTodos(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id, estado, id_mozo, id_pedido FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function IdMesaDisponible(){
        $db = AccesoDatos::ObjetoInstancia();
        $consulta = $db->prepararConsulta("SELECT id FROM mesas WHERE estado = 'disponible' LIMIT 1");
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
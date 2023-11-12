<?php
include_once "pedido.php";
class PedidoController{
    public function TraerTodos($request, $response, $args){
        $lista = Pedido::ObtenerTodos();
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
            $pedido->fecha_emision = date("Y-m-d h:i:s");
            $pedido->estado_individual = "sin empezar";
            $pedido->estado_general = "pendiente";
            $pedido->alfanumerico = $pedido->CodigoCliente();
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
}
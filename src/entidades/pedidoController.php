<?php
include_once "pedido.php";
include_once "producto.php";
class PedidoController{

    public function TraerTodos($request, $response, $args){
        $rol = $request->getQueryParams()["rol"];
        switch($rol){
            case "socio":
                $lista = Pedido::ObtenerTodos();
                break;
            default:
                $lista = Pedido::ObtenerTodosRol($rol);
                break;
        }
        
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

        $arrayPedido = array($parametros["platoUno"],$parametros["platoDos"],$parametros["platoTres"],$parametros["platoCuatro"]);
        $nombre = $parametros["nombre"];

        $objId = Mesa::IdMesaDisponible();

        if(isset($objId->id)){
            //MEJORAR
            $pedido = new Pedido();
            $pedido->nombre =  $nombre;
            //MODIFICAR PARA TOMAR EL VALOR MAYOR ENTRE LOS PLATOS
            $pedido->tiempo_estimado = rand(5,20);
            //
            $pedido->fecha_emision = date("Y-m-d h:i:s");
            $pedido->estado_individual = "sin empezar";
            $pedido->estado_general = "pendiente";
            $pedido->alfanumerico = $pedido->CodigoCliente();

            $listaProductos = Producto::ObtenerPlatos(Producto::ParsePlatos($arrayPedido));
            if($listaProductos !== false){

                $pedido->plato_uno = $arrayPedido[0];
                $pedido->plato_dos = $arrayPedido[1];
                $pedido->plato_tres = $arrayPedido[2];
                $pedido->plato_cuatro = $arrayPedido[3];
    
                $retorno = $pedido->Insertar();
                $retornoDos = Producto::NuevoPedido($listaProductos); 
                //cambiar el estado de la mesa a esperar pedido
                if($retorno === 0 && $retornoDos === 0){
                    $payload = json_encode(array("Error" => "Error en el alta de pedido"));
                    $response->withStatus(424,"ERROR");
                    $response->getBody()->write($payload);
                }else{
                    $payload = json_encode(array('Exito' => "El codigo de su pedido es {$pedido->alfanumerico}"));
                    $response->withStatus(200,"EXITO");
                    $response->getBody()->write($payload);
                }

            }else{
                $payload = json_encode(array("Error" => "Los platos no existen en el sistema"));
                $response->withStatus(424,"ERROR");
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
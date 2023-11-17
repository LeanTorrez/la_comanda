<?php
include_once __DIR__."/../entidades/pedido.php";
include_once __DIR__."/../entidades/producto.php";
include_once __DIR__."/../entidades/productosPedidos.php";

class PedidoController{

    public function TraerTodos($request, $response, $args){
        $rol = $request->getQueryParams()["rol"];
        switch($rol){
            case "socio":
                $lista = Pedido::ObtenerTodos();
                $lista = Pedido::FormatoPlatos($lista);
                break;
            case "cocinero":
                $lista = Pedido::ObtenerTodosRol("comida");
                break;
            case "cervezero":
                $lista = Pedido::ObtenerTodosRol("cerveza");
                break;
            case "bartender":
                $lista = Pedido::ObtenerTodosRol("coctel");
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
        $parametros = $request->getParsedBody()["user"];
        $nombre = $parametros["nombre"];
        $platos = $parametros["platos"];

        $objId = Mesa::IdMesaDisponible();

        var_dump($objId);
        if(isset($objId->id)){
            //MEJORAR
            $listaProductos = Producto::ObtenerPlatos($platos);
            if($listaProductos !== false){

                $pedido = new Pedido();
                $pedido->nombre =  $nombre;
                $pedido->mozo_id = 1;//JWT->data id mozo
                $pedido->tiempo_estimado = Producto::ObtenerTiempoMasAlto($listaProductos);
                $pedido->alfanumerico = $pedido->CodigoCliente();

                if($pedido->Insertar() === 0){
                    $payload = json_encode(array("Error" => "Error en el alta del pedido"));
                    $response->withStatus(424,"ERROR");
                    $response->getBody()->write($payload); 
                }else{
                    if(ProductoPedido::PedidosInsertar($pedido->alfanumerico ,$listaProductos)){
                        $payload = json_encode(array('Exito' => "Se llevo a cabo el pedido, su codigo es: {$pedido->alfanumerico}"));
                        $response->withStatus(200,"EXITO");
                        $response->getBody()->write($payload);
                        //modificar Mesa en cuestion
                    }
                }             
            }else{
                $payload = json_encode(array("Error" => "Uno de los platos no existe en el menu"));
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
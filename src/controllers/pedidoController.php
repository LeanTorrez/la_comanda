<?php
include_once __DIR__."/../entidades/pedido.php";
include_once __DIR__."/../entidades/producto.php";
include_once __DIR__."/../entidades/productosPedidos.php";

class PedidoController{

    public function TraerTodos($request, $response, $args){
        $rol = $request->getQueryParams()["rol"];
        $lista = false;
        switch($rol){
            case "socio":
                $lista = Pedido::ObtenerTodos();
                $lista = Pedido::FormatoPlatos($lista);
                break;
            case "cocinero":
                $lista = Pedido::ObtenerTodosRol("comida");
                break;
            case "cervecero":
                $lista = Pedido::ObtenerTodosRol("cerveza");
                break;
            case "bartender":
                $lista = Pedido::ObtenerTodosRol("coctel");
                break;  
            }   
        if($lista !== false || $lista !== null){
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

    public function TraerUno($request, $response, $args){
        $alfanumerico = $request->getQueryParams()["alfanumerico"];

        $lista = Pedido::ObtenerUno($alfanumerico);  
        $lista = Pedido::FormatoPlatos($lista);
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

    public function BorrarUno($request, $response, $args){
        $alfanumerico = $request->getQueryParams()["alfanumerico"];
        $retorno = Pedido::Borrar($alfanumerico);
        if($retorno !== 0){
            $payload = json_encode(array("Exito" => "Se elimino el pedido")); 
            $response->withStatus(200,"Exito");  
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "El pedido no existe"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload);  
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $plato = Producto::ObtenerPlatos("'{$parametros["nombre_plato"]}'");
        if($plato !== false){
            $producto = new ProductoPedido();
            $producto->id = $parametros["id"];
            $producto->alfanumerico = $parametros["alfanumerico"];
            $producto->nombre_producto = $plato[0]->nombre;
            $producto->tipo_producto = $plato[0]->tipo;
            $retorno = $producto->Modificar();  
            if($retorno !== 0){
                $payload = json_encode(array('Exito' => "Se Modifico el producto con exito"));
                $response->withStatus(200,"EXITO");
                $response->getBody()->write($payload); 
            }else{
                $payload = json_encode(array("Error" => "Error al Modificar"));
                $response->withStatus(424,"ERROR");
                $response->getBody()->write($payload); 
            }
        }else{
            $payload = json_encode(array("Error" => "Alguno de los datos no son correctos"));
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
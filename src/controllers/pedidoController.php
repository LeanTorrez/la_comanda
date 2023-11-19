<?php
include_once __DIR__."/../entidades/pedido.php";
include_once __DIR__."/../entidades/producto.php";
include_once __DIR__."/../entidades/productosPedidos.php";
include_once __DIR__."/../utils/autenticadorJWT.php";

class PedidoController{

    public function TraerTodos($request, $response, $args){
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);   
        $data = AutentificadorJWT::ObtenerData($token);

        $lista = false;
        switch($data->rol){
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
            case "mozo":
                $lista = Pedido::ObtenerPedidosMozo($data->id);
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

    public function ModificarEstado($request, $response, $args){
        $parametros = $request->getParsedBody();

        $producto = new ProductoPedido();
        $producto->id_producto = $parametros["id"];
        $producto->alfanumerico = $parametros["alfanumerico"];
        $producto->estado = $parametros["estado"];
        $retorno = $producto->ModificarEstado();  
        if($retorno){
            $payload = json_encode(array('Exito' => "Se Modifico el estado con exito"));
            $response->withStatus(200,"EXITO");
            $response->getBody()->write($payload); 
        }else{
            $payload = json_encode(array("Error" => "Error al Modificar el estado"));
            $response->withStatus(424,"ERROR");
            $response->getBody()->write($payload); 
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $plato = Producto::ObtenerPlatos("'{$parametros["nombre"]}'");
        if($plato !== false){
            $producto = new ProductoPedido();
            $producto->id = $parametros["id"];
            $producto->alfanumerico = $parametros["alfanumerico"];
            $producto->nombre_producto = $plato[0]->nombre;
            $producto->tipo_producto = $plato[0]->tipo;
            $producto->id_producto = $plato[0]->id;
            $retorno = $producto->Modificar();  
            if($retorno){
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
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);   
        $data = AutentificadorJWT::ObtenerData($token);

        $parametros = $request->getParsedBody()["user"];
        $nombre = $parametros["nombre"];
        $platos = $parametros["platos"];

        $objId = Mesa::IdMesaDisponible();

        if(isset($objId->id)){

            $listaProductos = Producto::ObtenerPlatos($platos);
            if($listaProductos !== false){

                $pedido = new Pedido();
                $pedido->nombre =  $nombre;
                $pedido->mozo_id = $data->id;
                $pedido->tiempo_estimado = Producto::ObtenerTiempoMasAlto($listaProductos);
                $pedido->alfanumerico = $pedido->CodigoCliente();

                if($pedido->Insertar() === 0){
                    $payload = json_encode(array("Error" => "Error en el alta del pedido"));
                    $response->withStatus(424,"ERROR");
                    $response->getBody()->write($payload); 
                }else{
                    if(ProductoPedido::PedidosInsertar($pedido->alfanumerico ,$listaProductos)){
                        $mesa = new Mesa();
                        $mesa->id = $objId->id;
                        $mesa->id_mozo = $data->id;
                        $mesa->id_pedido = $pedido->alfanumerico;
                        $mesa->estado = "cliente esperando pedido";
                        
                        if($mesa->Insertar() !== 0){
                            $payload = json_encode(array('Exito' => "Se llevo a cabo el pedido, su codigo es: {$pedido->alfanumerico}"));
                            $response->withStatus(200,"EXITO");
                            $response->getBody()->write($payload);
                        }else{
                            $payload = json_encode(array("Error" => "Error en el ingreso de la mesa"));
                            $response->withStatus(424,"ERROR");
                            $response->getBody()->write($payload);
                        }
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
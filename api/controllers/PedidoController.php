<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class PedidoController implements IApiUsable
{
    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
    
        $listaProductos = $parametros['listaProductos'];
        $clienteAsignado = $parametros['clienteAsignado'];
        $estado = $parametros['estado']; 
        $preciototal = $parametros['preciototal']; 

        $pedido = new Pedido();
        $pedido->cliente_asignado = $clienteAsignado;
        $pedido->estado = $estado;
        $pedido->preciototal = $preciototal;
        
        $pedido->crearPedido($listaProductos);

        $payload = json_encode(array("mensaje" => "Pedido creado con éxito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerUno($request, $response, $args){
        $params = $request->getQueryParams();

        $idPedido = $params['idPedido'];
        $pedido = Pedido::obtenerPedido($idPedido);
        $payload = json_encode(array("Pedido: " => $pedido));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
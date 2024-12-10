<?php
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../utils/AutentificadorJWT.php';
require_once __DIR__ . '/../middlewares/ConfirmarTipo.php';


class EmpleadoController extends Empleado
{

    public function TraerEmpleadoPorID($request, $response, $args)
    {   
        $id = $args['id'];

        $empleado = Empleado::obtenerPorId($id);
        
        if ($empleado) {
            $response->getBody()->write(json_encode($empleado)); 
            return $response
                ->withHeader('Content-Type', 'application/json') 
                ->withStatus(200);  
        }
        
        $response->getBody()->write(json_encode(['error' => 'Empleado no encontrado'])); 
        return $response
            ->withHeader('Content-Type', 'application/json')  
            ->withStatus(404);  
    }


    public function cargarPedido($request, $response, $args)
    {
        $data = json_decode($request->getBody(), true);

        $pedido = new Pedido();
        $pedido->cliente_asignado = $data['cliente_asignado'];
        $pedido->estado = $data['estado'];
        //$pedido->preciototal = $data['precioTotal'];
        $pedido->mesa_asignada = $data['mesaAsignada'];
        $pedido->mozo_asignado = $data['mozoAsignado'];

        $productos = $data['productos'];
        $idPedido = $pedido->crearPedido($productos);

        
        $responseData = ['idPedido' => $idPedido];
        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function cargarFoto($request, $response, $args)
    {
        $files = $request->getUploadedFiles();
        $parametros = $request->getParsedBody();
        
        if (!isset($files['foto'])) {
            $response->getBody()->write(json_encode(['error' => 'foto no enviada']));
            return $response->withStatus(401);
        }
        else{
            $foto = $files['foto'];
            $idPedido = $parametros['idPedido'];
            $directorio = 'uploads/fotos_mesas/';
            
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $nombreArchivo = uniqid('foto_', true) .$idPedido. '.' . pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION);
            $foto->moveTo($directorio . $nombreArchivo);

            if ($idPedido) {
                if (Empleado::guardarFotoEnPedido($idPedido, $nombreArchivo)){
                    $response->getBody()->write("Foto guardada correctamente");
                }
                else{
                    $response->getBody()->write(json_encode(['error' => 'foto no guardada']));
                }
            }
            return $response->withStatus(200);
        }
    }
    public function listarProductosPendientesCocina($request, $response, $args)
    {
        $listaProductos = Producto::listarProductosPedidos('cocina');
        $response->getBody()->write(json_encode($listaProductos));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        
    }
    public function listarProductosPendientesCervezeria($request, $response, $args)
    {
        $listaProductos = Producto::listarProductosPedidos('cervezeria');
        $response->getBody()->write(json_encode($listaProductos));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        
    }
    public function listarProductosPendientesBar($request, $response, $args)
    {
        $listaProductos = Producto::listarProductosPedidos('bar');
        $response->getBody()->write(json_encode($listaProductos));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        
    }

    public function cambiarEstadoProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];
        $nombreProducto = $parametros['nombreProducto'];
        $tiempo_preparacion = $parametros['tiempo_preparacion'];

        try {
            // Llamar al método de la clase Producto para cambiar el estado
            Producto::cambiarEstado($idPedido, $nombreProducto, $tiempo_preparacion);
            Pedido::actualizarTiempoEspera($idPedido,$tiempo_preparacion);
            // Responder con éxito
            $message = ['message' => 'Estado actualizado correctamente'];
            $response->getBody()->write(json_encode($message));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            // En caso de error, devolver una respuesta de error
            $error = [
                'error' => true,
                'message' => 'Error al actualizar el estado del producto',
                'details' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }


    public function cambiarAListo($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];
        $nombreProducto = $parametros['nombreProducto'];
        //$tiempo_preparacion = $parametros['tiempo_preparacion'];

        try {
            // Llamar al método de la clase Producto para cambiar el estado
            Producto::cambiarAListo($idPedido, $nombreProducto);
            //Pedido::actualizarTiempoEspera($idPedido,$tiempo_preparacion);
            // Responder con éxito
            $message = ['message' => 'Estado actualizado correctamente'];
            $response->getBody()->write(json_encode($message));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) {
            // En caso de error, devolver una respuesta de error
            $error = [
                'error' => true,
                'message' => 'Error al actualizar el estado del producto',
                'details' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function pedidoListo($request, $response, $args){
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];
        $idmesa = $parametros['idMesa'];

        try{
            Pedido::EntregarPedido($idPedido);
            $exitomesa = Mesa::cambiarEstado($idmesa,'con cliente comiendo');
            var_dump($exitomesa);
            $payload = json_encode(array('message' => 'pedido entregado y mesa actualizada'));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        catch (Exception $e){
            $payload = array('message' => 'Error,no se pudo actualizar los datos');
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

    }

}


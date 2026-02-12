<?php
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../utils/AutentificadorJWT.php';
require_once __DIR__ . '/../middlewares/ConfirmarTipo.php';
require_once __DIR__ . '/../utils/RespuestaJson.php';
require_once __DIR__ . '/../validadores/ValidadorProductos.php';


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
        $pedido->mesa_asignada = $data['mesaAsignada'];
        $pedido->mozo_asignado = $data['mozoAsignado'];

        $productos = $data['productos'];

        if(!Empleado::ValidarMozoExistente($data['mozoAsignado']))
        {
            return RespuestaJson::Error($response,"No existe el Mozo,Verifique los campos",400);
        }
        if(!Mesa::ValidarMesaExistente($data['mesaAsignada']))
        {
            return RespuestaJson::Error($response,"No existe la Mesa,Verifique los campos",400);
        }

        foreach ($productos as $producto) 
        {
            if (!Producto::ExisteProductoPorNombre($producto['nombre'])) 
            {
                return RespuestaJson::Error($response,"No existe el producto: {$producto['nombre']}",400);
            }
        }

        $productos = $data['productos'];
        $idPedido = $pedido->crearPedido($productos);

        if($idPedido != null || isset($idPedido))
        {
            return RespuestaJson::Exito($response,"idPedido = $idPedido",200);
        }
        else
        {
        return RespuestaJson::Error($response,"No se pudo crear el pedido",400);
        }
    }


    public function cargarFoto($request, $response, $args)
    {
        $files = $request->getUploadedFiles();
        $parametros = $request->getParsedBody();
        
        if (!isset($files['foto'])) {
            return RespuestaJson::Error($response,"Foto no enviada",400);
        }
        else{
            $foto = $files['foto'];
            $idPedido = $parametros['idPedido'];
            $directorio = 'uploads/fotos_mesas/';
            
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
                if (!$idPedido) {
                    if (Empleado::guardarFotoEnPedido($idPedido, $nombreArchivo)){
                        $nombreArchivo = uniqid('foto_', true) .$idPedido. '.' . pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION);
                        $foto->moveTo($directorio . $nombreArchivo);
                        return RespuestaJson::Exito($response,"Foto guardada correctamente",200);
                    }
                    else
                    {
                        return RespuestaJson::Error($response,"Foto no guardada",400);
                    }
                }
            }
        }
        return RespuestaJson::Error($response,"Foto no guardada",400);
    }

    public function listarProductosPendientesCocina($request, $response, $args)
    {
        $listaProductos = Producto::listarProductosPedidos('cocina');
        
        if (empty($listaProductos)) {

            return RespuestaJson::Mensaje($response,"No hay productos pendientes en la cocina",200);
        }
        return RespuestaJson::Mensaje($response, $listaProductos,200);
    }

    public function listarProductosPendientesCervezeria($request, $response, $args)
    {
       $listaProductos = Producto::listarProductosPedidos('cerveceria');
        
        // Verificar si la lista de productos está vacía
        if (empty($listaProductos)) {

            return RespuestaJson::Mensaje($response,"No hay productos pendientes en la cerveceria",200);
        }
        return RespuestaJson::Mensaje($response, $listaProductos,200);
        
    }
    public function listarProductosPendientesBar($request, $response, $args)
    {
        $listaProductos = Producto::listarProductosPedidos('cocteleria');
        
        if (empty($listaProductos)) {
            return RespuestaJson::Mensaje($response,"No hay productos pendientes en la cocina",200);
        }
        return RespuestaJson::Mensaje($response, $listaProductos,200);
        
    }

    public function cambiarEstadoProducto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id_pedido = $parametros['id_pedido'];
        $nombre_producto = $parametros['nombre_producto'];
        $tiempo_preparacion = $parametros['tiempo_preparacion'];

        $producto = ["nombre_producto" => $nombre_producto,"id_pedido" => $id_pedido];
        
        if(Producto::ExisteProductoPendiente($producto))
        {
            try 
            {
                Producto::cambiarEstado($id_pedido, $nombre_producto, $tiempo_preparacion);
                Pedido::actualizarTiempoEspera($id_pedido,$tiempo_preparacion);
                return RespuestaJson::Exito($response,"Cambio el estado del producto",200);

            } catch (Exception $e) {
                return RespuestaJson::Error($response,"Error al actualizar producto",500);
            }
        }
        return RespuestaJson::Error($response,"El producto no esta pendiente",500);
        
    }


    public function cambiarAListo($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $id_pedido = $parametros['id_pedido'];
        $nombre_producto = $parametros['nombre_producto'];

        if (!ValidadorProducto::ValidarCamposProducto($parametros)) {
            return RespuestaJson::Error($response, "Complete los campos", 400);
        }
        
        try 
        {
            if(Producto::ExisteProductoPendiente($parametros))
            {
                Producto::cambiarAListo($id_pedido, $nombre_producto);
                return RespuestaJson::Exito($response,"Producto actualizado : listo para servir");
            }
            return RespuestaJson::Error($response, "el producto no esta en pendientes",200);
            
        } catch (Exception $e) {
            return RespuestaJson::Error("Error al cambiar estado a listo : $e");
        }
        
        
    }

    public static function VerPedidosListos($request, $response, $args)
    {
        $lista = Pedido::VerPedidosListos();
        if(!$lista)
        {
            return RespuestaJson::Error($response,"Error al obtener la lista",200);
        }
        return RespuestaJson::Exito($response,(array("Pedidos Listos " => $lista)),200);
        
    }


    public function PedidoListo($request, $response, $args){
        
        $parametros = $request->getParsedBody();
        $id_pedido = $parametros['id_pedido'];
        $id_mesa = $parametros['id_mesa'];

        if(Pedido::ValidarPedidoExistente($id_pedido) && Mesa::ValidarMesaExistente($id_mesa))
        {
            Pedido::EntregarPedido($id_pedido);
            Mesa::cambiarEstado($id_mesa,'con cliente comiendo');
            return RespuestaJson::Exito($response,"Pedido entregado , Cliente comiendo",200);
        }
        return RespuestaJson::Error($response,"No se puedo entregar el pedido al cliente",400);
            
        
        
    }

    public function CobrarPedido($request, $response, $args){
        $parametros = $request->getParsedBody();
        $id_pedido = $parametros['id_pedido'];
        $id_mesa = $parametros['id_mesa'];


        if(Pedido::ValidarPedidoExistente($id_pedido) && Mesa::ValidarMesaExistente($id_mesa))
        {
            Pedido::EntregarPedido($id_pedido);
            Mesa::cambiarEstado($id_mesa,'con cliente pagando');
            return RespuestaJson::Exito($response,"Mesa actualizada : El cliente esta pagando",200);
        }
        return RespuestaJson::Error($response,"No se puedo realizar el cobro",400);
    }

}


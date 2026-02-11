<?php

require_once "Usuario.php";
require_once "Pedido.php";

class Empleado extends Usuario{

    public function tomarPedido($pedidoDetalles)
    {
        // Verificar si el usuario es del tipo "mozo"
        if ($this->tipo !== "mozo") {
            throw new Exception("El usuario no tiene permisos para tomar pedidos.");
        }

        // Preparar los detalles del pedido
        $mesaId = $pedidoDetalles['mesa_id'];
        $clienteId = $pedidoDetalles['cliente_id'];
        $productos = $pedidoDetalles['productos']; // Array de productos

        // Instancia de conexión a la base de datos
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Crear un nuevo pedido en la tabla 'pedidos'
        $consultaPedido = $objAccesoDatos->prepararConsulta(
            "INSERT INTO pedidos (mesa_id, cliente_id, mozo_id, fecha) VALUES (:mesa_id, :cliente_id, :mozo_id, :fecha)"
        );
        $consultaPedido->bindValue(':mesa_id', $mesaId, PDO::PARAM_INT);
        $consultaPedido->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $consultaPedido->bindValue(':mozo_id', $this->id, PDO::PARAM_INT);
        $consultaPedido->bindValue(':fecha', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $consultaPedido->execute();

        // Obtener el ID del pedido recién creado
        $pedidoId = $objAccesoDatos->obtenerUltimoId();

        // Insertar cada producto del pedido en la tabla intermedia 'productos_pedidos'
        foreach ($productos as $producto) {
            $productoId = $producto['id'];

            $consultaProductoPedido = $objAccesoDatos->prepararConsulta(
                "INSERT INTO productos_pedidos (pedido_id, producto_id) VALUES (:pedido_id, :producto_id)"
            );
            $consultaProductoPedido->bindValue(':pedido_id', $pedidoId, PDO::PARAM_INT);
            $consultaProductoPedido->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
            $consultaProductoPedido->execute();
        }

        // Retornar un mensaje de confirmación
        return "Pedido tomado y guardado correctamente.";
    }

    public static function obtenerPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        // Obtener el resultado como una instancia de Empleado
        $consulta->setFetchMode(PDO::FETCH_CLASS, 'Empleado');
        $empleado = $consulta->fetch();
    
        // Si no hay coincidencia, retornar null
        if (!$empleado) {
            return null;
        }
    
        return $empleado;
    }


    public static function guardarFotoEnPedido($idPedido, $nombreFoto)
    {
        
    try
    {
        if(Producto::ExisteProductoPorNombre($idPedido))
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET fotomesa = :fotomesa WHERE id_pedido = :id_pedido");

            // Vinculamos los parámetros
            $consulta->bindValue(':fotomesa', $nombreFoto, PDO::PARAM_STR);
            $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);

            // Ejecutamos la consulta
            $consulta->execute();
            $exito = true;
            return $exito;
        }
        
    }
    catch (Exception $e) {
            return RespuestaJson::Error($response, [ 'error' => $e->getMessage()], 500);
        }

    }

    public static function ValidarMozoExistente($idMozo)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();

    $consulta = $objAccesoDatos->prepararConsulta(
        "SELECT EXISTS (
            SELECT 1 
            FROM usuarios 
            WHERE id = :idMozo 
            AND tipo = 'mozo'
        ) AS existe"
    );

    $consulta->bindValue(':idMozo', $idMozo, PDO::PARAM_INT);
    $consulta->execute();

    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

    return $resultado['existe'] == 1;
}
}

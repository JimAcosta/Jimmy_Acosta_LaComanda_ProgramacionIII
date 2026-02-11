<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../utils/RespuestaJson.php';
class Producto{
    public $id;
    public $nombre;
    public $precio;
    public $sector;

    public function crearProducto()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre, precio, sector) VALUES (:nombre, :precio, :sector)");
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);

            if ($consulta->execute()) {
                return $objAccesoDatos->obtenerUltimoId();
            } else {
                return false; 
            }
        } catch (Exception $e) {
            error_log("Error en crearProducto: " . $e->getMessage());
            return false;
        }
    }


    public static function obtenerTodos()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio,sector FROM productos");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
        } catch (Exception $e) {
            error_log("Error al obtener los productos: " . $e->getMessage());
            throw $e; 
        }
    }



    public static function obtenerProductoPorNombre($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE nombre = :nombre");
        
        $consulta->bindParam(':nombre', $nombre);
        $consulta->execute();
        
        $resultado = $consulta->fetch(); 
        if ($resultado) {
            return $resultado;  
        } else {
            return null;
        }
    }

   public static function listarProductosPedidos($sector)
    {
        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, nombre , estado FROM productos_pedidos WHERE sector = :sector ");
            $consulta->bindParam(':sector', $sector, PDO::PARAM_STR);
            $consulta->execute();
            $productosPedidos =  $consulta->fetchAll(PDO::FETCH_ASSOC); 
            $productosPendientes = [];

            foreach($productosPedidos as $producto) {
                if($producto['estado'] == 'pendiente') {
                    $productosPendientes[] = $producto;
                }
            }
            return $productosPendientes;
        }catch (Exception $e) {
            error_log("Error al obtener la lista de productos pendientes: " . $e->getMessage());
            throw $e;
        }

    }

    public static function cambiarEstado($idPedido, $nombreProducto, $tiempo_preparacion)
    {
       
       try
       {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            
            $consulta = $objAccesoDatos->prepararConsulta(
                "UPDATE productos_pedidos 
                SET estado = :estado, tiempo_preparacion = :tiempo_preparacion 
                WHERE id_pedido = :id_pedido AND nombre = :nombreProducto"
            );
            
            $consulta->bindValue(':estado', 'en preparación', PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_preparacion', $tiempo_preparacion, PDO::PARAM_INT);
            $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':nombreProducto', $nombreProducto, PDO::PARAM_STR);

            $consulta->execute();
        }catch(Exception $e){
            error_log("error al obtener la lista de productos pedidos". $e->getMessage());
            throw $e;
        }
       
        
    }

    public static function cambiarAListo($idPedido, $nombreProducto)
    {

        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE productos_pedidos SET estado = :estado, tiempo_preparacion = :tiempo_preparacion 
                WHERE id_pedido = :id_pedido AND nombre = :nombreProducto");
            
            $consulta->bindValue(':estado', 'listo para servir', PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_preparacion', NULL, PDO::PARAM_INT);
            $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':nombreProducto', $nombreProducto, PDO::PARAM_STR);

            $consulta->execute();
        }catch(Exception $e){
            error_log("No se pudo cambiar el estado a listo para servir" . $e->getMessage());
        }
    }



    public static function ValidarProducto($producto) 
    {
        if (!isset($producto->nombre) || !is_string($producto->nombre) || strlen($producto->nombre) > 35) {
            error_log("en el nombre");
            return false;
        }

        if (!isset($producto->precio) || !is_numeric($producto->precio) || $producto->precio <= 0) {
            error_log("en el precio");
            return false;
        }

        $sectoresValidos = ['cocina', 'cerveceria', 'cocteleria'];
        if (!isset($producto->sector) || !in_array(strtolower($producto->sector), $sectoresValidos)) {
            error_log("en el sector");
            return false;
        }

        return true;
    }
    


    public static function ExisteProductoPendiente($producto)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT 1 
                FROM productos_pedidos 
                WHERE id_pedido = :id_pedido 
                AND nombre = :nombre_producto 
                AND estado = 'en preparacion'
                LIMIT 1"
            );

            $consulta->bindValue(':id_pedido', $producto['id_pedido'], PDO::PARAM_INT);
            $consulta->bindValue(':nombre_producto', $producto['nombre_producto'], PDO::PARAM_STR);

            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_ASSOC) !== false;

        } catch (Exception $e) {
            error_log("Error ExisteProductoPendiente: " . $e->getMessage());
            return false;
        }
    }


    public static function ExisteProductoPorNombre(string $nombre): bool
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT 1 FROM productos WHERE nombre = :nombre LIMIT 1"
            );

            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $consulta->execute();

            return (bool) $consulta->fetchColumn();

        } catch (Exception $e) {
            error_log("Error ExisteProductoPorNombre: " . $e->getMessage());
            return false;
        }
    }



}
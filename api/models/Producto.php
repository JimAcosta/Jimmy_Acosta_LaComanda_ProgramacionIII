<?php
require_once __DIR__ . '/../models/Pedido.php';
class Producto{
    public $id;
    public $nombre;
    public $precio;
    public $sector;
    public $tiempo_preparacion;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre, precio,sector,tiempo_preparacion) VALUES (:nombre, :precio,:sector,:tiempo_preparacion)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio,PDO::PARAM_INT);
        $consulta->bindValue(':sector', $this->sector,PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_preparacion', $this->tiempo_preparacion,PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }


    public static function obtenerProductoPorNombre($nombre)
    {
        // Obtener la instancia de la clase de acceso a datos
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Preparar la consulta SQL para obtener el id del producto
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE nombre = :nombre");
        
        // Ejecutar la consulta
        $consulta->bindParam(':nombre', $nombre);
        $consulta->execute();
        
        // Retornar solo el valor del 'id' del producto
        $resultado = $consulta->fetch(); 
        
        // Verifica si el producto fue encontrado y retorna solo el 'id'
        if ($resultado) {
            return $resultado;  // Retorna solo el id
        } else {
            return null;  // Si no se encuentra el producto, retorna null
        }
    }

    public static function listarProductosPedidos($sector) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, nombre FROM productos_pedidos WHERE sector = :sector");
        $consulta->bindParam(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC); 
    }

    public static function cambiarEstado($idPedido, $nombreProducto, $tiempo_preparacion)
    {
        // Obtener la instancia de acceso a datos
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Actualizar el estado y el tiempo de preparación en la tabla productos_pedidos
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE productos_pedidos 
            SET estado = :estado, tiempo_preparacion = :tiempo_preparacion 
            WHERE id_pedido = :id_pedido AND nombre = :nombreProducto"
        );
        
        // Enlazar los parámetros a la consulta
        $consulta->bindValue(':estado', 'en preparación', PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_preparacion', $tiempo_preparacion, PDO::PARAM_INT);
        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':nombreProducto', $nombreProducto, PDO::PARAM_STR);

        // Ejecutar la consulta
        $consulta->execute();
    }

    public static function cambiarAListo($idPedido, $nombreProducto)
    {
        // Obtener la instancia de acceso a datos
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        // Actualizar el estado y el tiempo de preparación en la tabla productos_pedidos
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE productos_pedidos 
            SET estado = :estado, tiempo_preparacion = :tiempo_preparacion 
            WHERE id_pedido = :id_pedido AND nombre = :nombreProducto"
        );
        
        // Enlazar los parámetros a la consulta
        $consulta->bindValue(':estado', 'listo para servir', PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_preparacion', NULL, PDO::PARAM_INT);
        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':nombreProducto', $nombreProducto, PDO::PARAM_STR);

        // Ejecutar la consulta
        $consulta->execute();
    }
    
}
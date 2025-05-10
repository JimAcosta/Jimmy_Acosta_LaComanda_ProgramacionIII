<?php

class Pedido
{
    public $id_pedido;
    public $cliente_asignado;
    public $estado;
    public $preciototal;
    public $mesa_asignada; 
    public $mozo_asignado;
    public $tiempo_entrega;

    
    private function generarId() {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 5);
    }
    
    // Método para crear un pedido
    public function crearPedido($productos)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $idPedido = $this->generarId();  // Generar ID de pedido
        $this->tiempo_entrega = 0;
        foreach ($productos as $producto) {
            $item = $this->insertarProductoPedido($idPedido, $producto['nombre'], $this->estado,$this->tiempo_entrega);
            $this-> preciototal += $item['precio'];
        }
        
        
        var_dump("entra aca01");

        // Insertar el pedido en la tabla 'pedidos'
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id_pedido, cliente_asignado, estado, precio_total, mesa_asignada, mozo_asignado, tiempo_entrega) 
                                                    VALUES (:id_pedido, :cliente_asignado, :estado, :precio_total, :mesa_asignada, :mozo_asignado, :tiempo_entrega)");
        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':cliente_asignado', $this->cliente_asignado, PDO::PARAM_STR); 
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR); 
        $consulta->bindValue(':precio_total', $this->preciototal, PDO::PARAM_INT);
        $consulta->bindValue(':mesa_asignada', $this->mesa_asignada, PDO::PARAM_STR);
        $consulta->bindValue(':mozo_asignado', $this->mozo_asignado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_entrega', $this->tiempo_entrega, PDO::PARAM_INT);

        $consulta->execute();
        var_dump("es aca01");
        //var_dump("dentro del for",$producto['estado']);
        
        return $idPedido;
    }

    // Método para insertar productos en la tabla 'productos_pedidos'
    private function insertarProductoPedido($idPedido, $nombreProducto, $estado,$tiempo_preparacion)
    {
        var_dump("es aca en el metodo");
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $producto = Producto::obtenerProductoPorNombre($nombreProducto);
        
        if ($producto) {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos_pedidos(producto_id,nombre, id_pedido, estado,sector,tiempo_preparacion) 
            VALUES (:producto_id,:nombre, :id_pedido, :estado,:sector,:tiempo_preparacion)");
            
            $consulta->bindValue(':producto_id', $producto['id'], PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $nombreProducto, PDO::PARAM_STR);  // Corregido el nombre  // Corregido el nombre y tipo
            $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':sector', $producto['sector'], PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_preparacion',$tiempo_preparacion,PDO::PARAM_INT);
            var_dump("es aca");
            // Ejecuta la consulta
            $consulta->execute();
            return $producto;
        } else {
            throw new Exception("Producto no encontrado: " . $nombreProducto);
        }
    }

    // Método para obtener todos los pedidos
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, cliente_asignado, estado, preciototal, mesa_asignada, mozo_asignado, tiempo_entrega FROM pedidos");
        
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        return $resultado;
    }

    public static function actualizarTiempoEspera($idPedido, $tiempo_entrega)
    {
        // Obtener la instancia de acceso a datos
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Consulta para obtener el máximo tiempo de preparación de los productos asociados al pedido
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT MAX(tiempo_preparacion) AS max_tiempo_preparacion 
            FROM productos_pedidos
            WHERE id_pedido = :idPedido
        ");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        
        //var_dump("maximotiempo: ",$resultado['max_tiempo_preparacion']);
        //var_dump($tiempomax);
        //return $resultado;
        $tiempomax = $resultado['max_tiempo_preparacion'];
        $consulta = $objAccesoDatos->prepararConsulta("
            UPDATE pedidos 
            SET tiempo_entrega = :tiempomax , estado = :estado
            WHERE id_pedido = :idPedido
        ");
        
        // Vinculando los parámetros de la consulta con los valores correspondientes
        $consulta->bindValue(':tiempomax', $tiempomax, PDO::PARAM_INT); // Vinculando el tiempo máximo
        $consulta->bindValue(':estado', 'en preparacion', PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR); // Vinculando el id del pedido
        
        // Ejecutando la consulta
        $consulta->execute();
    }

    public static function ObtenerPedido($idPedido)
    {
        // Obtener la instancia de acceso a la base de datos
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        // Consulta para obtener los detalles del pedido usando el idPedido
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id_pedido = :idPedido");
    
        // Asignar el valor de idPedido al parámetro de la consulta
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
    
        // Ejecutar la consulta
        $consulta->execute();
    
        // Obtener el resultado
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        // Verificar si se encontró el pedido
        if ($resultado) {
            return $resultado; // Retorna los datos del pedido
        } else {
            return null; // No se encontró el pedido
        }
    }

    public static function EntregarPedido($idPedido)
    {
        // Obtener la instancia de acceso a la base de datos
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        // Consulta para obtener los detalles del pedido usando el idPedido
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = 'entregado' WHERE id_pedido = :idPedido");
    
        // Asignar el valor de idPedido al parámetro de la consulta
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
    
        // Ejecutar la consulta
        $consulta->execute();
    
        // Obtener el resultado
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        // Verificar si se encontró el pedido
        if ($resultado) {
            return $resultado; // Retorna los datos del pedido
        } else {
            return null; // No se encontró el pedido
        }
    }   
}
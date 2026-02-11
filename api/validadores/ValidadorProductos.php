<?php
require_once __DIR__ . '/../utils/RespuestaJson.php';
require_once __DIR__ . '/../models/Producto.php';

class ValidadorProducto
{
    public static function ValidarCamposProducto(array $producto)
    {
        return (isset($producto['id_pedido']) && isset($producto['nombre_producto']) &&
        !empty($producto['id_pedido']) && !empty($producto['nombre_producto'])
        );
    }
}

<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = new Producto();
        $producto->nombre = $parametros['nombre'] ?? null;
        $producto->precio = $parametros['precio'] ?? null;
        $producto->sector = $parametros['sector'] ?? null;

        if(!Producto::ValidarProducto($producto)){
            return RespuestaJson::Error($response,"Producto Invalido,Verifique los campos",400);
        }
        if (!$producto->crearProducto()) {
            return RespuestaJson::error($response, "No se pudo crear el producto", 500);
        }

        return RespuestaJson::exito($response, "Producto creado con exito", 201);
    }


    public function TraerTodos($request, $response, $args)
    {
        try {
            $lista = Producto::obtenerTodos();
            
            if ($lista) {
                return RespuestaJson::Exito($response, ['listaProductos' => $lista], 200);
            } else {
                return RespuestaJson::Error($response, "No se pudo realizar la consulta", 400);
            }

        } catch (Exception $e) {
            return RespuestaJson::Error($response, [ 'error' => $e->getMessage()], 500);
        }
    }

}
<?php
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';



class EmpleadoController extends Empleado implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $tipo = $parametros['tipo'];
        $estado = $parametros['estado'];

        // Creamos el usuario
        $usr = new Empleado();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->tipo = $tipo;
        $usr->estado = $estado;
        $usr->fechaAlta = new DateTime('now');
        $usr->crearEmpleado();

        $payload = json_encode(array("mensaje" => "Empleado creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}
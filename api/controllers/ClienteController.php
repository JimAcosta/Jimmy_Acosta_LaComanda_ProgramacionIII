<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';



class ClienteController extends Cliente implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        $usr = new Cliente();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->crearCliente();

        $payload = json_encode(array("mensaje" => "Cliente creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}
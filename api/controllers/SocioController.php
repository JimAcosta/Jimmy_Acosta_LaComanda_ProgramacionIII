<?php
require_once __DIR__ . '/../models/Socio.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';



class SocioController extends Socio implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        // Creamos el usuario
        $usr = new Socio();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->crearSocio();

        $payload = json_encode(array("mensaje" => "Socio creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function CerrarMesa($request, $response, $args){
        $parametros = $request->getParsedBody();
        $id_mesa = $parametros['id_mesa'];

        if(Mesa::cambiarEstado($id_mesa,'cerrada'))
        {
            return RespuestaJson::Exito($response,"El cliente pago, Mesa Cerrada",200);
        }
        return RespuestaJson::Error($response,"no se pudo cerrar la mesa",400);

    }
}
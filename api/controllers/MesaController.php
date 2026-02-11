<?php
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';
require_once __DIR__ . '/../utils/RespuestaJson.php';
require_once __DIR__ . '/../validadores/ValidadorMesa.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
{
    try {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado']?? null;

        if(ValidadorMesa::ValidarAltaMesa($estado))
        {
            $mesa = new Mesa();
            $mesa->estado = $estado;
            $mesa->crearMesa();

        return RespuestaJson::Exito($response, "Mesa creada con exito");
        }
        else{
            RespuestaJson::Error($response,"Error al crear la mesa Verifique campos ",404);
        }
        
    } catch (Exception $e) {
        return RespuestaJson::Error($response,"Error al crear la mesa: " . $e->getMessage(),404);
    }
}



    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
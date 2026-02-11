<?php
require_once __DIR__ . '/../models/Encuesta.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../utils/RespuestaJson.php';


class EncuestaController extends Encuesta #implements IApiUsable
{
    public function RealizarEncuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id_pedido = $parametros['id_pedido'];
        $id_mesa = $parametros['id_mesa'];

        if (!Pedido::ValidarPedidoExistente($id_pedido) || !Mesa::ValidarMesaExistente($id_mesa)) {
            return RespuestaJson::Error($response,"El pedido o la Mesa no Existen",400);
        }
        $puntuacion_mesa = $parametros['puntuacion_mesa'] ?? null;
        $puntuacion_mozo = $parametros['puntuacion_mozo'] ?? null;
        $puntuacion_cocinero = $parametros['puntuacion_cocinero'] ?? null;
        $puntuacion_restaurante = $parametros['puntuacion_restaurante'] ?? null;
        $opinion = $parametros['opinion'] ?? null;

        if (!Encuesta::ValidarDatosEncuesta($puntuacion_mesa,$puntuacion_mozo,$puntuacion_cocinero,
        $puntuacion_restaurante,$opinion)) 
        {
            
            return RespuestaJson::Error($response,"Ingrese una puntuacion valida",400);
        }

        $encuesta = new Encuesta();
        $encuesta->id_pedido = $id_pedido;
        $encuesta->id_mesa = $id_mesa;
        $encuesta->puntuacion_mesa = $puntuacion_mesa;
        $encuesta->puntuacion_mozo = $puntuacion_mozo;
        $encuesta->puntuacion_cocinero = $puntuacion_cocinero;
        $encuesta->puntuacion_restaurante = $puntuacion_restaurante;
        $encuesta->opinion = $opinion;

        $encuesta->crearEncuesta();

        return RespuestaJson::Exito($response,"Encuesta Realizada con Exito",200);
    }

    public static function VerMejoresComentarios($request, $response, $args)
    {
        $lista = Encuesta::ObtenerMejoresEncuestas();
        if($lista)
        {
            return RespuestaJson::Exito($response,(array("Mejores comentarios " => $lista)),200);
        }
        else{
            return RespuestaJson::Error($response,"No se pudo obtener los mejores comentarios",400);
        }
        
    }
}
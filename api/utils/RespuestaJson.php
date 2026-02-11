<?php

class RespuestaJson
{
    public static function Exito($response, $mensaje, $status = 200)
    {
        $payload = is_string($mensaje) 
        ? json_encode(["Exito:" => $mensaje], JSON_UNESCAPED_UNICODE)
        : json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public static function Error($response, $mensaje, $status)
    {
        $payload = is_string($mensaje) 
            ? json_encode(["Error" => $mensaje], JSON_UNESCAPED_UNICODE)
            : json_encode($mensaje, JSON_UNESCAPED_UNICODE);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public static function Mensaje($response, $mensaje, $status)
    {
        $payload = is_string($mensaje) 
            ? json_encode([$mensaje], JSON_UNESCAPED_UNICODE)
            : json_encode($mensaje, JSON_UNESCAPED_UNICODE);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
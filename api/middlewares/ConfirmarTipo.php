<?php

class ConfirmarTipo
{
    private $perfilesPermitidos;

    // Constructor que recibe los perfiles permitidos
    public function __construct($perfiles)
    {
        $this->perfilesPermitidos = is_array($perfiles) ? $perfiles : [$perfiles];
    }

    // Método invocable (__invoke)
    public function __invoke($request, $handler)
    {
        $headers = $request->getHeader('Authorization');
        if (empty($headers)) {
            return $this->crearRespuestaError("Token no encontrado", 401);
        }

        $token = str_replace('Bearer ', '', $headers[0]);

        try {
            $data = AutentificadorJWT::obtenerData($token);

            // Verificar si el perfil del token está en los perfiles permitidos
            if (!in_array($data->tipo, $this->perfilesPermitidos)) {
                return $this->crearRespuestaError("Tipo no autorizado", 403);
            }

            // Si el perfil es válido, continúa con el siguiente middleware o controlador
            return $handler->handle($request);
        } catch (Exception $e) {
            return $this->crearRespuestaError("Token inválido: " . $e->getMessage(), 401);
        }
    }

    // Método para crear respuestas de error
    private function crearRespuestaError($mensaje, $codigo)
    {
        $response = new Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => $mensaje]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($codigo);
    }
}
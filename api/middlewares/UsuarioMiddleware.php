<?php
class UsuarioMiddleware
{
    public function __invoke($request, $handler)
    {
        $datosUsuario = $request->getParsedBody();
        $camposRequeridos = ['usuario', 'clave', 'tipo', 'fechaAlta', 'estado'];

        foreach ($camposRequeridos as $campo) {
            if (empty($datosUsuario[$campo]) || trim($datosUsuario[$campo]) === '') {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(['error' => "El campo '$campo' es requerido y no puede estar vacío."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }

        // Si todo está bien, continuar al siguiente middleware/controlador
        return $handler->handle($request);
    }
}
<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';
require_once __DIR__ . '/../utils/AutentificadorJWT.php' ;

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $tipo =  $parametros['tipo'];  
        $estado =  $parametros['estado'];  
        $fechaAlta = $parametros['fechaAlta'];
        $fechaBaja = isset($parametros['fechaBaja']) ? $parametros['fechaBaja'] : null;  // valor por defecto NULL

        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->tipo = $tipo;
        $usr->estado = $estado;
        $usr->fechaAlta = $fechaAlta;
        $usr->fechaBaja = $fechaBaja;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con éxito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con éxito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }*/

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con éxito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Login($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $usuario = $params['usuario'];
        $clave = $params['clave'];

        $usuarioAutenticado = Usuario::Logearse($usuario, $clave);

        if ($usuarioAutenticado) {
            $token = AutentificadorJWT::CrearToken(['usuario' => $usuarioAutenticado->usuario, 'tipo' => $usuarioAutenticado->tipo]);

            // Responder con el token
            $payload = json_encode(['token' => $token]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            // Responder con un mensaje de error si las credenciales no son correctas
            $payload = json_encode(['mensaje' => 'Usuario o contraseña incorrectos']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function verTiempoDeEspera($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $idPedido = $params['idPedido'];
        $idMesa = $params['idMesa'];

        $tiempoEspera = Usuario::verTiempoEspera($idPedido, $idMesa);

        if ($tiempoEspera) {
            // Responder con el token
            $payload = json_encode(['tiempo de espera de su pedido' => $tiempoEspera]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            // Responder con un mensaje de error si las credenciales no son correctas
            $payload = json_encode(['mensaje' => 'El pedido no existe']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

}
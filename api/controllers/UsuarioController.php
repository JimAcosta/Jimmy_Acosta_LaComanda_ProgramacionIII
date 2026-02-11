<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../interfaces/IApiUsable.php';
require_once __DIR__ . '/../utils/AutentificadorJWT.php' ;
require_once __DIR__ . '/../validadores/ValidadorUsuario.php' ;

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
        if(ValidadorUsuario::ValidarUsuarioNuevo($usr))
        {
            if($usr->crearUsuario()){
                return RespuestaJson:: Exito($response,"Usuario Nuevo Creado con exito",200);
            }
    
        }else{
            return RespuestaJson::Error($response,"Algo salio mal",400);
        }

        
    }

    public function TraerUno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        $usr = $parametros['usuario'] ;
        $id = $parametros['id'] ;
        if($usuario = Usuario::obtenerUsuario($usr,$id)){
            return RespuestaJson::Exito($response,$usuario,200);
        }
        else{
            return RespuestaJson::Error($response,"No se pudo obtener el usuario",500); 
        }
        
    }

    public function TraerTodos($request, $response, $args)
    {
        if($lista = Usuario::obtenerTodos())
        {
            RespuestaJson::Exito($response,"Lista de usuarios: $lista",200);
        }

        return RespuestaJson::Error($response,"No se pudo obtener la lista de usuarios",400);

    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(Usuario::modificarUsuario($nombre))
        {
            return Respuestajson::Exito($response,"usuario modificado con exito",200);
        }
        return RespuestaJson::Error($response,"No se pudo modificar el usuario",400);

        


    }

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
            $token = AutentificadorJWT::CrearToken(['usuario' => $usuarioAutenticado->usuario, 'tipo' => $usuarioAutenticado->tipo, 'id'=> $usuarioAutenticado->id]);
            return RespuestaJson::Exito($response,"Token = $token",200);
        } else {
            return RespuestaJson::Error($response,"Usuario o clave incorrectas",200);
        }
    }

    public function verTiempoDeEspera($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $idPedido = $params['idPedido'];
        $idMesa = $params['idMesa'];

        $tiempoEspera = Usuario::verTiempoEspera($idPedido, $idMesa);

        if ($tiempoEspera) {
            return RespuestaJson::Exito($response,"tiempo de espera de su pedido $tiempoEspera minutos",200);
        }
         else {
            return RespuestaJson::Error($response,"El pedido no existe",401);
        }

    }   
}
<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $tipo;
    public $fecha_alta;
    public $fecha_baja;
    public $estado;

    public function crearUsuario()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, tipo, fecha_alta, fecha_baja, estado) 
            VALUES (:usuario, :clave, :tipo, :fecha_alta, :fecha_baja , :estado)");

            $this->fechaBaja = null;

            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_alta', $this->fechaAlta, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_baja', $this->fechaBaja, PDO::PARAM_NULL); // También puede ser PDO::PARAM_STR si usás '' o fechas

            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

            if($consulta->execute()){
                return $objAccesoDatos->obtenerUltimoId();
            }
            else{
                return false;
            }    
        }catch (Exception $e) {
            error_log("Error en crearUsuario: " . $e->getMessage());
            return false;
        }
    }

    public static function obtenerUsuario($usuario,$id)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, tipo,estado,fecha_alta AS fechaAlta FROM usuarios WHERE usuario = :usuario AND id = :id");
            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        
            if($consulta->execute()){
                return $consulta->fetchObject('Usuario');
            }else{
                return false;
            } 
        }catch (Exception $e) {
            error_log("Error en obtenerUsuario: " . $e->getMessage());
            return false;
        }
    }


    public static function obtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, tipo, fecha_alta,
            fecha_baja, estado FROM usuarios");
            
            if($consulta->execute()){
                return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
            }
            else{
                return false;
            }
        }catch (Exception $e) {
            error_log("Error en obtenerTodos: " . $e->getMessage());
            return false;
        }
        
        
    }


    public static function modificarUsuario($usuario){

    }
    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    


    public static function ValidarUsuario($usuario)
    {
        if (!isset($usuario->usuario) || !is_string($usuario->usuario) || strlen($usuario->usuario) > 15) {
            error_log("en el nombre");
            return false;
        }

        if (!isset($usuario->clave) || !is_string($usuario->clave)) {
            error_log("en la clave");
            return false;
        }

        $tiposValidos = ['cocinero', 'cervecero', 'bartender','socio','cliente','mozo'];
        if (!isset($usuario->tipo) || !in_array(strtolower($usuario->tipo), $tiposValidos)) {
            error_log("en el tipo");
            return false;
        }

        $estadosValidos = ['activo', 'suspendido', 'de baja'];
        if (!isset($usuario->estado) || !in_array(strtolower($usuario->estado), $estadosValidos)) {
            error_log("en el estado");
            return false;
        }
        
    }



    






    public static function Logearse($usuario, $clave)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario AND clave =:clave");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();

        $usuarioDb = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($usuarioDb) {
            $usuarioObj = new Usuario();
            $usuarioObj->usuario = $usuarioDb['usuario'];
            $usuarioObj->tipo = $usuarioDb['tipo']; 
            $usuarioObj->id = $usuarioDb['id']; 
            return $usuarioObj;
        }
        return null;
    }

    public static function verTiempoEspera($idPedido, $idMesa){

        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
            $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempo_entrega FROM pedidos 
                WHERE id_pedido = :idPedido AND mesa_asignada = :idMesa");
    
            $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_STR);
    
            $consulta->execute();
    
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($resultado)
            {
                return $resultado['tiempo_entrega'];
            }
            else {
                return null; 
            }
        } catch (Exception $e) {
            error_log("Error al obtener el tiempo de entrega: " . $e->getMessage());
            return null;
        }
    }

}

<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $tipo;
    public $fechaAlta;
    public $fechaBaja;
    public $estado;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, tipo, fecha_alta, fecha_baja, estado) 
                                                    VALUES (:usuario, :clave, :tipo, :fecha_alta, :fecha_baja , :estado)");


        $fechaAlta = new DateTime();
        $fechaAltaFormatted = $fechaAlta->format('Y-m-d H:i:s');

        $fechaBajaFormatted = null;


        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_alta', $fechaAltaFormatted, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_baja', $fechaBajaFormatted, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        // Seleccionamos todos los campos de la tabla 'usuarios'
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, tipo, fecha_alta, fecha_baja, estado FROM usuarios");
        $consulta->execute();

        // Devolvemos todos los resultados como una lista de objetos 'usuario'
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
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

    /*public static function Logearse($usuario, $clave)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        $usuarioDb = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$usuarioDb) {
            return null;
        }

        // Verificar la contraseña usando password_verify
        /*if (password_verify($clave, $usuarioDb['clave'])) {

            $usuarioObj = new Usuario();
            $usuarioObj->usuario = $usuarioDb['usuario'];
            $usuarioObj->tipo = $usuarioDb['tipo']; 
            return $usuarioObj;
        }

        return null;
    }*/

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
        try {
            // Obtener la instancia de acceso a la base de datos
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
            // Preparar la consulta para obtener el tiempo de entrega
            $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempo_entrega FROM pedidos 
                WHERE id_pedido = :idPedido AND mesa_asignada = :idMesa
            ");
    
            // Asignar los valores de los parámetros
            $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
            $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
    
            // Ejecutar la consulta
            $consulta->execute();
    
            // Obtener el resultado
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
            // Retornar el tiempo de entrega si existe
            if ($resultado) {
                return $resultado['tiempo_entrega'];
            } else {
                return null; // No se encontró el pedido
            }
        } catch (Exception $e) {
            error_log("Error al obtener el tiempo de entrega: " . $e->getMessage());
            return null;
        }
    }

}

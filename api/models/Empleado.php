<?php

require_once "Usuario.php";
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class Empleado extends Usuario{
    public $tipo;
    public $estado;
    public $fechaAlta;
    public $FechaBaja;

    public function crearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);

        $consultaUsuario = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
        $consultaUsuario->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consultaUsuario->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consultaUsuario->execute();

        
        $idUsuario = $objAccesoDatos->obtenerUltimoId();
        $consultaEmpleado = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (id, usuario, clave, tipo, estado, fecha_alta) VALUES (:id, :usuario, :clave, :tipo, :estado, :fechaAlta)");
        $consultaEmpleado->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        $consultaEmpleado->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consultaEmpleado->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consultaEmpleado->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consultaEmpleado->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consultaEmpleado->bindValue(':fechaAlta', $this->fechaAlta->format('Y-m-d'), PDO::PARAM_STR); // Fecha en formato Y-m-d

        $consultaEmpleado->execute();

        return $idUsuario;
    }

    public static function obtenerEmpleados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM empleados");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }


    public static function obtenerEmpleadoEspecifico($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

}
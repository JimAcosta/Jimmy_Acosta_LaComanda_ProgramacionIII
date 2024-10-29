<?php

require_once "Usuario.php";
require_once __DIR__ . '/../interfaces/IApiUsable.php';

class Cliente extends Usuario{

    public function crearCliente()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);

        $consultaUsuario = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
        $consultaUsuario->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consultaUsuario->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consultaUsuario->execute();

        $idUsuario = $objAccesoDatos->obtenerUltimoId();

        $consultaSocio = $objAccesoDatos->prepararConsulta("INSERT INTO clientes (id, usuario, clave) VALUES (:id, :usuario, :clave)");
        $consultaSocio->bindValue(':id', $idUsuario, PDO::PARAM_INT);  
        $consultaSocio->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consultaSocio->bindValue(':clave', $claveHash, PDO::PARAM_STR);

        $consultaSocio->execute();

        return $idUsuario;
    }
}
<?php

class Mesa
{
    public $id;
    public $estado;


    public static function generarId() {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 5);
    }
    public function crearMesa()
    {
        $id = $this::generarId();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (id, estado) VALUES (:id, :estado)");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
    
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }


    public static function cambiarEstado($idMesa, $estadoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Consulta para actualizar el estado de la mesa
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = :estadoMesa WHERE id = :idMesa");
        var_dump($estadoMesa,$idMesa);
        $consulta->bindValue(':estadoMesa', $estadoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_STR);

        // Ejecutar la consulta
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            return true;  // Si se actualizó al menos una fila
        } else {
            return false;  // Si no se actualizó ninguna fila (por ejemplo, si no existe la mesa)
        }
    }



    
}
<?php

class Mesa
{
    public $id_mesa;
    public $estado;


    public static function generarid_mesa() {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 5);
    }
    public function crearMesa()
    {
        $id_mesa = $this::generarid_mesa();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (id_mesa, estado) VALUES (:id_mesa, :estado)");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
    
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoid_mesa();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, estado FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }


    public static function cambiarEstado($id_mesaMesa, $estadoMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        // Consulta para actualizar el estado de la mesa
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE mesas SET estado = :estadoMesa WHERE id_mesa = :id_mesaMesa");
        var_dump($estadoMesa,$id_mesaMesa);
        $consulta->bindValue(':estadoMesa', $estadoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesaMesa', $id_mesaMesa, PDO::PARAM_STR);

        // Ejecutar la consulta
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            return true;  // Si se actualizó al menos una fila
        } else {
            return false;  // Si no se actualizó ninguna fila (por ejemplo, si no existe la mesa)
        }
    }



    
}
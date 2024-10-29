<?php

function generarId() {
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 5);
}

class Pedido
{
    public $id_pedido;
    public $cliente_asignado;
    public $lista_productos;
    public $estado;
    public $precioTotal; 

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $idPedido = generarId();

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id_pedido, cliente_asignado, lista_productos, estado, preciototal) VALUES (:id_pedido, :cliente_asignado, :lista_productos, :estado, :preciototal)");
        
        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':cliente_asignado', $this->cliente_asignado, PDO::PARAM_STR);
        $consulta->bindValue(':lista_productos', json_encode($this->lista_productos), PDO::PARAM_STR); 
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR); 
        $consulta->bindValue(':preciototal', $this->precioTotal, PDO::PARAM_STR);
        $consulta->execute();

        return $idPedido;
    }
    public static function obtenerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_pedido, lista_productos, cliente_asignado , estado, precioTotal FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
}


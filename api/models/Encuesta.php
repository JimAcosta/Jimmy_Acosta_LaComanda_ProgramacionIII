<?php
require_once __DIR__ . '/../interfaces/IApiUsable.php';


class Encuesta
{
    public $id_mesa;
    public $id_pedido;
    public $puntuacion_mesa;
    public $puntuacion_mozo;
    public $puntuacion_cocinero;
    public $puntuacion_restaurante;
    public $opinion;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (id_mesa, id_pedido, puntuacion_mesa, puntuacion_mozo, puntuacion_cocinero, puntuacion_restaurante,opinion) 
        VALUES (:id_mesa, :id_pedido, :puntuacion_mesa, :puntuacion_mozo, :puntuacion_cocinero , :puntuacion_restaurante, :opinion)");

        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacion_mesa', $this->puntuacion_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacion_mozo', $this->puntuacion_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacion_cocinero', $this->puntuacion_cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacion_restaurante', $this->puntuacion_restaurante, PDO::PARAM_INT);
        $consulta->bindValue(':opinion', $this->opinion, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ValidarDatosEncuesta($puntuacion_mesa, $puntuacion_mozo, $puntuacion_cocinero, $puntuacion_restaurante, $opinion)
    {
        if(
            !is_numeric($puntuacion_mesa) || $puntuacion_mesa < 1 || $puntuacion_mesa > 10 ||
            !is_numeric($puntuacion_mozo) || $puntuacion_mozo < 1 || $puntuacion_mozo > 10 ||
            !is_numeric($puntuacion_cocinero) || $puntuacion_cocinero < 1 || $puntuacion_cocinero > 10 ||
            !is_numeric($puntuacion_restaurante) || $puntuacion_restaurante < 1 || $puntuacion_restaurante > 10
        ) {
            return false;
        }

        if (empty($opinion) || strlen($opinion) > 66) {
            return false;
        }

        return true;
    }



    public static function ObtenerMejoresEncuestas($limite = 5)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();

    $consulta = $objAccesoDatos->prepararConsulta(
        "SELECT *,
        (puntuacion_mesa +
         puntuacion_mozo +
         puntuacion_cocinero +
         puntuacion_restaurante) AS puntuacion_total
         FROM encuestas
         ORDER BY puntuacion_total DESC
         LIMIT :limite"
    );

    $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}


}
<?php
require_once __DIR__ . '/../../vendor/autoload.php';  // Asegúrate de que el autoload esté correctamente referenciado.

class AccesoDatos
{
    private static $objAccesoDatos;
    private $objetoPDO;

    private function __construct()
    {
        $dbType = $_ENV["DB_TYPE"];
        try {
            if ($dbType === "mysql") {
                $this->objetoPDO = new PDO(
                    'mysql:host=' . $_ENV['MYSQL_HOST'] . ';dbname=' . $_ENV['MYSQL_DATABASE'],
                    $_ENV['MYSQL_USER'],
                    $_ENV['MYSQL_PASSWORD'],
                    array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );
                $this->objetoPDO->exec("SET CHARACTER SET utf8");

            } 
            else if ($dbType === "pgsql") {
                $this->objetoPDO = new PDO(
                    'pgsql:host=' . $_ENV['POSTGRES_HOST'] . ';dbname=' . $_ENV['POSTGRES_DATABASE'],
                    $_ENV['POSTGRES_USER'],
                    $_ENV['POSTGRES_PASSWORD'],
                    array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );
            } else {
                throw new Exception('Tipo de base de datos no reconocido.');
            }

        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    // Método para obtener la instancia única (Singleton)
    public static function obtenerInstancia()
    {
        if (!isset(self::$objAccesoDatos)) {
            self::$objAccesoDatos = new AccesoDatos();
        }
        return self::$objAccesoDatos;
    }

    // Preparar consulta SQL
    public function prepararConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    // Obtener el último ID insertado
    public function obtenerUltimoId()
    {
        return $this->objetoPDO->lastInsertId();
    }

    // Prevenir clonación
    public function __clone()
    {
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }
}



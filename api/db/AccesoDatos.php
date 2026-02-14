<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class AccesoDatos
{
    private static $objAccesoDatos;
    private $objetoPDO;

    private function __construct()
    {
        $dbType = $_ENV["DB_TYPE"] ?? null;

        try {

            if ($dbType === "mysql") {

                $host = $_ENV['MYSQL_HOST'];
            $port = $_ENV['MYSQL_PORT'];
            $db   = $_ENV['MYSQL_DATABASE'];
            $user = $_ENV['MYSQL_USER'];
            $pass = $_ENV['MYSQL_PASSWORD'];

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";

            $this->objetoPDO = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                    PDO::MYSQL_ATTR_SSL_CA => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );

            } 
            else if ($dbType === "pgsql") {

                $this->objetoPDO = new PDO(
                    'pgsql:host=' . $_ENV['POSTGRES_HOST'] .
                    ';port=' . $_ENV['POSTGRES_PORT'] .
                    ';dbname=' . $_ENV['POSTGRES_DATABASE'],
                                $_ENV['POSTGRES_USER'],
                                $_ENV['POSTGRES_PASSWORD'],
                    [
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]
                );

            } 
            else {
                throw new Exception('Tipo de base de datos no reconocido.');
            }

        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            die();
        }
    }

    public static function obtenerInstancia()
    {
        if (!isset(self::$objAccesoDatos)) {
            self::$objAccesoDatos = new AccesoDatos();
        }
        return self::$objAccesoDatos;
    }

    public function prepararConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    public function obtenerUltimoId()
    {
        return $this->objetoPDO->lastInsertId();
    }

    public function __clone()
    {
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }
}

<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once __DIR__ . '/controllers/UsuarioController.php';
require_once __DIR__ . '/controllers/EmpleadoController.php';
require_once __DIR__ . '/controllers/SocioController.php';
require_once __DIR__ . '/controllers/ProductoController.php';
require_once __DIR__ . '/controllers/PedidoController.php';
require_once __DIR__ . '/controllers/MesaController.php';
require_once __DIR__ . '/controllers/ClienteController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('/VerUsuarios', \UsuarioController::class . ':TraerTodos');
$app->post('/CargarEmpleado', \EmpleadoController::class . ':CargarUno');
$app->post('/CargarSocio', \SocioController::class . ':CargarUno');
$app->post('/CargarCliente', \ClienteController::class . ':CargarUno');
$app->post('/AgregarProducto', \ProductoController::class . ':CargarUno');
$app->get('/VerProductos', \ProductoController::class . ':TraerTodos');
$app->post('/AgregarPedido', \PedidoController::class . ':CargarUno');
$app->get('/VerPedidos', \PedidoController::class . ':TraerTodos');
$app->post('/AgregarMesa', \MesaController::class . ':CargarUno');
$app->get('/VerMesas', \MesaController::class . ':TraerTodos');



$app->run();

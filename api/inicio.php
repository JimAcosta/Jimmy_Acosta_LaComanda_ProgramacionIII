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

require __DIR__ . '../../vendor/autoload.php';

require_once __DIR__ . '/db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once __DIR__ . '/controllers/UsuarioController.php';
require_once __DIR__ . '/controllers/EmpleadoController.php';
require_once __DIR__ . '/controllers/SocioController.php';
require_once __DIR__ . '/controllers/ProductoController.php';
require_once __DIR__ . '/controllers/PedidoController.php';
require_once __DIR__ . '/controllers/MesaController.php';
require_once __DIR__ . '/controllers/ClienteController.php';
require_once __DIR__ . '/controllers/EncuestaController.php';
require_once __DIR__ . '/middlewares/UsuarioMiddleware.php';
require_once __DIR__ . '/middlewares/ConfirmarTipo.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../"); 
$dotenv->safeLoad();
// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->get('/ping', function($req,$res,$args){
    $res->getBody()->write(json_encode(['msg'=>'API funcionando!']));
    return $res->withHeader('Content-Type','application/json');
});
$app->get('/VerUsuarios', \UsuarioController::class . ':TraerTodos');
$app->get('/VerUsuario', \UsuarioController::class . ':TraerUno');
$app->put('/Modificar',\UsuarioController::class . ':ModificarUno');

$app->post('/CrearMesa',\MesaController::class . ':CargarUno')->add(new ConfirmarTipo(['socio']));

$app->post('/Login',\UsuarioController::class . ':Login');
$app->post('/CargarUsuario', \UsuarioController::class . ':CargarUno');
$app->get('/VerEmpleado/{id}', \EmpleadoController::class . ':TraerEmpleadoPorID');
$app->post('/TomarFotoMesa', \EmpleadoController::class . ':cargarFoto')->add(new ConfirmarTipo(['mozo']));
$app->post('/TomarPedido', \EmpleadoController::class . ':cargarPedido')->add(new ConfirmarTipo(['mozo']));
$app->get('/listarProductosPendientesCocina', \EmpleadoController::class . ':listarProductosPendientesCocina')->add(new ConfirmarTipo(['cocinero']));
$app->get('/listarProductosPendientesCervezeria', \EmpleadoController::class . ':listarProductosPendientesCervezeria')->add(new ConfirmarTipo(['cervecero']));
$app->get('/listarProductosPendientesBar', \EmpleadoController::class . ':listarProductosPendientesBar')->add(new ConfirmarTipo(['bartender']));
///en preparacion
$app->put('/CambiarEstadoProductoCocina', \EmpleadoController::class . ':cambiarEstadoProducto')->add(new ConfirmarTipo(['cocinero']));
$app->put('/CambiarEstadoProductoCervezeria', \EmpleadoController::class . ':cambiarEstadoProducto')->add(new ConfirmarTipo(['cervecero']));
$app->put('/CambiarEstadoProductoBar', \EmpleadoController::class . ':cambiarEstadoProducto')->add(new ConfirmarTipo(['bartender']));
$app->get('/VerTiempoEspera', \UsuarioController::class . ':verTiempoDeEspera')->add(new ConfirmarTipo(['cliente']));
$app->get('/VerPedidos', \PedidoController::class . ':TraerTodos')->add(new ConfirmarTipo(['socio']));
$app->get('/VerPedido', \PedidoController::class . ':TraerUno')->add(new ConfirmarTipo(['socio']));
//listo para servir
$app->put('/ProductoDeCocinaListo', \EmpleadoController::class . ':cambiarAListo')->add(new ConfirmarTipo(['cocinero']));
$app->put('/ProductoDeCerveceriaListo', \EmpleadoController::class . ':cambiarAListo')->add(new ConfirmarTipo(['cervecero']));
$app->put('/ProductoDeBarListo', \EmpleadoController::class . ':cambiarAListo')->add(new ConfirmarTipo(['bartender']));
//cambiarestadomesa


$app->get('/VerPedidosListos', \EmpleadoController::class . ':VerPedidosListos')->add(new ConfirmarTipo(['mozo']));
$app->put('/PedidoListo', \EmpleadoController::class . ':PedidoListo')->add(new ConfirmarTipo(['mozo']));

$app->put('/CobrarPedido', \EmpleadoController::class . ':CobrarPedido')->add(new ConfirmarTipo(['mozo']));
$app->put('/CerrarMesa', \SocioController::class . ':CerrarMesa')->add(new ConfirmarTipo(['socio']));

$app->post('/RealizarEncuesta', \EncuestaController::class . ':RealizarEncuesta')->add(new ConfirmarTipo(['cliente']));


//Productos
$app->post('/AgregarProducto', \ProductoController::class . ':CargarUno');
$app->get('/VerProductos', \ProductoController::class . ':TraerTodos');
$app->post('/AgregarPedido', \PedidoController::class . ':CargarUno');

$app->post('/AgregarMesa', \MesaController::class . ':CargarUno');
$app->get('/VerMesas', \MesaController::class . ':TraerTodos');
$app->get('/VerMejoresComentarios',\EncuestaController::class . ':VerMejoresComentarios')->add(new ConfirmarTipo(['socio']));



$app->run();

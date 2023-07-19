<?php
//Paiz Belen 2do Parcial
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use App\Controllers\UsuariosController;
use App\Controllers\ArmamentoController;
use App\Controllers\VentasController;

require __DIR__ . '/../vendor/autoload.php';
require_once '../src/Controllers/ArmamentoController.php';
require_once '../src/middlewares/LoginMiddlewareEspecifico.php';
require_once '../src/middlewares/LoginMiddlewareTodos.php';
require_once '../src/middlewares/RegistroArmaBorradaMW.php';
require '../src/AccesoDatos.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);
//$app->addBodyParsingMiddleware();

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  if(!isset($_GET['accion'])){
    $group->post('[/]', UsuariosController::class . ':ErrorDatos');
    $group->get('[/]', UsuariosController::class . ':ErrorDatos');
  }
  else{
    switch($_GET['accion']){
      case "login":
        $group->post('[/]', UsuariosController::class . ':POST_Login');
      break;
      case "listar":
        $group->get('[/]', UsuariosController::class . ':GET_TraerTodos')
        ->add(new LoginMiddlewareEspecifico("admin"));
      break;
      case "insertar":
        $group->post('[/]', UsuariosController::class . ':POST_InsertarUsuario')
        ->add(new LoginMiddlewareEspecifico("admin"));
      break;
    }
  }
  });

  $app->group('/armamento', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', ArmamentoController::class . ':GET_TraerTodos');
        break;
        case "listarNacionalidad":
          $group->get('[/]', ArmamentoController::class . ':GET_TraerTodos_segunNacionalidad');
        break;
        case "traerArmaId":
          $group->get('[/]', ArmamentoController::class . ':GET_TraerArma_Id')
          ->add(new LoginMiddlewareTodos());
        break;
        case "descargarCSV":
          $group->get('[/]', ArmamentoController::class . ':GET_GuardarArmasEnCSV');
        break;
        case "descargarLogsCSV":
          $group->get('[/]', ArmamentoController::class . ':GET_GuardarLogsEnCSV');
        break;
        case "bajarCSV":
          $group->get('[/]', ArmamentoController::class . ':GET_DescargarArmasCSV');
        break;
        case "alta":
          $group->post('[/]', ArmamentoController::class . ':POST_InsertarArmamento')
          ->add(new LoginMiddlewareEspecifico("admin"));
        break;
        case "borrarArma":
          $group->delete('[/]', ArmamentoController::class . ':DELETE_borrarArma_Id')
          ->add(new LoginMiddlewareEspecifico("admin"))->add(new RegistroArmaBorradaMW());
        break;
        case "modificar":
          $group->put('[/]', ArmamentoController::class . ':PUT_ModificarArma_Id')
          ->add(new LoginMiddlewareEspecifico("admin"));
        break;
      }
    }
    });
    $app->group('/ventas', function (RouteCollectorProxy $group) {
      if(!isset($_GET['accion'])){
        $group->post('[/]', UsuariosController::class . ':ErrorDatos');
        $group->get('[/]', UsuariosController::class . ':ErrorDatos');
      }
      else{
        switch($_GET['accion']){
          case "alta":
            $group->post('[/]', VentasController::class . ':POST_AltaVenta')
            ->add(new LoginMiddlewareTodos());
          break;
          case "listarPorFechas":
            $group->get('[/]', VentasController::class . ':GET_TraerVentas_rangoFechas_naionalidad')
            ->add(new LoginMiddlewareEspecifico("admin"));
          break;
          case "listarEmailsPorVenta":
            $group->get('[/]', VentasController::class . ':GET_TraerEmailVentas_porNombreArma')
            ->add(new LoginMiddlewareEspecifico("admin"));
          break;
          case "ventasPDF":
            $group->get('[/]', VentasController::class . ':GET_GuardarVentasOrdenadas_PDF');
          break;
        }
      }
      });

// Run application
$app->run();
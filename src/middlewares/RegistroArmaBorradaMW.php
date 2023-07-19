<?php
use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

require_once '../src/Clases/Autenticador.php';

class RegistroArmaBorradaMW
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        $param = $request->getQueryParams();
        $token = $param['token'];
        $id_arma = $param['id_arma'];
        $id_usuario = Autenticador::TraerIdDesdeToken($token);
        $accion = "borrar";
        $fecha_accion = date("Y-m-d");
        $response = $handler->handle($request);
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("insert into logs (id_usuario, id_arma, accion, fecha_accion)values('$id_usuario','$id_arma','$accion','$fecha_accion')");
		$consulta->execute();
        return $response;
    }
}
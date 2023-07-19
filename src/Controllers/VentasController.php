<?php
namespace App\Controllers;

use Autenticador;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Venta;
use Dompdf\Dompdf;
require '..\vendor\autoload.php';

require '..\vendor\autoload.php';
require '../src/Clases/Ventas.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Autenticador.php';

class VentasController
{
    public static function GET_TraerVentas_rangoFechas_naionalidad(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(isset($param['fechaInicio'], $param['fechafinal'], $param['nacionalidad'])){
            $fechaInicio = $param['fechaInicio'];
            $fechafinal = $param['fechafinal'];
            $nacionalidad = $param['nacionalidad'];
            $ventas = Venta::TraerTodasLasVentasEnRangoFechas_conNacionalidad($nacionalidad, $fechaInicio, $fechafinal);
            $retorno = json_encode(array("Listado de ventas en el rango de fechas y de la nacionalidad ingresada: "=>$ventas));
        }
        else{
            $retorno = json_encode(array("mensaje:"=>"Error en el ingreso de datos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function GET_TraerEmailVentas_porNombreArma(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(isset($param['nombre_arma'])){
            $nombre_arma = $param['nombre_arma'];
            
            $arrayEmails = Venta::TraerUsuariosDeVentas_porNombreArma($nombre_arma);
            $retorno = json_encode(array("Listado de ususarios que compraron el arma solicitada: "=>$arrayEmails));
        }
        else{
            $retorno = json_encode(array("mensaje:"=>"Error en el ingreso de datos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_GuardarVentasOrdenadas_PDF(Request $request, Response $response, array $args)
{
    $param = $request->getQueryParams();
    if (isset($param['criterio'])) {
        $criterio = $param['criterio'];

        $arrayVentas = Venta::TraerTodasLasVentasOrdenadas_parametro($criterio);

        $dompdf = new Dompdf();
        $html = '<h1>Datos</h1>';
        $contador = 1;
        foreach ($arrayVentas as $i) {
            $html .= '<h2>N° ' . $contador . '</h2>';
            foreach ($i as $propiedad => $valor) {
                $html .= '<p style="font-size: 12px;">' . ucfirst($propiedad) . ': ' . $valor . '</p>';
            }
            $html .= '<hr>';
            $contador++;
        }
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = $response->withHeader('Content-Disposition', 'attachment; filename="archivo.pdf"');
        $response->getBody()->write($dompdf->output());
    } else {
        $response = json_encode(array("mensaje" => "Error en el ingreso de datos"));
    }

    return $response;
}
    public static function POST_AltaVenta(Request $request, Response $response, array $args){
        $rutaImagen = 'C:\xampp\htdocs\2-2doParcial\src\Controllers\Media\imagenes_ventas';
        $parametros = $request->getParsedBody();
        $param = $request->getQueryParams();
        if(isset($parametros['nombre_arma'], $parametros['cantidad'], $_FILES['imagen'])){
            $nombre_arma = $parametros['nombre_arma'];
            $cantidad = $parametros['cantidad'];
            $imagen = $_FILES['imagen'];
            $token = $param['token'];
            $email = Autenticador::TraerEmailDesdeToken($token);
            $venta = new Venta($nombre_arma, $cantidad, $email);
    
            $id_insertado = $venta->Alta_venta();
            $destino = $venta->DefinirDestinoImagen($rutaImagen);
                move_uploaded_file($imagen['tmp_name'], $destino);
    
            if($id_insertado != null){
                $retorno = json_encode(array("mensaje" => "Venta guardada con exito"));
            }
            else{
                $retorno = json_encode(array("mensaje" => "No se pudo crear"));
            } 
        }
        else{
            $retorno = json_encode(array("mensaje" => "Error en la carga de datos"));
        }
           

        $response->getBody()->write($retorno);
        return $response;
    }
    
}


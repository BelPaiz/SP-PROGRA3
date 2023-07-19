<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Armamento;

require '../src/Clases/Armamento.php';
require_once '../src/Clases/Autenticador.php';

class ArmamentoController
{
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $productos = Armamento::TraerTodoLosProductos();
        $retorno = json_encode(array("Listado de armamentos"=>$productos));
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_GuardarArmasEnCSV(Request $request, Response $response, array $args){
        $path = "Armas.csv";
        $armas = Armamento::TraerTodasLasArmas_EnArray();
        $archivo = fopen($path, "w");
        $encabezado = array("id", "precio", "nombre", "nacionalidad");
        fputcsv($archivo, $encabezado);
        foreach($armas as $fila){
            fputcsv($archivo, $fila);
        }
        fclose($archivo);
        $retorno = json_encode(array("mensaje"=>"Datos descargados a CSV"));
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_GuardarLogsEnCSV(Request $request, Response $response, array $args){
        $path = "Logs.csv";
        $armas = Armamento::TraerTodosLosLogs_EnArray();
        $archivo = fopen($path, "w");
        $encabezado = array("id_usuario", "id_arma", "accion", "fecha_accion");
        fputcsv($archivo, $encabezado);
        foreach($armas as $fila){
            fputcsv($archivo, $fila);
        }
        fclose($archivo);
        $retorno = json_encode(array("mensaje"=>"Datos descargados a CSV"));
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_DescargarArmasCSV(Request $request, Response $response, array $args){
        $path = "Armas.csv";
        $response = $response
        ->withHeader('Content-Type', 'text/csv')
        ->withHeader('Content-Disposition', 'attachment; filename="armas.csv"');

        $response->getBody()->write(file_get_contents($path));
        return $response;
    }
    public static function GET_TraerTodos_segunNacionalidad(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(isset($param['nacionalidad'])){
            $nacionalidad = $param['nacionalidad'];
            $productos = Armamento::TraerTodoLosProductos_segunNacionalidad($nacionalidad);
            $retorno = json_encode(array("Listado de armamentos"=>$productos));
        }
        else{
            $retorno = json_encode(array("Listado de armamentos"=>"Error en la carga de datos"));
        }
        
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_TraerArma_Id(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(isset($param['id'])){
            $id = $param['id'];
            $arma = Armamento::TraerUnProducto_Id($id);
            if($arma != null){
                $retorno = json_encode(array("mensaje"=>$arma));
            }
            else{
                $retorno = json_encode(array("mensaje"=>"No existe arma con ese ID"));
            }
            
        }
        else{
            $retorno = json_encode(array("mensaje"=>"Error en la carga de datos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_InsertarArmamento(Request $request, Response $response, array $args){
        $rutaImagen = 'C:\xampp\htdocs\2-2doParcial\src\Controllers\Media\imagenes_armas';
        $parametros = $request->getParsedBody();
        if(isset($parametros['precio'], $parametros['nombre'], $parametros['nacionalidad'], $_FILES['imagen'])){
            $precio = $parametros['precio'];
            $nombre = $parametros['nombre'];
            $nacionalidad = $parametros['nacionalidad'];
            $imagen = $_FILES['imagen'];
    
            $producto = new Armamento($precio, $nombre, $nacionalidad);
            $ok = $producto->InsertarProducto();
            $destino = $producto->DefinirDestinoImagen($rutaImagen);
            move_uploaded_file($imagen['tmp_name'], $destino);
            if($ok != null){
                $retorno = json_encode(array("mensaje" => "Armamento dado de alta con exito"));
            }
            else{
                $retorno = json_encode(array("mensaje" => "No se pudo crear"));
            }   
        }
        else{
            $retorno = json_encode(array("mensaje" => "Error! en carga de datos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function DELETE_borrarArma_Id(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(isset($param['id_arma'])){
            $id_arma = $param['id_arma'];
            if(Armamento::BorrarUnProducto_Id($id_arma)){
                $retorno = json_encode(array("mensaje"=>"el arma se borro correctamente"));
            }
            else{
                $retorno = json_encode(array("mensaje"=>"Ocurrio un erro inesperado"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje"=>"Error en la carga de datos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function PUT_ModificarArma_Id(Request $request, Response $response, array $args){
        $rutaImagenExistente = 'C:\xampp\htdocs\2-2doParcial\src\Controllers\Media\Backup_2023';
        $rutaImagen = 'C:\xampp\htdocs\2-2doParcial\src\Controllers\Media\imagenes_armas';
        $parametros = $request->getQueryParams();
        $body = $request->getBody();
        if(isset($parametros['id_arma'], $parametros['precio'], $parametros['nombre'], $parametros['nacionalidad'])){
            $id_arma = $parametros['id_arma'];
            $precio = $parametros['precio'];
            $nombre = $parametros['nombre'];
            $nacionalidad = $parametros['nacionalidad'];

            $tempFilePath = tempnam(sys_get_temp_dir(), 'imagen');
            file_put_contents($tempFilePath, $body);

            $arma = new Armamento($precio, $nombre, $nacionalidad, $id_arma);
            $destino = $arma->DefinirDestinoImagen($rutaImagen);
            $destinoAlternativo = $arma->DefinirDestinoImagen($rutaImagenExistente);
            
            if(file_exists($destino)){
                rename($tempFilePath, $destinoAlternativo);
            }
            else{
                rename($tempFilePath, $destino);
            }
            if($arma->ModificarProducto()){
                $retorno = json_encode(array("mensaje"=>"el arma se modifico correctamente"));
            }
            else{
                $retorno = json_encode(array("mensaje"=>"Ocurrio un erro inesperado"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje"=>"Error en la carga de datos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}
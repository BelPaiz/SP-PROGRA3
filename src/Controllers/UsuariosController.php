<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Usuario;
use Autenticador;

require_once '../src/Clases/Autenticador.php';

class UsuariosController
{
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $usuarios = Usuario::TraerTodoLosUsuarios();
        $usuariosFiltrados = Usuario::FiltrarParaMostrar($usuarios);
        $retorno = json_encode(array("ListadoUsuarios"=>$usuariosFiltrados));
        
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_InsertarUsuario(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();
        $tipo = $parametros['tipo'];
        $email = $parametros['email'];
        $contraseña = $parametros['password'];
        $user = new Usuario($email, $tipo, $contraseña);
        $ok = $user->InsertarUsuario();
        if($ok != null){
            $retorno = json_encode(array("mensaje" => "Usuario creado con exito"));
        }
        else{
            $retorno = json_encode(array("mensaje" => "No se pudo crear"));
        }  
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_Login(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();

        $email = $parametros['email'];
        $contraseña = $parametros['password'];

        $usuarioEncontrado = null;
        $usuarioEncontrado = Usuario::TraerUnUsuarioEmail($email);
        if($usuarioEncontrado != null){
            if($contraseña == $usuarioEncontrado->password){
                $token = Autenticador::Definir_token($usuarioEncontrado->id, $email, $usuarioEncontrado->tipo);
                $retorno = json_encode(array("mensaje" => "OK!", "perfil de usuario"=>$usuarioEncontrado->tipo,"token" => $token));
            }
            else{
                $retorno = json_encode(array("mensaje" => "Contrasenia incorrecta"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}
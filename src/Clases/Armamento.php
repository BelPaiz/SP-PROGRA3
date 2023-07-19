<?php

class Armamento
{
    public $id;
    public $precio;
    public $nombre;
    public $nacionalidad;
    
    public function __construct($precio, $nombre, $nacionalidad, $id = null)
    {
        $this->precio = $precio;
        $this->nombre = $nombre;
        $this->nacionalidad = $nacionalidad;
        if($id != null){
            $this->id = $id;
        }
    }
    public function InsertarProducto()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("insert into armamento (precio, nombre, nacionalidad)values('$this->precio','$this->nombre','$this->nacionalidad')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public function ModificarProducto()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("update armamento set precio = ?, nombre = ?, nacionalidad = ? where id = ?");
		$consulta->bindValue(1, $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(2, $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(3, $this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(4, $this->id, PDO::PARAM_INT);
		return $consulta->execute();
	}
    public static function TraerTodoLosProductos()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, precio as precio , nombre as nombre, nacionalidad as nacionalidad from armamento");
        $consulta->execute();
        $arrayObtenido = array();
        $armas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $producto = new Armamento($i->precio, $i->nombre, $i->nacionalidad, $i->id );
            $armas[] = $producto;
        }
        return $armas;
	}
    public static function TraerTodoLosProductos_segunNacionalidad($nacionalidad)
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, precio as precio , nombre as nombre, nacionalidad as nacionalidad from armamento where nacionalidad = ?");
        $consulta->bindValue(1, $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();
        $arrayObtenido = array();
        $armas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $producto = new Armamento($i->precio, $i->nombre, $i->nacionalidad, $i->id );
            $armas[] = $producto;
        }
        return $armas;
	}
    public static function TraerUnProducto_Id($id) 
	{
        $producto = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from armamento where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $productoBuscado= $consulta->fetchObject();
        if($productoBuscado != null){
            $producto = new Armamento($productoBuscado->precio, $productoBuscado->nombre, $productoBuscado->nacionalidad, $productoBuscado->id,);
        }
        return $producto;
	}
    public static function TraerPrecio_Nombre($nombre) 
	{
        $precio = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select precio from armamento where nombre = ?");
        $consulta->bindValue(1, $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $precio= $consulta->fetchObject();
        return $precio;
	}
    public static function TraerUnProducto_Nombre($nombre_producto) 
	{
        $producto = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from armamento where nombre = ?");
        $consulta->bindValue(1, $nombre_producto, PDO::PARAM_STR);
        $consulta->execute();
        $productoBuscado= $consulta->fetchObject();
        if($productoBuscado != null){
            $producto = new Producto($productoBuscado->precio, $productoBuscado->nombre, $productoBuscado->nacionalidad, $productoBuscado->id);
        }
        return $producto;
	}
    public function DefinirDestinoImagen($ruta){
        $destino = $ruta."\\"."-".$this->nombre."-".$this->nacionalidad.".png";
        return $destino;
    }
    public static function BorrarUnProducto_Id($id) 
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("delete from armamento where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        return $consulta->execute();
	}
    public static function TraerTodasLasArmas_EnArray()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, precio as precio , nombre as nombre, nacionalidad as nacionalidad from armamento");
        $consulta->execute();
        $armas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $arma = array($i->id, $i->precio, $i->nombre, $i->nacionalidad);
            $armas[] = $arma;
        }
        return $armas;
	}
    public static function TraerTodosLosLogs_EnArray()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id_usuario, id_arma, accion, fecha_accion from logs");
        $consulta->execute();
        $logs = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $arma = array($i->id_usuario, $i->id_arma, $i->accion, $i->fecha_accion);
            $logs[] = $arma;
        }
        return $logs;
	}
}
?>
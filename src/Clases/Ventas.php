<?php

class Venta
{
    public $id;
    public $fecha;
    public $nombre_arma;
    public $cantidad;
    public $email_cliente;

    public function __construct($nombre_arma, $cantidad, $email, $fecha = null, $id = null)
    {
        $this->nombre_arma = $nombre_arma;
        $this->cantidad = $cantidad;
        $this->email_cliente = $email;
        if($fecha == null){
            $this->fecha =  date("Y-m-d");
        }
        else{
            $this->fecha = $fecha;
        }
        if($id != null){
            $this->id = $id;
        }
    }
    public function Alta_venta()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("insert into ventas (fecha, nombre_arma, cantidad, email_cliente)values('$this->fecha','$this->nombre_arma','$this->cantidad','$this->email_cliente')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodasLasVentas()
	{
        $venta = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, fecha as fecha, nombre_arma as nombre_arma, cantidad as cantidad, email_cliente as email_cliente from ventas");
        $consulta->execute();
        $arrayObtenido = array();
        $ventas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $venta = new Venta($i->nombre_arma, $i->cantidad, $i->email_cliente, $i->fecha, $i->id );
            $ventas[] = $venta;
        }
        return $ventas;
	}
    public static function TraerUnaVenta_Id($id)
	{
        $venta = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from ventas where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $ventaBuscada= $consulta->fetchObject();
        if($ventaBuscada != null){
            $venta = new Venta($ventaBuscada->nombre_arma, $ventaBuscada->cantidad, $ventaBuscada->email_cliente, $ventaBuscada->fecha, $ventaBuscada->id,);
        }
        return $venta;
	}
    public function DefinirDestinoImagen($ruta){
        $usuarioMail = strtok($this->email_cliente, '@');
        $destino = $ruta."\\".$this->nombre_arma."-".$usuarioMail."-".$this->fecha.".png";
        return $destino;
    }
    public static function TraerTodasLasVentasEnRangoFechas_conNacionalidad($nacionalidad, $fecha_inicio, $fecha_final)
	{
        $venta = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from ventas v INNER JOIN armamento a ON v.nombre_arma = a.nombre WHERE v.fecha >= cast(? AS date) AND v.fecha <= cast(? AS date) AND a.nacionalidad = ?");
        $consulta->bindValue(1, $fecha_inicio, PDO::PARAM_STR);
        $consulta->bindValue(2, $fecha_final, PDO::PARAM_STR);
        $consulta->bindValue(3, $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();
        $arrayObtenido = array();
        $ventas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $venta = new Venta($i->nombre_arma, $i->cantidad, $i->email_cliente, $i->fecha, $i->id );
            $ventas[] = $venta;
        }
        return $ventas;
	}
    public static function TraerUsuariosDeVentas_porNombreArma($nombre_arma)
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select email_cliente from ventas where nombre_arma = ?");
        $consulta->bindValue(1, $nombre_arma, PDO::PARAM_STR);
        $consulta->execute();
        $arrayObtenido = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        
        return $arrayObtenido;
	}
    public static function TraerTodasLasVentasOrdenadas_parametro($orden)
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * FROM ventas ORDER BY cantidad  $orden");
        $consulta->execute();
        $arrayObtenido = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        
        return $arrayObtenido;
	}
    
}



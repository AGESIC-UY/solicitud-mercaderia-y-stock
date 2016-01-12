<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$idart=$_REQUEST['idart'];
$unidadcall=$_COOKIE['usuunidad'];

$sentencia="Select * from StkSolicitudes where StkSolId='".$solid."'";
$resultado=mysqli_query($cn,$sentencia) or die('La consulta fall&oacute;: ' .mysqli_error());
$laSol=mysqli_fetch_assoc($resultado);
$estadosol=$laSol['StkSolEstado'];
$sentencia="Delete from StkSolArticulos where StkSolId='$solid' and StkArtId='$idart'";
if (!mysqli_query($cn,$sentencia))
{
	die('Error al eliminar el Articulo de la Solicitud'.mysqli_error());
}
else
{
       if ($_COOKIE['usuperfil']==4)
	{
		//solo para perfil autorizador, no corresponde para el solicitante autorizador
		$sentenciaI="Update StkSolicitudes set StkSolCambio='S' where StkSolId=".$solid;
		if (!mysqli_query($cn,$sentenciaI))
		{
			die('Error al indicar Solicitud modificada'.mysqli_error());
		}
	}
}
echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=ingresoartsol.php?solid=$solid&unidadcall=$unidadcall&estadocall=$estadosol'>";    
?>


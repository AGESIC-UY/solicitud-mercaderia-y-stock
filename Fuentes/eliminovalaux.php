<!--
//Durante la impresi�n de remito de entrega el atributo StkSolArtCantAcred con valor mayor a cero indica que se trata del articulo al cual se le asigno
//cantidad durante �l proceso "Entrega de material o disponibilidad". Por lo que Los art�culos deben aparecer en una nueva acreditaci�n con los valores
//en pendiente si los tiene y una acreditaci�n en cero. Por lo tanto llevo a cero las cantidades acred y pend para que no afecten los resultados en la 
//nueva impresi�n y/o entrega. 

//Ahora bien esta inicializaci�n solo va a suceder cuando quedaron cantidades pendientes de algun articulo de la solicitud, y deben volver a tramitar
//una entrega de material. De lo contrario estas cantidades podr�an quedar con valor. Ya que para que se realice el call a este objeto deberia hacer 
//click en el icono de disponibilidad para que se ejecute.
-->

<?php
session_start();
if (!isset($_SESSION['logged']))
{
     echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
else
{
	if ($_SESSION['logged']==2)
	{
	     echo '<meta http-equiv="refresh" content="0; url=login.php">';
	}
}
$fechaGuardada = $_SESSION["ultimoAcceso"];
$ahora = date("Y-n-j H:i:s");
$tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
if($tiempo_transcurrido >= 1800) //30 min en seg.
{
   echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
else
{
  $_SESSION["ultimoAcceso"] = $ahora;
} 
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];


$Inicializo=0;
$consultaXIII="Update StkSolArticulos set StkSolArtCantPen='".$Inicializo."', StkSolArtCantAcred='".$Inicializo."' where StkSolId=".$solid;
$resultadoXIII=mysqli_query($cn,$consultaXIII);

$consultaXII="Update StkSolicitudes set StkSolImprimiendo=0 where StkSolId=".$solid;
$resultadoXII=mysqli_query($cn,$consultaXII);

echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=disponibilidadstk.php?solid=$solid&estadocall=$estadocall&unidadcall=$unidadcall'>";   
//Cuando ingresa en disponibilidastk.php se recalcula el valor StkSolArtCantAcred y el StkSolArtCantPen cuando guardo o aplico la generaci�n del remito.
?>




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
$usuid=$_COOKIE['usuid'];
$unidad=$_COOKIE['usuunidad'];
$fchnow=date("Y-m-d H:i");
$estado="Construyendo";

if(isset($_SESSION['variable'])){
unset($_SESSION['variable']);
$sentencia="Insert into StkSolicitudes(StkSolUsuSol, StkSolSecId, StkSolFchSol, StkSolEstado, StkSolUsuCre, StkSolFchCre) values ('".$usuid."', '".$unidad."', '".$fchnow."', '".$estado."', '".$usuid."', '".$fchnow."')";
$insert = mysqli_query($cn, $sentencia);
}
?>
<script type="text/javascript"> alert ("Se ha agregado una nueva Solicitud");</script>
<?php
echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=index.php?estado=$estado&unidad=$unidad'>";
?>


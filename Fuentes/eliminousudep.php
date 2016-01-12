<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$usuele=$_REQUEST['usuele'];
$unidad=$_REQUEST['iddep'];
$usudepid=$_REQUEST['usudepid'];

$sentencia="Update UsuDep set UsuDepFchFin='".date("Y-m-d H:i")."' where UsuDepId='".$usudepid."'";
if (!mysqli_query($cn,$sentencia))
{
	die('Error al cerrar el vinculo con la unidad que autoriza'.mysqli_error());
}
echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=usuautorizadep.php?usuele=$usuele&unidad=$unidad'>";    
?>


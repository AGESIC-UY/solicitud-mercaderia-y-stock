<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$clase=$_REQUEST['clase'];
$idart=$_REQUEST['idart'];
$fchdesde=date("d/m/Y", mktime(0, 0, 0, $mes, 1, $year));
$fchhasta=$_REQUEST['fchhasta'];

?>
<?php

	$sentencia="Delete from StkArticulos where StkArtId='$idart'";
	if (!mysqli_query($cn,$sentencia))
	{
	die('Error al eliminar el Articulo'.mysqli_error());
	}
	echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=articulos.php?clase=$clase&articulo=$idart&fchdesde=$fchdesde&fchhasta=$fchhasta>";    

?>


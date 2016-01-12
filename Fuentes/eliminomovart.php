<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$idfac=$_REQUEST['idfac'];
$idart=$_REQUEST['idart'];
?>
<?php

	$sentencia="Delete from StkMovArticulos where StkPrvFacId='$idfac' and StkArtId='$idart'";
	if (!mysqli_query($cn,$sentencia))
	{
	die('Error al eliminar el Articulo de la Solicitud'.mysqli_error());
	}
	echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=ingresofacprvart.php?idprv=$idprv&idfac=$idfac'>";    
?>


<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$iddep=$_REQUEST['iddep'];
$idart=$_REQUEST['idart'];
?>
<?php
	$sentencia="Delete from StkArtDep where StkArtId='".$idart."' and DepId='".$iddep."'";
	if (!mysqli_query($cn,$sentencia))
	{
		die('Error al eliminar la unidad que reserva el Articulo'.mysqli_error());
	}
	echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=articulosdep.php?idart=$idart'>";    
?>


<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$idfac=$_REQUEST['idfac'];
?>
<?php
	$sentencia="Delete from StkMovArticulos where StkPrvFacId='$idfac'";
	if (!mysqli_query($cn,$sentencia))
	{
	die('Error al eliminar el detalle de Factura del Proveedor'.mysqli_error());
	}
	else
	{
		$sentencia="Delete from StkPrvFacturas where StkPrvFacId='$idfac'";
		if (!mysqli_query($cn,$sentencia))
		{
		die('Error al eliminar la Factura del Proveedor'.mysqli_error());
		}
	}

	echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=proveedoresfacver.php?idprv=$idprv'>";    
?>


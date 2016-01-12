<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$prvid=$_REQUEST['prvid'];

?>
<?php

	$sentencia="Delete from StkProveedores where StkPrvId='$prvid'";
	if (!mysqli_query($cn,$sentencia))
	{
		die('Error al eliminar el Proveedor'.mysqli_error());
	}
	else
	{
		echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=proveedores.php'>";    
	}

?>


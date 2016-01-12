<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	1.- Este objeto confirma el ingreso de la factura en forma definitiva, siendo ésta la segunda confirmación de que la factura esta ok.
		El atributo "StkPrvFacFin", comienza con valor "0" cuando ingresa la factura, cambia a "1" cuando obtiene su primer confirmación(administrador - quien se encarga
		de registrar la factura al sistema con su detalle de articulos y precios), cambia a "2" cuando esta es confirmada por el operador(o administrador de inventario), quien se 

	2.- Para los bienes de uso ademas de alterar las cantidades en el stock debo generar las etiquetas correspondientes. Eti.Pri., Eti.Sec. y especificaciones correspondientes
		Además genero Log del vínculo de las sec con las pri, hago insert en tabla StkArtBCLogAdj, representando el vinculo con la eti.pri. cargando una fecha de inicio del
		vinculo, cuando se desvincule debo indicar una fecha fin. Se indica en este log pues las eti.sec. heredan la adjudicacion de la eti.pri.
-->

<?php
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$idfac=$_REQUEST['idfac'];
$fin=2;
$Obs='--';
$lafecha=date("Y-m-d H:i");

//Por cada renglon de factura altero cantidades en StkArticulos 
$consulta="Select * from StkMovArticulos as m, StkPrvfacturas as f where m.StkPrvFacId='".$idfac."' and m.StkPrvFacId=f.StkPrvFacId";
$resultado=mysqli_query($cn,$consulta) or die (mysql_error());
while($unMovArt=mysqli_fetch_assoc($resultado))
{
	$sentencia="Update StkArticulos set StkArtCantReal=StkArtCantReal+'".$unMovArt['StkMovArtCant']."',StkArtAsignado=StkArtAsignado+'".$unMovArt['StkMovArtCant']."',StkArtUsuMod='".$_COOKIE['usuid']."',StkArtFchMod='".date("Y-m-d H:i")."' where StkArtId='".$unMovArt['StkArtId']."'";
	$update=mysqli_query($cn,$sentencia);
}

//Actualizo estado de factura, No actualizo el usuario ni fecha de modificacion, pues perdería quien certifico y confirmo factura en cuanto a articulos y montos. 
//En la tabla de StkMovArticulos queda registrado el otro usuario, quien certifica y confirma el ingreso de las cantidades al stock por la factura en cuestión
$sentencia="Update StkPrvFacturas set StkPrvFacFin='".$fin."', StkPrvFacObs='".$Obs."' where StkPrvFacId='".$idfac."'";
$update=mysqli_query($cn,$sentencia);
echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=proveedoresfacstk.php'>";
?>

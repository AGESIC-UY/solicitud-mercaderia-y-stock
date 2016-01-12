<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
	1.- Este objeto corresponde al ingreso del cabezal de factura. al guardar paso al objeto donde se ingresaría el detalle de la factura, articulo por articulo.
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$fchfac=$_REQUEST['fchfac'];

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{ 
	$fchele=cambiaf_a_mysql($_POST['fchfac']);
	$fchhoy=cambiaf_a_mysql(date("d/m/Y"));
	if ($fchele > $fchhoy)
	{
		echo 'Atenci&oacute;n: La fecha de factura no puede ser mayor a hoy';
	}
	else
	{
		if ($_POST['prvfacnum']=="0" or $_POST['prvfacnum']==NULL)
		{
			echo 'Atenci&oacute;n: el numero de factura no puede ser nulo';
		}
		else
		{
			//Reviso que factura no exista ya para el proveedor
			$sentenciaI="select * from StkPrvFacturas where StkPrvId='".$idprv."' and trim(StkPrvFacNum)='".trim($_POST['prvfacnum'])."'";
			$resultadoI = mysqli_query($cn, $sentenciaI);
			$unaFacPrv=mysqli_fetch_array($resultadoI);
			if (mysqli_affected_rows($cn)==0)
			{
				//Insertar factura del proveedor
				$sentencia="Insert into StkPrvFacturas (StkPrvId, StkPrvFacNum, StkPrvFacFch, StkPrvFacUsuCre, StkPrvFacFchCre) values ('".$idprv."','".$_POST['prvfacnum']."','".cambiaf_a_mysql($_POST['fchfac'])."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
				$facturaprv = mysqli_query($cn, $sentencia);
				if (mysqli_affected_rows($cn)==0)
				       echo 'Atenci&oacute;n: No se pudo ingresar la factura del proveedor '.mysqli_error();
				else
				{
					$sentencia="select * from StkPrvFacturas where StkPrvId='".$idprv."' and StkPrvFacNum='".$_POST['prvfacnum']."'";
					$resultado=mysqli_query($cn, $sentencia);
					$lafac=mysqli_fetch_assoc($resultado);
					$idfac=$lafac['StkPrvFacId'];
					echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=proveedoresfacver.php?idprv=$idprv'>";    
				}
			}
			else
			{
				echo 'Atenci&oacute;n: La factura ya esta registrada para el proveedor';
			}
		}
	}
} //finaliza accion guardar if

$sentencia="select * from StkProveedores where StkPrvId=".$idprv;
$resultado = mysqli_query($cn, $sentencia);
$unProveedor=mysqli_fetch_array($resultado);
$prvnom=$unProveedor['StkPrvRzoSoc'];
if (mysqli_affected_rows($cn)==0)
{
	echo 'Atenci&oacute;n: No se encontr&oacute; el Proveedor';
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
////////////////////////////////////////////////////
//Convierte fecha de mysql a normal
////////////////////////////////////////////////////
-->
function cambiaf_a_normal($fchfac){
    ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fchfac, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    return $lafecha;
}
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
<!--
////////////////////////////////////////////////////
//Convierte fecha de normal a mysql
////////////////////////////////////////////////////
-->
function cambiaf_a_mysql(){
	$fecha=$_POST['fchfac']
	$fch=explode("/",$fecha);
	$fecha=$fch[2]."-".$fch[1]."-".$fch[0];
	return $fecha;
} 
</SCRIPT>

<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/modificar.png" width="30" height="30" alt="Nuevo" border=0/> Ingreso Factura de Proveedor </font>
	<br>
	<font size="5" color="#000066">"<?php echo $prvnom;?>"</font>
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td>Factura Nro.:</td>
	 <td>
		<input type="text" name="prvfacnum" maxlength="120" size="12"/>
        </td>
      </tr>
      <tr>
	<?php
		include "calendariofactura.php";
	?>
      </tr>
    </table>
    </td>
  </tr>

  <tr>
    <td align="left">
	<a href="proveedoresfacver.php?idprv=<?php echo $idprv;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
    </td>
    <td align="left">
	<input type="image" width="40" height="40" src="Images/guardar.png">
	<input type="hidden" name="accion" value="guardar" >
    </td>
  </tr>

</table>
</form>
</center>

<?php
require_once("pie.php");
?>
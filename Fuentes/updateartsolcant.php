<?php
session_start();
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idart=$_REQUEST['idart'];
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_SESSION['StkSolArtCantAcred']<$_POST['cantacred'])
	{
		echo "<font color='#FF0000'>No puede acreditar mayor cantidad que la solicitada en el pedido</font>";
	}
	else
	{
		if ($_SESSION['StkArtCantReal']<$_POST['cantacred'])
		{
			echo "<font color='#FF0000'>No puede acreditar mayor cantidad de la existente en el stock</font>";
		}
		else
		{
			$ValorPendiente=($_SESSION['StkSolArtCantAcred']-$_POST['cantacred']);
			$sentenciaI="Update StkSolArticulos set StkSolArtCantPen=".$ValorPendiente.",StkSolArtCantAcred=".$_POST['cantacred'].",StkSolArtUsuMod=".$_COOKIE['usuid'].", StkSolArtFchMod='".date("Y-m-d H:i")."' where StkSolId=".$solid." and StkArtId='".trim($idart)."'";
			//echo $sentenciaI;
			if (!mysqli_query($cn,$sentenciaI))
			{
				('Error No se pudo modificar la cantidad'.mysqli_error());
			}
			else
			{
				unset($_SESSION['StkSolArtCantReal']);
				unset($_SESSION['StkSolArtCantAcred']);
				echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=disponibilidadstk.php?solid=$solid&estadocall=$estadocall&unidadcall=$unidadcall'>";
			}
		}  //fin if else cantidad acreditada supero stock
	}  //fin if else cantidad incorrecta
}  //fin if accion guardar	

$sentencia="select * from StkArticulos where StkArtId=".$idart;
$resultado = mysqli_query($cn,$sentencia);
$elArt=mysqli_fetch_array($resultado);
$_SESSION['StkArtCantReal']=$elArt['StkArtCantReal'];

if (mysqli_affected_rows($cn)==0)
{
	echo 'Atenci&oacute;n: No se encontr&oacute; Art&iacute;culo';
}
else
{
	$sentenciaII="select * from StkSolArticulos where StkSolId='".$solid."' and StkArtId='".$idart."'";
	$resultadoII=mysqli_query($cn,$sentenciaII);
	$laSolArt=mysqli_fetch_assoc($resultadoII);

	$tipomov="S";
	$sentenciaIII="Select * from StkMovArticulos where StkArtId='".$idart."' and StkSolId='".$solid."' and StkMovArtTpo='".$tipomov."'";
	$resultadoIII=mysqli_query($cn,$sentenciaIII);
	$CantEntregada=0;
       if (mysqli_num_rows($resultadoIII)==0)
	{
		$CantEntregada=0;
	}
	else
	{
		while($unMovArticulo=mysqli_fetch_assoc($resultadoIII))
		{
			$CantEntregada=$CantEntregada+$unMovArticulo['StkMovArtCant'];			
		}
	}
	$_SESSION['StkSolArtCantAcred']=$laSolArt['StkSolArtCantSol']-$CantEntregada;
}
?>

<SCRIPT LANGUAGE="JavaScript">
function validate(string) {
    if (!string) return false;
    var Chars = "0123456789";  <!--var Chars = "0123456789-"; incluyendo negativos-->
    for (var i = 0; i < string.length; i++)
	{
	if (Chars.indexOf(string.charAt(i)) == -1)
		return false;
	}
		return true;
	}
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
function menor(string) {
    if (string>$_SESSION['StkSolArtCantAcred']) return false;}
	return true;}
</SCRIPT>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="updateartsolcant.php?idart=<?php echo $idart;?>&solid=<?php echo $solid;?>&estadocall=<?php echo $estadocall;?>&unidadcall=<?php echo $unidadcall;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/editar.png" width="30" height="30" alt="Nuevo" border=0/>  Modificar Art&iacute;culo de Solicitud </font>
    </td>
    </tr>
    <tr>
	<td align="center" colspan="2">
	<font size="4" color="#000066">Solicitud:  <?php echo $solid;?></font>
	<br>
	<br>
	</td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left"><label>Art&iacute;culo:</label></td>
        <td>
            <input type="text" name="artdsc" maxlength="120" size="70" value="<?php echo $elArt['StkArtDsc']; ?>" readonly="readonly"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Cantidad Stock:</label></td>
        <td>
       	<input type="text" name="stkcant" maxlength="120" size="15" value="<?php echo $_SESSION['StkArtCantReal']; ?>" " readonly="readonly"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Cantidad Solicita:</label></td>
        <td>
       	<input type="text" name="poracred" maxlength="120" size="15" value="<?php echo $laSolArt['StkSolArtCantSol']; ?>" " readonly="readonly"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Entregas anteriores:</label></td>
        <td>
       	<input type="text" name="poracred" maxlength="120" size="15" value="<?php echo $CantEntregada; ?>" " readonly="readonly"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Entregar:</label></td>
        <td>
       	<input type="text" name="cantacred" maxlength="120" size="15" value="<?php echo $_SESSION['StkSolArtCantAcred']; ?>" onChange="if (!validate(this.value)) alert('Indique un valor num&eacute;rico')"/>
        </td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td align="left">
	<a href="disponibilidadstk.php?solid=<?php echo $solid; ?>&estadocall=<?php echo $estadocall; ?>&unidadcall=<?php echo $unidadcall; ?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
    </td>
    <td align="left">
	<input type="image" width="40" height="40" src="Images/guardar.png" >
	<input type="hidden" name="accion" value="guardar"/>
    </td>
  </tr>
</table>
<br>
</form>
</center>
</html>
<?php
require_once("pie.php");
?>
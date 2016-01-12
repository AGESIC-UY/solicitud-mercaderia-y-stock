<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['prvrzosoc']=="")
		echo "<font color='#FF0000'>La Raz&oacute;n Social del Proveedor no puede ser vac&iacute;a</font>";
	else
	{
		$service= 0;
		if (isset($_POST['service']) and $_POST['service']=='on')
		{
			$service= 1;
		}
		$sentencia="Update StkProveedores set StkPrvRzoSoc='".$_POST['prvrzosoc']."', StkPrvRut='".$_POST['rut']."', StkPrvDir='".$_POST['prvdir']."', StkPrvTel='".$_POST['prvtel']."', StkPrvFax='".$_POST['prvfax']."', StkPrvMail='".$_POST['prvmail']."', StkPrvObs='".$_POST['prvobs']."', StkPrvService='".$service."', StkPrvUsuMod='".$_COOKIE['usuid']."', StkPrvFchMod='".date("Y-m-d H:i")."' where StkPrvId=".$idprv;
		$proveedor = mysqli_query($cn, $sentencia);
		if ($proveedor==0)
			echo 'Atenci&oacute;n: No se pudo modificar ficha del Proveedor por el error: '.mysqli_error();
		else
		{
			echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=proveedores.php'>";
		}//fin echo $sentencia
	} //fin else modifica proveedor
}  //fin else descripción vacia y guardo
$sentencia="select * from StkProveedores where StkPrvId=".$idprv;
$resultado = mysqli_query($cn, $sentencia);
$unProveedor=mysqli_fetch_array($resultado);
if (mysqli_affected_rows($cn)==0)
{
	echo 'Atenci&oacute;n: No se encontr&oacute; Proveedor';
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
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="updateprv.php?idprv=<?php echo $idprv;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/modificar.png" width="30" height="30" alt="Nuevo" border=0/>  Ficha del Proveedor  </font>
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left"><label>Raz&oacute;n Social:</label></td>
        <td>
            <input type="text" name="prvrzosoc" maxlength="120" size="70" value="<?php echo $unProveedor['StkPrvRzoSoc']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Rut:</label></td>
        <td>
            <input type="text" name="rut" maxlength="120" size="70" value="<?php echo $unProveedor['StkPrvRut']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Direcci&oacute;n:</label></td>
        <td>
	    	<input type="text" name="prvdir" maxlength="120" size="70" value="<?php echo $unProveedor['StkPrvDir']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Mail:</label></td>
        <td>
       	<input type="text" name="prvmail" maxlength="120" size="70" value="<?php echo $unProveedor['StkPrvMail']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Tel&eacute;fono:</label></td>
        <td>
            <input type="text" name="prvtel" maxlength="70" size="30" value="<?php echo $unProveedor['StkPrvTel']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Fax:</label></td>
        <td>
       	<input type="text" name="prvfax" maxlength="70" size="30" value="<?php echo $unProveedor['StkPrvFax']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Brinda Service?</label></td>
        <td>
	    	<input name="service" type="checkbox" value="1" <?php if($unProveedor['StkPrvService'] == 1) { ?>checked<?php } ?> />
        </td>
      </tr>
      <tr>
        <td align="left"><label>Observaciones:</label></td>
        <td>
       	<input type="text" name="prvobs" maxlength="120" size="70" value="<?php echo $unProveedor['StkPrvObs']; ?>"/>
        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left">
	<a href="proveedores.php"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
    </td>
    <td align="left">
	<input type="image" width="40" height="40" src="Images/guardar.png">
	<input type="hidden" name="accion" value="guardar" >
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
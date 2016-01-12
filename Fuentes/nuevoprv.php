<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");

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
		$sentencia="Insert into StkProveedores (StkPrvRzoSoc, StkPrvRut, StkPrvDir, StkPrvTel, StkPrvFax, StkPrvMail, StkPrvObs, StkPrvService, StkPrvUsuCre, StkPrvFchCre) values ('".$_POST['prvrzosoc']."','".$_POST['rut']."','".$_POST['prvdir']."','".$_POST['prvtel']."','".$_POST['prvfax']."','".$_POST['prvmail']."','".$_POST['prvobs']."','".$service."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
		$proveedor = mysqli_query($cn, $sentencia);
		if (mysqli_affected_rows($cn)==0)
			echo 'Atenci&oacute;n: No se pudo ingresar el proveedor por el error: '.mysqli_error();
		else
		{//echo $sentencia;
			echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=nuevoprv.php'>";
			?>
		       <script type="text/javascript"> alert ("Se ha ingresado con  \xE9xito");</script>
			<?php
		}//fin echo $sentencia
	} //fin else ingreso proveedor
}  //fin else descripción vacia y guardo
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
<form name="datos" action="nuevoprv.php" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/nuevo.png" width="30" height="30" alt="Nuevo"/>   Nuevo Proveedor de Mercader&iacute;a y/o Servicio</font>
	<hr style="color: rgb(69, 106, 221);">
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left"><label>Raz&oacute;n Social:</label></td>
        <td>
            <input type="text" name="prvrzosoc" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Rut:</label></td>
        <td>
            <input type="text" name="rut" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Direcci&oacute;n:</label></td>
        <td>
       	<input type="text" name="prvdir" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Mail:</label></td>
        <td>
       	<input type="text" name="prvmail" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Tel&eacute;fono:</label></td>
        <td>
            <input type="text" name="prvtel" maxlength="70" size="15"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Fax:</label></td>
        <td>
       	<input type="text" name="prvfax" maxlength="70" size="15"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Brinda Service?</label></td>
        <td>
       	<input type="checkbox" name="service"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Observaciones:</label></td>
        <td>
       	<input type="text" name="prvobs" maxlength="120" size="70"/>
        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left">
	<a href="proveedores.php"><img width="40" height="40" src="Images/volver.jpg"></a>
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
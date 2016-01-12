<!--
Creación:	Alicia Acevedo
Fecha:		12/2010
Algunas Características:
	1.- Todo usuario nuevo se le indica una contraseña por defecto "nueva", esta es palabra reservada para reconocer que esta iniciando por primera vez y se
	le obliga a cambiar la contraseña. Desde login.php llamamos a este objeto si es el caso, sino pasa directamente el index.php
-->
<?php
function revisopost($variable)
{
	$variable=addslashes(trim($variable));
	return $variable;
}

ob_start();
session_start();
require_once("funcionesbd.php");
$miEstado=$_COOKIE['estadosol'];
$miUnidadEle=$_COOKIE['usuunidadele'];
$usuid=$_COOKIE['usuid'];
$passres="nueva";
$clase="99";
$articulo="99";
$dia = date('d');
$mes = date('m');
$year = date('y')+2000;
$fchhasta=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $year));
$fchdesde=date("d/m/Y", mktime(0, 0, 0, $mes, 1, $year));
$valor=0;

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='Ingresar')
{
	$pass=$_POST['pass'];
	$passII=$_POST['passII'];
	if (isset($pass) and $pass<>"" and $pass<>Null)
	{
		if (isset($passII) and $passII<>"" and $passII<>Null)
		{
			$pass=revisopost($pass);
			$passII=revisopost($passII);
			if (trim($passII)<>trim($pass))
			{
				$_SESSION['logged']=2;
				?>
		       	<script type="text/javascript"> alert("La contraseña y su repetición no son iguales, rectifique");</script>
				<?php
			}
			else
			{
				if (trim($pass)==$passres)
				{
					$_SESSION['logged']=2;
					?>
			    	   	<script type="text/javascript"> alert("Cambie contrase&ntilde;a la palabra nueva es una palabra reservada");</script>
					<?php
				}
				else
				{
					if($pwdvieja==trim(MD5($pass)))
					{
						$_SESSION['logged']=2;
						?>
			    		   	<script type="text/javascript"> alert("Cambie su contrase&ntilde;a no repita la anterior");</script>
						<?php
					}
					else
					{
						$sentencia="Update usuarios set UsuPass='".MD5($pass)."', UsuFchMod='".date("Y-m-d H:i:s")."', UsuPassInicia='".$valor."' where UsuId=".$usuid;
						$usuario = mysqli_query($cn, $sentencia);
						$_SESSION['logged']=1;
						$_SESSION["ultimoAcceso"]= date("Y-n-j H:i:s");
					       if ($_COOKIE['usuperfil']<8)
						{
							echo '<meta http-equiv="refresh" content="0; url=index.php?estado='.$miEstado.'&unidad='.$miUnidadEle.'">';
						}
						else
						{
						       if ($_COOKIE['usuperfil']==8)
							{
								echo '<meta http-equiv="refresh" content="0; url=articulos.php?clase='.$clase.'&articulo='.$articulo.'&fchdesde='.$fchdesde.'&fchhasta='.$fchhasta.'">';
							}
						       if ($_COOKIE['usuperfil']==9)
							{
								echo '<meta http-equiv="refresh" content="0; url=proveedores.php">';
							}
						       if ($_COOKIE['usuperfil']==10 or $_COOKIE['usuperfil']==11)
							{
								echo '<meta http-equiv="refresh" content="0; url=articulosinv.php?clase='.$clase.'&articulo='.$articulo.'&fchdesde='.$fchdesde.'&fchhasta='.$fchhasta.'">';
							}
						}
					}
				}
			}
		}
		else
		{
			$_SESSION['logged']=2;
			?>
	       	<script type="text/javascript"> alert("Debe repetir contraseña para confirmar el cambio");</script>
			<?php
		}
	}
	else
	{
		$_SESSION['logged']=2;
		?>
       	<script type="text/javascript"> alert("Debe ingresar contraseña para confirmar el cambio");</script>
		<?php
	}
}
if ($accion=='Cancelar')
{
	echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
$consulta="Select * from usuarios where UsuId='".$usuid."'";
$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
$miUsuario=mysqli_fetch_assoc($resultado);
$usuusu=$miUsuario['UsuUsuario'];
$pwdvieja=$miUsuario['UsuPass'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<html>
<style type="text/css">
</style><head><title>Sistema de Solicitud de Mercader&iacute;a y Stock</title><link rel="stylesheet" type="text/css" href="Estilos/color.css" title="default">
</head>

<style type="text/css" media="all">
img {
border:none

}

table {

background-color:#E5E5E5
}

 body {
    background-image: url(Images/LogoFondo.jpg);
	background-position:center;


}
</style>

<body>
<center>
<form name="form1" method="post" action="nuevopass.php" >
<div align="center"  style="margin-top:150; border: medium #666666; width:509">
  <table  height="150" cellpadding="0" cellspacing="0" id="table" >
    <tr>
    <td height="150" rowspan="3" align="center" ><img src="Images/LogoLogin.jpg" height="150" ></td>
  </tr>
  <tr>
    <td>
    <table width="400" align="center" id="table">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="right"><label>Usuario:</label></td>
        <td><input type="text" name="usuario" value="<?php echo $usuusu; ?>" " disabled="true" readonly="readonly"/>&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td align="right"><label>Nueva ContraseÃ±a:</label></td>
        <td><input type="password" name="pass" value="<?php echo $pwdvieja; ?>"/>&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td align="right"><label>Repita Nueva ContraseÃ±a:</label></td>
        <td><input type="password" name="passII" value="<?php echo $pwdvieja; ?>"/>&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <td align="right"><input type="submit" name="accion" value="Ingresar" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="accion" value="Cancelar" />&nbsp;&nbsp;&nbsp;
  <br>
  <br>
  </td>
  </tr>
  </tr>
  <tr bgcolor="#999999" ><td height="40" align="center" valign="middle" bgcolor="#608BCC"></td> 
    <td height="40" align="center" valign="middle" bgcolor="#608BCC"><a href="docs/Instructivo_Acceso.pdf" target="_blank" >Descargar ->> Instructivo de Acceso</a><br></td>
  </tr>
</table>
</div>
</form>
</center>
</body>
</html>
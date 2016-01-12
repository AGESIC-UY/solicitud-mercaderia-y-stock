<?php
function revisopost($variable)
{
	$variable=addslashes(trim($variable));
	return $variable;
}
?>
<?php
session_destroy();
session_start();
$_SESSION['logged']=2;
require_once("funcionesbd.php");
$miUnidadEle="0";
$miAreaEle="0";

//Para usuario con rol Articulador
$clase="99";
$articulo="99";
$dia = date('d');
$mes = date('m');
$year = date('y')+2000;
$fchhasta=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $year));
$fchdesde=date("d/m/Y", mktime(0, 0, 0, $mes, 1, $year));
$fchhoy=date("Y-m-d");
if (isset($_POST['usuario']))
{
	$valorp=revisopost($_POST['usuario']);
	$consulta="Select * from Usuarios where usuUsuario='".$valorp."'";
	$resultadousuarios=mysqli_query($cn,$consulta) or die('La consulta 1fall&oacute;: ' .mysqli_error());
	$miUsuario=mysqli_fetch_assoc($resultadousuarios);
	if(mysqli_num_rows($resultadousuarios)==0)
	{
		?>
		<script type="text/javascript"> alert("El usuario ingresado no existe");</script>
              <?php
	}
	else
	{
		if ($miUsuario['UsuFchFin']<>NULL)
		{
			?>
			<script type="text/javascript"> alert("Este usuario ha sido dado de baja");</script>
              	<?php
		}
		else
		{
			$consulta="Select * from sistemas"; //un solo reg
			$resultado=mysqli_query($cn,$consulta) or die('La consulta 2fall&oacute;: ' .mysqli_error());
			$miSistema=mysqli_fetch_assoc($resultado);

			$consulta="Select * from departamentos where DepId='".$miUsuario['seccionesId']."'";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta 3fall&oacute;: ' .mysqli_error());
			$miSeccion=mysqli_fetch_assoc($resultado);

			$consulta="Select * from SisPflUsuarios where UsuId='".$miUsuario['UsuId']."'";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta 4fall&oacute;: ' .mysqli_error());
			$miPerfil=mysqli_fetch_assoc($resultado);

			$consulta="Select * from SisPerfiles where SisPflId='".$miPerfil['SisPflId']."'";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta 5fall&oacute;: ' .mysqli_error());
			$miPermiso=mysqli_fetch_assoc($resultado);
	
			$miAreaEle=$miSeccion['DepIdDep'];
			if ($miPermiso['SisPflUniAll']=='N')
			{
			$miUnidadEle=$miUsuario['seccionesId'];
			}
			else
			{
			$miUnidadEle="0";
			}

			$miEstado=$miPermiso['SisPflEstIni']; //En el atributo se indica el Estado en el cual inicia el perfil para que por default inicie en ese estado
			$TipoBien=$miPermiso['SisPflBien']; //Uso o Consumo según el usuario logged

	
			if (setcookie("usuario", $valorp))
			{
				setcookie("estadosol",$miEstado);
				setcookie("usuid",$miUsuario['UsuId']);
				setcookie("usuherederos",$miSeccion['DepHerederos']);
				setcookie("usuunidad",$miUsuario['seccionesId']);
				setcookie("usuunidadele",$miUnidadEle);
				setcookie("usuareaele",$miAreaEle);
				setcookie("usupermiso",$miPermiso['SisPflUniAll']);
				setcookie("ususeccion",$miSeccion['DepNombre']);
				setcookie("usuperfil",$miPerfil['SisPflId']);
				setcookie("usumail",$miUsuario['UsuMail']);
				setcookie("tipobien",$TipoBien);
				setcookie("parcial",$miSistema['SisStkEntParcial']);

				if ($miPermiso['SisPflUniAll']=='S')
				{
					$consulta="Select * from StkArtCls as c, StkArtClsUsu as u where c.StkArtClsBien='".$TipoBien."' and c.StkArtClsId=u.StkArtClsId and u.UsuId='".$miUsuario['UsuId']."'";
					$resultado=mysqli_query($cn,$consulta);
					$All=0;
					while($unaCls=mysqli_fetch_assoc($resultado))
					{
						$clase=$unaCls['StkArtClsId'];
						$All=$All+1;
					}
					if ($All>1) //tiene permiso para ver todos los articulos, no exclusivamente los indicados en StkArtClsUsu
					{
						$clase="99";
					}
				}

				$valort=$miUsuario['UsuPass'];
				$valors=$_POST['pass'];
				if($miUsuario['UsuPassInicia']==1)			
				{//sin md5
					if($valort==$valors) 
					{
						echo '<meta http-equiv="refresh" content="0; url=nuevopass.php">';
					}				
					else
					{
						?>
						<script type="text/javascript"> alert("Error en el usuario y/o contraseña");</script>
				       	<?php
					}							
				}
				else
				{
					if($valort==trim(MD5($valors))) 
					{
						setcookie("lastlog",$miUsuario['UsuLogLast']);				
						$fchnow=date("Y-m-d H:i:s");
						$valors=$miUsuario['UsuId'];
						$sentencia="Update usuarios set UsuLogLast='".$fchnow."' where UsuId=".$valors;
						$resultado=mysqli_query($cn,$sentencia);
						
						$fchhoy=date("Y-m-d");
						$fecha=strtotime($fchhoy);
						$fecha=date("Y-m-d H:i:s",$fecha);			
						$_SESSION['logged']=1;
						$_SESSION["ultimoAcceso"]= date("Y-n-j H:i:s");

					       if ($miPerfil['SisPflId']==8)//Articulador
						{
							$actividad="1";
							echo '<meta http-equiv="refresh" content="0; url=articulos.php?clase='.$clase.'&articulo='.$articulo.'&fchdesde='.$fchdesde.'&fchhasta='.$fchhasta.'&actividad='.$actividad.'">';
						}
						else
						{
						       if ($miPerfil['SisPflId']==9)// Proveedores
							{
								echo '<meta http-equiv="refresh" content="0; url=proveedores.php">';
							}
							else
							{//Operador, Administrador, Solicitante, Autorizador, Consultor - Financiero, Consultor - Unidad
		//						if ($_COOKIE['usuid']=='80')
		//						{
								echo '<meta http-equiv="refresh" content="0; url=index.php?estado='.$miEstado.'&unidad='.$miUnidadEle.'">';
		//						}
		//						else
		//						{
		//						echo "Aplicacion en reparacion, disculpe las molestias!!!";
		//						}
							}
						}
					}
					else
					{
						?>
						<script type="text/javascript"> alert("Error en el usuario y/o contraseña");</script>
				       	<?php
					}							
				}
			}
			else
			{
				die("No pudo escribir las cookies");
			}
		}
	}
}
	$consulta="Select * from sistemas"; //un solo reg
	$resultado=mysqli_query($cn,$consulta) or die('La consulta 6fall&oacute;: ' .mysqli_error());
	$miSistema=mysqli_fetch_assoc($resultado);
	$logologin=$miSistema['SisLogoLogin'];
	$logofondo=$miSistema['SisLogoFondo'];
	$sisactivo=$miSistema['SisActivo'];	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<html>
<style type="text/css">
</style><head><title>Sistema de Solicitud de Mercader&iacute;a y Stock </title><link rel="stylesheet" type="text/css" href="Estilos/color.css" title="default">
</head>

<style type="text/css" media="all">
img 
{
border:none
}
table
{
background-color:#E5E5E5
}
body 
{
background-color:#DCDCDC;
background-position:center;
<!--background-image: url('<?php echo $logofondo;?>')-->;
}
</style>
<body>
<center>
<form name="form1" method="post" action="login.php" >
<div align="center"  style="margin-top:150; border: medium #666666; width:509">
  <table  height="150" cellpadding="0" cellspacing="0" id="table" >
    <tr>
<?php 
	echo '<td height="150" rowspan="3" align="center" ><img src="'.$logologin.'" height="150" ></td>';
?>
  </tr>
  <tr>
    <td>
    <table width="316" align="center" id="table">
      <tr>
        <td align="right"><label>Usuario:</label></td>
        <td><input type="text" name="usuario" width="200" /></td>
      </tr>
      <tr>
        <td align="right"><label>ContraseÃ±a:</label></td>
        <td><input type="password" name="pass"  width="200" /></td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
<?php 
if ($sisactivo==1)
{
?>
	<td align="center"><input type="submit" name="Submit" value="Ingresar" /> </td>
<?php
}
else
{
	echo "<td align='center'><font size='4' color='#FF0000'>Sistema Inactivo, se encuentra en Mantenimiento</font></td>";
}
?>
  </tr>
  <tr bgcolor="#999999" ><td height="40" align="center" valign="middle" bgcolor="#608BCC"></td> 
    <td height="40" align="center" valign="middle" bgcolor="#608BCC"><a href="docs/Instructivo_Acceso.pdf" target="_blank" >Descargar ->> Instructivo de Acceso</a></td>
  </tr>
</table>
</div>
</form>
<br><br>
</center>
</body>
</html>
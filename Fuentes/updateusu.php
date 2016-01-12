<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.7.3.custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/jquery-ui.min.js"></script>
<script type="text/javascript">
jQuery(function($){
	$.datepicker.regional['es'] = {
		closeText: 'Cerrar',
		prevText: '&#x3c;Ant',
		nextText: 'Sig&#x3e;',
		currentText: 'Hoy',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
		dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['es']);
});    
 
$(document).ready(function() {
   $("#datepicker").datepicker();
 });

$(document).ready(function() {
   $("#datepickerI").datepicker();
 });

</script>

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$usuele=$_REQUEST['usuele'];
$unidad=$_REQUEST['unidad'];
$fchhoy=date("Y-m-d");
$esu=1;

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['usunom']=="" or $_POST['usuape']=="")
	{
		echo "<font color='#FF0000'>Ni el Nombre ni el Apellido pueden ser vacios</font>";
	}
	else
	{
		$inactivo= 0;
		if (isset($_POST['inactivo']) and $_POST['inactivo']=='on')
		{
			$sentencia="Update Usuarios set UsuNombre='".$_POST['usunom']."', UsuApellido='".$_POST['usuape']."', UsuMail='".$_POST['usumail']."', SeccionesId='".$_POST['ususec']."', UsuUsuMod='".$_COOKIE['usuid']."', UsuFchMod='".date("Y-m-d H:i")."', UsuFchFin='".date("Y-m-d H:i")."' where UsuId=".$usuele;
		}
		else
		{
			$sentencia="Update Usuarios set UsuNombre='".$_POST['usunom']."', UsuApellido='".$_POST['usuape']."', UsuMail='".$_POST['usumail']."', SeccionesId='".$_POST['ususec']."', UsuUsuMod='".$_COOKIE['usuid']."', UsuFchMod='".date("Y-m-d H:i")."', UsuFchFin=NULL where UsuId=".$usuele;
		}

		if (isset($_POST['inipass']) and $_POST['inipass']=='on')
		{
			$usupass= 'nueva';
			$sentencia="Update Usuarios set UsuPass='".$usupass."', UsuPassInicia='".$esu."' where UsuId=".$usuele;
		}

		$usuario = mysqli_query($cn, $sentencia);
		if ($usuario==0)
		{
			echo 'Atenci&oacute;n: No se pudo modificar ficha del usuario por el error: '.mysqli_error();
		}
		else
		{
			$sentencia="Update SisPflUsuarios set SisPflId='".$_POST['usupfl']."', SisPflUsuUsuMod='".$_COOKIE['usuid']."', SisPflUsuFchMod='".date("Y-m-d H:i")."' where UsuId=".$usuele;
			$perfilusu = mysqli_query($cn, $sentencia);
			if ($perfilusu==0)
			{
				echo 'Atenci&oacute;n: No se pudo modificar el perfil del usuario por el error: '.mysqli_error();
			}
			else
			{
				//Siempre elimino usudeppri en true, e inserto la unidad de la que depende no importando si esta cambio
				//Descartando la posibilidad de error.
				//No importando si el usuario tiene o no rol de autorizador, este registro debe existir siempre en la tabla de UsuDep

				$usudeppri=1;
				$sentenciaI="Delete from UsuDep where UsuId='".$usuele."' and UsuDepPri='".$usudeppri."'";
				if (!mysqli_query($cn,$sentenciaI))
				{
					die('Error al eliminar la unidad del usuario en UsuDep avisar a Informatica'.mysqli_error());
				}
				else
				{
					$sentenciaI="Insert into UsuDep (UsuId, DepId, UsuDepPri, UsuDepFchIni, UsuDepUsuCre, UsuDepFchCre) values('".$usuele."','".$_POST['ususec']."','".$usudeppri."','".date("Y-m-d H:i")."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
					$usudep= mysqli_query($cn, $sentenciaI);
				}

				//StkArtClsUsu cuenta con las clases de clasificación de los articulos, para que el usuario pueda consultar los articulos de una clase
				//debe estar vinculado a la clase en esta tabla, es el caso de usuario con rol Administrador, Operador, Consultor-financiero y Articulador
				//Los que no cuentan con este acceso son los usuarios con rol de: Solicitante, Consultor-Unidad, Proveedores.
				//Y en el caso de rol Autorizador(por ahora es el caso de autorizadores en Informatica). Lo tratamos en forma manual para permitir a este 
				//autorizador consultar "Articulos Informaticos - Insumos". Es un caso muy especifico. Insertamos los registros en forma manual

				//Para mantener la información para los roles mencionados, salvo el caso mencionado de Autorizador, inserto todas las clases para el usuario
				//si este es rol Administrador, Operador, Consultor-financiero y Articulador
				//Descartando la posibilidad de error.

				//Previamente elimino los registros existentes por si lo que estoy haciendo en call a este objeto es el update del rol, podría no contar con los
				//mismos accesos o premisos de clase

				if ($_POST['usupfl']<>5)//Autorizador(manual)
				{
					$sentenciaI="Delete from StkArtClsUsu where UsuId='".$usuele."'";
					if (!mysqli_query($cn,$sentenciaI))
					{
						die('Error al eliminar las clases de articulos para el usuario - avisar a Informatica'.mysqli_error());
					}
					else
					{
						if ($_POST['usupfl']<>1 and $_POST['usupfl']<>5 and $_POST['usupfl']<>7 and $_POST['usupfl']<>9)//solicitante, consultor-unidad, proveedores no corresponde articulos
						{
							$sentencia="select * from StkArtCls";
							$resultado = mysqli_query($cn, $sentencia);
							while($unaclase=mysqli_fetch_assoc($resultado))
							{
								$esuno=1;
								$sentenciaI="Insert into StkArtClsUsu (UsuId, StkArtClsId,StkArtClsHab) values('".$usuele."','".$unaclase['StkArtClsId']."','".$esuno."')";
								$clsusu= mysqli_query($cn, $sentenciaI);
							}
						}
					}
				}
				echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=usuarios.php?usuele=$usuele&unidad=$unidad'>";
			}
		} //fin else modifica usuario

	} //fin else modifica usuario

}  //fin else descripción vacia y guardo

$sentencia="select * from Usuarios as u, SisPflUsuarios as p, SisPerfiles as s, Departamentos as d where u.UsuId='".$usuele."' and u.UsuId=p.UsuId and s.SisPflId=p.SisPflId and u.SeccionesId=d.DepId";
$resultado = mysqli_query($cn, $sentencia);
$unUsuario=mysqli_fetch_array($resultado);
if (mysqli_affected_rows($cn)==0)
{
	echo 'Atenci&oacute;n: No se encontr&oacute; Usuario';
}

?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="updateusu.php?usuele=<?php echo $usuele;?>&unidad=<?php echo $unidad;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/modificar.png" width="30" height="30" alt="Nuevo" border=0/>  Ficha del Usuario  </font>
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
      <tr>
        <td align="left">Nombre:</td>
        <td>
            <input type="text" name="usunom" maxlength="120" size="70" value="<?php echo $unUsuario['UsuNombre']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left">Apellido:</td>
        <td>
       	<input type="text" name="usuape" maxlength="120" size="70" value="<?php echo $unUsuario['UsuApellido']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left">Mail privado:</td>
        <td>
       	<input type="text" name="usumail" maxlength="120" size="70" value="<?php echo $unUsuario['UsuMail']; ?>"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Repartici&oacute;n:</td>
        <td>
	       <select name="ususec">
		<?php
			$consultaI="Select * from Departamentos order by DepNombre";
			$resultadoI=mysqli_query($cn,$consultaI);
			while($usudep=mysqli_fetch_assoc($resultadoI))
			{
				$depid=$usudep['DepId'];
				$depnom=$usudep['DepNombre'];
				if ($depid==$unUsuario['SeccionesId'])
				{
					echo "<option value='".$depid."' selected>".$depnom."</option>";
				}
				else
				{
					echo "<option value='".$depid."'>".$depnom."</option>";
				}
			}
		?>	
	       </select>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Rol o Perfil:</td>
        <td>
	       <select name="usupfl">
		<?php
			$consultaI="Select * from SisPerfiles order by SisPflDsc";
			$resultadoI=mysqli_query($cn,$consultaI);
			while($pfl=mysqli_fetch_assoc($resultadoI))
			{
				$pflid=$pfl['SisPflId'];
				$pfldsc=$pfl['SisPflDsc'];
				if ($pflid==$unUsuario['SisPflId'])
				{
					echo "<option value='".$pflid."' selected>".$pfldsc."</option>";
				}
				else
				{
					echo "<option value='".$pflid."'>".$pfldsc."</option>";
				}
			}
		?>	
	       </select>
        </td>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Usuario:</td>
        <td>
       	<input type="text" name="usuusu" maxlength="120" size="70" value="<?php echo $unUsuario['UsuUsuario']; ?>" readonly="readonly"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Reiniciar Contrase&ntilde;a:</td>
        <td>
	   	<input name="inipass" type="checkbox" />
        </td>
      </tr>
      <tr>
        <td align="left"><label>Inactivo?</td>
        <td>
  	 <?php
	 if($unUsuario['UsuFchFin'] <> null)
	 {
	 ?>
	   	<input name="inactivo" type="checkbox" checked/>&nbsp;&nbsp;&nbsp;<?php echo cambiaf_a_normal($unUsuario['UsuFchFin']); ?>
	 <?php
	 }
	 else
	 {
 	 ?>
	   	<input name="inactivo" type="checkbox" />
	 <?php
	 }
	 ?>
        </td>
      </tr>
    </table></td>
  </tr>

  <tr>
    <td align="left">
	<a href="usuarios.php?usuele=<?php echo $usuele;?>&unidad=<?php echo $unidad;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
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
//require_once("ambientesusu.php");
require_once("pie.php");
?>
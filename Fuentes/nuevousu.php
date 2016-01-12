<!--
Este objeto va a ser de acceso al Administrador del Sistema
Al dar nuevo usuario se debe insertar registro en usudep con sección a la que pertenece, e indicar especificamente en true el atributo
UsuDepPri, en esta tabla se podrán ir incorporando el vinculo de otras unidades si es que el usuario puede actuar de autorizador de otras 
que no es la unidad propia, en este último caso UsuDepPri va en false.

Si el usuario Autorizador cambia de rol, debería eliminar los registros en el Usudep distintos de la unidad primaria indicada como tal en la tabla
o la que se indica en seccionId en la tabla de usuarios. Este dato se encuentra redundante pues hubo que integrar la tabla usudep para el requerimiento
nuevo de "autoriazador compartido", pero luego resulto comodo reutilizarla para desplegar todas las solicitudes de las unidades en la que es autorizador
en la misma pantalla(index.php), sino el usuario debe seleccionar en el cabezal que undidad quería consultar y resultaba incomodo. 

A la inversa solo indico el rol, de ser autorizador compartido se ira creando a medida que se indique.

Similar la situación en la que cambia la unidad primaria, debo eliminar la primaria original, insertar la nueva unidad primaria en usudep si no existe e indicarla como tal
Si existe solo la indico como primaria. Y elimino las secundarias
-->

<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$uniusu='99999';
$esu=1;

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['usunom']=="" or $_POST['usuape']=="" or $_POST['usumail']=="")
		echo "<font color='#FF0000'>La informaci&oacute;n no ha sido completada correctamente</font>";
	else
	{
		$inicial=trim($_POST['usunom']);
		$masuno=0;
		$usuusu=trim(substr($inicial, 0, 1)).trim($_POST['usuape']);
		$usubusco=$usuusu;
		$buscandousuario=0;
		while($buscandousuario=='0')
		{//debo analizar si el UsuUsuario ya no existe, de existir incremento un autonumerico al final
			$consulta="Select * from Usuarios where UsuUsuario='".$usubusco."'";
			$resultado=mysqli_query($cn,$consulta);
			if (mysqli_affected_rows($cn)==0)
			{
				$buscandousuario=1;
			}
			else
			{//encontre el usuario, debo concatenar un numero(incremental) para el usuario que estoy creando y volver a buscar que este no exista
				$masuno=$masuno+1;
				$usubusco=$usuusu.$masuno;
			}
		}

		$usuusu = strtolower($usubusco);
		$sentencia="Insert into Usuarios (UsuUsuario, UsuNombre, UsuApellido, SeccionesId, UsuFchCre, UsuUsuCre, UsuMail) values('".$usuusu."','".$_POST['usunom']."','".$_POST['usuape']."','".$_POST['sec']."','".date("Y-m-d H:i")."','".$_COOKIE['usuid']."','".$_POST['usumail']."')";

		$usuarionew = mysqli_query($cn, $sentencia);
		if (mysqli_affected_rows($cn)==0)
			echo 'Atenci&oacute;n: No se pudo ingresar el Usuario por el error: '.mysqli_error();
		else
		{//echo $sentencia;
			//Debo localizar el usuid autonumerico generado luego del insert
			$consulta="Select * from Usuarios where UsuUsuario='".$usuusu."'";
			$resultado=mysqli_query($cn,$consulta);
			while($usunew=mysqli_fetch_assoc($resultado))
			{
				$usuele=$usunew['UsuId'];
				$sentencia="Insert into SisPflUsuarios (SisId, SisPflId, UsuId, SisPflUsuUsuCre, SisPflUsuFchCre) values ('".$esu."','".$_POST['pfl']."','".$usuele."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
				$usuariopfl = mysqli_query($cn, $sentencia);

				//Creo la unidad primaria del nuevo usuario y debo identificarla como tal. La información en la tabla UsuDep corresponde a los departamentos o 
				//secciones en las que el usuario tiene vinculo, como usuario perteneciente y en el caso de autorizador a las que podría actuar como autorizador
				$usudeppri=1;
				$sentenciaI="Insert into UsuDep (UsuId, DepId, UsuDepPri, UsuDepFchIni, UsuDepUsuCre, UsuDepFchCre) values('".$usuele."','".$_POST['sec']."','".$usudeppri."','".date("Y-m-d H:i")."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
				$usudep= mysqli_query($cn, $sentenciaI);

				//StkArtClsUsu cuenta con las clases de clasificación de los articulos, para que el usuario pueda consultar los articulos de una clase
				//debe estar vinculado a la clase en esta tabla, es el caso de usuario con rol Administrador, Operador, Consultor-financiero y Articulador
				//Los que no cuentan con este acceso son los usuarios con rol de: Solicitante, Consultor-Unidad, Proveedores.
				//Y en el caso de rol Autorizador(por ahora es el caso de autorizadores en Informatica). Lo tratamos en forma manual para permitir a este 
				//autorizador consultar "Articulos Informaticos - Insumos". Es un caso muy especifico. Insertamos los registros en forma manual

				//Para mantener la información para los roles mencionados, salvo el caso mencionado de Autorizador, inserto todas las clases para el usuario
				//si este es rol Administrador, Operador, Consultor-financiero y Articulador
				//Descartando la posibilidad de error.

				$esuno=1; //Por defecto clases habilitadas
				if ($_POST['pfl']<>1 and $_POST['pfl']<>5 and $_POST['pfl']<>7 and $_POST['pfl']<>9)//solicitante, Autorizador(manual), consultor-unidad, proveedores no corresponde articulos
				{

					$sentencia="select * from StkArtCls";
					$resultado = mysqli_query($cn, $sentencia);
					while($unaclase=mysqli_fetch_assoc($resultado))
					{
						$sentenciaI="Insert into StkArtClsUsu (UsuId, StkArtClsId, StkArtClsHab) values('".$usuele."','".$unaclase['StkArtClsId']."','".$esuno."')";
						$clsusu= mysqli_query($cn, $sentenciaI);
					}
				}
			}
			echo "<META HTTP-EQUIV='refresh' CONTENT='1; URL=usuenviomail.php?usuele=$usuele'>";
			?>
			<script type="text/javascript"> alert ("Se ha ingresado con  \xE9xito");</script>
			<?php
		}//fin echo $sentencia
	} //fin else ingreso solicitud
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
<form name="datos" action="nuevousu.php" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="2">
	<br>
	<font size="5" color="#000066"><img src="Images/nuevo.png" width="30" height="30" alt="Nuevo" border=0/>   Nuevo Usuario de Sistema</font>
	<hr style="color: rgb(69, 106, 221);">
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="2">
    <table class="inventario">
	<tr>
	<font size="4" color="#000066">Cuando confirme, el sistema generar&aacute; el usuario y enviar&aacute; un mail con el usuario y contrase&ntilde;a inicial. Durante el primer ingreso,el sistema le pedir&aacute sustituir dicha contrase&ntilde;a por una personal.</font>
	<br>
	</tr>
	<tr>
	<font size="4" color="#000066">La direcci&oacute;n de mail debe ser personal, en esta recibir&aacute informaci&oacute;n de su usuario e informaci&oacute;n de trabajo de su inter&eacute;s.</font>
	</tr>
      <tr>
        <td align="left"><label>Nombre:</label></td>
        <td>
            <input type="text" name="usunom" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Apellido:</label></td>
        <td>
            <input type="text" name="usuape" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Mail privado:</label></td>
        <td>
            <input type="text" name="usumail" maxlength="120" size="70"/>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Repartici&oacute;n:</label></td>
        <td>
	       <select name="sec">
		<?php
			$consulta="Select * from Departamentos order by DepNombre";
			$resultado=mysqli_query($cn,$consulta);
			while($Seccusu=mysqli_fetch_assoc($resultado))
			{
				$depId=$Seccusu['DepId'];
				$depDsc=$Seccusu['DepNombre'];
				echo '<option value="'.$depId.'" selected>'.$depDsc.'</option>';
			}
		?>	
	       </select>
        </td>
      </tr>
      <tr>
        <td align="left"><label>Rol o Perfil:</label></td>
        <td>
	       <select name="pfl">
		<?php
			$consulta="Select * from SisPerfiles order by SisPflDsc";
			$resultado=mysqli_query($cn,$consulta);
			while($perfil=mysqli_fetch_assoc($resultado))
			{
				$PflId=$perfil['SisPflId'];
				$PflDsc=$perfil['SisPflDsc'];
				echo '<option value="'.$PflId.'" selected>'.$PflDsc.'</option>';
			}

		?>	
	       </select>
        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left">
	<a href="usuarios.php?unidad=<?php echo $uniusu;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
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
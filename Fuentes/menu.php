<!--
Creación:	Alicia Acevedo
Fecha:		04/2010
Cookies en login:
	setcookie("sistema",$miSistema);
	setcookie("estadosol",$miEstado);
	setcookie("usuid",$miUsuario['UsuId']);
	setcookie("usuunidad",$miUsuario['SeccionesId']);
	setcookie("usuunidadele",$miUnidadEle);
	setcookie("usupermiso",$miPermiso['SisPflUniAll']);
	setcookie("ususeccion",$miSeccion['DepNombre']);
	setcookie("usuperfil",$miPerfil['SisPflId']);
	setcookie("usumail",$miUsuario['UsuMail']);
	setcookie("tipobien",$TipoBien);
Algunas Características:
	1.- En la cookie estadosol guardamos el estado consultado para index.php, pero este sera utilizado por otros php, para volver al index.php con el estado
		previamente selecionado. por lo que esta cookie ira cambiando en el proceso
	2.- En la cookie usuunidadele guardamos la unidad elegida en caso que el usuario puede navegar por otras unidades que no sea la propia consultado para el
		index.php, pero este sera utilizado por otros php, para volver al index.php con la unidad seleccionada previamente. por lo que esta cookie ira cambiando
		en el proceso. Por la negativa a esta funcionalidad(usuario sin acceso a otras uni) en esta se indica la propio así como en usuunidad.
	3.- En la cookie usuunidad se utilizara para el despligue en el encabezado de las pantalla indicando unidad de pertenencia 
	4.- Según el permiso del perfil en este objeto habilitaría la elección del combo por unidades

-->
<?php
require_once("funcionesbd.php");
?>
<tr></tr>
 
	<font style="font-family: 'Times New Roman', Times, serif;font-size: 16px;font-weight: bold; color: #11A230">
	Usuario conectado:&nbsp;
	<?php echo $_COOKIE['usuario']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Unidad:&nbsp;
	<?php echo $_COOKIE['ususeccion']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Rol:&nbsp;
	<?php
		//Busco nombre de rol, en la consulta posterior puede no tener registros por el join y no trae la descripción
		$consulta="Select * from SisPerfiles where SisPflId='".$_COOKIE['usuperfil']."'";
		$resultado=mysqli_query($cn,$consulta);
		$ElPfl=mysqli_fetch_assoc($resultado);
		echo $ElPfl['SisPflDsc'];

		$HayImpresiones="Imprimir Remito";
		$consulta="Select * from SisPerfiles as p, StkEstPerfiles as e where p.SisPflId=e.SisPflId and p.SisId='".$_COOKIE['sistema']."' and p.SisPflId='".$_COOKIE['usuperfil']."' and e.StkEstId='".$HayImpresiones."'";
		$resultado=mysqli_query($cn,$consulta);
		$ElPfl=mysqli_fetch_assoc($resultado);
		if (mysqli_affected_rows($cn)==0)
		{
			// No se debe detallar la existencia de impresiones
		}
		else
		{
		?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		Impresiones Pendientes:&nbsp;
		<?php
			$estadosol="Imprimir Remito";
			$consulta="Select * from StkSolicitudes where StkSolEstado='".$estadosol."'";
			$resultado=mysqli_query($cn,$consulta);
			$CantImp=0;
			while($Impresiones=mysqli_fetch_assoc($resultado))
			{
				$CantImp=$CantImp+1;
			}
			echo $CantImp;
		}
		?>

	<hr style="color: rgb(69, 106, 221);">

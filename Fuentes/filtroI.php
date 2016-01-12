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

$estadosol="Imprimir Remito";
$consulta="Select * from StkSolicitudes where StkSolEstado='".$estadosol."'";
$resultado=mysqli_query($cn,$consulta);
$CantImp=0;
while($Impresiones=mysqli_fetch_assoc($resultado))
{
	$CantImp=$CantImp+1;
}

?>

<!-- Funciones a ser usadas para guardar cookie con lo elegido en el combo -->
<SCRIPT LANGUAGE="JavaScript">
function cookieestadosol(string){
	$estado=$_POST['estado'];
	setcookie("estadosol",$estado);
	}
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
function cookieunidadele(string){
	$unidad=$_POST['unidad'];
	setcookie("usuunidadele",$unidad);
	}
</SCRIPT>

<center>
<form name="filtros" action="index.php?estado=<?php echo $estado;?>&unidad=<?php echo $unidad;?>" method="post">
	<label>Filtrar Solicitudes de: </label>
       <select name="unidad" onchange="cookieunidadele(this.value)">
		<?php
			if ($_COOKIE['usupermiso']='S')
			{
				$consulta="Select * from Departamentos order by DepNombre";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				while($unDepto=mysqli_fetch_assoc($resultado))
				{
					$unidadele=$unDepto['DepId'];
					$deptonom=$unDepto['DepNombre'];
					echo '<option value="'.$unidadele.'" selected>'.$deptonom.'</option>';
				}
				echo '<option value="0" selected>Todas</option>';
			}
		?>	
       </select>
       <select name="estado" onchange="cookieestadosol(this.value)">
		<?php
			$consulta="Select * from StkEstPerfiles where SisPflId='".$_COOKIE['usuperfil']."'";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
			while($unEstado=mysqli_fetch_assoc($resultado))
				{
				$estado=$unEstado['StkEstId'];
				echo '<option value="'.$estado.'" selected>'.$estado.'</option>'; 
				}
			echo '<option value="  -- Seleccionar Estado --" selected>  -- Seleccionar Estado -- </option>';
		?>	
       </select>
	<input name="submit" type="submit" value="Aplicar" /> 
	<hr style="color: rgb(69, 106, 221);">
</form>
</center>
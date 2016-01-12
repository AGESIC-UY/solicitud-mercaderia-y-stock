<!--
//Los articulos ingresados a la solicitud, se agregan en estado pendiente. La solicitud en esta situación aún esta en estado "Construyendo", esperando que 
//el usuario cambie su estado a Pendiente en forma voluntaria(estado de solicitud). Quedando para ser tomado por el usuario operador y asignar el material. 
//En estado pendiente no podría modificarla, por lo que no podra acceder a este objeto en este estado.
Algunas características:
	1.- el estado del artículo en este objeto solo puede ser "Pendiente", por eso se inicializa en variable al comienzo del objeto
	2.- el estado de la solicitud en este objeto solo puede ser "Construyendo", por eso se inicializa en variable al comienzo del objeto para volver a index.php en ese estado
-->
<?php
$solid=$_REQUEST['solid'];
$unidadcall=$_REQUEST['unidadcall'];
$estadosol=$_REQUEST['estadocall'];
$estadoart="Pendiente";
require_once("principioseleccion.php");
require_once("funcionesbd.php");
if (isset($_REQUEST['idart'])) {
$idart=$_REQUEST['idart'];
} else {
$idart= "";
}

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	if ($_POST['cant']>0)
	{
		//Insertar articulo a la solicitud
		$sentencia="Insert into StkSolArticulos (StkSolId, StkArtId, StkSolArtCantSol, StkSolArtEstado, StkSolArtUsuCre, StkSolArtFchCre) values ('".$solid."','".$_POST['artid']."','".$_POST['cant']."','".$estadoart."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
		$articulosol = mysqli_query($cn, $sentencia);
		if (mysqli_affected_rows($cn)==0)
		       echo 'Atenci&oacute;n: No se pudo ingresar el articulo a la solicitud: '.mysqli_error();
		else
		{
		       if ($_COOKIE['usuperfil']==4)
			{
				//solo para perfil autorizador, no corresponde para el solicitante autorizador
				$sentenciaI="Update StkSolicitudes set StkSolCambio='S' where StkSolId=".$solid;
				if (!mysqli_query($cn,$sentenciaI))
				{
					die('Error al indicar Solicitud modificada'.mysqli_error());
				}
			}
		}
	}
} //finaliza accion guardar if

$consulta="Select * from StkSolicitudes where StkSolId='".$solid."'";
$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
$unaSol=mysqli_fetch_assoc($resultado);
$elestadosol=$unaSol['StkSolEstado'];
$iduni=$unaSol['StkSolSecId'];
$mensaje="Para confirmar la solicitud, localice la misma en la lista de solicitudes en estado Construyendo";
?>

<SCRIPT LANGUAGE="JavaScript">
function validate(string)
{if (!string) return false;
    var Chars = "0123456789";  <!--var Chars = "0123456789-"; incluyendo negativos-->
    for (var i = 0; i < string.length; i++)
	{if (Chars.indexOf(string.charAt(i)) == -1)
		return false;
	}
return true;
}
</SCRIPT>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />

<center>
<form name="datos" action="ingresoartsol.php?solid=<?php echo $solid;?>&unidadcall=<?php echo $unidadcall;?>&estadocall=<?php echo $elestadosol;?>" method="post">
<table class="inventario">
    <tr>
    <td align="center" colspan="3">
	<br>
	<font size="5" color="#000066"><img src="Images/modificar.png" width="30" height="30" alt="Nuevo" border=0/> Solicitud <?php echo $solid;?> - Ingreso de Art&iacute;culos </font>
	<hr style="color: rgb(69, 106, 221);">
	<br>
    </td>
    </tr>
  <tr>
    <td colspan="3">
    <table class="inventario">
     <tr>
	<font size="4"><?php echo $mensaje;?></font>
	<br>
     </tr>
	<br>

      <tr>
        <td width="102" align="left"><label>Art&iacute;culo:</label></td>
	 <td>
        <select name="artid">
          <?php
		$consultaI="Select * from StkArticulos as a, StkArtCls as c where a.StkCauBjaId=0 and a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc";
		$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
		while($unArt=mysqli_fetch_assoc($resultadoI))
		{
			//Analizo si el articulo está reservado para algunos departamentos en particular, existe tabla con el vinculo
			//en caso de ningun registro del articulo en esta tabla(StkArtDep), cargo en el combo. En caso que exista articulo
			//verifico si la unidad lo puede seleccionar.

			$agrego=1;
			$consultaII="Select * from StkArtDep where StkArtId='".$unArt['StkArtId']."'";
			$resultadoII=mysqli_query($cn,$consultaII);
			while($unArtDep=mysqli_fetch_assoc($resultadoII))
			{	
				$agrego=0; //encontre articulo reservado pero debo rectificar que la unidad puede seleccionarlo
				if ($unArtDep['DepId']==$unidadcall)
				{
					$agrego=1;
					break;
				}
			}
			if ($agrego==1)
			{
				$ArtId=$unArt['StkArtId'];
				$ArtDsc=$unArt['StkArtDsc'];
				if ($ArtId==$idart)
				{
					echo "<option value='".$ArtId."' selected>".$ArtDsc."</option>";
				}
				else
				{
					echo "<option value='".$ArtId."'>".$ArtDsc."</option>";
				}
	              }
              }
          ?>
        </select>
	 </td>
      </tr>
      <tr>
        <td width="180" align="left"><label>Cantidad:</label></td>
	 <td>
		<input type="text" name="cant" maxlength="120" size="15" onChange="if (!validate(this.value)) alert('Indique un valor num&eacute;rico mayor a cero')"/>
        </td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td align="left">
	<?php
	$estadocalled=$_REQUEST['estadocall'];
	?>
	<a href="index.php?estado=<?php echo $elestadosol;?>&unidad=<?php echo $unidadcall;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
    </td>
    <td>
	<?php
	$estadocalled=$_REQUEST['estadocall'];
	?>
	<a href="articulosbuscar.php?solid=<?php echo $solid;?>&unidadcall=<?php echo $unidadcall;?>&estadocall=<?php echo $estadosol;?>"><img width="40" height="40" src="Images/lupita.gif" border=0></a>
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
require_once("articulossol.php");
require_once("pie.php");
?>
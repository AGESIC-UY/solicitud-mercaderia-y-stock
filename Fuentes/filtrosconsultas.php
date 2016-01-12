<!--
Creación:	Alicia Acevedo
Fecha:		10/2010
Caract.:	Filtros de la consulta de consumo en cantidades, debo armar según el usuario logged, lo que puede o no filtrar.
	
-->

<?php
require_once("funcionesbd.php");
$estado=$_COOKIE['estadosol'];
$unidad=$_COOKIE['usuunidadele'];
$area=$_COOKIE['usuareaele'];

//Verifico si la unidad del usuario logged es área, de interes para el caso especial de autorizador, si este pertenece a unidad del tipo area, estaría con 
//permiso de consultar como si fuese jefe de area. 

	$consultaI="Select * from Departamentos where DepIdDep='".$unidad."'";
	$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
	$unidadesdearea=0;
       if (mysqli_num_rows($resultadoI)<>0) 
	{//La unidad es área, debo permitir seleccionar unidades de dicha área (área del usuario) no de área dependiente
		if ($_COOKIE['usuperfil']==5)
		{
			$unidadesdearea=$unidad;
			$areaele=$unidadesdearea;
		}
	}

	if ($_COOKIE['usuperfil']==1 or $_COOKIE['usuperfil']==5 or $_COOKIE['usuperfil']==7)
	{//El rol Solicitante, Consultor de Unidad o Autorizador(no jefe de area), no le permito consultar otra unidad que no sea la propia
		$uniele=$_COOKIE['usuunidadele'];
		$areaele=$_COOKIE['usuareaele'];
		if ($unidadesdearea<>0)
		{//Como permito la seleccion de la unidad siendo que sería un Autorizador(como jefe de area) tomo el get
			$uniele=$_GET['uniele'];
			$areaele=$unidadesdearea;
		}
	}
	else
	{//area y unidad seleccionadas del combo
		$uniele=$_GET['uniele'];
		if (isset($_REQUEST['areaele'])) {
		$areaele=$_GET['areaele'];
		} else {
		$areaele= "";
		}
	}

	$consultaX="Select * from Departamentos where DepId='".$areaele."'";
	$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
	$elArea=mysqli_fetch_assoc($resultadoX);
	if ($elArea['DepNoVigente']==1)
	{
	$AreaNombre=$elArea['DepNombre']." "."(No Vigente)";
	}
	else
	{
	$AreaNombre=$elArea['DepNombre'];
	}

	$consultaX="Select * from Departamentos where DepId='".$uniele."'";
	$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
	$elDepto=mysqli_fetch_assoc($resultadoX);
	$UniNombre=$elDepto['DepNombre'];

	if ($uniele==0)
	{
		$UniNombre="Todas";
		if ($areaele==0)
		{
			$AreaNombre="Todas";
		}
		//else es el area seleccionada
	}
	else
	{//Si seleccione unidad 
		if ($areaele==0)
		{//Si no seleccione area, el area debe indicar la correspondiente a la unidad
			$consultaX="Select * from Departamentos where DepId='".$elDepto['DepIdDep']."'";
			$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
			$elareadeunidad=mysqli_fetch_assoc($resultadoX);
			if ($elareadeunidad['DepNoVigente']==1)
			{
			$AreaNombre=$elareadeunidad['DepNombre']." "."(No Vigente)";
			}
			else
			{
			$AreaNombre=$elareadeunidad['DepNombre'];
			}
		}
		else
		{//Si seleccione area, debe pertenecer a la unidad seleccionada
			$consultaX="Select * from Departamentos where DepId='".$uniele."' and DepIdDep='".$areaele."'";
			$resultadoX=mysqli_query($cn,$consultaX) or die('La consulta fall&oacute;: ' .mysqli_error());
		       if (mysqli_num_rows($resultadoX)<>0) 
			{
				$elareadeunidad=mysqli_fetch_assoc($resultadoX);
				if ($elareadeunidad['DepNoVigente']==1)
				{
				$AreaNombre=$elareadeunidad['DepNombre']." "."(No Vigente)";
				}
				else
				{
				$AreaNombre=$elareadeunidad['DepNombre'];
				}
			}
			else
			{//else unidad no corresponde al AREA
				$AreaNombre="La unidad no corresponde con el area seleccionada!!!";
			}
			if ($_COOKIE['usuperfil']==1 or $_COOKIE['usuperfil']==7)
			{
				$areaele=$elareadeunidad['DepIdDep'];
			}
	
		}

	}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
////////////////////////////////////////////////////
//Convierte fecha de mysql a normal
////////////////////////////////////////////////////
-->
function cambiaf_a_normal($fecha){
    ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
    return $lafecha;
}
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
<!--
////////////////////////////////////////////////////
//Convierte fecha de normal a mysql
////////////////////////////////////////////////////
-->
function cambiaf_a_mysql(){
	$fecha=$_POST['fecha']
	$fch=explode("/",$fecha);
	$fecha=$fch[2]."-".$fch[1]."-".$fch[0];
	return $fecha;
} 
</SCRIPT>


<center>
<form name="filcon" action="cnsuniart.php?fchdesde=<?php echo $fchdesde;?>&fchhasta=<?php echo $fchhasta;?>&detallo=<?php echo $detallo;?>&costos=<?php echo $costos;?>&uniele=<?php echo $uniele;?>&artid=<?php echo $artid;?>&artbus=<?php echo $artbus;?>&areaele=<?php echo $areaele;?>" method="get">
	<table class="inventario"><td align="center"><font size="5" color="#000066">Filtros de la consulta</td></table>
	<table class="inventario">
		<?php
		if ($unidadesdearea<>0)
		{//Permito la seleccion de la unidad siendo que sería un Autorizador(como jefe de area), solo unidades del area y ella misma
		?>	
			<tr>
			<th>Unidad:</th>
			<th align="left">
				<select name="uniele">
				<?php
				$consulta="Select * from Departamentos where DepIdDep='".$unidadesdearea."' or DepId='".$unidadesdearea."'";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				while($unDepto=mysqli_fetch_assoc($resultado))
				{
					$depele=$unDepto['DepId'];
					if ($unDepto['DepNoVigente']==1)
					{
					$deptonom=$unDepto['DepNombre']." "."(No Vigente)";
					}
					else
					{
					$deptonom=$unDepto['DepNombre'];
					}
					echo '<option value="'.$depele.'" selected>'.$deptonom.'</option>';
				}
				echo '<option value="0" selected>Todas</option>';
				?>	
			       </select>
			</th>
			</tr>
		<?php
		}
		else
		{
			if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==3 or $_COOKIE['usuperfil']==6 or $_COOKIE['usuperfil']==10)
			{//El rol Operador, Administrador, Consultor financiero, Administrador - Inventario
		?>	
			<tr>
			<th>&Aacute;rea:</th>
			<th align="left">
				<select name="areaele">
				<?php
				//Filtramos todos los departamentos del tipo area, nivel superior a la unidad para poder agrupar el gasto
//anterior a DepTipoArea	$consulta="Select * from Departamentos group by DepIdDep";
				$consulta="Select * from Departamentos where DepTipoArea=1 order by DepNombre";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				while($unArea=mysqli_fetch_assoc($resultado))
				{
				//	$consultaI="Select * from Departamentos where DepId='".$unArea['DepIdDep']."'";
				//	$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
				//	$unDepto=mysqli_fetch_assoc($resultadoI);
				//	$depele=$unDepto['DepId'];
				//	$deptonom=$unDepto['DepNombre'];

					$depele=$unArea['DepId'];
					if ($unArea['DepNoVigente']==1)
					{
					$deptonom=$unArea['DepNombre']." "."(No Vigente)";
					}
					else
					{
					$deptonom=$unArea['DepNombre'];
					}
					echo '<option value="'.$depele.'" selected>'.$deptonom.'</option>';
				}
				echo '<option value="0" selected>Todas</option>';
				?>	
			       </select>
			</th>
			</tr>
			<tr>
			<th>Unidad:</td>
			<th align="left">
				<select name="uniele">
				<?php
				$consulta="Select * from Departamentos order by DepNombre";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				while($unDepto=mysqli_fetch_assoc($resultado))
				{
					$depele=$unDepto['DepId'];
					if ($unDepto['DepNoVigente']==1)
					{
					$deptonom=$unDepto['DepNombre']." "."(No Vigente)";
					}
					else
					{
					$deptonom=$unDepto['DepNombre'];
					}
					echo '<option value="'.$depele.'" selected>'.$deptonom.'</option>';
				}
				echo '<option value="0" selected>Todas</option>';
				?>	
			       </select>
			</th>
			</tr>
		<?php
			}
		}
		?>	
		<tr>
		<th>Articulo/s:</th>
		<th align="left">
		       <select name="artid">
				<?php
				$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkArtClsId=c.StkArtClsId order by StkArtDsc";
				$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
				while($unArticulo=mysqli_fetch_assoc($resultado))
				{
					$artidele=$unArticulo['StkArtId'];
					$artdsc=trim($unArticulo['StkArtDsc']);
					echo '<option value="'.$artidele.'" selected>'.$artdsc.'</option>'; 
				}
				echo '<option value="0" selected>Todos</option>';
				?>	
		       </select>
		</th>
		<tr>
		<th>Filtrar parte del art&iacute;culo:</th>
		<th align="left"><input type="text" name="artbus" value="<?php echo $artbus;?>"/></th>
		</tr>
		<tr>
		<th>Detallar fechas:</label></th>
		<th align="left">
       		<input type="checkbox" name="detallo" maxlength="70" size="15" UNCHECKED/>
		</th>
		</tr>
		<?php
		if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==3 or $_COOKIE['usuperfil']==6 or $_COOKIE['usuperfil']==10)
		{//El rol Operador, Administrador, Consultor financiero, Administrador - Inventario
		?>	
			<tr>
			<th align="center">Incluir Costos:</th>
			<th align="left">
       			<input type="checkbox" name="costos" maxlength="70" size="15" UNCHECKED/>
			</th>
			</tr>
		<?php
		}
		?>	
		<tr>
			<?php
			include "calendarioentre.php";
			?>
		</tr>
	</table>
</center>



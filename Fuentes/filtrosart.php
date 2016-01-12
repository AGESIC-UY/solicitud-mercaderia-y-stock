<!--
Creación:	Alicia Acevedo
Fecha:		11/2010
-->
<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
?>

<center>
<form name="filtrosart" action="articulos.php?clase=<?php echo $clase;?>&fchhasta=<?php echo $fchhasta;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $_POST['actividad'];?>" method="post">
	<tr>
	<td width="180" align="left"><label>Activos:</label></td>
	<td>
		<select name="actividad">
			<option value="1">Activos</option>
			<option value="0">Inactivos</option>
		</select> 
	</td>
	</tr>
	<tr>
	<td width="180" align="left"><label>Filtrar Clasificaci&oacute;n:</label></td>
	<td>
       	<select name="clase">
		<?php
		if ($_COOKIE['usupermiso']=='S')
		{
			//Por default en "0" pero de ser > as "0" es la clase que mas prioridad tiene así no tiene que estar trayendo todas las clases
			$escero="0";
			$consulta="Select * from SisPflUsuarios where SisPflId='".$_COOKIE['usuperfil']."'";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
			$ClsPrimaria=mysqli_fetch_assoc($resultado);
			if ($ClsPrimaria['SisClsPri']>$escero)
			{
				echo '<option value="99">Todos</option>';
			}
			$uno=1;
			$consulta="Select * from StkArtCls as c, StkArtClsUsu as u where c.StkArtClsId=u.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' and u.StkArtClsHab='".$uno."' order by c.StkArtClsDsc";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
			$All=0;
			while($unaCls=mysqli_fetch_assoc($resultado))
			{
				$ClsId=$unaCls['StkArtClsId'];
				$ClsDsc=$unaCls['StkArtClsDsc'];
				if ($ClsPrimaria['SisClsPri']>$escero)
				{
					if ($ClsId=$ClsPrimaria['SisPflPri']==$ClsId)
					{	
						echo '<option value="'.$ClsId.'" selected>'.$ClsDsc.'</option>';
					}
					else
					{
						echo '<option value="'.$ClsId.'">'.$ClsDsc.'</option>';
					}
				}
				else
				{
					echo '<option value="'.$ClsId.'" selected>'.$ClsDsc.'</option>';
				}
				$All=$All+1;
			}
			if ($All>1 and $ClsPrimaria['SisClsPri']==$escero) //tiene permiso para ver todos los articulos, no exclusivamente los indicados en StkArtClsUsu
			{
				echo '<option value="99" selected>Todos</option>';
			}
		}
		else
		{
			//Esta opcion corresponde a la CONSULTA de articulos de stock especificos, como es el caso de Informatica, donde se pidio 
			//poder consultar los articulos informaticos(insumos). No voy a permitir un filtro "Todos" para no complicar el acceso a todos
			//los articulos del stock no importando su clasificación. Aqui fitlro por tipo de clasificación y/o like descripción
			$consulta="Select * from StkArtCls as c, StkArtClsUsu as u where u.UsuId='".$_COOKIE['usuid']."' and c.StkArtClsId=u.StkArtClsId and u.StkArtClsHab='".$uno."'order by c.StkArtClsDsc";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
			$All=0;
			while($unaCls=mysqli_fetch_assoc($resultado))
			{
				$ClsId=$unaCls['StkArtClsId'];
				$ClsDsc=$unaCls['StkArtClsDsc'];
				echo '<option value="'.$ClsId.'" selected>'.$ClsDsc.'</option>';
			}
		}
		?>	
	       </select>
	</td>
	</tr>
	<tr>
	<td width="180" align="left"><label>Filtrar por texto: </label></td>
	<td><input type="text" name="artbus" value="<?php echo $artbus;?>"/></td>
	</tr>
	<?php
		include "calendariohasta.php";
	?>
</form>
</center>

<!--
Creación	Alicia Acevedo
Fecha:		04/2010
Algunas Características:
-->
<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];
if (isset($_REQUEST['artbus'])){
$artbus=$_REQUEST['artbus'];
} else {
$artbus= "";
}
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
<form name="datos" action="articulosbuscar.php?solid=<?php echo $solid;?>&unidadcall=<?php echo $unidadcall;?>&estadocall=<?php echo $estadocall;?>" method="post">
	<table class="inventario">
		<tr>
		<td align="center"><font size="6" color="#000066">Buscar Art&iacute;culo</font>&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="ingresoartsol.php?solid=<?php echo $solid;?>&unidadcall=<?php echo $unidadcall;?>&estadocall=<?php echo $estadocall;?>"><img width="30" height="30" src="Images/volver.jpg" border=0></a>
		</td>
		</tr>
	</table>
	<table class="filtros">
		<br>
		<font size="4" color="#000066">Filtrar por: </font>
		<input type="text" name="artbus" value="<?php echo $artbus;?>"/>
		<input name="submit" type="submit" value="Aplicar"/>
		<br>
		<br>
	</table>
	<?php
		if ($artbus=="")
		{
			$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkArtClsId=c.StkArtClsId order by a.StkArtDsc";
		}
		else
		{
			$elLike="%".$artbus."%";
			$consulta="Select * from StkArticulos as a, StkArtCls as c where a.StkArtDsc LIKE '".$elLike."' and a.StkArtClsId=c.StkArtClsId";
		}
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0){
	            	echo '<br><center><label>No existen articulos en el stock.</label></center><br>';
		}
		else
		{
	             	echo '<br><table class="inventario">
				<tr>
              	      	<th>Art&iacute;culo</th>
                     	<th>Seleccionar</th>
				</tr>';
			while($labusqueda=mysqli_fetch_assoc($resultado))
			{
				//Analizo si el articulo está reservado para algunos departamentos en particular, existe tabla con el vinculo
				//en caso de ningun registro del articulo en esta tabla(StkArtDep), cargo en el combo. En caso que exista articulo
				//verifico si la unidad lo puede seleccionar.
				$agrego=1;
				$consultaII="Select * from StkArtDep where StkArtId='".$labusqueda['StkArtId']."'";
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
					if($labusqueda['StkCauBjaId']=='0')
					{
					$icoalerta="Images/ok.jpg";
					echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
		                     	<td>'.$labusqueda['StkArtDsc'].'</td>';
			              echo '<td align="center"><a href="ingresoartsol.php?solid='.$solid.'&unidadcall='.$unidadcall.'&estadocall='.$estadocall.'&idart='.$labusqueda['StkArtId'].'"><img src="'.$icoalerta.'" witdh="20" height="20" border=0></a></td>
					</tr>';
					}
				}
			}//Cierra el WHILE que imprime los resultados obtenidos
		}//Cierra el IF donde se pregunta si hay resultados o no
		echo '</table><br>';
	?>
	<tr>
       <td align="center">&nbsp;</td>
	</tr>
</form>
</center>
<?php
require_once("pie.php");
?>
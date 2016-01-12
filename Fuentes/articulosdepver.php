<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$clase=$_REQUEST['clase'];
$idart=$_REQUEST['artid'];
$fchhasta=$_REQUEST['fchhasta'];
$artbus=$_REQUEST['artbus'];
$actividad=$_REQUEST['actividad'];
$sentencia="select * from StkArticulos where StkArtId='".$idart."'";
$resultado = mysqli_query($cn,$sentencia);
$elArticulo=mysqli_fetch_array($resultado);
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<form name="datos" action="articulosdep.php" method="post">
<center>
	<table class="inventario">
		<tr>
		<td align="center"><font size="5" color="#000066"> <?php echo $elArticulo['StkArtDsc'];?></font>&nbsp;&nbsp;&nbsp;
		<a href="articulos.php?clase=<?php echo $clase;?>&articulo=<?php echo $idart;?>&fchhasta=<?php echo $fchhasta;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>"><img width="30" height="30" src="Images/volver.jpg"></a>
		</td>
		</tr>
		<tr>
		<td align="center"><font size="5" color="#000066"> Reservado para las Unidades </font></td>
		</tr>
	</table>

	<table class="inventario">
            <?php
		$seleccion="Select * from StkArtDep as a, Departamentos as d, Usuarios as u where d.DepId=a.DepId and a.StkArtDepUsuCre=u.UsuId and a.StkArtId='".$idart."'";
		$resultado=mysqli_query($cn,$seleccion) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0)
		{
            		echo '<br><center><label>No existen unidades que reserven el articulo</label></center><br>';
		}
		else
		{
	             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                     	<th>Unidad</th>
                     	<th>Fecha</th>
                     	<th>V&iacute;nculado por</th>
			</tr>';
			while($unArtDep=mysqli_fetch_assoc($resultado))
			{
				$usunomcompleto=$unArtDep['UsuNombre'].' '.$unArtDep['UsuApellido'];
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                     	<td>'.$unArtDep['DepNombre'].'</td>
                     	<td>'.cambiaf_a_normal($unArtDep['StkArtDepFchCre']).'</td>
                     	<td>'.$usunomcompleto.'</td>
				</tr>';
                  
			}//Cierra el WHILE que imprime los resultados obtenidos
		}//Cierra el IF donde se pregunta si hay resultados o no
		echo '</table><br>';
            ?>
	    <tr>
	       <td align="center">&nbsp;</td>
	    </tr>
	</table>
</center>
</form>

<?php
require_once("pie.php");
?>
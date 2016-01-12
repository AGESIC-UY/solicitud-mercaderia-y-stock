<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idart=$_REQUEST['idart'];
$clase="99";
$articulo="99";
$dia = date('d');
$mes = date('m');
$year = date('y')+2000;
$fchhasta=date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $year));
$artbus=$_REQUEST['artbus'];
$actividad=$_REQUEST['actividad'];
?>

<?php
$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	$sentenciaI="Insert into StkArtDep (StkArtId, DepId, StkArtDepUsuCre, StkArtDepFchCre) values('".$_REQUEST['idart']."','".$_POST['depid']."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
	$artdepins= mysqli_query($cn, $sentenciaI);
} 
$sentencia="select * from StkArticulos where StkArtId='".$idart."'";
$resultado = mysqli_query($cn,$sentencia);
$elArticulo=mysqli_fetch_array($resultado);
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<form name="datos" action="articulosdep.php?idart=<?php echo $idart;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>" method="post">
<center>
	<table class="inventario">
		<tr>
		<td align="center"><font size="5" color="#000066"> <?php echo $elArticulo['StkArtDsc'];?></font>&nbsp;&nbsp;&nbsp;
		<a href="articulos.php?clase=<?php echo $clase;?>&articulo=<?php echo $articulo;?>&fchhasta=<?php echo $fchhasta;?>&artbus=<?php echo $artbus;?>&actividad=<?php echo $actividad;?>"><img width="30" height="30" src="Images/volver.jpg" border=0></a>
		</td>
		</tr>
		<tr>
		<td align="center"><font size="5" color="#000066"> Reservado para las Unidades </font></td>
		</tr>
	</table>
	<table class="inventario">
	      <tr>
       	 <td align="center"><label>Unidades:</label>
		       <select name="depid" >
			<?php
				$consultaI="Select * from Departamentos order by DepNombre";
				$resultadoI=mysqli_query($cn,$consultaI);
				while($Depto=mysqli_fetch_assoc($resultadoI))
				{
					$DepId=$Depto['DepId'];
					$DepNombre=$Depto['DepNombre'];
					echo "<option value='".$DepId."'>".$DepNombre."</option>";
				}
				echo '<option value="  -- Seleccionar Unidad --" selected>  -- Seleccionar Unidad -- </option>';
			?>	
		       </select>
		      <td align="left"><input type="hidden" name="accion" value="guardar" > <input type="image" width="30" height="30" src="Images/guardar.png"></td>
		 </td>
	      </tr>
            <?php
		$seleccion="Select * from StkArtDep as a, Departamentos as d where d.DepId=a.DepId and a.StkArtId='".$idart."'";
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
                        	<th>Eliminar de la Lista</th>
			</tr>';
			while($unArtDep=mysqli_fetch_assoc($resultado))
			{
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                     	<td>'.$unArtDep['DepNombre'].'</td>
				<td align="center"><a href="eliminoartdep.php?idart='.$idart.'&iddep='.$unArtDep['DepId'].'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>
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
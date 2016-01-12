<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$usuele=$_REQUEST['usuele'];
$unidadusu=$_REQUEST['unidad'];
$unidad='99999';

$accion=(isset($_REQUEST['accion']))?$_REQUEST['accion']:'desconocida';
if ($accion=='guardar')
{
	$sentenciaI="Insert into UsuDep (UsuId, DepId, UsuDepFchIni, UsuDepUsuCre, UsuDepFchCre) values('".$_REQUEST['usuele']."','".$_POST['depid']."','".date("Y-m-d H:i")."','".$_COOKIE['usuid']."','".date("Y-m-d H:i")."')";
	$usudep= mysqli_query($cn, $sentenciaI);
} 
$sentencia="select * from Usuarios where usuId='".$usuele."'";
$resultado = mysqli_query($cn,$sentencia);
$elUsuario=mysqli_fetch_array($resultado);
$usunomcompleto=$elUsuario['UsuNombre'].' '.$elUsuario['UsuApellido'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<form name="datos" action="usuautorizadep.php?usuele=<?php echo $usuele;?>" method="post">
<center>
	<table class="inventario">
		<tr>
		<td align="center"><font size="5" color="#000066">Usuario: <?php echo $usunomcompleto;?></font>&nbsp;&nbsp;&nbsp;
		<a href="usuarios.php?unidad=<?php echo $unidad;?>"><img width="30" height="30" src="Images/volver.jpg"></a>
		</td>
		</tr>
		<tr>
		<td align="center"><font size="5" color="#000066"> Autorizador Sustituto en otras Unidades </font></td>
		</tr>
	</table>

	<table class="inventario">
	      <tr>
       	 <td align="center"><label>Unidades:</label>
		       <select name="depid" >
			<?php
				$consultaI="Select * from Departamentos where DepId<>'".$unidadusu."' order by DepNombre";
				$resultadoI=mysqli_query($cn,$consultaI);
				while($Depto=mysqli_fetch_assoc($resultadoI))
				{
					$DepId=$Depto['DepId'];
					$DepNombre=$Depto['DepNombre'];
					echo "<option value='".$DepId."'>".$DepNombre."</option>";
				}
				echo '<option value="  -- Seleccionar Unidad --" selected>  -- Seleccione unidad para habilitar como usuario Autorizador -- </option>';
			?>	
		       </select>
		      <td align="left"><input type="hidden" name="accion" value="guardar" > <input type="image" width="30" height="30" src="Images/guardar.png"></td>
		 </td>
	      </tr>
            <?php
		$usudeppri=1;
		$seleccion="Select * from UsuDep as u, Departamentos as d where d.DepId=u.DepId and u.UsuId='".$usuele."' and u.UsuDepPri<>'".$usudeppri."'";
		$resultado=mysqli_query($cn,$seleccion) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0)
		{
            		echo '<br><center><label>No existen Otras unidades en las que el Usuario sea autorizador</label></center><br>';
		}
		else
		{
	             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                     	<th>Unidad</th>
                     	<th>Fch.Inicia</th>
                     	<th>Fch.Fin</th>
                        	<th>Cerrar vinculo</th>
			</tr>';
			while($unUsuDep=mysqli_fetch_assoc($resultado))
			{
              		if($colorlinea=='#F3F3F3')
				{
       				$colorlinea='#FEFEFE';
				}
				else
				{
					$colorlinea='#FEFEFE';
				}
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
                     	<td>'.$unUsuDep['DepNombre'].'</td>
                     	<td>'.$unUsuDep['UsuDepFchIni'].'</td>
                     	<td>'.$unUsuDep['UsuDepFchFin'].'</td>';
				if( (cambiaf_a_normal($unUsuDep['UsuDepFchFin']) == "00/00/0000" ) || ( $unUsuDep['UsuDepFchFin'] == NULL ) ) 
				{
				echo '<td align="center"><a href="eliminousudep.php?usuele='.$usuele.'&iddep='.$unUsuDep['DepId'].'&usudepid='.$unUsuDep['UsuDepId'].'"><img src="Images/cancelar.gif" witdh="15" height="15"></a></td>';
				}
				else
				{
	       	       echo '<td align="center"><a><img src="Images/blank.gif" witdh="15" height="15"></a></td>';
				}
				echo '</tr>';
                  
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
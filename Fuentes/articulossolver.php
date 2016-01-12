<!--
Con la posibilidad de realizar la sustiticuion de un articulo por otro, a nivel del operador tuve que considerar en este objeto la posibilidad de que aparezcan articulos
sin resolver, pero su estado particular esté en finalizado. Esto se debe a que el atributo de estado en finalizado me lo retira de la lista de pendiente de entrega, pero 
debería de alguna manera mostrar en este objeto que su estado particular es ese.

Situacion de canje de articulo....
Si la solicitud esta en Estado "Pendiente de Entrega", y hay Articulo en estado "Finalizado" (estado especifico del articulo), o se resolvio la entrega en un instancia anterior 
la cual debe estar respaldad por StkMovArticulo para la solicitud movimiento por la cantidad solicitada, Si existiera una diferencia en el total es porque se canjeo por otro articulo.

El estado de los artículos en particular unicamente pasan a "Finalizado" cuando se resuelva la entrega en su totalidad pero no la totalidad de los articulos de la solicitud o 
porque hubo un canje por otra articulo que en este caso no se resolvio la totalidad de la cantidad.
-->
<?php
session_start();
if (!isset($_SESSION['logged']))
{
     echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
else
{
	if ($_SESSION['logged']==2)
	{
	     echo '<meta http-equiv="refresh" content="0; url=login.php">';
	}
}
$fechaGuardada = $_SESSION["ultimoAcceso"];
$ahora = date("Y-n-j H:i:s");
$tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
if($tiempo_transcurrido >= 1800) //30 min en seg.
{
   echo '<meta http-equiv="refresh" content="0; url=login.php">';
}
else
{
  $_SESSION["ultimoAcceso"] = $ahora;
} 
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];
$parcial=$_COOKIE['parcial'];
?>

<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr>
		<td align="center"><font size="6" color="#000066">Art&iacute;culos de Solicitud <?php echo $solid;?></font><br></td>
		</tr>
	       <?php
			$consultaII="Select * from StkSolicitudes where StkSolId='".$solid."'";
			$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
			$laSolicitud=mysqli_fetch_assoc($resultadoII);
			$elEstado=$laSolicitud['StkSolEstado'];
			$laUnidad=$laSolicitud['StkSolSecId'];

			//La unidad buscada es la indicada en la tabla de solicitudes, no paso por parametro pues podría estar filtrando por unidad=0 ("Todas"), dato que no nos serviría en esta instancia
			$consultaIII="Select * from Departamentos where DepId='".$laUnidad."'";
			$resultadoIII=mysqli_query($cn,$consultaIII) or die('La consulta fall&oacute;: ' .mysqli_error());
		       $uniEle=mysqli_fetch_assoc($resultadoIII);
			$uniNombre=$uniEle['DepNombre'];
		?>
		<tr>
			<td align="center"><font size="4" color="#000066">Estado Solicitud: <?php echo $elEstado;?> - Unidad: <?php echo $uniNombre;?></font><br></td>
		</tr>
		<td align="center"><a href="index.php?estado=<?php echo $estadocall;?>&unidad=<?php echo $unidadcall;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a></td>
	</table>
       <?php
		$consulta="Select * from StkSolArticulos as s, StkArticulos as a, Usuarios as u where s.StkSolArtUsuCre=u.UsuId and a.StkArtId=s.StkArtId and s.StkSolId='".$solid."' order by a.StkArtDsc";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0){
	            	echo '<br><center><label>No existen articulos registrados para la Solicitud</label></center><br>';
		}
		else
		{
		   if($elEstado=="Cancelada" or $elEstado=="Construyendo" or $elEstado=="Autorizar")
		   {
     	        	echo '<br><table class="inventario">
			<tr bgcolor="#6495ED">
                     	<th>Art&iacute;culo</th>
                       	<th>Cant.Solicitada</th>
                       	<th>Registrado</th>
				<th>Observaciones</th>
			</tr>';
			while($unSolArticulo=mysqli_fetch_assoc($resultado))
			{
              		if($colorlinea=='#F3F3F3')
				{
       			$colorlinea='#FEFEFE';
				}
				else
				{
				$colorlinea='#F3F3F3';
				}
				$Observa="--";
				if ($unSolArticulo['StkSolArtCantCanje']>0)
				{
					$Observa="Hubo Canje por cantidad no entregada";
				}
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
              	       	<td>'.$unSolArticulo['StkArtDsc'].'</td>
					<td align="center">'.$unSolArticulo['StkSolArtCantSol'].'</td>
              	       	<td align="left">'.$unSolArticulo['UsuNombre']." ".$unSolArticulo['UsuApellido'].'</td>
	             	       	<td>'.$Observa.'</td>
				</tr>';
			}
		   }
		   if($elEstado=="Finalizada" or $elEstado=="Pendiente de Entrega" or $elEstado=="Imprimir Remito")
		   {
     	        	echo '<br><table class="inventario">
			<tr bgcolor="#6495ED">
                     	<th>Art&iacute;culo</th>
                       	<th>Registrado</th>
                       	<th>Solicita</th>
                       	<th>Pendiente</th>
                       	<th>Entregas</th>
                       	<th>Estado</th>';
			if ($parcial==1)
			{
                       	echo '<th>Fechas de Entregas</th>';
			}
			echo '<th>Observaciones</th>
			</tr>';
			while($unSolArticulo=mysqli_fetch_assoc($resultado))
			{
              		if($colorlinea=='#F3F3F3')
				{
       			$colorlinea='#FEFEFE';
				}
				else
				{
				$colorlinea='#F3F3F3';
				}
				$consultaIV="Select * from StkMovArticulos where StkArtId='".$unSolArticulo['StkArtId']."' and StkSolId='".$solid."'";
				$resultadoIV=mysqli_query($cn,$consultaIV);
				$CantEnt=0;
				$Observa="--";
				if (mysqli_num_rows($resultadoIV)==0)
				{
					$icoalerta="Images/borrar.jpg";
					$CantPen=$unSolArticulo['StkSolArtCantSol'];
				}
				else
				{
					$icoalerta="Images/icono_check.gif";
					while($unMovArticulo=mysqli_fetch_assoc($resultadoIV))
					{
						if ($unMovArticulo['StkMovArtTpo']=="S")
						{
							$CantEnt=$CantEnt+$unMovArticulo['StkMovArtCant'];
						}
						else
						{
							$Observa="Hubo devolucion de articulo.";
							$CantEnt=$CantEnt-$unMovArticulo['StkMovArtCant'];
						}
					}
					$CantPen=$unSolArticulo['StkSolArtCantSol']-$CantEnt;
				}
				if($CantPen<>$unSolArticulo['StkSolArtCantSol'] and $CantPen>0)
				{
					$icoalerta="Images/icono_check_rojo.gif";
				}
				if($unSolArticulo['StkSolArtEstado']=='Finalizada' and $unSolArticulo['StkSolArtCantCanje']>0)
				{
					$Observa="Hubo canje de articulo.";
				}
				
				echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
              	       	<td align="left">'.$unSolArticulo['StkArtDsc'].'</td>
              	       	<td align="left">'.$unSolArticulo['UsuNombre']." ".$unSolArticulo['UsuApellido'].'</td>
					<td>'.$unSolArticulo['StkSolArtCantSol'].'</td>
					<td>'.$CantPen.'</td>
					<td>'.$CantEnt.'</td>
					<td><a><img src="'.$icoalerta.'" witdh="15" height="15" border=0></a></td>';
					if ($parcial==1)
					{
					echo '<td><a href="articulossolmov.php?solid='.$unSolArticulo['StkSolId'].'&artid='.$unSolArticulo['StkArtId'].'&estadocall='.$estadocall.'&unidadcall='.$unidadcall.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>';
					}
					echo '<td align="left">'.$Observa.'</td>
				</tr>';
                  
			}
		   }
		}
		echo '</table><br>';
    ?>
    <tr>
       <td align="center">&nbsp;</td>
    </tr>
</center>
<?php
require_once("pie.php");
?>


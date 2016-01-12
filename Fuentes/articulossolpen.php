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
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
	<tr>
		<td align="center"><font size="6" color="#000066">Detalle de art&iacute;culos sin entrega</font><br></td>
	</tr>
	<tr>
		<td align="center"><font size="6" color="#000066">de Solicitudes en estado Pendiente</font><br></td>
	</tr>
	<tr>
		<td align="center">
		<a href="index.php?estado=<?php echo $estadocall;?>&unidad=<?php echo $unidadcall;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
		</td>
	</tr>
	<?php
		$estadoart="Pendiente"; //Involucrado con requerimiento de entrega parcial, si no hubo entrega alguna o hubo entrega parcial el renglon del articulo tiene este valor
		$estadosol="Pendiente de Entrega";
		$consultaII="Select * from StkSolArticulos as a, StkSolicitudes as s, StkArticulos as k where a.StkSolArtEstado='".$estadoart."' and s.StkSolEstado='".$estadosol."' and a.StkSolId=s.StkSolId and a.StkArtId=k.StkArtId order by k.StkArtDsc";
		$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
		$colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultadoII)==0)
		{
	            	echo '<br><center><label>No hay art&iacute;culos pendientes de entrega</label></center><br>';
		}
		else
		{
	             	echo '<br><table class="inventario">
				<tr bgcolor="#MM0077">
		             	<th>Art&iacute;culo</th>
	       	      	<th>Stock actual</th>
	             		<th>Cantidad sin entregar</th>
       	      		<th>Solicitudes involucradas</th>
			</tr>';
			$elart=0;
			$Total=0;
			while($losArticulos=mysqli_fetch_assoc($resultadoII))
			{
				if($elart<>$losArticulos['StkArtId'])
				{
					if($elart>0)
					{
						$consultaIV="Select * from StkArticulos where StkArtId='".$elart."'";
						$resultadoIV=mysqli_query($cn,$consultaIV);
						$elArticulo=mysqli_fetch_assoc($resultadoIV);
						echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	       		         		<td>'.$elArticulo['StkArtDsc'].'</td>
							<td align="center">'.$elArticulo['StkArtCantReal'].'</td>
							<td align="center">'.$Total.'</td>
					              <td align="center"><a href="solicitudesartpen.php?artid='.$elArticulo['StkArtId'].'&artdsc='.$elArticulo['StkArtDsc'].'&estadocall='.$estadocall.'&unidadcall='.$unidadcall.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
						</tr>';
						$Total=0;
 					}
					$elart=$losArticulos['StkArtId'];
				}
				//Involucra entrega parcial
				$tipomov="S";
				$consultaIII="Select * from StkMovArticulos where StkArtId='".$losArticulos['StkArtId']."' and StkSolId='".$losArticulos['StkSolId']."' and StkMovArtTpo='".$tipomov."'";
				$resultadoIII=mysqli_query($cn,$consultaIII);
				$Solicitado=$losArticulos['StkSolArtCantSol'];
				$Entregado=0;
				if (mysqli_num_rows($resultadoIII)==0)
				{
					//No hay entrega parcial sino hay movimientos en esta tabla
				}
				else
				{
					while($unMovArticulo=mysqli_fetch_assoc($resultadoIII))
					{
						$Entregado=$Entregado+$unMovArticulo['StkMovArtCant'];			
					}
				}
				$Entregable=$Solicitado-$Entregado;
				$Total=$Total+$Entregable;
			}
			$consultaIV="Select * from StkArticulos where StkArtId='".$elart."'";
			$resultadoIV=mysqli_query($cn,$consultaIV);
			$elArticulo=mysqli_fetch_assoc($resultadoIV);
			echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	         		<td>'.$elArticulo['StkArtDsc'].'</td>
				<td align="center">'.$elArticulo['StkArtCantReal'].'</td>
				<td align="center">'.$Total.'</td>
		              <td align="center"><a href="solicitudesartpen.php?artid='.$elArticulo['StkArtId'].'&artdsc='.$elArticulo['StkArtDsc'].'&estadocall='.$estadocall.'&unidadcall='.$unidad.'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
			</tr>';
		}
	?>
	</table>
	<tr>
	       <td align="center">&nbsp;</td>
	</tr>
</center>
<?php
require_once("pie.php");
?>
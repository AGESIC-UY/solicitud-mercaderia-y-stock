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
$artid=$_REQUEST['artid'];
$artdsc=$_REQUEST['artdsc'];
$estadocall=$_REQUEST['estadocall'];
$unidadcall=$_REQUEST['unidadcall'];
$parcial=$_COOKIE['parcial'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
	<tr>
		<td align="center"><font size="6" color="#000066">Solicitudes involucradas</font><br></td>
	</tr>
	<tr>
		<td align="center"><font size="6" color="#000066">de Art&iacute;culo no entregado</font><br></td>
	</tr>
	<tr>
		<td align="center"><font size="6" color="#000066"><?php echo $artdsc;?></font><br></td>
	</tr>
	<tr>
		<td align="center"><a href="index.php?estado=<?php echo $estadocall;?>&unidad=<?php echo $unidadcall;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a></td>
	</tr>
       <?php
		$estadoart="Pendiente";  //Involucrado con requerimiento de entrega parcial, si no hubo entrega alguna o hubo entrega parcial el renglon del articulo tiene este valor
		$estadosol="Pendiente de Entrega";
		$consultaII="Select * from StkSolArticulos as a, StkSolicitudes as s, Usuarios as u, Departamentos as d where a.StkSolArtEstado='".$estadoart."' and s.StkSolEstado='".$estadosol."' and a.StkSolId=s.StkSolId and a.StkArtId='".$artid."' and u.usuid=s.StkSolUsuCre and d.DepId=s.StkSolSecId order by a.StkSolId";
		$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
	             	<th>Solicitud</th>
	             	<th>Usuario</th>
	             	<th>Unidad</th>
	             	<th>Cantidad Solicitada</th>';
			if($parcial==1)
			{
	       		echo '<th><font color="#FFFF00">Cantidad Pendiente</th>';
			}
		echo '</tr>';
		while($lasSolicitudes=mysqli_fetch_assoc($resultadoII))
		{
			$tipomov="S";
			$consultaIII="Select * from StkMovArticulos where StkArtId='".$artid."' and StkSolId='".$lasSolicitudes['StkSolId']."' and StkMovArtTpo='".$tipomov."'";
			$resultadoIII=mysqli_query($cn,$consultaIII);
			$Solicitado=$lasSolicitudes['StkSolArtCantSol'];
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
			echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	         		<td>'.$lasSolicitudes['StkSolId'].'</td>
	         		<td>'.$lasSolicitudes['UsuUsuario'].'</td>
	         		<td>'.$lasSolicitudes['DepNombre'].'</td>
	         		<td align="center">'.$lasSolicitudes['StkSolArtCantSol'].'</td>';
				if($parcial==1)
				{
		         		echo '<td align="center">'.$Entregable.'</td>';
				}
			echo '</tr>';
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

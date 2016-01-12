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
require_once("Includes/conviertefecha.php");
$solid=$_REQUEST['solid'];
$artid=$_REQUEST['artid'];

$consultaI="Select * from StkArticulos where StkArtId='".$artid."'";
$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
$elArticulo=mysqli_fetch_assoc($resultadoI);
$elArtDsc=$elArticulo['StkArtDsc'];

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

//Operador o Administrador, así vuelve a la lista de solicitudes de todas las unidades no de la especifica de la solicitud consultada
if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==3)
{
	$laUnidad=0;
}
?>
<center>
	<table width="800" border="0" align="center">
	<tr>
		<td align="center"><font size="6" color="#000066">Solicitud <?php echo $solid;?> - Movimientos de Art&iacute;culo</font><br></td>
	</tr>
	<tr>
		<td align="center"><font size="6" color="#000066"><?php echo $elArtDsc;?></font><br></td>
	</tr>
	<tr>
		<td align="center"><font size="4" color="#000066">Estado Solicitud: <?php echo $elEstado;?> - Unidad: <?php echo $uniNombre;?></font><br></td>
	</tr>
	<td align="center"><a href="index.php?estado=<?php echo $elEstado;?>&unidad=<?php echo $laUnidad;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a></td>
	</table>
            <?php
		$consulta="Select * from StkMovArticulos where StkSolId='".$solid."' and StkArtId='".$artid."'";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());

	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0){
	            	echo '<br><center><label>No hubo entrega de articulo cantidad cero</label></center><br>';
		}
		else
		{
	        	echo '<br><table border="1"><tr bgcolor="#MM0077">
		             	<td><label><font color="#FFFF00">Fecha</label></td>
		             	<td><label><font color="#FFFF00">Cantidad</label></td>
	       	      	<td><label><font color="#FFFF00">Tipo Movimiento</label></td>
			</tr>';
			while($unMovArticulo=mysqli_fetch_assoc($resultado))
			{
				if ($unMovArticulo['StkMovArtTpo']=='S')
				{
					$tipomov="entrega";
				}
				else
				{
					$tipomov="devolucion";
				}
				$laFch=cambiaf_a_normal($unMovArticulo['StkMovArtFch']);
				echo '<tr align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
		      	       	<td>'.$laFch.'</td>
					<td align="center">'.$unMovArticulo['StkMovArtCant'].'</td>
					<td align="left">'.$tipomov.'</td>
				</tr>';
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
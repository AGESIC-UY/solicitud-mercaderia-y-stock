<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprg=$_REQUEST['idprv'];
$idfac=$_REQUEST['idfac'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr>
		<td align="center"><font size="5" color="#000066">Art&iacute;culos de Factura</font><br></td>
		</tr>
	</table>

            <?php
		$consulta="Select * from StkMovArticulos where StkPrvFacId=".$idfac;
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0){
	            	echo '<br><center><label>No existen articulos registrados para la Factura</label></center><br>';
		}
		else
		{
		$consultaI="Select * from StkPrvFacturas where StkPrvFacId=".$idfac;
		$resultadoI=mysqli_query($cn,$consultaI);
		$laFactura=mysqli_fetch_assoc($resultadoI);
		if ($laFactura['StkPrvFacFin']=="1"){
	             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                     	<th>Descripci&oacute;n</th>
                       	<th>Cantidad</th>
                       	<th>Precio</th>
                       	<th>c/Iva</th>
			</tr>';
			while($unSolArticulo=mysqli_fetch_assoc($resultado)){
				$consultaI="Select * from StkProveedores where StkPrvId=".$unSolArticulo['StkMovArtServId'];
				$resultadoI=mysqli_query($cn,$consultaI);
				$elservice=mysqli_fetch_assoc($resultadoI);
				$prvservice=$elservice['StkPrvRzoSoc'];

				$consultaI="Select * from StkArticulos where StkArtId='".$unSolArticulo['StkArtId']."'";
				$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
			       $unArticulo=mysqli_fetch_assoc($resultadoI);
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                     	<td>'.$unArticulo['StkArtDsc'].'</td>
					<td>'.$unSolArticulo['StkMovArtCant'].'</td>
					<td>'.$unSolArticulo['StkMovArtPrecio'].'</td>
					<td>'.$unSolArticulo['StkMovArtPrecioCIva'].'</td>
				</tr>';
			}//Cierra el WHILE 
		}
		else
		{
	             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                     	<th>Descripci&oacute;n</th>
                       	<th>Cantidad</th>
                       	<th>Precio</th>
                       	<th>c/Iva</th>
                      	<th>Eliminar</th>
			</tr>';
			while($unSolArticulo=mysqli_fetch_assoc($resultado))
			{
				$consultaI="Select * from StkProveedores where StkPrvId=".$unSolArticulo['StkMovArtServId'];
				$resultadoI=mysqli_query($cn,$consultaI);
				$elservice=mysqli_fetch_assoc($resultadoI);
				$prvservice=$elservice['StkPrvRzoSoc'];

				$consultaI="Select * from StkArticulos where StkArtId='".$unSolArticulo['StkArtId']."'";
				$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
			       $unArticulo=mysqli_fetch_assoc($resultadoI);
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                     	<td>'.$unArticulo['StkArtDsc'].'</td>
					<td>'.$unSolArticulo['StkMovArtCant'].'</td>
					<td>'.$unSolArticulo['StkMovArtPrecio'].'</td>
					<td>'.$unSolArticulo['StkMovArtPrecioCIva'].'</td>
					<td align="center"><a href="eliminomovart.php?idprv='.$idprv.'&idfac='.$idfac.'&idart='.$unSolArticulo['StkArtId'].'"><img src="Images/cancelar.gif" witdh="15" height="15"></a></td>
				</tr>';
			}//Cierra el WHILE 
		}
	}//Cierra el IF donde se pregunta si hay resultados o no
	echo '</table><br>';
       ?>
    <tr>
       <td align="center">&nbsp;</td>
    </tr>
</center>
<?php
require_once("pie.php");
?>
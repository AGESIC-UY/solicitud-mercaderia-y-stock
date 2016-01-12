<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$idfac=$_REQUEST['idfac'];

	$consulta="Select * from StkMovArticulos where StkPrvFacId=".$idfac;
	$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	$TotalFac=0;
	$TotalFaccIva=0;
	while($unMovArticulo=mysqli_fetch_assoc($resultado))
	{
		$TotalFac=$TotalFac+$unMovArticulo['StkMovArtPrecio'];
		$TotalFaccIva=$TotalFaccIva+$unMovArticulo['StkMovArtPrecioCIva'];
	}
	
	$consultaI="Select * from StkPrvFacturas where StkPrvFacId=".$idfac;
	$resultadoI=mysqli_query($cn,$consultaI);
	$laFactura=mysqli_fetch_assoc($resultadoI);

	$consultaII="Select * from StkProveedores where StkPrvId=".$idprv;
	$resultadoII=mysqli_query($cn,$consultaII);
	$elProveedor=mysqli_fetch_assoc($resultadoII);
	
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
	<tr>
		<td align="center">
			<font size="5" color="#000066">Art&iacute;culos ingresados al Stock</font>
			<br>
			<font size="5" color="#000066"><?php echo $elProveedor['StkPrvRzoSoc'];?></font>
			<br>
			<font size="4" color="#000066">Factura Nro.<?php echo $laFactura['StkPrvFacNum'];?></font>
			<br>
			<font size="4" color="#000066">Monto: <?php echo $TotalFac;?></font>
			<br>
			<font size="4" color="#000066">Monto Iva inc.: <?php echo $TotalFaccIva;?></font>

			<br>
			<br>
			<?php
				if ($_COOKIE['usuperfil']==3 or $_COOKIE['usuperfil']==6) //Administrador, Consultor Financiero
				{
			?>
					<a href="proveedoresfacver.php?idprv=<?php echo $idprv;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
			<?php
				}

				if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==10) //Operador, Administrador inventario
				{
			?>
					<a href="proveedoresfacstk.php?idprv=<?php echo $idprv;?>"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
			<?php
				}
				//El resto de los perfiles no acceden a este objeto
			?>
		</td>
		<br>
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
		if ($laFactura['StkPrvFacFin']>="1"){
	             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                     	<th>Descripci&oacute;n</th>
                       	<th>Precio</th>
                       	<th>c/Iva</th>
			</tr>';
			while($unMovArticulo=mysqli_fetch_assoc($resultado)){
              		if($colorlinea=='#F3F3F3'){
	       			$colorlinea='#FEFEFE';
				}
				else
				{
					$colorlinea='#FEFEFE';
				}
				$consultaII="Select * from StkArticulos where StkArtId='".$unMovArticulo['StkArtId']."'";
				$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
			       $unArticulo=mysqli_fetch_assoc($resultadoII);
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                     	<td>'.$unArticulo['StkArtDsc'].'</td>
					<td>'.$unMovArticulo['StkMovArtPrecio'].'</td>
					<td>'.$unMovArticulo['StkMovArtPrecioCIva'].'</td>
				</tr>';
			}//Cierra el WHILE 
		}
		else
		{
	             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                     	<th>Descripci&oacute;n</th>
                       	<th>Precio</th>
                       	<th>c/Iva</th>
                      	<th>Eliminar</th>
			</tr>';
			while($unMovArticulo=mysqli_fetch_assoc($resultado)){
              		if($colorlinea=='#F3F3F3'){
	       			$colorlinea='#FEFEFE';
				}
				else
				{
					$colorlinea='#FEFEFE';
				}
				$consultaII="Select * from StkArticulos where StkArtId='".$unMovArticulo['StkArtId']."'";
				$resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
			       $unArticulo=mysqli_fetch_assoc($resultadoII);
				$coniva=$unMovArticulo['StkMovArtPrecio']*$unArticulo['StkArtIVA'];
				echo '<tr  align="left" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                     	<td>'.$unArticulo['StkArtDsc'].'</td>
					<td>'.$unMovArticulo['StkMovArtPrecio'].'</td>
					<td>'.$coniva.'</td>';
					if ($_COOKIE['usuperfil']==3 or $_COOKIE['usuperfil']==6) //Administrador, Consultor Financiero
					{
					echo '<td>--</td>';
					}
					if ($_COOKIE['usuperfil']==2 or $_COOKIE['usuperfil']==10) //Operador, Administrador inventario
					{
					echo '<td><a href="eliminomovart.php?idprv='.$idprv.'&idfac='.$idfac.'&idart='.$unMovArticulo['StkArtId'].'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>';
					}
				echo '</tr>';
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
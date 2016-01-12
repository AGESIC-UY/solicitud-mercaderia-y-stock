<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idprv=$_REQUEST['idprv'];
$consulta="Select * from StkProveedores where StkPrvId='".$idprv."'";
$resultado=mysqli_query($cn,$consulta) or die('La consulta 1fall&oacute;: ' .mysqli_error());
$elPrv=mysqli_fetch_assoc($resultado);
$elPrvNom=$elPrv['StkPrvRzoSoc'];
$fchfac=cambiaf_a_normal(Date("Y-m-d H:i"));
?>
<script type="text/JavaScript" language="javascript">
function confirmEliminar()
{
	var agree=confirm(" Confirma Eliminar la factura ? ");
	if (agree) return true ;
	else return false ;
}
</script>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr>
		<td align="center" colspan="2"><font size="6" color="#000066">Facturas de Proveedor "<?php echo $elPrvNom;?>"</font><br></td>
		</tr>
		<td align="left">
		<a href="proveedores.php"><img width="40" height="40" src="Images/volver.jpg" border=0></a>
		</td>
		<?php
		if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfila para ingresar en este objeto
		{
			echo '<td align="left"><a href="ingresofacprv.php?idprv='.$idprv.'&fchfac='.$fchfac.'"><img src="Images/nuevo.png" height="40" width="40" border=0><br></a></td>';
		}
		else
		{
			echo '<td>&nbsp;</td>';
		}
		?>
	</table>

	       <?php
		$consulta="Select * from StkPrvFacturas where StkPrvId='".$idprv."' order by StkPrvFacFch DESC";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta 2fall&oacute;: ' .mysqli_error());
	       if (mysqli_num_rows($resultado)==0)
		{
	            	echo '<br><center><label>No existen facturas registradas para el Proveedor</label></center><br>';
		}
		else
		{
             	echo '<br><table class="inventario">
			<tr bgcolor="#MM0077">
                    	<th>Fecha de factura</th>
                    	<th>Factura Numero</th>
                   	<th>Monto</th>
                    	<th>Monto c/Iva</th>
                    	<th>Art&iacute;culos</th>
	 		<th>Detalle</th>
                    	<th>Eliminar</th>
                    	<th>Estado</th>
             	 	<th>Observaciones</th>
			</tr>';
		while($laFactura=mysqli_fetch_assoc($resultado))
		{
			$consultaI="Select * from StkMovArticulos where StkPrvFacId=".$laFactura['StkPrvFacId'];
			$resultadoI=mysqli_query($cn,$consultaI);
			$TotalFac=0;
			$TotalFacIva=0;
			$TotalArt=0;
			while($unMovArticulo=mysqli_fetch_assoc($resultadoI))
			{
				$TotalFac=$TotalFac+$unMovArticulo['StkMovArtPrecio'];
				$TotalFacIva=$TotalFacIva+$unMovArticulo['StkMovArtPrecioCIva'];
				$TotalArt=$TotalArt+1;
			}

			if ($_COOKIE['usuperfil']<>6) //consultor el else corresponde al administrador o personal con perfila para ingresar en este objeto
			{
			       if ($laFactura['StkPrvFacFin']>=1){
					echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	       	             	<td>'.cambiaf_a_normal($laFactura['StkPrvFacFch']).'</td>
	              	      	<td>'.$laFactura['StkPrvFacNum'].'</td>
	                     	<td>'.$TotalFac.'</td>
	                     	<td>'.$TotalFacIva.'</td>
	                     	<td>'.$TotalArt.'</td>
					<td><a href="articulosfacstk.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
					<td><img src="Images/blank.gif" witdh="15" height="15" border=0></td>';
				       if ($laFactura['StkPrvFacFin']==1)
					{
						echo '<td><img src="Images/clock.png" witdh="15" height="15" border=0></td>';
					}
					else
					{
						echo '<td><img src="Images/icono_check.gif" witdh="15" height="15" border=0></td>';
					}
	                     	echo '<td>'.$laFactura['StkPrvFacObs'].'</td>
					</tr>';
				}
				else
				{
					echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                     	<td>'.cambiaf_a_normal($laFactura['StkPrvFacFch']).'</td>
	                     	<td>'.$laFactura['StkPrvFacNum'].'</td>
	                     	<td>'.$TotalFac.'</td>
	                     	<td>'.$TotalFacIva.'</td>
	                     	<td>'.$TotalArt.'</td>
					<td><a href="ingresofacprvart.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/modificar.png" witdh="15" height="15" border=0></a></td>
					<td onclick="return(confirmEliminar())"><a href="eliminofacprvart.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>
					<td><a href="confirmafac.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/guardar.gif"  witdh="15" height="15" border=0></a></td>
	                     	<td>'.$laFactura['StkPrvFacObs'].'</td>';
					echo '</tr>';
				}
			}
			else
			{
					echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
	                     	<td>'.cambiaf_a_normal($laFactura['StkPrvFacFch']).'</td>
	                     	<td>'.$laFactura['StkPrvFacNum'].'</td>
	                     	<td>'.$TotalFac.'</td>
	                     	<td>'.$TotalFacIva.'</td>
	                     	<td>'.$TotalArt.'</td>
					<td><a href="articulosfacstk.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/information.png" witdh="15" height="15" border=0></a></td>
					<td><img src="Images/blank.gif" witdh="15" height="15" border=0></td>';
				       if ($laFactura['StkPrvFacFin']==1)
					{
						echo '<td><img src="Images/clock.png" witdh="15" height="15" border=0></td>';
					}
					else
					{
					       if ($laFactura['StkPrvFacFin']>1)
						{
							echo '<td><img src="Images/icono_check.gif" witdh="15" height="15" border=0></td>';
						}
						else
						{
							echo '<td><img src="Images/modificar.png" witdh="15" height="15" border=0></td>';
						}
					}
                     		echo '<td>'.$laFactura['StkPrvFacObs'].'</td>
					</tr>';
			}
		}//Cierra el WHILE de las facturas
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
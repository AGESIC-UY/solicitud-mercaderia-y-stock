<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
?>
<script type="text/JavaScript" language="javascript">
function confirmNot()
{
	var agree=confirm("Confirma rechazar de la factura? La misma volvera a la orbita de adquisiciones para analizar ");
	if (agree) return true ;
	else return false ;
}
</script>
<script type="text/JavaScript" language="javascript">
function confirmFac()
{
	var agree=confirm("Confirma la factura? Si la confirma el material sera sumado al Stock ");
	if (agree) return true ;
	else return false ;
}
</script>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
		<tr>
		<td align="center"><font size="6" color="#000066">Facturas para ingresar Art&iacute;culos al Stock</font><br></td>
		</tr>
	</table>

	       <?php
			//
			$fin=1;
			$consulta="Select * from StkPrvFacturas as p, StkMovArticulos as m, StkArticulos as a, StkArtCls as c , StkArtClsUsu as u where p.StkPrvFacFin='".$fin."' and p.StkPrvFacId=m.StkPrvFacId and m.StkArtId=a.StkArtId and a.StkArtClsId=c.StkArtClsId and c.StkArtClsBien='".$_COOKIE['tipobien']."' and u.StkArtClsId=c.StkArtClsId and u.UsuId='".$_COOKIE['usuid']."' group by p.StkPrvFacId";
			$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());

		       $colorlinea='#F3F3F3';
		       if (mysqli_num_rows($resultado)==0)
			{
	       	     	echo '<br><center><label>No existen facturas para ingresar Art&iacute;culos al Stock</label></center><br>';
			}
			else
			{//Integrando ahora bienes de uso o consumo, debo considerar el bien del usuario logged. 
			    	echo '<br><table class="inventario">
				<tr bgcolor="#MM0077">
                     	<th>Fecha de factura</th>
                     	<th>Factura Numero</th>
                     	<th>Proveedor</th>
                       	<th>Monto</th>
                       	<th>Art&iacute;culos</th>
                       	<th>Detalle</th>
                       	<th>Confirmar Ingreso</th>
                       	<th>Rechazar Ingreso</th>
				</tr>';
				while($laFactura=mysqli_fetch_assoc($resultado))
				{
              			if($colorlinea=='#F3F3F3')
					{
       					$colorlinea='#FEFEFE';
					}
					else
					{
						$colorlinea='#FEFEFE';
					}

					$consultaI="Select * from StkProveedores where StkPrvId='".$laFactura['StkPrvId']."'";
					$resultadoI=mysqli_query($cn,$consultaI) or die('La consulta fall&oacute;: ' .mysqli_error());
					$elPrv=mysqli_fetch_assoc($resultadoI);
					$elPrvNom=$elPrv['StkPrvRzoSoc'];

					$consultaII="Select * from StkMovArticulos where StkPrvFacId=".$laFactura['StkPrvFacId'];
					$resultadoII=mysqli_query($cn,$consultaII);
					$TotalFac=0;
					$TotalArt=0;
					while($unMovArticulo=mysqli_fetch_assoc($resultadoII))
					{
						$TotalFac=$TotalFac+$unMovArticulo['StkMovArtPrecio'];
						$TotalArt=$TotalArt+1;
					}

					echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
              	       	<td>'.cambiaf_a_normal($laFactura['StkPrvFacFch']).'</td>
	                     	<td>'.$laFactura['StkPrvFacNum'].'</td>
	                     	<td>'.$elPrvNom.'</td>
	                     	<td>'.$TotalFac.'</td>
       	              	<td>'.$TotalArt.'</td>
					<td><a href="articulosfacstk.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/information.png" witdh="15" height="15"></a></td>
					<td onclick="return(confirmFac())"><a href="ingresoartfac.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/guardar.gif"  witdh="15" height="15"></a></td>
					<td onclick="return(confirmNot())"><a href="confirmafac.php?idprv='.$laFactura['StkPrvId'].'&idfac='.$laFactura['StkPrvFacId'].'"><img src="Images/cancelar.gif"  witdh="15" height="15"></a></td>
					</tr>';
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
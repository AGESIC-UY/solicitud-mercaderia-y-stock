<?php
$menutab=basename(__FILE__, ".php"); // devuelve el nombre sin extension .php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$solid=$_REQUEST['solid'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
	<table class="inventario">
	<tr>
            <?php
            	echo '<th align="center"><font size="5" color="#000066">Art&iacute;culos a solicitar </font><br></th></tr>';
            ?>

	</table>

            <?php
		$consulta="Select * from StkSolArticulos as s, StkArticulos as a where s.StkSolId='".$solid."' and s.StkArtId=a.StkArtId order by a.StkArtDsc";
		$resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
	       $colorlinea='#F3F3F3';
	       if (mysqli_num_rows($resultado)==0)
		{
            		echo '<br><center><label>No existen articulos registrados para la Solicitud</label></center><br>';
		}
		else
		{
	             	echo '<br><table class="inventario">
			<tr>
                     	<th>Descripci&oacute;n</th>
                       	<th>Cantidad</th>
                        	<th>Eliminar de la Lista</th>
                        	<th>Observaci&oacute;n</th>
			</tr>';
			while($unSolArticulo=mysqli_fetch_assoc($resultado))
			{
              		if($unSolArticulo['StkArtCantReal']=='0'){
	       			$observa='No hay Stock para cumplir el pedido ';
				}
				else
				{
					if ($unSolArticulo['StkSolArtCantSol']>$unSolArticulo['StkArtCantReal'])
					{
	       			$observa='Cantidad solicitada supera la cantidad en stock';
					}	
					else
					{
					$observa=' ';
					}
				}

				echo '<tr align="left">
                     	<td>'.$unSolArticulo['StkArtDsc'].'</td>
				<td align="center">'.$unSolArticulo['StkSolArtCantSol'].'</td>
				<td align="center"><a href="eliminosolart.php?solid='.$solid.'&idart='.$unSolArticulo['StkArtId'].'"><img src="Images/cancelar.gif" witdh="15" height="15" border=0></a></td>
				<td align="center">'.$observa.'</td>
				</tr>';
			}//Cierra el WHILE que imprime los resultados obtenidos
		}//Cierra el IF donde se pregunta si hay resultados o no
		echo '</table><br>';
            ?>

    <tr>
       <th align="center">&nbsp;</th>
    </tr>
</center>

<?php
require_once("pie.php");
?>
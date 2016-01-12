<?php
require_once("principioseleccion.php");
require_once("funcionesbd.php");
$idsol=$_REQUEST['findsol'];
?>
<link href="css/estilo_inventario.css" rel="stylesheet" type="text/css" />
<center>
    <table class="inventario">
        <tr>
            <td align="center"><font size="6" color="#11A230">B&uacute;squeda de solicitud: <?php echo $idsol;?></font><br></td>
        </tr>
    </table>
    <?php
       $consulta="Select * from StkSolicitudes where StkSolId='".$idsol."'";
       $resultado=mysqli_query($cn,$consulta) or die('La consulta fall&oacute;: ' .mysqli_error());
       $colorlinea='#F3F3F3';
       if (mysqli_num_rows($resultado)==0)
	{
		echo '<br><center><label>No existen resultados posibles para '.$idsol.'</label></center><br>';
       }
       else
       {
      	       if($_COOKIE['usuperfil']==1 or $_COOKIE['usuperfil']==5 or $_COOKIE['usuperfil']==7) //solicitante, autorizador, consultor de unidad
		{
              	$buscarok=1;
		       $consultaII="Select * from StkSolicitudes where StkSolId='".$idsol."' and StkSolSecId='".$_COOKIE['usuunidad']."'";
		       $resultadoII=mysqli_query($cn,$consultaII) or die('La consulta fall&oacute;: ' .mysqli_error());
		       if (mysqli_num_rows($resultadoII)==0)
			{
				echo '<br><center><label>La solicitud '.$idsol.' no pertenece a la Unidad o &aacute;rea del usuario </label></center><br>';
				$buscarok=0;
		       }
		}
              else
              {
              	$buscarok=1;
		}
		
	       if ($buscarok==1)
		{
			echo '<br><table class="inventario">
			<tr bgcolor="#F3F3F3">
              	<th>Fecha</th>
	              <th>Oficina</th>
       	       <th>Estado</th>
              	<th>Fecha fin</th>
	              <th>Detalle</th>
       	       </tr>';
              	while($unaSolicitud=mysqli_fetch_assoc($resultado))
			{
			       $consultaI="Select * from Departamentos where DepId='".$unaSolicitud['StkSolSecId']."'";
			       $resultadoI=mysqli_query($cn,$consultaI);
				$LaOficina=mysqli_fetch_assoc($resultadoI);
	              	echo '<tr  align="center" bgcolor='.$colorlinea.' bordercolor='.$colorlinea.'>
       	              <td>'.cambiaf_a_normal($unaSolicitud['StkSolFchSol']).'</td>
              	       <td>'.$LaOficina['DepNombre'].'</td>
                    		<td>'.$unaSolicitud['StkSolEstado'].'</td>
	                     <td>'.cambiaf_a_normal($unaSolicitud['StkSolFchFin']).'</td>
				<td><a href="articulossolver.php?solid='.$unaSolicitud['StkSolId'].'&estadocall='.$unaSolicitud['StkSolEstado'].'&unidadcall='.$unaSolicitud['StkSolSecId'].'"><img src="Images/information.png" witdh="15" height="15"></a></td>
				</tr>';
	       		echo '</table><br>';
			}//Cierra el WHILE que imprime los resultados obtenidos
		}
	}//Cierra el IF donde se pregunta si hay resultados o no
    ?>
    <tr>
        <td align="center">&nbsp;</td>
    </tr>
    </table>
</center>

<?php
require_once("pie.php");
?>